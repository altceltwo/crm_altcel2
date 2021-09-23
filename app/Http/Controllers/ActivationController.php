<?php

namespace App\Http\Controllers;
use Http;
use DB;
use DateTime;
use App\Activation;
use App\Assignment;
use App\Client;
use App\Clientsson;
use App\Number;
use App\Device;
use App\Offer;
use App\User;
use App\Simexternal;
use App\Instalation;
use App\Pack;
use App\Pay;
use App\Ethernetpay;
use App\Radiobase;
use App\Rate;
use App\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendAccess;
use App\GuzzleHttp;

class ActivationController extends Controller
{
    public function index(Request $request){
        if(isset($request['start']) && isset($request['end'])){
            if($request['start'] != null && $request['end'] != null){
                $year =  substr($request['start'],6,4);
                $month = substr($request['start'],0,2);
                $day = substr($request['start'],3,2);
                $date_init = $year.'-'.$month.'-'.$day;

                $year =  substr($request['end'],6,4);
                $month = substr($request['end'],0,2);
                $day = substr($request['end'],3,2);
                $date_final = $year.'-'.$month.'-'.$day;

                // return $date_init.' y '.$date_final;
            }else{
                
                $date_final = date('Y-m-d');
                $date_init = strtotime('-30 days', strtotime($date_final));
                $date_init = date('Y-m-d', $date_init);
            }
        }else{
            $date_final = date('Y-m-d');
            $date_init = strtotime('-30 days', strtotime($date_final));
            $date_init = date('Y-m-d', $date_init);
        }

        $data = ActivationController::indexFilter($date_init,$date_final);
        $data['date_init'] = $date_init;
        $data['date_final'] = $date_final;
        $current_role = auth()->user()->role_id;
        // return $date_init.' - '.$date_final;
        
        return view('activations.index',$data);
    }

    public function create(Request $request){
        if(isset($request['from'])){
            $data['name'] = $request->get('name');
            $data['lastname'] = $request->get('lastname');
            $data['rfc'] = $request->get('rfc');
            $data['date_born'] = $request->get('date_born');
            $data['address'] = $request->get('address');
            $data['email'] = $request->get('email');
            $data['ine_code'] = $request->get('ine_code');
            $data['cellphone'] = $request->get('cellphone');
            $data['petition'] = $request->get('petition');
        }else{
            $data['name'] = '';
            $data['lastname'] = '';
            $data['rfc'] = '';
            $data['date_born'] = '';
            $data['address'] = '';
            $data['email'] = '';
            $data['ine_code'] = '';
            $data['cellphone'] = '';
            $data['petition'] = 0;
        }
        $data['packs'] = Pack::all()->where('status','activo');
        $data['radiobases'] = Radiobase::all();
        // $current_role = auth()->user()->role_id;

        return view('activations.create',$data);
    }

