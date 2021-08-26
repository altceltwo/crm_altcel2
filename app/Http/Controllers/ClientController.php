<?php

namespace App\Http\Controllers;
use DB;
use App\Offer;
use App\Channel;
use App\User;
use App\Reference;
use App\Pay;
use App\Ethernetpay;
use App\Client;
use App\Instalation;
use App\Activation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NewclientsExport;

class ClientController extends Controller
{
    public function index(){
        $data['clients'] = DB::table('users')
                              ->join('activations','activations.client_id','=','users.id')
                              ->join('numbers','numbers.id','=','activations.numbers_id')
                              ->join('rates','rates.id','=','activations.rate_id')
                              ->leftJoin('devices','devices.id','=','activations.devices_id')
                              ->leftJoin('clients','clients.user_id','=','users.id')
                              ->select('users.name AS name','users.lastname AS lastname',
                              'clients.cellphone AS cellphone','numbers.MSISDN AS MSISDN',
                              'numbers.producto AS service','devices.no_serie_imei AS imei',
                              'rates.name AS rate_name','rates.price_subsequent AS amount_rate','activations.date_activation AS date_activation','activations.amount_device AS amount_device')
                              ->get();

        $data['clientsTwo'] = DB::table('users')
                                 ->join('instalations','instalations.client_id','=','users.id')
                                 ->join('packs','packs.id','=','instalations.pack_id')
                                 ->leftJoin('clients','clients.user_id','=','users.id')
                                 ->select('users.name AS name','users.lastname AS lastname',
                                 'clients.cellphone AS cellphone','instalations.number AS number',
                                 'packs.name AS pack_name','packs.price AS amount_pack',
                                 'packs.service_name AS service','instalations.date_instalation','instalations.amount_install AS amount_install')
                                 ->get();

        return view('clients.index',$data);
    }
    public function rechargeGenerateClient() {
        $current_id = auth()->user()->id;
        $data['clientDatas'] = DB::table('activations')
                            ->join('numbers','numbers.id','=','activations.numbers_id')
                            ->where('activations.client_id','=',$current_id)
                            ->select('activations.*','numbers.MSISDN', 'numbers.producto', 'numbers.id AS number_id')
                            ->get();
        $data['offers'] = Offer::all()->where('action','=','Recarga');
        $data['channels'] = Channel::all();
        return view('clients.rechargeView',$data);
    }

    public function clientsPayAll() {
        // $data['clients'] = DB::table('users')
        //                       ->leftJoin('clients','clients.user_id','=','users.id')
        //                       ->where('users.role_id',3)
        //                       ->select('users.*','clients.cellphone AS client_phone','clients.rfc AS RFC','clients.address AS client_address')
        //                       ->get();
        
        $data['clients'] = DB::table('users')
                          ->leftJoin('activations','activations.client_id','=','users.id')
                          ->leftJoin('instalations','instalations.client_id','=','users.id')
                          ->leftJoin('clients','clients.user_id','=','users.id')
                          ->where('activations.client_id','!=',null)
                          ->orWhere('instalations.client_id','!=',null)
                          ->select('users.*','clients.cellphone AS client_phone','clients.rfc AS RFC','clients.address AS client_address')
                          ->distinct()
                          ->get();
        // return $data['clients'];
        $current_role = auth()->user()->role_id;
        $current_id = auth()->user()->id;
        
        return view('clients.showAll',$data);
    }

