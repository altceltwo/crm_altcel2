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

    public function consultUF(){
        $accessTokenResponse = AltanController::accessTokenRequestPost();

        if($accessTokenResponse['status'] == 'approved'){
            $accessToken = $accessTokenResponse['accessToken'];
            
            $url_production = 'https://altanredes-prod.apigee.net/cm/v1/subscribers/3339064244/profile';
                    
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

            if($http_code == 1){
                $x = Activation::where('id',$activation_id)->update([
                    'offer_id' => $offer_id,
                    'rate_id' => $rate_id
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
                    "status" => $status
                ]);

                if(!$x){
                    $http_code = 2;
                    $message = 'El cambio de producto se realizó con éxito, pero no se pudo actualizar la información de MSISDN '.$msisdn.' en la Base de Datos.';
                }
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
            }else{
                $http_code = 0;
                $message = $response['description'];
            }
            $data['http_code'] = $http_code;
            $data['message'] = $message;
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

                    Purchase::insert([
                        "number_id" => $number_id,
                        "offer_id" => $offer_id,
                        "rate_id" => $rate_id,
                        "who_did_id" => $user_id,
                        "amount" => $price,
                        "date" => $date,
                        "comment" => $comment,
                        "reason" => $reason,
                        "status" => $status
                    ]);
                }else{
                    $message = $response['description'];
                    $http_code = 0;
                }
            // } while ($i < $quantity_gb);

            return response()->json(['http_code'=>$http_code,'message'=>$message]);
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

        if ($status == 'NO') {
            if($accessTokenResponse['status']== 'approved'){
                $accessToken = $accessTokenResponse['accessToken'];
                $urlServices = 'https://altanredes-prod.apigee.net/cm/v1/imei/'.$imei.'/lock';
                $response = Http::withHeaders(['Authorization'=>'Bearer '.$accessToken])->post($urlServices);
                if (isset($response['imei'])) {
                    Number::where('MSISDN', $msisdn)->update(['status_imei'=>'locked']);
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
                    return response()->json(['http_code'=> 1, 'message'=>$response['imei']]);
                    // return 'Desbloqueo exitoso';
                }else{
                    return $response;
                }
            }
        }
    }

}