    public function activationGeneral(Request $request) {
        $scheduleDate = $request->get('scheduleDate');
        $offer_id = $request->get('offer_id');
        $rate_id = $request->get('rate_id');
        $rate_recurrency = $request->get('rate_recurrency');
        $imei = $request->get('imei');
        $serial_number = $request->get('serial_number');
        $icc_id = $request->get('icc_id');
        $msisdn = $request->get('msisdn');
        $price = $request->get('price');
        $price_rate = $request->get('price_rate');
        $price_device = $request->get('price_device');
        $clientName = $request->get('name');
        $clientLastname = $request->get('lastname');
        $email = $request->get('email');
        $cellphone = $request->get('cellphone');
        $from = $request->get('from');
        $address = $request->get('address');
        $ine_code = $request->get('ine_code');
        $rfc = $request->get('rfc');
        $date_born = $request->get('date_born');
        $sim_altcel = $request->get('sim_altcel');
        $user_id = $request->get('who_did_id');
        $lat_hbb = $request->get('lat_hbb');
        $lng_hbb = $request->get('lng_hbb');
        $product = $request->get('product');

        // Datos de Cliente Child
        $name_child = $request->get('name_child');
        $lastname_child = $request->get('lastname_child');
        $email_child = $request->get('email_child');
        $rfc_child = $request->get('rfc_child');
        $date_born_child = $request->get('date_born_child');
        $ine_code_child = $request->get('ine_code_child');
        $cellphone_child = $request->get('cellphone_child');
        $address_child = $request->get('address_child');
        $type_person = $request->get('type_person');
        $email_not = $request->get('email_not');
        $activate_bool = $request->get('activate_bool');
        $status = $request->get('statusActivation');
        $petition = $request->get('petition');
        
        $request = request()->except('_token','offer_id','rate_id','imei','icc_id','msisdn','from','name','lastname','email','email_not','activate_bool','scheduleDate','petition');
        
        $number = Number::where('icc_id',$icc_id)->where('MSISDN',$msisdn)->first();
        $number_id = $number->id;
        $number_status = $number->status;

        $pack_altcel = Rate::where('id',$rate_id)->first();
        $pack_id = $pack_altcel->altcel_pack_id;
        $rate_price = $pack_altcel->price;
        // return $pack_id;
        
        if($number_status == 'taken'){
            return 0;
        }
        // Notificación y envío de datos a Altcel en Plan Alianza
        if($sim_altcel != 'nothing'){
            $now = now();
            $x = ActivationController::sendToAltcel($clientName,$clientLastname,$address,$ine_code,$msisdn,$sim_altcel,$pack_id,$now);
            if($x != 'true'){
                // $r = '';
                // if(isset($x['simAltcel'])){
                //     $r = $x['simAltcel'];
                // }
                // return $x;
            }
        }

        if($status == 'activated'){
            if($activate_bool == 0){
                $accessToken = app('App\Http\Controllers\AltanController')->accessTokenRequestPost();
                if($accessToken['status'] == 'approved'){
                    $accessToken = $accessToken['accessToken'];
                    $activationAltan = app('App\Http\Controllers\AltanController')->activationRequestPost($accessToken,$msisdn,$offer_id,$lat_hbb,$lng_hbb,$product,$scheduleDate);
                    
                    if($activationAltan['msisdn'] == $msisdn){
    
                    }else{
                        return 2;
                    }
                }else{
                    return 3;
                }
            }
        }
            
            // Verificar existencia de cliente en DB
            $flag = User::where('email','=',$email)->exists();
            if($flag){
                $clientData = User::where('email',$email)->first();
                $idClient = $clientData->id;
            }else{
                $namePass = ActivationController::exceptSpecials($clientName);
                $lastnamePass = ActivationController::exceptSpecials($clientLastname);
                $pass = substr($namePass,0,2).$lastnamePass;
                $pass = strtolower($pass);

                User::insert([
                    'name' => $clientName,
                    'lastname' => $clientLastname,
                    'email' => $email,
                    'password' => Hash::make($pass),
                    'role_id' => 3,
                ]);

                $idClient = User::where('email',$email)->first();
                $idClient = $idClient->id;

                Client::insert([
                    'address' => $address,
                    'ine_code' => $ine_code,
                    'rfc' => $rfc,
                    'cellphone' => $cellphone,
                    'date_born' => $date_born,
                    'user_id' => $idClient,
                    'who_did_id' => $user_id
                ]);
                if($email_not == 0){
                    ActivationController::sendCredentials($email,$pass,$clientName,$clientLastname);
                }
            }

            // Actualización de status de SIM en DB
            Number::where('icc_id',$icc_id)->where('MSISDN',$msisdn)->update(['status'=>'taken']);
            
            // Extracción de ID Oferta
            $offer_id = Offer::where('offerID',$offer_id)->first();
            $offer_id = $offer_id->id;

            // Actualización de status de Dispositivo en DB
            if($imei != 'null'){
                Device::where('no_serie_imei','like','%'.$imei.'%')->update(['status'=>'taken']);
                $device_id = Device::where('no_serie_imei','like','%'.$imei.'%')->first();
                $device_id = $device_id->id;
            }else{
                $device_id = null;
            }

            // Verificación de existencia de Clientson
            if($type_person == 'moral'){
                $client_son = Clientsson::where('rfc','=',$rfc_child)->exists();
            }else{
                $client_son = Clientsson::where('email','=',$email_child)->exists();
            }

            // Inserción/Extracción de id de Clientson, según status de existencia (true/false)
            if($client_son){
                if($type_person == 'moral'){
                    $client_son = Clientsson::where('rfc','=',$rfc_child)->first();
                }else{
                    $client_son = Clientsson::where('email','=',$email_child)->first();
                }
                $client_son_id = $client_son->id;
            }else{
                Clientsson::insert([
                    'name' => $name_child,
                    'lastname' => $lastname_child,
                    'rfc' => $rfc_child,
                    'date_born' => $date_born_child,
                    'address' => $address_child,
                    'email' => $email_child,
                    'ine_code' => $ine_code_child,
                    'cellphone' => $cellphone_child,
                    'type_person' => $type_person
                ]);

                if($type_person == 'moral'){
                    $client_son = Clientsson::where('rfc','=',$rfc_child)->first();
                }else{
                    $client_son = Clientsson::where('email','=',$email_child)->first();
                }
                $client_son_id = $client_son->id;
            }

            
            if($scheduleDate == ''){
                $now = now();
            }else{
                $now = $scheduleDate;
            }
            // $user_id = auth()->user()->id;
            $dataActivation = [
                'client_id' => $idClient,
                'numbers_id' => $number_id,
                'devices_id' => $device_id,
                'serial_number' => $serial_number,
                'date_activation' => $now,
                'who_did_id' => $user_id,
                'offer_id' => $offer_id,
                'rate_id' => $rate_id,
                'dependence_id' => $dependence = $from == 'self' || $from == 'promoters' ? null : $from,
                'amount' => $price,
                'amount_rate' => $price_rate,
                'amount_device' => $price_device,
                'clientson_id' => $client_son_id,
                'lat_hbb' => $lat_hbb,
                'lng_hbb' => $lng_hbb,
                'payment_status' => 'pendiente',
                'status' => $status,
                'petition_id' => $petition
            ];

            if($petition == 1){
                $date = date('Y-m-d H:i:s');
                DB::table('petitions')->where('id',$petition)->update([
                    'status' => 'activado',
                    'who_attended' => $user_id,
                    'date_activated' => $date
                ]);
            }

            Activation::insert($dataActivation);
            $activationID = Activation::latest('id')->first();
            $activationID = $activationID->id;

            if($sim_altcel != 'nothing'){
                Simexternal::insert([
                    'sim_altcel' => $sim_altcel,
                    'activation_id' => $activationID,
                    'client_id' => $idClient
                ]);

            }

            if($from == 'promoters'){
                $x = Assignment::where('promoter_id',$user_id)
                               ->where('number_id',$number_id)
                               ->where('type','sim')
                               ->update(['status'=>'taken']);
                $y = Assignment::where('promoter_id',$user_id)->where('device_id',$device_id)->where('type','device')->update(['status'=>'taken']);
                if(!$x || !$y){
                    return 4;
                }
            }
            return 1;
            /* else{
                $date_now = date("Y-m-d");

                $date_pay = new DateTime($date_now);
                // $date_pay = $date_pay->format('Y-m-d');
                $date_pay->modify('last day of this month');
                $date_pay = $date_pay->format('Y-m-d');

                $date_limit = strtotime('+5 days', strtotime($date_pay));
                $date_limit = date('Y-m-d', $date_limit);

                Pay::insert([
                    'date_pay' => $date_pay,
                    'date_pay_limit' => $date_limit,
                    'status' => 'pendiente',
                    'activation_id' => $activationID,
                    'amount' => $rate_price
                ]);
                return 1;
            } */
    }