    public function clientDetails($id){
        // return $_SERVER["HTTP_CLIENT_IP"];
        $clientData = User::where('id',$id)->first();

        $data['mypays'] = DB::table('pays')
                             ->join('activations','activations.id','=','pays.activation_id')
                             ->join('numbers','numbers.id','=','activations.numbers_id')
                             ->join('rates','rates.id','=','activations.rate_id')
                             ->where('activations.client_id',$id)
                             ->where('pays.status','pendiente')
                             ->select('pays.*','numbers.MSISDN AS DN','numbers.producto AS number_product','numbers.id AS number_id','activations.id AS activation_id','rates.name AS rate_name','rates.price AS rate_price')
                             ->get();
        $data['my2pays'] = DB::table('ethernetpays')
                              ->join('instalations','instalations.id','=','ethernetpays.instalation_id')
                              ->join('packs','packs.id','=','instalations.pack_id')
                              ->where('instalations.client_id',$id)
                              ->where('ethernetpays.status','pendiente')
                              ->select('ethernetpays.*','packs.name AS pack_name','packs.price AS pack_price','packs.service_name AS service_name','instalations.id AS instalation_id','instalations.number AS instalation_number')
                              ->get();
        
        $data['completemypays'] = DB::table('pays')
                             ->join('activations','activations.id','=','pays.activation_id')
                             ->join('numbers','numbers.id','=','activations.numbers_id')
                             ->join('rates','rates.id','=','activations.rate_id')
                             ->leftJoin('references','references.reference_id','=','pays.reference_id')
                             ->where('activations.client_id',$id)
                             ->where('pays.status','completado')
                             ->select('pays.*','numbers.MSISDN AS DN','numbers.producto AS number_product','rates.name AS rate_name','rates.price AS rate_price','references.reference AS reference_folio')
                             ->get();
                            
        $data['completemy2pays'] = DB::table('ethernetpays')
                              ->join('instalations','instalations.id','=','ethernetpays.instalation_id')
                              ->join('packs','packs.id','=','instalations.pack_id')
                              ->leftJoin('references','references.reference_id','=','ethernetpays.reference_id')
                              ->where('instalations.client_id','=',$id)
                              ->where('ethernetpays.status','completado')
                              ->select('ethernetpays.*','packs.name AS pack_name','packs.price AS pack_price','packs.service_name AS service_name','references.reference AS reference_folio','instalations.number AS instalation_number')
                              ->get();

        $data['activations'] = DB::table('activations')
                                  ->join('numbers','numbers.id','=','activations.numbers_id')
                                  ->join('rates','rates.id','=','activations.rate_id')
                                  ->where('activations.client_id',$id)
                                  ->select('activations.*','numbers.MSISDN AS DN','numbers.producto AS service','rates.name AS pack_name')
                                  ->get();
        
        $data['instalations'] = DB::table('instalations')
                                   ->join('packs','packs.id','=','instalations.pack_id')
                                   ->where('instalations.client_id',$id)
                                   ->select('instalations.*','packs.service_name AS service','packs.name AS pack_name')
                                   ->get();
        $data['client_id'] = $id;
        $data['client_name'] = $clientData->name.' '.$clientData->lastname;
        // return $data['completemy2pays'];
        return view('clients.clientDetails',$data);
    }

    public function showReferenceClient(Request $request){
        $reference_id = $request->get('reference_id');
        $response = Reference::where('reference_id',$reference_id)->first();
        return $response;
    }

    public function searchClients(Request $request){
        $term = $request->get('term');
        $querys = DB::table('users')
                        ->join('clients', 'clients.user_id', '=', 'users.id')
                        ->where('role_id',3)
                        ->where('users.name', 'LIKE', '%'. $term. '%')
                        ->orWhere('users.email','LIKE','%'. $term. '%')
                        ->select('clients.*','users.name','users.lastname','users.email','users.id as user_id')
                        ->get();

        return $querys;
    }

    public function searchClientProduct(Request $request) {
        $id = $request->get('id');
        $querys = DB::table('activations')
                     ->join('numbers','numbers.id','=','activations.numbers_id')
                     ->where('activations.client_id','=',$id)
                     ->select('activations.*','numbers.MSISDN', 'numbers.producto', 'numbers.id AS number_id')
                     ->get();
        return $querys;
    }

