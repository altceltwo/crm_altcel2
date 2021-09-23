<?php

namespace App\Http\Controllers;
use App\Pack;
use App\Radiobase;
use App\Rate;
use App\Politic;
use App\Pay;
use App\Ethernetpay;
use App\User;
use App\Number;
use App\Device;
use App\Change;
use App\Purchase;
use App\Assignment;
use DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index() {
        $data['packs'] = Pack::all();
        $data['radiobases'] = Radiobase::all();
        return view('ethernet.administration',$data);
    }

    public function createPack(Request $request) {
        Pack::insert([
            'name' => $request->post('name'),
            'description' => $request->post('description'),
            'service_name' => $request->post('service_name'),
            'recurrency' => $request->post('recurrency'),
            'price' => $request->post('price'),
            'price_s_iva' => $request->post('price_s_iva'),
            'price_install' => $request->post('price_install')
        ]);
    }

    public function createRadiobase(Request $request) {
        Radiobase::insert([
            'name' => $request->post('name'),
            'address' => $request->post('address'),
            'ip_address' => $request->post('ip_address'),
            'lat' => $request->post('lat'),
            'lng' => $request->post('lng')
        ]);
    }

    public function createPoliticRate() {
        $data['politics'] = Politic::all();
        return view('rates.politics',$data);
    }

    public function insertPoliticRate(Request $request) {
        $request = request()->except('_token');
        $x = Politic::insert($request);
        if($x){
            return 1;
        }else{
            return 0;
        }
    }

    public function destroy($politic_id){
        Politic::where('id', $politic_id)->delete();
        return back();
    }

    public function getPolitic($politic_id){
        $response = Politic::find($politic_id);;
        return $response;
    }

    public function updatePolitic(Request $request,Politic $politic){
        $id = $politic->id;
        $request = request()->except('_method','_token');
        $x = Politic::where('id',$id)->update($request);

        if($x){
            $message = 'Cambios guardados.';
            return back()->with('message',$message);
        }else{
            $message = 'Parece que ha ocurrido un error, intente de nuevo.';
            return back()->with('error',$message);
        }
    }

    public function changeStatusPacksRates(Request $request){
        $status = $request['status'];
        $id = $request['id'];
        $type = $request['type'];

        if($type == 'ethernet'){
            if($status == 'activo'){
                Pack::where('id',$id)->update(['status' => 'inactivo']);
            }else if($status == 'inactivo'){
                Pack::where('id',$id)->update(['status' => 'activo']);
            }
        }else if($type == 'altan'){
            if($status == 'activo'){
                Rate::where('id',$id)->update(['status' => 'inactivo']);
            }else if($status == 'inactivo'){
                Rate::where('id',$id)->update(['status' => 'activo']);
            }
        }
    }

    public function getPackEthernet(Request $request){
        $response = Pack::find($request->get('id'));
        return $response;
    }
    
    public function updatePackEthernet(Pack $pack_id, Request $request){
        $id = $pack_id->id;
        $price_install = $request->post('price_install');
        $request = request()->except('_token','id');
        if($price_install == null){
            $price_install = 0;
            $request['price_install'] = $price_install;
        }
        Pack::where('id',$id)->update($request);
        return back();
    }

    public function checkOverduePayments(Request $request){
        $response = Pay::all();
        return $response;
    }

    public function concesionesGeneral(){
        $data['concesiones'] = DB::table('users')
                                 ->leftJoin('purchases','purchases.who_did_id', '=', 'users.id')
                                 ->leftJoin('changes','changes.who_did_id', '=', 'users.id')
                                 ->leftJoin('pays','pays.who_did_id', '=', 'users.id')
                                 ->leftJoin('ethernetpays','ethernetpays.who_did_id', '=', 'users.id')
                                 ->where('purchases.who_did_id', '!=', 'null')
                                 ->orWhere('changes.who_did_id', '!=', 'null')
                                 ->select('users.name','users.id')
                                 ->distinct()
                                 ->get();
        return view('finance.cute',$data);
    }
    public function consulta($id ){
        $data['users'] = User::where('id', $id)->get();
        return view('finance.detalles',$data);
    }
    public function indexConcesiones(){
        $changes = DB::table('changes')
                      ->join('numbers','numbers.id', '=', 'changes.number_id')
                      ->join('rates','rates.id', '=', 'changes.rate_id')
                      ->join('activations','activations.numbers_id', '=', 'changes.number_id')
                      ->join('users', 'users.id', '=', 'changes.who_did_id')
                      ->where('changes.status', '=', 'pendiente')
                      ->where('changes.reason', '=', 'cobro')
                      ->select('changes.id', 'changes.status','changes.who_did_id', 'changes.reason', 'changes.amount', 'rates.name AS name_rate','numbers.MSISDN','users.name AS user_name','users.lastname AS user_lastname','changes.date AS date','activations.client_id AS client')
                      ->get();

        $data['changes'] = [];
        $data['changesTotal'] = 0;
        $data['changesCount'] = 0;
        foreach ($changes as $change) {
            $clientData = User::where('id',$change->client)->first();
            $clientname = $clientData->name. ' '.$clientData->lastname;
            array_push($data['changes'],array(
                'id' => $change->id,
                'status' => $change->status,
                'who_did_id' => $change->who_did_id,
                'reason' => $change->reason,
                'amount' => $change->amount,
                'name_rate' => $change->name_rate,
                'MSISDN' => $change->MSISDN,
                'user' => $change->user_name.' '.$change->user_lastname,
                'date' => $change->date,
                'client' => $clientname
            ));
            $data['changesTotal']+=$change->amount;
            $data['changesCount']+=1;
        }
        
        $purchases = DB::table('purchases')
                        ->join('numbers','numbers.id', '=', 'purchases.number_id')
                        ->join('offers','offers.id', '=', 'purchases.offer_id')
                        ->join('activations','activations.numbers_id', '=', 'purchases.number_id')
                        ->join('users', 'users.id', '=', 'purchases.who_did_id')
                        ->where('purchases.status', '=', 'pendiente')
                        ->where('purchases.reason', '=', 'cobro')
                        ->select('purchases.id', 'purchases.status', 'purchases.who_did_id', 'purchases.reason', 'purchases.amount', 'offers.name AS name_rate','numbers.MSISDN','users.name AS user_name', 'users.lastname AS user_lastname','purchases.date AS date','activations.client_id AS client')
                        ->get();

        $data['purchases'] = [];
        $data['purchasesTotal'] = 0;
        $data['purchasesCount'] = 0;
        foreach ($purchases as $purchase) {
            $clientData = User::where('id',$purchase->client)->first();
            $clientname = $clientData->name. ' '.$clientData->lastname;
            array_push($data['purchases'],array(
                'id' => $purchase->id,
                'status' => $purchase->status,
                'who_did_id' => $purchase->who_did_id,
                'reason' => $purchase->reason,
                'amount' => $purchase->amount,
                'name_rate' => $purchase->name_rate,
                'MSISDN' => $purchase->MSISDN,
                'user' => $purchase->user_name.' '.$purchase->user_lastname,
                'date' => $purchase->date,
                'client' => $clientname
            ));
            $data['purchasesTotal']+=$purchase->amount;
            $data['purchasesCount']+=1;
        }

        $data['pays'] = [];
        $data['paysTotal'] = 0;
        $data['paysCount'] = 0;
        $pays = DB::table('pays')
                   ->join('activations','activations.id', '=', 'pays.activation_id')
                   ->join('rates','rates.id', '=', 'activations.rate_id')
                   ->join('numbers','numbers.id', '=', 'activations.numbers_id')
                   ->join('users','users.id', '=', 'pays.who_did_id')
                   ->where('pays.status_consigned', 'pendiente')
                   ->select('pays.amount_received AS amount','pays.who_did_id', 'pays.status_consigned AS status','users.name AS  user_name', 'pays.id', 'numbers.MSISDN', 'rates.name AS name_rate', 'users.lastname AS user_lastname','pays.date_pay AS date','activations.client_id AS client')
                   ->get();

        foreach ($pays as $pay) {
            $clientData = User::where('id',$pay->client)->first();
            $clientname = $clientData->name. ' '.$clientData->lastname;
            array_push($data['pays'],array(
                'id' => $pay->id,
                'status' => $pay->status,
                'who_did_id' => $pay->who_did_id,
                'amount' => $pay->amount,
                'name_rate' => $pay->name_rate,
                'MSISDN' => $pay->MSISDN,
                'user' => $pay->user_name.' '.$pay->user_lastname,
                'date' => $pay->date,
                'client' => $clientname
            ));
            $data['paysTotal']+=$pay->amount;
            $data['paysCount']+=1;
        }
        return view('finance.index',$data);
    }

    public function consultaCortes(Request $request){
        $id = $request['id'];
        $status = $request['status'];
        $type = $request['type'];
        $bonificacion = $request['bonificacion'];
        $start = $request['start'];
        $año = substr($start, -4);
        $mes = substr($start, 0,2);
        $dia = substr($start, 3, -5);
        $dateStart = $año. '-'. $mes.'-'.$dia;
        $dateStart = $dateStart.' 00:00:00';
        $end = $request['end'];
        $añoEnd = substr($end, -4);
        $mesEnd = substr($end, 0,2);
        $diaEnd = substr($end, 3, -5);
        $dateEnd = $añoEnd. '-'. $mesEnd.'-'.$diaEnd;
        $dateEnd = $dateEnd.' 23:59:59';

        // //cambio

        // if ($type == 'changes') {
        //     $resp['consultas'] = Change::where('who_did_id', $id)->where('status', $status)->where('reason', $bonificacion)->whereBetween('date',[$dateStart,$dateEnd])->get();
        // }//compra
        // else if ($type == 'purchases') {
        //     $resp['consultas'] = Purchase::where('who_did_id', $id)->where('status', $status)->where('reason', $bonificacion)->whereBetween('date',[$dateStart,$dateEnd])->get();
        // }
        if ($type == 'purchases') {
            $resp['consultas'] = DB::table('purchases')
                                    ->join('numbers','numbers.id', '=', 'purchases.number_id')
                                    ->join('offers','offers.id', '=', 'purchases.offer_id')
                                    ->join('activations','activations.numbers_id', '=', 'purchases.number_id')
                                    ->join('users', 'users.id', '=', 'activations.client_id')
                                    ->where('purchases.who_did_id', '=', $id)
                                    ->where('purchases.status', '=', $status)
                                    ->where('purchases.reason', '=', $bonificacion)
                                    ->whereBetween('date', [$dateStart, $dateEnd])
                                    ->select('purchases.id', 'purchases.status', 'purchases.reason', 'purchases.amount', 'offers.name AS name_product','numbers.MSISDN','users.name AS client', 'users.lastname AS lastname','purchases.date AS date')
                                    ->get();
        }elseif ($type == 'changes') {
            $resp['consultas'] = DB::table('changes')
                                    ->join('numbers','numbers.id', '=', 'changes.number_id')
                                    ->join('rates','rates.id', '=', 'changes.rate_id')
                                    ->join('activations','activations.numbers_id', '=', 'changes.number_id')
                                    ->join('users', 'users.id', '=', 'activations.client_id')
                                    ->where('changes.who_did_id', $id)
                                    ->where('changes.status', '=', $status)
                                    ->where('changes.reason', '=', $bonificacion)
                                    ->whereBetween('date', [$dateStart, $dateEnd])
                                    ->select('changes.id', 'changes.status', 'changes.reason', 'changes.amount', 'rates.name AS name_product','numbers.MSISDN','users.name AS client', 'users.lastname AS lastname','changes.date AS date')
                                    ->get();
        }elseif ($type == 'monthly') {
            $resp['consultas'] = DB::table('pays')
                                   ->join('activations','activations.id', '=', 'pays.activation_id')
                                   ->join('rates','rates.id', '=', 'activations.rate_id')
                                   ->join('numbers','numbers.id', '=', 'activations.numbers_id')
                                   ->join('users','users.id', '=', 'activations.client_id')
                                   ->where('pays.status_consigned', $status)
                                   ->where('pays.who_did_id', $id)
                                   ->whereBetween('date_pay', [$dateStart, $dateEnd])
                                   ->select('pays.amount_received AS amount', 'pays.status_consigned AS status','users.name AS client', 'pays.id', 'numbers.MSISDN', 'rates.name AS name_product', 'users.lastname AS lastname','pays.date_pay AS date')
                                   ->get();
        }
        return $resp;
    }

    public function statusCortes(Request $request){
        $id = $request['idpay'];
        $user_consigned = $request['id_consigned'];
        $type = $request['type'];
        $status = $request['status'];
        $x = false;

        if ($type == 'purchases') {
            if ($status =='pendiente') {
                $x = Purchase::where('id', $id)->update(['status'=>'completado', 'who_consigned'=>$user_consigned]);
            }elseif ($status == 'completado') {
                $x = Purchase::where('id', $id)->update(['status'=>'pendiente', 'who_consigned'=>$user_consigned]);
            }
        }elseif ($type == 'changes') {
            if ($status =='pendiente') {
                $x = Change::where('id', $id)->update(['status'=>'completado', 'who_consigned'=>$user_consigned]);
            }elseif ($status == 'completado') {
                $x = Change::where('id', $id)->update(['status'=>'pendiente', 'who_consigned'=>$user_consigned]);
            }
        }elseif ($type == 'monthly') {
            if ($status =='pendiente') {
                $x = Pay::where('id', $id)->update(['status_consigned'=>'completado', 'who_consigned'=>$user_consigned]);
            }elseif ($status == 'completado') {
                $x = Pay::where('id', $id)->update(['status_consigned'=>'pendiente', 'who_consigned'=>$user_consigned]);
            }
        }

        if ($x) {
            return 1;
        }else {
            return 0;
        }
    }

    public function payAll(Request $request){
        $id = $request['id'];
        $user_consigned = $request['id_consigned'];
        $status = $request['status'];
        $type = $request['type'];
        $start = $request['start'];
        $año = substr($start, -4);
        $mes = substr($start, 0,2);
        $dia = substr($start, 3, -5);
        $dateStart = $año. '-'. $mes.'-'.$dia;
        $end = $request['end'];
        $añoEnd = substr($end, -4);
        $mesEnd = substr($end, 0,2);
        $diaEnd = substr($end, 3, -5);
        $dateEnd = $añoEnd. '-'. $mesEnd.'-'.$diaEnd;
        $x = false;

        if ($type == 'changes') {
            if ($status == 'pendiente') {
                // return $dateStart.' // '.$dateEnd;
                $x = Change::where('who_did_id', $id)->where('status', $status)->whereBetween('date', [$dateStart,$dateEnd])->update(['status'=>'completado', 'who_consigned'=>$user_consigned]);
            }else if ($status == 'completado') {
                // return $dateStart.' // '.$dateEnd;
                $x = Change::where('who_did_id', $id)->where('status', $status)->whereBetween('date', [$dateStart, $dateEnd])->update(['status'=>'pendiente', 'who_consigned'=>$user_consigned]);
            }
        }else if ($type == 'purchases') {
            if ($status == 'pendiente') {
                $x = Purchase::where('who_did_id', $id)->where('status', $status)->whereBetween('date', [$dateStart, $dateEnd])->update(['status'=>'completado', 'who_consigned'=>$user_consigned]);
            }else if ($status == 'completado') {
                $x = Purchase::where('who_did_id', $id)->where('status', $status)->whereBetween('date', [$dateStart, $dateEnd])->update(['status'=>'pendiente', 'who_consigned'=>$user_consigned]);
            }
        }elseif ($type == 'monthly') {
            if ($status == 'pendiente') {
                $x = Pay::where('who_did_id', $id)->where('status_consigned', $status)->whereBetween('date_pay', [$dateStart, $dateEnd])->update(['status_consigned'=>'completado', 'who_consigned'=>$user_consigned]);
            }elseif ($status == 'completado') {
                $x = Pay::where('who_did_id', $id)->where('status_consigned', $status)->whereBetween('date_pay', [$dateStart, $dateEnd])->update(['status_consigned'=>'pendiente', 'who_consigned'=>$user_consigned]);
            }
        }
        if ($x) {
            return 1;
        }else {
            return 0;
        }
    }
}