    public function activationEthernet(Request $request) {
        // Datos personales del cliente
        $name = $request->get('name');
        $lastname = $request->get('lastname');
        $email = $request->get('email');
        // Usuario que ejecutó el movimiento
        $who_did_id = $request->get('who_did_id');

        // Datos de vivienda y extras del cliente
        $address = $request->get('client_address');
        $ine_code = $request->get('ine_code');
        $rfc = $request->get('rfc');
        $date_born = $request->get('date_born');
        $cellphone = $request->get('cellphone');

        // Dato de Paquete
        $pack_id = $request->get('pack_id');
        $packDB = Pack::where('id',$pack_id)->first();
        $recurrency = $packDB->recurrency;
        $pack_price = $packDB->price;
        $pack_service = $request->get('pack_service');

        // Datos de Cliente Child
        $name_child = $request->get('name_child');
        $lastname_child = $request->get('lastname_child');
        $rfc_child = $request->get('rfc_child');
        $date_born_child = $request->get('date_born_child');
        $client_address_child = $request->get('client_address_child');
        $ine_code_child = $request->get('ine_code_child');
        $email_child = $request->get('email_child');
        $cellphone_child = $request->get('cellphone_child');
        $type_person = $request->get('type_person');
        $email_not = $request->get('email_not');

        $client_id = $request->get('client_id');
        $schedule_flag = $request->get('schedule_flag');
        $schedule_id = 0;
        
        if($pack_service == 'Conecta'){
            if($schedule_flag == 1){
                $schedule_id = $request->get('schedule_id');
                $request = request()->except(
                    '_token','name','lastname','email','rfc','date_born',
                    'ine_code','cellphone','client_address','name_child',
                    'lastname_child','email_child','rfc_child','date_born_child',
                    'ine_code_child','cellphone_child','client_address_child',
                    'pack_service','schedule_flag','schedule_id','type_person','email_not'); 
            }else if($schedule_flag == 0){
                $schedule_id = null;
                $request = request()->except(
                    '_token','name','lastname','email','rfc','date_born',
                    'ine_code','cellphone','client_address','name_child',
                    'lastname_child','email_child','rfc_child','date_born_child',
                    'ine_code_child','cellphone_child','client_address_child',
                    'pack_service','schedule_flag','type_person','email_not');  
            }
                    
        }else{
            $request = request()->except(
                '_token','name','lastname','email','rfc','date_born',
                'ine_code','cellphone','client_address','pack_service',
                'no_serie_antena','mac_address_antena','model_antena',
                'ip_address_antena','no_serie_router','mac_address_router',
                'model_router','radiobase_id','name_child','lastname_child',
                'email_child','rfc_child','date_born_child','ine_code_child',
                'cellphone_child','client_address_child','schedule_flag','type_person','email_not');
        }

        $client_flag = User::where('email',$email)->exists();

        if($client_flag){
            $client_id = User::where('email',$email)->first();
            $client_id = $client_id->id;
            $request['client_id'] = $client_id;
        }else{
            $namePass = ActivationController::exceptSpecials($name);
            $lastnamePass = ActivationController::exceptSpecials($lastname);
            $pass = substr($namePass,0,2).$lastnamePass;
            $pass = strtolower($pass);

            User::insert([
                'name' => $name,
                'lastname' => $lastname,
                'email' => $email,
                'password' => Hash::make($pass),
                'role_id' => 3
            ]);

            $client_id = User::where('email',$email)->first();
            $client_id = $client_id->id;

            Client::insert([
                'address' => $address,
                'ine_code' => $ine_code,
                'rfc' => $rfc,
                'date_born' => $date_born,
                'cellphone' => $cellphone,
                'user_id' => $client_id,
                'who_did_id' => $who_did_id
            ]);

            $request['client_id'] = $client_id;
            if($email_not == 0){
                ActivationController::sendCredentials($email,$pass,$name,$lastname);
            }
        }

        // Verificación de existencia de Clientson
        if($type_person == 'moral'){
            $client_son = Clientsson::where('rfc','=',$rfc_child)->exists();
        }else{
            $client_son = Clientsson::where('email','=',$email_child)->exists();
        }

        // Inserción/Extracción de id de Clientson, según status de existencia (true/false)
        if($client_son){
            if($type_person == 'moral'){
                $client_son = Clientsson::where('rfc','=',$rfc_child)->first();
            }else{
                $client_son = Clientsson::where('email','=',$email_child)->first();
            }
            $client_son_id = $client_son->id;
        }else{
            Clientsson::insert([
                'name' => $name_child,
                'lastname' => $lastname_child,
                'rfc' => $rfc_child,
                'date_born' => $date_born_child,
                'address' => $client_address_child,
                'email' => $email_child,
                'ine_code' => $ine_code_child,
                'cellphone' => $cellphone_child,
                'type_person' => $type_person
            ]);

            if($type_person == 'moral'){
                $client_son = Clientsson::where('rfc','=',$rfc_child)->first();
            }else{
                $client_son = Clientsson::where('email','=',$email_child)->first();
            }
            $client_son_id = $client_son->id;
        }

        $flag_client_child = Clientsson::where('email','=',$email_child)->exists();
        if($flag_client_child){
            $client_child = Clientsson::where('email',$email_child)->first();
            $client_child_id = $client_child->id;
        }else{
            Clientsson::insert([
                'name' => $name_child,
                'lastname' => $lastname_child,
                'rfc' => $rfc_child,
                'date_born' => $date_born_child,
                'address' => $client_address_child,
                'email' => $email_child,
                'ine_code' => $ine_code_child,
                'cellphone' => $cellphone_child,
                'type_person' => $type_person
            ]);
            $client_child = Clientsson::where('email',$email_child)->first();
            $client_child_id = $client_child->id;
        }

        $request['date_instalation'] = $now = now();
        $request['clientson_id'] = $client_child_id;
        $request['payment_status'] = 'pendiente';
        $x = Instalation::insert($request);
        $activationID = Instalation::latest('id')->first();
        $activationID = $activationID->id;
        if($x){

            /* $date_now = date("Y-m-d");

            $date_pay = new DateTime($date_now);
            // $date_pay = $date_pay->format('Y-m-d');
            $date_pay->modify('last day of this month');
            $date_pay = $date_pay->format('Y-m-d');

            $date_limit = strtotime('+5 days', strtotime($date_pay));
            $date_limit = date('Y-m-d', $date_limit);

            Ethernetpay::insert([
                'date_pay' => $date_pay,
                'date_pay_limit' => $date_limit,
                'status' => 'pendiente',
                'instalation_id' => $activationID,
                'amount' => $pack_price
            ]); */

            if($schedule_flag == 1){
                Schedule::where('id',$schedule_id)->update([
                    'status' => 'completado',
                    'user_id' => $who_did_id,
                    'instalation_id' => $activationID
                ]);
            }

            $response = 'Instalación añadida con éxito.';
        }else{
            $response = 'Parece que hubo un error.';
        }
        return $response;
    }