    public function generateReference($id,$type,$user_id){
        $current_role = auth()->user()->role_id;
        $employe_id = $current_role == 3 ? 'null' : auth()->user()->id;
            $user = DB::table('users')
                   ->join('clients','clients.user_id','=','users.id')
                   ->where('users.id',$user_id)
                   ->select('users.*','clients.cellphone AS client_cellphone')
                   ->get();
        $user_name = $user[0]->name;
        $user_lastname = $user[0]->lastname;
        $user_email = $user[0]->email;
        $user_phone = $user[0]->client_cellphone;
        if($type == 'MIFI' || $type == 'HBB' || $type == 'MOV'){
            $referencestype = 1;
            $pay = DB::table('pays')
                      ->join('activations','activations.id','=','pays.activation_id')
                      ->join('rates','rates.id','=','activations.rate_id')
                      ->join('numbers','numbers.id','=','activations.numbers_id')
                      ->join('offers','offers.id','=','rates.alta_offer_id')
                      ->where('pays.id',$id)
                      ->where('activations.client_id',$user_id)
                      ->select('pays.*','activations.numbers_id AS activation_number_id',
                               'numbers.MSISDN AS DN','rates.name AS rate_name','rates.id AS rate_id',
                               'rates.price AS rate_price','offers.id as offer_id')
                               ->get();
            $number_id = $pay[0]->activation_number_id;
            $DN = $pay[0]->DN;
            $rate_id = $pay[0]->rate_id;
            $rate_name = $pay[0]->rate_name;
            
            $amount = $pay[0]->amount;
            $amount_received = $pay[0]->amount_received;
            if($amount_received == null){
                $rate_price = $pay[0]->amount;
            }else{
                $rate_price = $pay[0]->amount - $pay[0]->amount_received;
            }
            // return $rate_price;
            // $rate_price = $pay[0]->amount;
            $offer_id = $pay[0]->offer_id;

            $channels =  Channel::all();
            if($type == 'MIFI'){
                $concepto = 'MIFI';
            }else if($type == 'HBB'){
                $concepto = 'HBB';
            }else if($type == 'MOV'){
                $concepto = 'de Telefonía Celular (Movilidad)';
            }

            $rates = DB::table('rates')
                        ->join('offers','offers.id','=','rates.alta_offer_id')
                        ->where('offers.type','normal')
                        ->where('offers.product','like','%'.$type.'%')
                        ->where('rates.id','!=',$rate_id)
                        ->where('rates.status','=','activo')
                        ->where('rates.type','=','publico')
                        ->select('rates.*','offers.id AS offer_id')
                        ->get();

            // $data['rates'] = $rates;

            $data = array(
                'datos' => [
                    "referencestype" => $referencestype,
                    "number_id" => $number_id,
                    "DN" => $DN,
                    "rate_id" => $rate_id,
                    "rate_name" => $rate_name,
                    "rate_price" => $rate_price,
                    "offer_id" => $offer_id,
                    "concepto" => "Pago de Servicio ".$concepto
                ],
                'channels' => $channels,
                'data_client' => [
                    "client_name" => $user_name,
                    "client_lastname" => $user_lastname,
                    "client_email" => $user_email,
                    "client_phone" => $user_phone,
                    "client_id" => $user_id,
                    "employe_id" => $employe_id,
                    "pay_id" => $id,
                    "service" => "Altan Redes"
                ],
                'rates' => $rates
                );
                // return $data;
        }else if($type == 'Conecta' || $type == 'Telmex'){
            $referencestype = 2;
            $pay = DB::table('ethernetpays')
                      ->join('instalations','instalations.id','=','ethernetpays.instalation_id')
                      ->join('packs','packs.id','=','instalations.pack_id')
                      ->where('ethernetpays.id',$id)
                      ->where('instalations.client_id',$user_id)
                      ->select('ethernetpays.*','packs.id AS pack_id','packs.name AS pack_name','packs.price AS pack_price')
                      ->get();
        
            $pack_id = $pay[0]->pack_id;
            $pack_name = $pay[0]->pack_name;

            $amount = $pay[0]->amount;
            $amount_received = $pay[0]->amount_received;
            if($amount_received == null){
                $pack_price = $pay[0]->amount;
            }else{
                $pack_price = $pay[0]->amount - $pay[0]->amount_received;
            }
            // return $pack_price;

            // $pack_price = $pay[0]->amount;
            $channels =  Channel::all();

            $data = array(
                'datos' => [
                    "referencestype" => $referencestype,
                    "pack_id" => $pack_id,
                    "pack_name" => $pack_name,
                    "pack_price" => $pack_price,
                    "concepto" => "Pago de Servicio de Internet."
                ],
                'channels' => $channels,
                'data_client' => [
                    "client_name" => $user_name,
                    "client_lastname" => $user_lastname,
                    "client_email" => $user_email,
                    "client_phone" => $user_phone,
                    "client_id" => $user_id,
                    "employe_id" => $employe_id,
                    "pay_id" => $id,
                    "service" => "Conecta"
                ]
                );
        }

        return view('clients.generatePay')->with($data);
    }

