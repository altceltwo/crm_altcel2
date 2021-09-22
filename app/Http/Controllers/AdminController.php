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
        $end = $request['end'];
        $añoEnd = substr($end, -4);
        $mesEnd = substr($end, 0,2);
        $diaEnd = substr($end, 3, -5);
        $dateEnd = $añoEnd. '-'. $mesEnd.'-'.$diaEnd;

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
                                    ->select('purchases.id', 'purchases.status', 'purchases.reason', 'purchases.amount', 'offers.name AS name_product','numbers.MSISDN','users.name AS client')
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
                                    ->select('changes.id', 'changes.status', 'changes.reason', 'changes.amount', 'rates.name AS name_product','numbers.MSISDN','users.name AS client')
                                    ->get();
        }
        return $resp;
    }

    public function statusCortes(Request $request){
        $id = $request['idpay'];
        $type = $request['type'];
        $status = $request['status'];
        $x = false;

        if ($type == 'purchases') {
            if ($status =='pendiente') {
                $x = Purchase::where('id', $id)->update(['status'=>'completado']);
                
            }elseif ($status == 'completado') {
                $x = Purchase::where('id', $id)->update(['status'=>'pendiente']);
                
            }
        }elseif ($type == 'changes') {
            if ($status =='pendiente') {
                $x = Change::where('id', $id)->update(['status'=>'completado']);
                
            }elseif ($status == 'completado') {
                $x = Change::where('id', $id)->update(['status'=>'pendiente']);
                
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
                $x = Change::where('who_did_id', $id)->where('status', $status)->whereBetween('date', [$dateStart,$dateEnd])->update(['status'=>'completado']);
            }else if ($status == 'completado') {
                // return $dateStart.' // '.$dateEnd;
                $x = Change::where('who_did_id', $id)->where('status', $status)->whereBetween('date', [$dateStart, $dateEnd])->update(['status'=>'pendiente']);
            }
        }else if ($type == 'purchases') {
            if ($status == 'pendiente') {
                Purchase::where('who_did_id', $id)->where('status', $status)->whereBetween('date', [$dateStart, $dateEnd])->update(['status'=>'completado']);
            }else if ($status == 'completado') {
                Purchase::where('who_did_id', $id)->where('status', $status)->whereBetween('date', [$dateStart, $dateEnd])->update(['status'=>'pendiente']);
            }
        }
        if ($x) {
            return 1;
        }else {
            return 0;
        }
    }
}