    public function sendToAltcel($clientName,$clientLastname,$clientAddress,$clientIneCode,$MSISDN,$simAltcel,$pack,$now){
        $url_altcel = "187.217.216.242/webhook/formulario";

        $response = Http::post($url_altcel,[
            "clientData" => array(
                "clientName" => $clientName,
                "clientLastname" => $clientLastname,
                "clientAddress" => $clientAddress,
                "clientIneCode" => $clientIneCode
            ),
            "simAltcel" => $simAltcel,
            "MSISDNAltcel2" => $MSISDN,
            "pack" => $pack,
            "createDate" => $now
        ]);
        return $response->json();
    }

    public function exceptSpecials($string){
        
        $string = str_replace(
        array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
        array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
        $string
        );
    
        //Reemplazamos la E y e
        $string = str_replace(
        array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
        array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
        $string );
    
        //Reemplazamos la I y i
        $string = str_replace(
        array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
        array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
        $string );
    
        //Reemplazamos la O y o
        $string = str_replace(
        array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
        array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
        $string );
    
        //Reemplazamos la U y u
        $string = str_replace(
        array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
        array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
        $string );
    
        //Reemplazamos la N, n, C y c
        $string = str_replace(
        array('Ñ', 'ñ', 'Ç', 'ç'),
        array('N', 'n', 'C', 'c'),
        $string
        );
        return $string;
    }