    public function productDetails($id_dn,$id_act,$service){
        if($service == 'MIFI' || $service == 'HBB' || $service == 'MOV'){
            $dataQuery = DB::table('activations')
                       ->join('numbers','numbers.id','=','activations.numbers_id')
                       ->join('rates','rates.id','=','activations.rate_id')
                       ->where('activations.id',$id_act)
                       ->where('activations.numbers_id',$id_dn)
                       ->select('numbers.MSISDN AS DN','numbers.traffic_outbound AS traffic_outbound',
                       'numbers.traffic_outbound_incoming AS traffic_outbound_incoming',
                       'rates.name AS rate_name','rates.price AS rate_price',
                       'activations.date_activation AS date_activation')
                       ->get();
            
            $data['DN'] = $dataQuery[0]->DN;
            $data['service'] = $service;
            $data['pack_name'] = $dataQuery[0]->rate_name;
            $data['pack_price'] = $dataQuery[0]->rate_price;
            $data['date_activation'] = $dataQuery[0]->date_activation;
            $data['traffic_out'] = $dataQuery[0]->traffic_outbound;
            $data['traffic_out_in'] = $dataQuery[0]->traffic_outbound_incoming;

            $data['mypays'] = DB::table('pays')
                                 ->join('references','references.reference_id','=','pays.reference_id')
                                 ->join('channels','channels.id','=','references.channel_id')
                                 ->where('pays.activation_id',$id_act)
                                 ->where('pays.status','completado')
                                 ->select('pays.*','references.amount','references.currency','references.reference','references.fee_amount','channels.name AS channel_name')
                                 ->get();
            $data['mypaysManual'] = DB::table('pays')
                                      ->leftJoin('references','references.reference_id','=','pays.reference_id')
                                      ->where('pays.status','completado')
                                      ->where('pays.type_pay','=','deposito')
                                      ->orWhere('pays.type_pay','=','transferencia')
                                      ->orWhere('pays.type_pay','=','efectivo')
                                      ->orWhere('pays.type_pay','=','efectivo/referencia')
                                      ->orWhere('pays.type_pay','=','deposito/referencia')
                                      ->orWhere('pays.type_pay','=','transferencia/referencia')
                                      ->select('pays.*','references.amount AS reference_amount')
                                      ->get();
                                    //  return $data['mypaysManual'];

        }else if($service == 'Conecta' || $service == 'Telmex'){
            $dataQuery = DB::table('instalations')
                            ->join('packs','packs.id','=','instalations.pack_id')
                            ->where('instalations.id',$id_act)
                            ->select('instalations.*','packs.name AS pack_name','packs.price AS pack_price')
                            ->get();
            
            $data['mypays'] = DB::table('ethernetpays')
                            ->join('references','references.reference_id','=','ethernetpays.reference_id')
                            ->join('channels','channels.id','=','references.channel_id')
                            ->where('ethernetpays.instalation_id',$id_act)
                            ->where('ethernetpays.status','completado')
                            ->select('ethernetpays.*','references.amount','references.currency','references.reference','references.fee_amount','channels.name AS channel_name')
                            ->get();
            
            $data['mypaysManual'] = DB::table('ethernetpays')
                            ->leftJoin('references','references.reference_id','=','ethernetpays.reference_id')
                            ->where('ethernetpays.status','completado')
                            ->where('ethernetpays.type_pay','=','deposito')
                            ->orWhere('ethernetpays.type_pay','=','transferencia')
                            ->orWhere('ethernetpays.type_pay','=','efectivo')
                            ->orWhere('ethernetpays.type_pay','=','efectivo/referencia')
                            ->orWhere('ethernetpays.type_pay','=','deposito/referencia')
                            ->orWhere('ethernetpays.type_pay','=','transferencia/referencia')
                            ->select('ethernetpays.*','references.amount AS reference_amount')
                            ->get();
            
            $data['service'] = $service;
            $data['pack_name'] = $dataQuery[0]->pack_name;
            $data['pack_price'] = $dataQuery[0]->pack_price;
            $data['date_activation'] = $dataQuery[0]->date_instalation;
        }
        return view('clients.productDetails',$data);
    }

