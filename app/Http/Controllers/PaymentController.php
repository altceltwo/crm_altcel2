<?php

namespace App\Http\Controllers;

use App\Activation;
use App\Instalation;
use App\Pay;
use App\Ethernetpay;
use DB;
use DateTime;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function createPayments($date_pay,$date_limit,$type){
        if($type == 'altan'){
            $activations = DB::table('activations')
                       ->join('rates','rates.id','=','activations.rate_id')
                       ->select('activations.*','rates.price_subsequent AS rate_price')
                       ->get();

            $status = 'pendiente';
            $activation_id = 0;
            $amount = 0;
            $lstAct = [];
            for ($i=0; $i < sizeof($activations) ; $i++) {
                $activation_id = $activations[$i]->id;
                $paymentBool = Pay::where('activation_id',$activation_id)->where('date_pay',$date_pay)->exists();

                if(!$paymentBool){
                    $amount = $activations[$i]->rate_price;
                    if($amount > 0){
                        array_push($lstAct,array(
                            'date_pay' => $date_pay,
                            'date_pay_limit' => $date_limit,
                            'status' => $status,
                            'activation_id' => $activation_id,
                            'amount' => $amount
                        ));
                    }
                }
            }
            Pay::insert($lstAct);
            return response()->json(['http_code'=>200,'message'=>'Payments already inserted successfully.','status'=>'completed']);
        }else if($type == 'oreda'){
            $instalations = DB::table('instalations')
                        ->join('packs','packs.id','=','instalations.pack_id')
                        ->select('instalations.*','packs.price AS pack_price')
                        ->get();

            $status = 'pendiente';
            $instalation_id = 0;
            $amount = 0;
            $lstInstall = [];
            for ($i=0; $i < sizeof($instalations) ; $i++) {
                $instalation_id = $instalations[$i]->id;
                $paymentBool = Ethernetpay::where('instalation_id',$instalation_id)->where('date_pay',$date_pay)->exists();

                if(!$paymentBool){
                    $amount = $instalations[$i]->pack_price;

                    array_push($lstInstall,array(
                        'date_pay' => $date_pay,
                        'date_pay_limit' => $date_limit,
                        'status' => $status,
                        'instalation_id' => $instalation_id,
                        'amount' => $amount
                    ));
                }
            }
            Ethernetpay::insert($lstInstall);
            return response()->json(['http_code'=>200,'message'=>'Payments already inserted successfully.','status'=>'completed']);
        }
            
        
    }

    public function checkOverduePayments($date_pay){
        $x = Pay::where('date_pay',$date_pay)->where('status','pendiente')->update(['status'=>'vencido']);
        $y = Ethernetpay::where('date_pay',$date_pay)->where('status','pendiente')->update(['status'=>'vencido']);
        if($x && $y){
            return response()->json(['http_code'=>200,'message'=>'Payments was updated successfully.','status'=>'completed']);
        }else{
            return response()->json(['http_code'=>200,'message'=>'Payments already was updated successfully.','status'=>'done']);
        }
    }

    public function checkOverduePaymentsSwitchCase(){
        $date_now = date("Y-m-d");
        $day_now = date("d");
        $date_limit = new DateTime($date_now);
        $date_limit->modify('last day of this month');
        $day_last = $date_limit->format('d');
        $date_limit = $date_limit->format('Y-m-d');

        $date_pay = strtotime('-5 days', strtotime($date_limit));
        $date_pay = date('Y-m-d', $date_pay);
        return $date_pay.' - '.$date_limit;
        switch ($day_last) {
            case 31:
                if($day_now == 31){
                    $response = PaymentController::checkOverduePayments($date_pay);
                    return $response;
                }
                break;
            case 30:
                if($day_now == 30){
                    $response = PaymentController::checkOverduePayments($date_pay);
                    return $response;
                }
                break;
            case 28:
                if($day_now == 28){
                    $response = PaymentController::checkOverduePayments($date_pay);
                    return $response;
                }
                break;
            case 29:
                if($day_now == 29){
                    $response = PaymentController::checkOverduePayments($date_pay);
                    return $response;
                }
                break;
        }
    }

    public function paymentsPendings(){
        $date_now = date("Y-m-d");
        $date_pay = new DateTime($date_now);
        $date_pay->modify('first day of this month');
        
        $day = $date_pay->format('d');
        $month = $date_pay->format('M');
        $year = $date_pay->format('Y');
        $data['formatDate'] = ' de '.$month.' de '.$year;
        $date_pay = $date_pay->format('Y-m-d');

        $date_limit = strtotime('+4 days', strtotime($date_pay));
        $date_limit = date('Y-m-d', $date_limit);

        $data['paymentsPendings'] = DB::table('pays')
                                      ->join('activations','activations.id','=','pays.activation_id')
                                      ->join('rates','rates.id','=','activations.rate_id')
                                      ->join('users','users.id','=','activations.client_id')
                                      ->join('numbers','numbers.id','=','activations.numbers_id')
                                      ->leftJoin('references','references.reference_id','=','pays.reference_id')
                                      ->where('pays.status','pendiente')
                                      ->select('pays.*','rates.name AS rate_name','users.id AS client_id','users.name AS client_name','users.lastname AS client_lastname','numbers.producto AS number_product','numbers.id AS number_id','numbers.MSISDN AS DN','references.reference_id AS referenceID')
                                      ->get();

        $data['paymentsPendings2'] = DB::table('ethernetpays')
                                      ->join('instalations','instalations.id','=','ethernetpays.instalation_id')
                                      ->join('packs','packs.id','=','instalations.pack_id')
                                      ->join('users','users.id','=','instalations.client_id')
                                      ->leftJoin('references','references.reference_id','=','ethernetpays.reference_id')
                                      ->where('ethernetpays.status','pendiente')
                                      ->select('ethernetpays.*','packs.name AS pack_name','packs.service_name AS service_name','users.id AS client_id','users.name AS client_name','users.lastname AS client_lastname','instalations.number AS number_install','references.reference_id AS referenceID')
                                      ->get();
        

        return view('webhooks.pendings',$data);
    }

    public function paymentsOverdue(){
        $date_now = date("Y-m-d");
        $date_pay = new DateTime($date_now);
        $date_pay->modify('first day of this month');
        
        $day = $date_pay->format('d');
        $month = $date_pay->format('M');
        $year = $date_pay->format('Y');
        $data['formatDate'] = ' de '.$month.' de '.$year;
        $date_pay = $date_pay->format('Y-m-d');

        $date_limit = strtotime('+4 days', strtotime($date_pay));
        $date_limit = date('Y-m-d', $date_limit);

        $data['paymentsOverdues'] = DB::table('pays')
                                      ->join('activations','activations.id','=','pays.activation_id')
                                      ->join('rates','rates.id','=','activations.rate_id')
                                      ->join('users','users.id','=','activations.client_id')
                                      ->where('pays.date_pay',$date_pay)
                                      ->where('pays.date_pay_limit',$date_limit)
                                      ->where('pays.status','vencido')
                                      ->select('pays.*','rates.name AS rate_name','users.name AS client_name','users.lastname AS client_lastname')
                                      ->get();
        

        return view('webhooks.overdues',$data);
    }

    public function createPaymentsSwitchCase(Request $request){
        
        // $type = $request->type;
        // // Obtención de fecha actual
        // $date_now = date("Y-m-d");
        // // Obtención de día actual (según el día de ejecución del WS => 31, 30, 28, 29)
        // $day_now = date('d');
        // // Transformación de la fecha actual a la fecha del último día del mes según el consumo del WS
        // $date_limit = new DateTime($date_now);
        // $date_limit->modify('last day of this month');
        // // Obtención del último día y formato de fecha en curso
        // $day_last = $date_limit->format('d');
        // $date_limit = $date_limit->format('Y-m-d');
        // // Obtención del día de pago, restando 5 días al último día del mes en curso
        // $date_pay = strtotime('-5 days', strtotime($date_limit));
        // $date_pay = date('Y-m-d', $date_pay);

        // // 
        // switch ($day_last) {
        //     case 31:
        //         if($day_now == 26){
        //             $response = PaymentController::createPayments($date_pay,$date_limit,$type);
        //             return $response;
        //         }
        //         break;
        //     case 30:
        //         if($day_now == 25){
        //             $response = PaymentController::createPayments($date_pay,$date_limit,$type);
        //             return $response;
        //         }
        //         break;
        //     case 28:
        //         if($day_now == 23){
        //             $response = PaymentController::createPayments($date_pay,$date_limit,$type);
        //             return $response;
        //         }
        //         break;
        //     case 29:
        //         if($day_now == 24){
        //             $response = PaymentController::createPayments($date_pay,$date_limit,$type);
        //             return $response;
        //         }
        //         break;
        // }
    }

    public function changeProductPayment(){
        $data = DB::table('references')
                    ->join('referencestypes','referencestypes.id', '=', 'references.referencestype_id')
                    ->join('users','users.id','=','references.client_id')
                    ->join('channels','channels.id', '=', 'references.channel_id')
                    ->join('rates','rates.id','=','references.rate_id')
                    ->join('numbers','numbers.id','=','references.number_id')
                    ->where('referencestype_id','=', 4)
                    ->select('references.user_id','references.reference','references.status','users.name AS client','channels.name AS channel','rates.name','numbers.MSISDN AS numbers','references.reference_id AS reference_id')
                    ->get();


        $x['products'] = [];    
        foreach($data as $change){
            $reference = $change->reference;
            $status = $change->status;
            $client = $change->client;
            $channel = $change->channel;
            $rate_name = $change->name;
            $numbers = $change->numbers;
            $user = $change->user_id;
           $id = $change->reference_id;
            $data_user = User::where('id', $user)->first();
            $user_name = $data_user->name.' '.$data_user->lastname;
            
            array_push($x['products'], array(
                'reference'=>$reference,
                'status'=>$status,
                'client'=>$client,
                'channel'=>$channel,
                'rate_name'=>$rate_name,
                'numbers'=>$numbers,
                'user_name'=>$user_name,
                'id'=>$id
            ));
            
        }
        return view('webhooks/changeProduct',$x);

        return $x;

    }
    public function excedentes(){
        $data = DB::table('references')
                    ->join('referencestypes','referencestypes.id', '=', 'references.referencestype_id')
                    ->join('users','users.id','=','references.client_id')
                    ->join('channels','channels.id', '=', 'references.channel_id')
                    ->join('rates','rates.id','=','references.rate_id')
                    ->join('numbers','numbers.id','=','references.number_id')
                    ->where('referencestype_id','=', 5)
                    ->select('references.user_id','references.reference','references.status','users.name AS client','channels.name AS channel','rates.name','numbers.MSISDN AS numbers','references.reference_id AS reference_id')
                    ->get();


        $x['excedentes'] = [];    
        foreach($data as $change){
            $reference = $change->reference;
            $status = $change->status;
            $client = $change->client;
            $channel = $change->channel;
            $rate_name = $change->name;
            $numbers = $change->numbers;
            $user = $change->user_id;
            $id = $change->reference_id;
            // return $user;

            $data_user = User::where('id', $user)->first();
            $user_name = $data_user->name.' '.$data_user->lastname;
            
            array_push($x['excedentes'], array(
                'reference'=>$reference,
                'status'=>$status,
                'client'=>$client,
                'channel'=>$channel,
                'rate_name'=>$rate_name,
                'numbers'=>$numbers,
                'user_name'=>$user_name,
                'id'=>$id
            ));
            
        }
        return view('webhooks/excedentes', $x);
    }


}