    public function sendCredentials($email,$pass,$name,$lastname) {
        $data= [
            "subject" => "Accesos Altcel II",
            "name" => $name,
            "lastname" => $lastname,
            "user" => $email,
            "password" => $pass
        ];
        Mail::to($email)->send(new SendAccess($data));
    }

    public function indexFilter($date_init,$date_final){
        $data['clients'] = DB::table('activations')
                              ->join('users','users.id','=','activations.client_id')
                              ->leftJoin('numbers', 'activations.numbers_id', '=', 'numbers.id')
                              ->leftJoin('devices', 'activations.devices_id', '=', 'devices.id')
                              ->leftJoin('pays','pays.activation_id','=','activations.id')
                              ->join('rates', 'rates.id', '=', 'activations.rate_id')
                              ->join('clients','activations.client_id','=','user_id')
                              ->whereBetween('pays.date_pay',[$date_init,$date_final])
                              ->select('activations.*', 'users.name','users.lastname',
                              'numbers.MSISDN','numbers.producto AS pack_service', 
                              'devices.description AS device_desc', 'users.name as promotor',
                              'rates.name AS name_rate','clients.address as address',
                              'clients.cellphone AS client_cellphone','pays.amount_received AS payment_amount',
                              'pays.status AS payment_status','pays.date_pay AS payment_date')
                              ->get();

        $data['clients2'] = DB::table('activations')
                              ->join('users','users.id','=','activations.client_id')
                              ->leftJoin('numbers', 'activations.numbers_id', '=', 'numbers.id')
                              ->leftJoin('devices', 'activations.devices_id', '=', 'devices.id')
                              ->leftJoin('pays','pays.activation_id','=','activations.id')
                              ->join('rates', 'rates.id', '=', 'activations.rate_id')
                              ->join('clients','activations.client_id','=','user_id')
                              ->whereBetween('activations.date_activation',[$date_init,$date_final])
                              ->where('pays.id',null)
                              ->select('activations.*', 'users.name','users.lastname',
                              'numbers.MSISDN','numbers.producto AS pack_service', 
                              'devices.description AS device_desc', 'users.name as promotor',
                              'rates.name AS name_rate','clients.address as address',
                              'clients.cellphone AS client_cellphone','pays.amount_received AS payment_amount',
                              'pays.status AS payment_status','pays.date_pay AS payment_date')
                              ->get();

            $data['instalations'] = DB::table('instalations')
                                   ->join('packs','packs.id','=','instalations.pack_id')
                                   ->join('radiobases','radiobases.id','=','instalations.radiobase_id')
                                   ->join('users','users.id','=','instalations.client_id')
                                   ->join('clients','clients.user_id','=','users.id')
                                   ->leftJoin('ethernetpays','ethernetpays.instalation_id','=','instalations.id')
                                   ->where('service_name','Conecta')
                                   ->whereBetween('ethernetpays.date_pay',[$date_init,$date_final])
                                   ->select('instalations.*','packs.name AS pack_name','packs.service_name AS pack_service',
                                   'radiobases.name as radiobase_name','users.name AS client_name',
                                   'users.lastname AS client_lastname','clients.cellphone AS client_cellphone','ethernetpays.amount_received AS payment_amount',
                                   'ethernetpays.status AS payment_status','ethernetpays.date_pay AS payment_date')
                                   ->get();

            $data['instalations2'] = DB::table('instalations')
                                   ->join('packs','packs.id','=','instalations.pack_id')
                                   ->join('radiobases','radiobases.id','=','instalations.radiobase_id')
                                   ->join('users','users.id','=','instalations.client_id')
                                   ->join('clients','clients.user_id','=','users.id')
                                   ->leftJoin('ethernetpays','ethernetpays.instalation_id','=','instalations.id')
                                   ->where('service_name','Conecta')
                                   ->whereBetween('instalations.date_instalation',[$date_init,$date_final])
                                   ->where('ethernetpays.id',null)
                                   ->select('instalations.*','packs.name AS pack_name','packs.service_name AS pack_service',
                                   'radiobases.name as radiobase_name','users.name AS client_name',
                                   'users.lastname AS client_lastname','clients.cellphone AS client_cellphone','ethernetpays.amount_received AS payment_amount',
                                   'ethernetpays.status AS payment_status','ethernetpays.date_pay AS payment_date')
                                   ->get();

            $data['TELMEXinstalations'] = DB::table('instalations')
                                   ->join('packs','packs.id','=','instalations.pack_id')
                                   ->join('users','users.id','=','instalations.client_id')
                                   ->join('clients','clients.user_id','=','users.id')
                                   ->leftJoin('ethernetpays','ethernetpays.instalation_id','=','instalations.id')
                                   ->where('service_name','Telmex')
                                   ->whereBetween('ethernetpays.date_pay',[$date_init,$date_final])
                                   ->select('instalations.*','packs.name AS pack_name','packs.service_name AS pack_service',
                                   'users.name AS client_name','users.lastname AS client_lastname',
                                   'clients.cellphone AS client_cellphone','ethernetpays.amount_received AS payment_amount',
                                   'ethernetpays.status AS payment_status','ethernetpays.date_pay AS payment_date')
                                   ->get();

            $data['TELMEXinstalations2'] = DB::table('instalations')
                                   ->join('packs','packs.id','=','instalations.pack_id')
                                   ->join('users','users.id','=','instalations.client_id')
                                   ->join('clients','clients.user_id','=','users.id')
                                   ->leftJoin('ethernetpays','ethernetpays.instalation_id','=','instalations.id')
                                   ->where('service_name','Telmex')
                                   ->whereBetween('instalations.date_instalation',[$date_init,$date_final])
                                   ->where('ethernetpays.id',null)
                                   ->select('instalations.*','packs.name AS pack_name','packs.service_name AS pack_service',
                                   'users.name AS client_name','users.lastname AS client_lastname',
                                   'clients.cellphone AS client_cellphone','ethernetpays.amount_received AS payment_amount',
                                   'ethernetpays.status AS payment_status','ethernetpays.date_pay AS payment_date')
                                   ->get();
        
        return $data;
    }