    public function create(){
        return view('clients.create');
    }

    public function store(Request $request){
        $time = time();
        $h = date("g", $time);
        
        $name = $request->post('name');
        $lastname = $request->post('lastname');
        $email = $request->post('email');

        $rfc = $request->post('rfc');
        $date_born = $request->post('date_born');
        $address = $request->post('address');
        $cellphone = $request->post('celphone');
        $ine_code = $request->post('ine_code');
        $user_id = $request->post('user');
        $interests = $request->post('interests');
        
         if($email == null){
             $email = str_replace(' ', '', $name).date("YmdHis", $time);
         }

        $x = User::where('email',$email)->exists();
        if($x){
            $error = '<p>El usuario con el email <strong>'.$email.'</strong> ya existe.<p>';
            return back()->with('error',$error);
        }
        

        User::insert([
            'name' => $name,
            'lastname' => $lastname,
            'email' => $email,
            'password' => Hash::make('123456789')
        ]);

        $client_id = User::where('email',$email)->first();
        $client_id = $client_id->id;

        Client::insert([
            'address' => $address,
            'ine_code' => $ine_code,
            'date_born' => $date_born,
            'rfc' => $rfc,
            'cellphone' => $cellphone,
            'user_id' => $client_id,
            'who_did_id' => $user_id,
            'interests' => $interests
        ]);
        $success = 'Cliente añadido con éxito.';
        return back()->with('success',$success);
    }

    public function storeAsync(Request $request){
        $time = time();
        $h = date("g", $time);

        $name = $request->get('name');
        $lastname = $request->get('lastname');
        $email = $request->get('email');

        $rfc = $request->get('rfc');
        $date_born = $request->get('date_born');
        $address = $request->get('address');
        $cellphone = $request->get('cellphone');
        $ine_code = $request->get('ine_code');
        $user_id = $request->get('user_id');
        $interests = $request->post('interests');
        $date_created = date('Y-m-d');
        
         if($email == null){
             $email = str_replace(' ', '', $name).date("YmdHis", $time);
         }

        $x = User::where('email',$email)->exists();
        if($x){
            $error = 'El usuario con el email <strong>'.$email.'</strong> ya existe.';
            return response()->json(['error'=>1,'message'=>$error]);
        }
        

        User::insert([
            'name' => $name,
            'lastname' => $lastname,
            'email' => $email,
            'password' => Hash::make('123456789')
        ]);

        $client_id = User::where('email',$email)->first();
        $client_id = $client_id->id;

        Client::insert([
            'address' => $address,
            'ine_code' => $ine_code,
            'date_born' => $date_born,
            'rfc' => $rfc,
            'cellphone' => $cellphone,
            'user_id' => $client_id,
            'who_did_id' => $user_id,
            'interests' => $interests,
            'date_created' => $date_created
        ]);

        $success = 'Cliente añadido con éxito.';
        return response()->json(['error'=>0,'message'=>$success]);
    }

    public function getNumberInstalation(Request $request){
        $id = $request->id;
        $type = $request->type;
        if ($type == 'activation') {
            $response = Activation::findOrFail($id);
        }else if($type == 'instalation'){
            $response = Instalation::findOrFail($id);
        }
        
        return $response;
    }

