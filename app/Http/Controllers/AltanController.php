<?php

namespace App\Http\Controllers;
use Http;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\GuzzleHttp;
use App\Number;
use App\Activation;
use App\Purchase;
use App\Change;
use App\Assignment;
use App\Reference;
use App\Pay;
use App\Historic;
use App\Otherpetition;

class AltanController extends Controller
{
    public function accessTokenRequestPost(){
        // $prelaunch = 'TzBpSndNOWlkc1ZvZDdoVThrOHcyQTJuQXhQTDdORWU6bm1GaHlCWjdYbWhtaTRTUw==';
        $production = 'ZjRWc3RzQXM4V1c0WFkyQVVtbVBSTE1pRDFGZldFQ0k6YkpHakpCcnBkWGZoajczUg==';

        $response = Http::withHeaders([
            'Authorization' => 'Basic '.$production
        ])->post('https://altanredes-prod.apigee.net/v1/oauth/accesstoken?grant-type=client_credentials', [
            'Authorization' => 'Basic '.$production,
        ]);
        return $response->json();
    }

    public function activationRequestPost($accessToken,$MSISDN,$offerID,$lat_hbb,$lng_hbb,$product,$scheduleDate){
        // return $accessToken.' - '.$MSISDN.' - '.$offerID;
        // $url_prelaunch = "https://altanredes-prod.apigee.net/cm-sandbox/v1/subscribers/".$MSISDN."/activate";
        $url_production = "https://altanredes-prod.apigee.net/cm/v1/subscribers/".$MSISDN."/activate";
        if($product == 'HBB'){
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken
            ])->post($url_production,[
                "offeringId" => $offerID,
                "address" => $lat_hbb.",".$lng_hbb,
                "startEffectiveDate" => "",
                "expireEffectiveDate" => "",
                "scheduleDate" => $scheduleDate,
                "idPoS" => ""
            ]);
        }else{
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken
            ])->post($url_production,[
                "offeringId" => $offerID,
                "startEffectiveDate" => "",
                "expireEffectiveDate" => "",
                "scheduleDate" => $scheduleDate,
                "idPoS" => ""
            ]);
        }
        return $response->json();
    }

    public function activateDeactivateDN(Request $request){
        $status = $request['status'];
        $type = $request['type'];
        $msisdn = $request['msisdn'];
        $accessTokenResponse = AltanController::accessTokenRequestPost();
        
        if($accessTokenResponse['status'] == 'approved'){
            $accessToken = $accessTokenResponse['accessToken'];

            if($type == 'out'){
                if($status == 'activo'){
                    // Estado de Barring
                    // $url_prelaunch = 'https://altanredes-prod.apigee.net/cm-sandbox/v1/subscribers/'.$msisdn.'/barring';
                    $url_production = 'https://altanredes-prod.apigee.net/cm/v1/subscribers/'.$msisdn.'/barring';
                    
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$accessToken
                    ])->post($url_production);

                    if(isset($response['msisdn'])){
                        Number::where('MSISDN',$msisdn)->update(['traffic_outbound'=>'inactivo']);
                        $message = 'El MSISDN '.$msisdn.' ha entrado en Barring.';
                        $bool = 1;
                        return response()->json(['bool' => $bool, 'message' => $message]);
                    }else{
                        $errorCode = $response['errorCode'];
                        $description = $response['description'];
                        $bool = 0;
                        return response()->json(['bool' => $bool,'errorCode' => $errorCode, 'description' => $description]);
                    }
                }else if($status == 'inactivo'){
                    // Estado de Unbarring
                    // $url_prelaunch = 'https://altanredes-prod.apigee.net/cm-sandbox/v1/subscribers/'.$msisdn.'/unbarring';
                    $url_production = 'https://altanredes-prod.apigee.net/cm/v1/subscribers/'.$msisdn.'/unbarring';
                    
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$accessToken
                    ])->post($url_production);

                    if(isset($response['msisdn'])){
                        Number::where('MSISDN',$msisdn)->update(['traffic_outbound'=>'activo']);
                        $message = 'El MSISDN '.$msisdn.' ha salido de Barring.';
                        $bool = 1;
                        return response()->json(['bool' => $bool, 'message' => $message]);
                    }else{
                        $errorCode = $response['errorCode'];
                        $description = $response['description'];
                        $bool = 0;
                        return response()->json(['bool' => $bool,'errorCode' => $errorCode, 'description' => $description]);
                    }
                }
            }else if($type == 'out_in'){

                if($status == 'activo'){
                    // Estado de Suspensión
                    // $url_prelaunch = 'https://altanredes-prod.apigee.net/cm-sandbox/v1/subscribers/'.$msisdn.'/suspend';
                    $url_production = 'https://altanredes-prod.apigee.net/cm/v1/subscribers/'.$msisdn.'/suspend';
                    
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$accessToken
                    ])->post($url_production);

                    if(isset($response['msisdn'])){
                        Number::where('MSISDN',$msisdn)->update(['traffic_outbound_incoming'=>'inactivo']);
                        $message = 'El MSISDN '.$msisdn.' ha entrado en Suspensión.';
                        $bool = 1;

                        return response()->json(['bool' => $bool, 'message' => $message]);
                    }else{
                        $errorCode = $response['errorCode'];
                        $description = $response['description'];
                        $bool = 0;
                        return response()->json(['bool' => $bool,'errorCode' => $errorCode, 'description' => $description]);
                    }
                }else if($status == 'inactivo'){
                    // Estado de Reanudación
                    // $url_prelaunch = 'https://altanredes-prod.apigee.net/cm-sandbox/v1/subscribers/'.$msisdn.'/resume';
                    $url_production = 'https://altanredes-prod.apigee.net/cm/v1/subscribers/'.$msisdn.'/resume';
                    
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$accessToken
                    ])->post($url_production);

                    if(isset($response['msisdn'])){
                        Number::where('MSISDN',$msisdn)->update(['traffic_outbound_incoming'=>'activo']);
                        $message = 'El MSISDN '.$msisdn.' ha salido de Suspensión.';
                        $bool = 1;

                        return response()->json(['bool' => $bool, 'message' => $message]);
                    }else{
                        $errorCode = $response['errorCode'];
                        $description = $response['description'];
                        $bool = 0;
                        return response()->json(['bool' => $bool,'errorCode' => $errorCode, 'description' => $description]);
                    }
                }
            }
        }
    }

    public function consultUF($msisdn){
        $accessTokenResponse = AltanController::accessTokenRequestPost();

        if($accessTokenResponse['status'] == 'approved'){
            $accessToken = $accessTokenResponse['accessToken'];
            
            $url_production = 'https://altanredes-prod.apigee.net/cm/v1/subscribers/'.$msisdn.'/profile';
                    
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken
            ])->get($url_production);

            return $response->json();
        }
    }

    public function predeactivateReactivate(Request $request){
        $msisdn = $request->post('msisdn');
        $type = $request->post('type');
        $scheduleDate = $request->post('scheduleDate');
        $dataNumber =  Number::where('MSISDN',$msisdn)->first();
        $status = $dataNumber->status_altan;
        
        if($type == 'reactivate' && $status == 'activo'){
            return response()->json(['http_code'=>2,'message'=>'El MSISDN ya tiene status ACTIVO.']);
        }

        if($type == 'predeactivate' && $status == 'predeactivate'){
            return response()->json(['http_code'=>2,'message'=>'El MSISDN ya se encuentra con BAJA TEMPORAL.']);
        }

        $accessTokenResponse = AltanController::accessTokenRequestPost();
        // return $accessTokenResponse;
        if($accessTokenResponse['status'] == 'approved'){
            $accessToken = $accessTokenResponse['accessToken'];
            
            $url_production = 'https://altanredes-prod.apigee.net/cm/v1/subscribers/'.$msisdn.'/'.$type;
            // $url_prelaunch = 'https://altanredes-prod.apigee.net/cm-sandbox/v1/subscribers/'.$msisdn.'/'.$type;
                    
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'application/json'
            ])->post($url_production,[
                'scheduleDate' => $scheduleDate
            ]);

            if(isset($response['order'])){
                $message = $type == 'reactivate' ? 'La REACTIVACIÓN se ejecutó exitosamente.' : 'La BAJA TEMPORAL se ejecutó exitosamente.';
                $type = $type == 'reactivate' ? 'activo' : $type;
                $x = Number::where('MSISDN',$msisdn)->update(['status_altan'=>$type]);
                
                if($x){
                    return response()->json(['http_code' => 1,'message' => $message]);
                }
            }else if(isset($response['errorCode'])){
                return response()->json(['http_code'=>0,'message'=>'Ha ocurrido un error al ejecutar la función deseada.']);
            }

            return $response->json();
        }
    }

    public function changeProduct(Request $request){
        $msisdn = $request->post('msisdn');
        $offerID = $request->post('offerID');
        $offer_id = $request->post('offer_id');
        $rate_id = $request->post('rate_id');
        $scheduleDate = $request->post('scheduleDate');
        $address = $request->post('address');
        $type = $request->post('type');
        $user_id = $request->post('user_id');
        $amount = $request->post('amount');
        $comment = $request->post('comment');
        $reason = $request->post('reason');
        $status = $request->post('status');
        $pay_id = $request->post('pay_id');
        $reference_id = $request->post('reference_id');
        $date = date('Y-m-d H:i:s');
        
        if($address == null){
            $address = '';
        }

        $dataNumber = DB::table('numbers')
                         ->join('activations','activations.numbers_id','=','numbers.id')
                         ->where('numbers.MSISDN',$msisdn)
                         ->select('numbers.MSISDN AS MSISDN','activations.id AS activation_id','numbers.id AS number_id')
                         ->get();

        $activation_id = $dataNumber[0]->activation_id;
        $number_id = $dataNumber[0]->number_id;

        if($type == 'internalChange'){
            $x = Activation::where('id',$activation_id)->update([
                'offer_id' => $offer_id,
                'rate_id' => $rate_id
            ]);

            $http_code = 1;
            $message = 'Cambio interno realizado con éxito.';

        }else if($type == 'internalExternalChange' || $type == 'internalExternalChangeCollect'){
            // return $request;
            $response = AltanController::changeProductResponse($msisdn,$offerID,$address,$scheduleDate);
            $http_code = $response['http_code'];
            $message = $response['message'];
            $orderID = $response['order'];

            if($http_code == 1){
                $x = Activation::where('id',$activation_id)->update([
                    'offer_id' => $offer_id,
                    'rate_id' => $rate_id,
                    'flag_rate' => 1
                ]);

                $x = Change::insert([
                    "number_id" => $number_id,
                    "offer_id" => $offer_id,
                    "rate_id" => $rate_id,
                    "who_did_id" => $user_id,
                    "amount" => $amount,
                    "date" => $date,
                    "comment" => $comment,
                    "reason" => $reason,
                    "order_id" => $orderID,
                    "status" => $status,
                    "pay_id" => $pay_id = $pay_id == 0 ? null : $pay_id,
                    "reference_id" => $reference_id
                ]);

                $dataActivation = Activation::where('numbers_id',$number_id)->first();
                $activation_id = $dataActivation->id;
                $expire_date = $dataActivation->expire_date;

                $date_limit = strtotime('-1 days', strtotime($expire_date));
                $date_limit = date('Y-m-d', $date_limit);

                $date_beforeFiveDays = strtotime('-5 days', strtotime($date_limit));
                $date_beforeFiveDays = date('Y-m-d', $date_beforeFiveDays);

                $dataPayment = Pay::where('activation_id',$activation_id)->where('status','pendiente')->exists();

                if($dataPayment){
                    $dataPayment = Pay::where('activation_id',$activation_id)->where('status','pendiente')->first();
                    $payment_id = $dataPayment->id;
                    Pay::where('id',$payment_id)->update(['status'=>'cambio plan','type_pay'=>'efectivo','who_did_id'=>$user_id,'amount_received'=>$amount]);
                }

                if(!$x){
                    $http_code = 2;
                    $message = 'El cambio de producto se realizó con éxito, pero no se pudo actualizar la información de MSISDN '.$msisdn.' en la Base de Datos.';
                }

                return response()->json(['http_code'=>$http_code,'message'=>$message,'order'=>$orderID]);
            }
        }else if($type = 'internalExternalPaymentChange'){

        }

        return response()->json(['http_code'=>$http_code,'message'=>$message]);
    }

    public function changeProductResponse($msisdn,$offerID,$address,$scheduleDate){
        $accessTokenResponse = AltanController::accessTokenRequestPost();
            // return $accessTokenResponse;
        if($accessTokenResponse['status'] == 'approved'){
            $accessToken = $accessTokenResponse['accessToken'];
            $url_production = 'https://altanredes-prod.apigee.net/cm/v1/subscribers/'.$msisdn;
                
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'application/json'
            ])->patch($url_production,[
                'primaryOffering' => array(
                    'offeringId' => $offerID,
                    'address' => $address,
                    'scheduleDate' => $scheduleDate,
                    'startEffectiveDate' => '',
                    'expireEffectiveDate' => ''
                )
            ]);

            if(isset($response['order'])){
                $http_code = 1;
                $message = 'Cambio de producto realizado con éxito.';
                $orderID = $response['order']['id'];
            }else{
                $http_code = 0;
                $message = $response['description'];
                $orderID = 1;
            }
            $data['http_code'] = $http_code;
            $data['message'] = $message;
            $data['order'] = $orderID;
            return $data;
        }
    }

    public function productPurchase(Request $request){
        $msisdn = $request->get('msisdn');
        $offer = $request->get('offer');
        $user_id = $request->get('user_id');
        $price = $request->get('price');
        $offer_id = $request->get('offer_id');
        $rate_id = $request->get('rate_id');
        $comment = $request->get('comment');
        $reason = $request->get('reason');
        $status = $request->get('status');
        $date = date('Y-m-d H:i:s');
        
        $dataNumber = Number::where('MSISDN',$msisdn)->first();
        $number_id = $dataNumber->id;
        // return $request;
        // $url_prelaunch = "https://altanredes-prod.apigee.net/cm-sandbox/v1/products/purchase";
        $url_production = "https://altanredes-prod.apigee.net/cm/v1/products/purchase";
        // return response()->json(['gbs'=>$quantity_gb,'$offerID'=>$offerID,'$MSISDN'=>$MSISDN]);
        $accessTokenResponse = AltanController::accessTokenRequestPost();

        if($accessTokenResponse['status'] == 'approved'){
            $accessToken = $accessTokenResponse['accessToken'];
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$accessToken
                ])->post($url_production,[
                    "msisdn" => $msisdn,
                    "offerings" => array(
                        $offer
                    ),
                    "startEffectiveDate" => "",
                    "expireEffectiveDate" => "",
                    "scheduleDate" => ""
                ]);
                if(isset($response['msisdn'])){
                    $message = 'Compra de producto hecha con éxito.';
                    $http_code = 1;
                    $orderID = $response['order']['id'];

                    Purchase::insert([
                        "number_id" => $number_id,
                        "offer_id" => $offer_id,
                        "rate_id" => $rate_id,
                        "who_did_id" => $user_id,
                        "amount" => $price,
                        "date" => $date,
                        "comment" => $comment,
                        "reason" => $reason,
                        "status" => $status,
                        "order_id" => $orderID
                    ]);
                }else{
                    $message = $response['description'];
                    $http_code = 0;
                    $orderID = 1;
                }
            // } while ($i < $quantity_gb);

            return response()->json(['http_code'=>$http_code,'message'=>$message,'order'=>$orderID]);
        }
    }

    public function productPurchaseBonusDealer($msisdn,$offer,$user_id,$price,$offer_id,$rate_id){
        $date = date('Y-m-d H:i:s');
        
        $dataNumber = Number::where('MSISDN',$msisdn)->first();
        $number_id = $dataNumber->id;
        $message = '';

        // $url_prelaunch = "https://altanredes-prod.apigee.net/cm-sandbox/v1/products/purchase";
        $url_production = "https://altanredes-prod.apigee.net/cm/v1/products/purchase";
        // return response()->json(['gbs'=>$quantity_gb,'$offerID'=>$offerID,'$MSISDN'=>$MSISDN]);
        $accessTokenResponse = AltanController::accessTokenRequestPost();

        if($accessTokenResponse['status'] == 'approved'){
            $accessToken = $accessTokenResponse['accessToken'];

            $url_productionResume = 'https://altanredes-prod.apigee.net/cm/v1/subscribers/'.$msisdn.'/resume';
                    
            $responseR = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken
            ])->post($url_productionResume);

            if(isset($responseR['msisdn'])){
                Number::where('MSISDN',$msisdn)->update(['traffic_outbound_incoming'=>'activo']);
                // 
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$accessToken
                ])->post($url_production,[
                    "msisdn" => $msisdn,
                    "offerings" => array(
                        $offer
                    ),
                    "startEffectiveDate" => "",
                    "expireEffectiveDate" => "",
                    "scheduleDate" => ""
                ]);

                if(isset($response['msisdn'])){
                    $message = 'Compra de producto hecha con éxito.';
                    $http_code = 1;
                    $orderID = $response['order']['id'];

                    Purchase::insert([
                        "number_id" => $number_id,
                        "offer_id" => $offer_id,
                        "rate_id" => $rate_id,
                        "who_did_id" => $user_id,
                        "amount" => $price,
                        "date" => $date,
                        "comment" => 'Bonificación por Quinta Mensualidad',
                        "reason" => 'bonificacion',
                        "status" => 'completado',
                        "order_id" => $orderID
                    ]);
                }else{
                    $message = $response['description'];
                    $http_code = 0;
                    $orderID = 1;
                }
                // 
            }else{
                $description = $response['description'];
                $http_code = 0;
                $orderID = 1;
            }

            return $data = ['http_code'=>$http_code,'message'=>$message,'order'=>$orderID];
        }
    }
    public function changeLink(Request $request){
        $msisdn = $request['msisdn'];
        $x = DB::table('numbers')
                ->join('activations','activations.numbers_id', '=', 'numbers.id')
                ->where('numbers.MSISDN', '=', $msisdn)
                ->select('numbers.*', 'activations.lat_hbb', 'activations.lng_hbb', 'activations.id as act_id')
                ->get();
        $producto = $x[0]->producto;
        $producto = trim($producto);
        $lat = $x[0]->lat_hbb;
        $lng = $x[0]->lng_hbb;
        if ($producto == 'HBB') {
            return $x;
        }else{
            return response()->json(['http_code'=>0, 'message'=>'Tipo de producto incorrecto']);
        }    
    }

    public function updateCoordinate(Request $request){
        $accessTokenResponse = AltanController::accessTokenRequestPost();
        $lat_hbb = $request['lat_hbb'];
        $lng_hbb = $request['lng_hbb'];
        $msisdn = $request['msisdn'];
        if ($accessTokenResponse['status'] == 'approved') {
            //verificar serviciabilidad
            $accessToken = $accessTokenResponse['accessToken'];
            $urlServices = "https://altanredes-prod.apigee.net/sqm/v1/network-services/serviceability";
            $response = Http::withHeaders(['Authorization' => 'Bearer '.$accessToken])->get($urlServices, ['address'=> $lat_hbb.',' .$lng_hbb]);
            $result = $response['result'];
            // return $response;
            //actualizacion de coordenadas
            if ($result != 'Without Coverage') {
                $url_updateLink = "https://altanredes-prod.apigee.net/cm/v1/subscribers/".$msisdn;
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$accessToken
                    ])->patch($url_updateLink,[
                        "updateLinking"=>array("coordinates"=> $lat_hbb.',' .$lng_hbb)
                    ]);

                    $dataNumber = Number::where('MSISDN',$msisdn)->first();
                    $number_id = $dataNumber->id;
                    Activation::where('numbers_id',$number_id)->update(['lat_hbb'=>$lat_hbb,'lng_hbb'=>$lng_hbb]);
                    
                    if (isset($response['msisdn'])) {
                        return response()->json(['http_code'=>1, 'message'=>'Las coordenadas se han actualizado']);
                    }else{
                        return response()->json(['http_code'=>0, 'message'=>$response['description']]);
                    }
            }else{
                return response()->json(['http_code'=>2,'message'=>'No hay cobertura en las coordenadas '.$lat_hbb. ', '.$lng_hbb]);
            }
        }
    }

    public function serviciabilidad(Request $request){
        $accessTokenResponse = AltanController::accessTokenRequestPost();
        $lat_serv = $request['lat_serv'];
        $lng_serv = $request['lng_serv'];

        if ($accessTokenResponse['status'] == 'approved') {
            $accessToken = $accessTokenResponse['accessToken'];
            $urlServices = "https://altanredes-prod.apigee.net/sqm/v1/network-services/serviceability";
            $response = Http::withHeaders(['Authorization' => 'Bearer '.$accessToken])->get($urlServices, ['address'=> $lat_serv.',' .$lng_serv]);
            $result = $response['result'];

            return $result;
        }
    }

    public function statusImei(Request $request){
        $accessTokenResponse = AltanController::accessTokenRequestPost();
        $msisdn = $request['msisdn'];
        $x = AltanController::consultUF($msisdn);
        $imei = $x['responseSubscriber']['information']['IMEI'];
        
        if($accessTokenResponse['status'] == 'approved'){
            $accessToken = $accessTokenResponse['accessToken'];
            $urlServices = 'https://altanredes-prod.apigee.net/ac/v1/imeis/'.$imei.'/status';
            $result = Http::withHeaders(['Authorization'=>'Bearer '.$accessToken])->get($urlServices);
            $response = $result['imei'];
            return $response;
        }
    }

    public function locked(Request $request){
        $accessTokenResponse = AltanController::accessTokenRequestPost();
        $imei = $request['imei'];
        $status = $request['status'];
        $msisdn = $request['msisdn'];
        $otherid = $request['otherid'];
        $user = $request['user'];

        if ($status == 'NO') {
            if($accessTokenResponse['status']== 'approved'){
                $accessToken = $accessTokenResponse['accessToken'];
                $urlServices = 'https://altanredes-prod.apigee.net/cm/v1/imei/'.$imei.'/lock';
                $response = Http::withHeaders(['Authorization'=>'Bearer '.$accessToken])->post($urlServices);
                if (isset($response['imei'])) {
                    Number::where('MSISDN', $msisdn)->update(['status_imei'=>'locked']);
                    if($otherid != null){
                        Otherpetition::where('id',$otherid)->update(['status'=>'completado','who_attended'=>$user]);
                    }
                    return response()->json(['http_code'=> 1, 'message'=>$response['imei']]);
                    // return 'Bloqueo Exitoso';
                }else{
                    return $response;
                }
    
            }
        }else if ($status == 'SI') {
            if ($accessTokenResponse['status'] == 'approved') {
                $accessToken = $accessTokenResponse['accessToken'];
                $urlServices = 'https://altanredes-prod.apigee.net/cm/v1/imei/'.$imei.'/unlock';
                $response = Http::withHeaders(['Authorization'=>'Bearer '.$accessToken])->post($urlServices);
                if (isset($response['imei'])) {
                    Number::where('MSISDN', $msisdn)->update(['status_imei'=>'unlocked']);
                    if($otherid != null){
                        Otherpetition::where('id',$otherid)->update(['status'=>'completado','who_attended'=>$user]);
                    }
                    return response()->json(['http_code'=> 1, 'message'=>$response['imei']]);
                    // return 'Desbloqueo exitoso';
                }else{
                    return $response;
                }
            }
        }
    }

    public function replacementSim(Request $request){
        $accessTokenResponse = AltanController::accessTokenRequestPost();
        $msisdn = $request['msisdn'];
        $icc = $request['icc'];
        $type = $request['type'];

        if ($accessTokenResponse['status']== 'approved') {
            $accessToken = $accessTokenResponse['accessToken'];
            $urlServices = 'https://altanredes-prod.apigee.net/cm/v1/subscribers/'.$msisdn;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
                'Content-Type' => 'application/json'
            ])->patch($urlServices,[
                'changeSubscriberSIM'=> array(
                    'newIccid'=>$icc
                )
            ]);
            if (isset($response['order'])) {
                $id_replaced = Number::where('MSISDN', $msisdn)->first();
                Number::where('id',$id_replaced->id)->delete();
                Number::where('icc_id', 'LIKE','%'.$icc.'%')->update(['MSISDN'=> $msisdn, 'status'=>'taken']);

                $id_new = Number::where('icc_id', 'LIKE','%'.$icc.'%')->first();
                Activation::where('numbers_id', $id_replaced->id)->update(['numbers_id'=> $id_new->id]);
                Assignment::where('number_id', $id_replaced->id)->update(['number_id'=> $id_new->id]);
                Change::where('number_id', $id_replaced->id)->update(['number_id'=> $id_new->id]);
                Purchase::where('number_id', $id_replaced->id)->update(['number_id'=> $id_new->id]);
                Reference::where('number_id', $id_replaced->id)->update(['number_id'=> $id_new->id]);

                return response()->json(['http_code'=>1, 'message'=>$response]);
            }else if ($response['errorCode']) {
                return response()->json(['http_code'=>0, 'message'=>$response['description']]);
            }
        }
    }

    public function bondingSIM(Request $request){
        $msisdn = $request->post('msisdn');
        $nir = $request->post('nir');
        $user_id = $request->post('user_id');
        $existsNumber = Number::where('MSISDN',$msisdn)->exists();

        if(!$existsNumber){
            return response()->json(['http_code'=>'3','message'=>'El MSISDN '.$msisdn.' no existe dentro de nuestros registros.']);
        }
        $dataNumber = Number::where('MSISDN',$msisdn)->first();
        $producto = $dataNumber->producto;
        $producto = trim($producto);
        $number_id = $dataNumber->id;

        if($producto == 'HBB'){
            return response()->json(['http_code'=>'2','message'=>'El cambio de MSISDN no está permitido, debido a que es de tipo HBB.']);
        }

        $accessTokenResponse = AltanController::accessTokenRequestPost();

        if(isset($accessTokenResponse['status'])){
            if($accessTokenResponse['status'] == 'approved'){

                $accessToken = $accessTokenResponse['accessToken'];
                $url_production = 'https://altanredes-prod.apigee.net/cm/v1/subscribers/'.$msisdn;
                
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'application/json'
                ])->patch($url_production,[
                    'changeSubscriberMSISDN' => array(
                        'nir' => $nir,
                        'msisdnType' => '1',
                    )
                ]);


                if(isset($response['order'])){
                    $effectiveDate = $response['effectiveDate'];
                    $newMsisdn = $response['newMsisdn'];
                    $oldMsisdn = $response['oldMsisdn'];
                    $order = $response['order'];
                    $order_id = $order['id'];

                    Number::where('MSISDN',$msisdn)->update(['MSISDN'=>$newMsisdn]);
                    Historic::insert([
                        'oldMSISDN' => $oldMsisdn,
                        'date_change' => $effectiveDate,
                        'order_id' => $order_id,
                        'number_id' => $number_id,
                        'who_did_id' => $user_id
                    ]);

                    return response()->json(['http_code'=>1,'message'=>'El MSISDN nuevo es '.$newMsisdn]);
                }else{
                    return response()->json(['http_code'=>0,'Ha ocurrido un error con la petición, consulte a Desarrollo.']);
                }

                return $response;
            }else{
                return "no aprobado";
            }
        }
    }

    public function consultaVinculacion(Request $request){
        // return $request;
        $msisdn = $request->post('msisdn');
        $existsNumber = Number::where('MSISDN',$msisdn)->exists();

        if(!$existsNumber){
            return response()->json(['http_code'=>'3','message'=>'El MSISDN '.$msisdn.' no existe dentro de nuestros registros.']);
        }
        $dataNumber = Number::where('MSISDN',$msisdn)->first();
        $producto = $dataNumber->producto;
        $producto = trim($producto);
        $number_id = $dataNumber->id;

        if($producto == 'MIFI'){
            return response()->json(['http_code'=>2,'message'=>'Debido a que el MSISDN es de tipo MIFI no se puede hacer la consulta']);
        }

        $accessTokenResponse = AltanController::accessTokenRequestPost();
        $be = $dataNumber->be_id;
        if(isset($accessTokenResponse['status'])){
            if($accessTokenResponse['status'] == 'approved'){
                $accessToken = $accessTokenResponse['accessToken'];
                $url_production = 'https://altanredes-prod.apigee.net/cm/v1/msisdns/'.$msisdn.'/linkings?be_id=202';

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'application/json'
                ])->get($url_production);

                if (isset($response['imsi'])) {
                    $imsi = $response['imsi'];
                    $periods = $response['period'];
                    $records = $response['records'];
                    return$response;
                    return response()->json(['http_code'=>'1','message'=>'Imsi: '.$response['imsi'].' Periodo: '.$response['period'].' Registros: '.$response['records']]);
                }else if(isset($response['errorCode'])){
                    return response()->json(['http_code'=>'0','message'=>$response['description']]);
                }
            }
        }

    } 

    public function validateIMEI(Request $request){
        $imei = $request->get('imei');

        $accessTokenResponse = AltanController::accessTokenRequestPost();
    
            if(isset($accessTokenResponse['status'])){
                if($accessTokenResponse['status'] == 'approved'){
    
                    $accessToken = $accessTokenResponse['accessToken'];
                    $url_production = 'https://altanredes-prod.apigee.net/ac/v1/imeis/'.$imei.'/status';
                    
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$accessToken,
                        'Content-Type' => 'application/json'
                    ])->get($url_production);
    
                    return $response;
                }else{
                    return "no aprobado";
                }
            }
    }

    public function activationsBatch(){
        $accessTokenResponse = AltanController::accessTokenRequestPost();
        if(isset($accessTokenResponse['status'])){
            if($accessTokenResponse['status'] == 'approved'){

                $accessToken = $accessTokenResponse['accessToken'];
                $url_production = 'https://altanredes-prod.apigee.net/cm/v1/subscribers/activations';
                
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'multipart/form-data'
                ])->post($url_production,[
                    'archivos' => file_get_contents('storage/uploads/batch.csv')
                ]);

                if(isset($response['transaction'])){
                    $fp = fopen ('storage/uploads/batch.csv','r');

                    while ($data = fgetcsv ($fp, 1000, ",")) {
                        $num = count ($data);
                        $msisdn = $data[0];
                        $offerID = $data[1];
                        $coordinates = $data[2];
                        $scheduleDate = $data[3];
                        echo $msisdn.' - '.$offerID.' - '.$coordinates.' - '.$scheduleDate;
                        echo "<br>";
                    }
                }else{
                    return 'BAD';
                }

            }else{
                return "no aprobado";
            }
        }
    }

    public function resumeSuspendMovility($msisdn,$coordinates){
        $accessTokenResponse = AltanController::accessTokenRequestPost();
        if(isset($accessTokenResponse['status'])){
            if($accessTokenResponse['status'] == 'approved'){

                $accessToken = $accessTokenResponse['accessToken'];
                $url_production = 'https://altanredes-prod.apigee.net/cm/v1/subscribers/'.$msisdn.'/resumespm';
                
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'application/json'
                ])->post($url_production,[
                    'address' => $coordinates
                ]);

                return $response;

            }else{
                return "ACCESS TOKEN no aprobado";
            }
        }
    }

    public function importPortInToAltan($msisdnTransitory,$msisdnPorted,$imsi,$approvedDateABD,$dida,$rida,$dcr,$rcr){
        $accessTokenResponse = AltanController::accessTokenRequestPost();
        if(isset($accessTokenResponse['status'])){
            if($accessTokenResponse['status'] == 'approved'){

                $accessToken = $accessTokenResponse['accessToken'];
                $url_production = 'https://altanredes-prod.apigee.net/ac/v1/msisdns/port-in-c';
                
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'application/json'
                ])->post($url_production,[
                    'msisdnTransitory' => $msisdnTransitory,
                    'msisdnPorted' => $msisdnPorted,
                    'imsi' => $imsi,
                    'approvedDateABD' => $approvedDateABD,
                    'dida' => $dida,
                    'rida' => $rida,
                    'dcr' => $dcr,
                    'rcr' => $rcr,
                    'autoScriptReg' => 'Y',
                ]);

                return $response;

            }else{
                return "ACCESS TOKEN no aprobado";
            }
        }
    }
}