    public function preactivationsIndex(){
        $data['preactivations'] = DB::table('activations')
                                     ->join('users','users.id','=','activations.client_id')
                                     ->join('numbers','numbers.id','=','activations.numbers_id')
                                     ->join('devices','devices.id','=','activations.devices_id')
                                     ->join('rates','rates.id','=','activations.rate_id')
                                     ->where('activations.status','preactivated')
                                     ->select('activations.*','rates.name AS rate_name','numbers.MSISDN AS MSISDN','numbers.icc_id AS ICC','numbers.producto AS producto',
                                     'devices.no_serie_imei AS IMEI','devices.description AS description_device','users.name AS client_name','users.lastname AS client_lastname','users.email AS client_email')
                                     ->get();
        return view('activations.preactivationsIndex',$data);
    }

    public function show(Activation $activation){
        $data['client'] = User::where('id',$activation->client_id)->first();
        $data['number'] = Number::where('id',$activation->numbers_id)->first();
        $data['device'] = Device::where('id',$activation->devices_id)->first();
        $data['rate'] = Rate::where('id',$activation->rate_id)->first();
        $data['offer'] = Offer::where('id',$activation->offer_id)->first();
        $data['user'] = User::where('id',$activation->who_did_id)->first();
        $data['dataClient'] = Client::where('user_id',$activation->client_id)->first();
        $data['activation'] = $activation;
        $producto = $data['number']->producto;
        $producto = trim($producto);
        $data['rates'] = DB::table('rates')
                            ->join('offers','offers.id','=','rates.alta_offer_id')
                            ->where('rates.id','!=',$activation->rate_id)
                            ->where('offers.product','=',$producto)
                            ->where('offers.type','normal')
                            ->select('rates.*')
                            ->get();
        // return $data;
        return view('activations.preactivationShow',$data);
    }