    public function setNumberInstalation(Request $request){
        $id = $request->id;
        $number = $request->number;
        $serial_number = $request->serial_number;
        $type = $request->type;
        
        // return $request;
        //TODO VALIDACION PARA UPDATE
        if ($type == 'instalation') {
            Instalation::where('id',$id)->update(['number'=>$number,'serial_number'=>$serial_number]);
        }else if($type == 'activation'){
            Activation::where('id',$id)->update(['serial_number'=>$serial_number]);
        }
        return 1;
    }

    public function prospects(){
        $news = DB::table('users')
               ->leftJoin('activations','activations.client_id','=','users.id')
               ->leftJoin('instalations','instalations.client_id','=','users.id')
               ->join('clients','clients.user_id','=','users.id')
               ->where('role_id',3)
               ->where('activations.client_id',null)
               ->where('instalations.client_id',null)
               ->select('users.id AS user_id','users.name','users.lastname','users.email','clients.address AS address','clients.cellphone AS phone','clients.who_did_id AS who_added','clients.interests AS interests')
               ->get();

        $newsHBB = DB::table('users')
                          ->leftJoin('activations','activations.client_id','=','users.id')
                          ->leftJoin('instalations','instalations.client_id','=','users.id')
                          ->join('clients','clients.user_id','=','users.id')
                          ->where('role_id',3)
                          ->where('activations.client_id',null)
                          ->where('instalations.client_id',null)
                          ->where('clients.interests','HBB')
                          ->select('users.name','users.lastname','users.email','clients.address AS address','clients.cellphone AS phone','clients.who_did_id AS who_added','clients.interests AS interests')
                          ->get();

        $newsMIFI = DB::table('users')
                          ->leftJoin('activations','activations.client_id','=','users.id')
                          ->leftJoin('instalations','instalations.client_id','=','users.id')
                          ->join('clients','clients.user_id','=','users.id')
                          ->where('role_id',3)
                          ->where('activations.client_id',null)
                          ->where('instalations.client_id',null)
                          ->where('clients.interests','MIFI')
                          ->select('users.name','users.lastname','users.email','clients.address AS address','clients.cellphone AS phone','clients.who_did_id AS who_added','clients.interests AS interests')
                          ->get();
        
        $newsTelmex = DB::table('users')
                          ->leftJoin('activations','activations.client_id','=','users.id')
                          ->leftJoin('instalations','instalations.client_id','=','users.id')
                          ->join('clients','clients.user_id','=','users.id')
                          ->where('role_id',3)
                          ->where('activations.client_id',null)
                          ->where('instalations.client_id',null)
                          ->where('clients.interests','Portabilidad Telmex')
                          ->select('users.name','users.lastname','users.email','clients.address AS address','clients.cellphone AS phone','clients.who_did_id AS who_added','clients.interests AS interests')
                          ->get();
               
        $newClients = [];

        foreach($news as $new){
            $who_added = $new->who_added;
            $user_who_added = User::where('id',$who_added)->first();
            $email_bool = strpos($new->email, '@');
            $email = $email_bool ? $new->email : 'N/A';
            array_push($newClients,array(
                'id' => $new->user_id,
                'name' => strtoupper($new->name),
                'lastname' => strtoupper($new->lastname),
                'email' => $email,
                'address' => strtoupper($new->address),
                'phone' => $new->phone,
                'who_did_id' => strtoupper($user_who_added->name.' '.$user_who_added->lastname),
                'interests' => $new->interests
            ));
        }

        $data['HBB'] = sizeof($newsHBB);
        $data['MIFI'] = sizeof($newsMIFI);
        $data['Telmex'] = sizeof($newsTelmex);
        $data['prospects'] = $newClients;
        return view('clients.prospects',$data);
    }