    public function changeRatePreactivate(Request $request){
        $activation = $request->post('activation_id');
        $request = request()->except('_token','activation_id');
        $x = Activation::where('id',$activation)->update($request);

        if($x){
            return 1;
        }else{
            return 0;
        }
    }

    public function rollbackPreactivate(Activation $activation){
        $number_id = $activation->numbers_id;
        $device_id = $activation->devices_id;
        $user_id = $activation->who_did_id;
        $id = $activation->id;
        Number::where('id',$number_id)->update(['status'=>'available']);
        Device::where('id',$device_id)->update(['status'=>'available']);

        if($number_id != null){
            Assignment::where('promoter_id',$user_id)->where('number_id',$number_id)->update(['status'=>'available']);
        }

        if($device_id != null){
            Assignment::where('promoter_id',$user_id)->where('device_id',$device_id)->update(['status'=>'available']);
        }
        $x = Activation::destroy($id);

        if($x){
            return 1;
        }else{
            return 0;
        }
    }

    public function executeActivation(Activation $activation){
        $id = $activation->id;
        $numberData = Number::where('id',$activation->numbers_id)->first();
        $offerData = Offer::where('id',$activation->offer_id)->first();
        $msisdn = $numberData->MSISDN;
        $product = $numberData->producto;
        $product = trim($product);
        $offer_id = $offerData->offerID;
        $date_activation = $activation->date_activation;
        $today = date('Y-m-d');

        if($date_activation == $today){
            $scheduleDate = '';
        }else{
            $date_activation = str_replace('-','',$date_activation);
            $scheduleDate = $date_activation;
        }

        if($product == 'HBB'){
            $lat_hbb = $activation->lat_hbb;
            $lng_hbb = $activation->lng_hbb;
        }else{
            $lat_hbb = '';
            $lng_hbb = '';
        }


        
        $accessToken = app('App\Http\Controllers\AltanController')->accessTokenRequestPost();
        if($accessToken['status'] == 'approved'){
            $accessToken = $accessToken['accessToken'];
            $activationAltan = app('App\Http\Controllers\AltanController')->activationRequestPost($accessToken,$msisdn,$offer_id,$lat_hbb,$lng_hbb,$product,$scheduleDate);
            // return $activationAltan;
            if(isset($activationAltan['msisdn'])){
                Activation::where('id',$id)->update(['status'=>'activated']);
                return response()->json(['http_code'=>1,'message'=>'Activación exitosa.']);
            }else if(isset($activationAltan['errorCode'])){
                return response()->json(['http_code'=>0,'message'=>$activationAltan['description']]);
            }
        }else{
            return response()->json(['http_code'=>2,'message'=>'No se pudo realizar la activación, consulte a Desarrollo.']);
        }
    }
    public function datePay(){
        $date_now = date("Y-m-d");

        $date_pay = new DateTime($date_now);
        // $date_pay = $date_pay->format('Y-m-d');
        $date_pay->modify('last day of this month');
        $date_pay = $date_pay->format('Y-m-d');

        $date_limit = strtotime('+5 days', strtotime($date_pay));
        $date_limit = date('Y-m-d', $date_limit);

        return $date_pay.' - '.$date_limit;

        // $fecha_pay = '2021-06-30';
        // $fecha_limit = '2021-07-05';
        // // Obtenemos el siguiente mes
        // $fecha_pay_new = strtotime('+3 days', strtotime($fecha_pay));
        // $fecha_pay_new = date('Y-m-d',$fecha_pay_new);
        // // Obtenemos su último día
        // $fecha_pay_new = new DateTime($fecha_pay_new);
        // $fecha_pay_new->modify('last day of this month');
        // $fecha_pay_new = $fecha_pay_new->format('Y-m-d');
        // // Obtenemos nueva fecha límite de pago
        // $fecha_limit_new = strtotime('+5 days', strtotime($fecha_pay_new));
        // $fecha_limit_new = date('Y-m-d',$fecha_limit_new);
        // return $fecha_pay_new.' - '.$fecha_limit_new;
        
    }