    public function getAllDataClient(Request $request){
        $id = $request->id;
        $dataClient = DB::table('users')
                         ->leftJoin('clients','clients.user_id','=','users.id')
                         ->where('users.id',$id)
                         ->select('users.*','clients.address AS address','clients.ine_code AS ine_code','clients.rfc AS rfc','clients.cellphone AS cellphone',
                         'clients.date_born AS date_born','clients.who_did_id AS who_did_id')
                         ->get();
        
        $user_id = $dataClient[0]->id;
        $name = $dataClient[0]->name;
        $lastname = $dataClient[0]->lastname;
        $email = $dataClient[0]->email;
        $address = $dataClient[0]->address;
        $ine_code = $dataClient[0]->ine_code;
        $rfc = $dataClient[0]->rfc;
        $cellphone = $dataClient[0]->cellphone;
        $date_born = $dataClient[0]->date_born;
        $who_did_id = $dataClient[0]->who_did_id;
        
        if($who_did_id == null){
            $who_did = 'ESTA PERSONA ES UN EMPLEADO';
        }else{
            $dataUser = User::where('id',$who_did_id)->first();
            $who_did = $dataUser->name.' '.$dataUser->lastname;
        }

        $response = array([
            'user_id' => $user_id,
            'name' => $name,
            'lastname' => $lastname,
            'email' => $email,
            'address' => $address,
            'ine_code' => $ine_code,
            'rfc' => $rfc,
            'cellphone' => $cellphone,
            'date_born' => $date_born,
            'who_did' => $who_did
        ]);

        return $response;
    }

    public function setAllDataClient(Request $request){
        $address = $request->post('address');
        $cellphone = $request->post('cellphone');
        $date_born = $request->post('date_born');
        $ine_code = $request->post('ine_code');
        $rfc = $request->post('rfc');
        $user_id = $request->post('user_id');

        $request = request()->except('_token','address','cellphone','date_born','ine_code','rfc','user_id');
        $x = User::where('id',$user_id)->update($request);

        $z = Client::where('user_id',$user_id)->exists();

        if($z){
            $y = Client::where('user_id',$user_id)->update([
                'address' => $address,
                'ine_code' => $ine_code,
                'rfc' => $rfc,
                'cellphone' => $cellphone,
                'date_born' => $date_born
            ]);
        }else{
            $y = Client::insert([
                'user_id' => $user_id,
                'address' => $address,
                'ine_code' => $ine_code,
                'rfc' => $rfc,
                'cellphone' => $cellphone,
                'date_born' => $date_born
            ]);
        }
        

        if($x && $y){
            return 1;
        }else{
            return 0;
        }
        return 2;
    }
    public function exportNewClients(){

        return Excel::download(new NewclientsExport, 'new-clients.xlsx');
    }

    public function specialOperations(){
        $data['channels'] = Channel::all();
        $data['clients'] = DB::table('activations')
                              ->join('numbers','numbers.id','=','activations.numbers_id')
                              ->join('users','users.id','=','activations.client_id')
                              ->select('users.name AS name','users.lastname AS lastname','users.email AS email','numbers.MSISDN AS msisdn','numbers.producto AS producto')
                              ->get();
        return view('clients.specialsOperations',$data);
    }

    public function getDataClientChangeProduct(Request $request){
        $msisdn = $request->get('msisdn');

        $response = DB::table('numbers')
                       ->join('activations','activations.numbers_id','=','numbers.id')
                       ->join('users','users.id','=','activations.client_id')
                       ->join('clients','clients.user_id','=','users.id')
                       ->where('MSISDN',$msisdn)
                       ->select('users.name AS name','users.lastname AS lastname',
                       'users.email AS email','clients.cellphone AS cellphone',
                       'users.id AS client_id','numbers.id AS number_id')
                       ->get();

        $response = $response[0];
        return response()->json($response);
    }

    public function getDataClientBySIM(Request $request){
        $number_id = $request->get('number_id');

        $response = DB::table('numbers')
                       ->join('activations','activations.numbers_id','=','numbers.id')
                       ->join('users','users.id','=','activations.client_id')
                       ->join('clients','clients.user_id','=','users.id')
                       ->where('numbers.id',$number_id)
                       ->select('users.name AS name','users.lastname AS lastname',
                       'users.email AS email','clients.cellphone AS cellphone',
                       'users.id AS client_id','numbers.id AS number_id')
                       ->get();

        $response = $response[0];
        return response()->json($response);
    }

}