    public function getDataPayment(Request $request){
        $id = $request->get('id');
        $type = $request->get('type');

        if($type == 'activation'){
            $response = DB::table('activations')
                           ->join('users','users.id','=','activations.client_id')
                           ->join('rates','rates.id','=','activations.rate_id')
                           ->join('offers','offers.id','=','rates.alta_offer_id')
                           ->join('devices','devices.id','=','activations.devices_id')
                           ->where('activations.id',$id)
                           ->select('activations.*','users.name AS name','users.lastname AS lastname','rates.name AS rate_name','offers.product AS service','devices.description AS device_description')
                           ->get();
            // $response = Activation::where('id',$id)->first();
        }else if($type == 'instalation'){
            $response = DB::table('instalations')
                           ->join('users','users.id','=','instalations.client_id')
                           ->join('packs','packs.id','=','instalations.pack_id')
                           ->where('instalations.id',$id)
                           ->select('instalations.amount AS amount_pack','instalations.amount_install AS amount_install','users.name AS name','users.lastname AS lastname','packs.name AS pack_name','packs.service_name AS service',)
                           ->get();
            // $response = Instalation::where('id',$id)->first();
        }
        return $response;
    }

    public function setPaymentStatus(Request $request){
        $id = $request->get('id');
        $type = $request->get('type');
        $request = $request->except('id','type');
        $request['payment_status'] = 'completado';

        if($type == 'activation'){
            // return $request;
            $x = Activation::where('id',$id)->update($request);
        }else if($type == 'instalation'){
            // return $request;
            $x = Instalation::where('id',$id)->update($request);
        }
        
        if($x){
            return 1;
        }else{
            return 0;
        }
    }
}
