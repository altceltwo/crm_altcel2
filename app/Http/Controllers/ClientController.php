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
use App\Number;
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
                        ->leftJoin('clients', 'clients.user_id', '=', 'users.id')
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
            $data['channels'] = Channel::all();
            $dataQuery = DB::table('activations')
                       ->join('numbers','numbers.id','=','activations.numbers_id')
                       ->join('rates','rates.id','=','activations.rate_id')
                       ->where('activations.id',$id_act)
                       ->where('activations.numbers_id',$id_dn)
                       ->select('numbers.MSISDN AS DN','numbers.traffic_outbound AS traffic_outbound',
                       'numbers.traffic_outbound_incoming AS traffic_outbound_incoming',
                       'rates.name AS rate_name','rates.price AS rate_price',
                       'activations.date_activation AS date_activation','activations.lat_hbb AS lat','activations.lng_hbb AS lng','numbers.id AS number_id')
                       ->get();
            
            $data['DN'] = $dataQuery[0]->DN;
            $data['lat'] = $dataQuery[0]->lat;
            $data['lng'] = $dataQuery[0]->lng;
            $data['service'] = $service;
            $data['pack_name'] = $dataQuery[0]->rate_name;
            $data['pack_price'] = $dataQuery[0]->rate_price;
            $data['date_activation'] = $dataQuery[0]->date_activation;
            $data['traffic_out'] = $dataQuery[0]->traffic_outbound;
            $data['traffic_out_in'] = $dataQuery[0]->traffic_outbound_incoming;
            $number_id = $dataQuery[0]->number_id;

            $consultUF = app('App\Http\Controllers\AltanController')->consultUF($data['DN']);
            // return $consultUF;

            $responseSubscriber = $consultUF['responseSubscriber'];
            $information = $responseSubscriber['information'];
            $status = $responseSubscriber['status']['subStatus'];
            $freeUnits = $responseSubscriber['freeUnits'];
            $coordinates = $responseSubscriber['information']['coordinates'];
            $char = explode(',',$coordinates);

            if($service == 'HBB'){
                $lat_hbb = $char[0];
                $lng_hbb = $char[1];
                $data['lat'] = $lat_hbb;
                $data['lng'] = $lng_hbb;
            }else if($service == 'MIFI' || $service == 'MOV'){
                $lat_hbb = null;
                $lng_hbb = null;
            }

            $data['consultUF']['status'] = $status;
            $data['consultUF']['imei'] = $information['IMEI'];
            $data['consultUF']['icc'] = $information['ICCID'];

            if($status == 'Active'){
                $data['consultUF']['status_color'] = 'success';
            }else if($status == 'Suspend (B2W)' || $status == 'Barring (B1W) (Notified by client)'){
                $data['consultUF']['status_color'] = 'warning';
            }

            if($service == 'MIFI' || $service == 'HBB'){
                $data['FreeUnitsBoolean'] = 0;
                $data['FreeUnits2Boolean'] = 0;
                $data['consultUF']['offerID'] = 0;

                for ($i=0; $i < sizeof($freeUnits); $i++) {
                    if($freeUnits[$i]['name'] == 'Free Units' || $freeUnits[$i]['name'] == 'FU_Altan-RN'){
                        $totalAmt = $freeUnits[$i]['freeUnit']['totalAmt'];
                        $unusedAmt = $freeUnits[$i]['freeUnit']['unusedAmt'];
                        $percentageFree = ($unusedAmt/$totalAmt)*100;
                        $data['FreeUnits'] = array('totalAmt'=>$totalAmt/1024,'unusedAmt'=>$unusedAmt/1024,'freePercentage'=>$percentageFree);
                        $data['FreeUnitsBoolean'] = 1;

                        $detailOfferings = $freeUnits[$i]['detailOfferings'];

                        $data['effectiveDatePrimary'] = ClientController::formatDateConsultUF($detailOfferings[0]['effectiveDate']);
                        $data['expireDatePrimary'] = ClientController::formatDateConsultUF($detailOfferings[0]['expireDate']);
                        $expire_date = $detailOfferings[0]['expireDate'];
                        $expire_date = substr($expire_date,0,8);

                        $data['consultUF']['offerID'] = $detailOfferings[0]['offeringId'];
                    }

                    if($freeUnits[$i]['name'] == 'Free Units 2' || $freeUnits[$i]['name'] == 'FU_Altan-RN_P2'){
                        $totalAmt = $freeUnits[$i]['freeUnit']['totalAmt'];
                        $unusedAmt = $freeUnits[$i]['freeUnit']['unusedAmt'];
                        $percentageFree = ($unusedAmt/$totalAmt)*100;
                        $data['FreeUnits2'] = array('totalAmt'=>$totalAmt/1024,'unusedAmt'=>$unusedAmt/1024,'freePercentage'=>$percentageFree);
                        $data['FreeUnits2Boolean'] = 1;

                        $detailOfferings = $freeUnits[$i]['detailOfferings'];

                        $data['effectiveDateSurplus'] = ClientController::formatDateConsultUF($detailOfferings[0]['effectiveDate']);
                        $data['expireDateSurplus'] = ClientController::formatDateConsultUF($detailOfferings[0]['expireDate']);
                    }
                }

                $rateData = DB::table('numbers')
                                   ->leftJoin('activations','activations.numbers_id','=','numbers.id')
                                   ->leftJoin('rates','rates.id','=','activations.rate_id')
                                   ->where('numbers.MSISDN',$data['DN'])
                                   ->select('rates.name AS rate_name')
                                   ->get();

                if($status == 'Suspend (B2W)'){
                    $data['consultUF']['rate'] = $rateData[0]->rate_name.'/Suspendido por falta de pago';    
                }else if($status == 'Active'){
                    $data['consultUF']['rate'] = $rateData[0]->rate_name;
                }

                if($status == 'Active'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'activo',
                        'traffic_outbound_incoming' => 'activo',
                        'status_altan' => 'activo'
                    ]);
    
                    if($service = 'MIFI'){
                        Activation::where('numbers_id',$number_id)->update(['expire_date'=>$expire_date]);
                    }
    
                    if($service = 'HBB'){
                        Activation::where('numbers_id',$number_id)->update(['expire_date'=>$expire_date,'lat_hbb'=>$lat_hbb,'lng_hbb'=>$lng_hbb]);
                    }
                }else if($status == 'Suspend (B2W)'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'activo',
                        'traffic_outbound_incoming' => 'inactivo',
                        'status_altan' => 'activo'
                    ]);
                    if($service = 'MIFI'){
                        Activation::where('numbers_id',$number_id)->update(['expire_date'=>$expire_date]);
                    }
                    if($service = 'HBB'){
                        Activation::where('numbers_id',$number_id)->update(['expire_date'=>$expire_date,'lat_hbb'=>$lat_hbb,'lng_hbb'=>$lng_hbb]);
                    }
                }else if($status == 'Predeactivate'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'activo',
                        'traffic_outbound_incoming' => 'activo',
                        'status_altan' => 'predeactivate'
                    ]);
                }else if($status == 'Barring (B1W) (Notified by client)'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'inactivo',
                        'traffic_outbound_incoming' => 'activo',
                        'status_altan' => 'activo'
                    ]);
                }

                if($data['FreeUnits2Boolean'] == 0){
                    $data['FreeUnits2'] = array('totalAmt'=>0,'unusedAmt'=>0,'freePercentage'=>0);
                    $data['effectiveDateSurplus'] = 'No se ha generado recarga.';
                    $data['expireDateSurplus'] = 'No se ha generado recarga.';
                }
            }else if($service == 'MOV'){
                $data['consultUF']['freeUnits']['extra'] = [];
                $data['consultUF']['freeUnits']['nacionales'] = [];
                $data['consultUF']['freeUnits']['ri'] = [];
                $data['consultUF']['offerID'] = 0;
                for ($i=0; $i < sizeof($freeUnits); $i++) {
                    $totalAmt = $freeUnits[$i]['freeUnit']['totalAmt'];
                    $unusedAmt = $freeUnits[$i]['freeUnit']['unusedAmt'];
                    $percentageFree = ($unusedAmt/$totalAmt)*100;
                    $indexDetailtOfferings = sizeof($freeUnits[$i]['detailOfferings']);
                    $indexDetailtOfferings = $indexDetailtOfferings-1;
                    $effectiveDate = ClientController::formatDateConsultUF($freeUnits[$i]['detailOfferings'][$indexDetailtOfferings]['effectiveDate']);
                    $expireDate = ClientController::formatDateConsultUF($freeUnits[$i]['detailOfferings'][$indexDetailtOfferings]['expireDate']);

                    if($freeUnits[$i]['name'] == 'FreeData_Altan-RN'){
                        $data['consultUF']['offerID'] = $freeUnits[$i]['detailOfferings'][$indexDetailtOfferings]['offeringId'];
                        array_push($data['consultUF']['freeUnits']['nacionales'],array(
                            'totalAmt'=>$totalAmt/1024,'unusedAmt'=>$unusedAmt/1024,'freePercentage'=>$percentageFree,'name'=>'Datos Nacionales','description'=>'MB','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }else if($freeUnits[$i]['name'] == 'FU_SMS_Altan-NR-LDI_NA'){
                        array_push($data['consultUF']['freeUnits']['nacionales'],array(
                            'totalAmt'=>$totalAmt,'unusedAmt'=>$unusedAmt,'freePercentage'=>$percentageFree,'name'=>'SMS Nacionales','description'=>'sms','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }else if($freeUnits[$i]['name'] == 'FU_Min_Altan-NR-LDI_NA'){
                        array_push($data['consultUF']['freeUnits']['nacionales'],array(
                            'totalAmt'=>$totalAmt,'unusedAmt'=>$unusedAmt,'freePercentage'=>$percentageFree,'name'=>'Minutos Nacionales','description'=>'min','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }else if($freeUnits[$i]['name'] == 'FU_Data_Altan-NR-IR_NA'){
                        array_push($data['consultUF']['freeUnits']['ri'],array(
                            'totalAmt'=>$totalAmt/1024,'unusedAmt'=>$unusedAmt/1024,'freePercentage'=>$percentageFree,'name'=>'Datos RI','description'=>'GB','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }else if($freeUnits[$i]['name'] == 'FU_SMS_Altan-NR-IR-LDI_NA'){
                        array_push($data['consultUF']['freeUnits']['ri'],array(
                            'totalAmt'=>$totalAmt,'unusedAmt'=>$unusedAmt,'freePercentage'=>$percentageFree,'name'=>'SMS RI','description'=>'sms','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }else if($freeUnits[$i]['name'] == 'FU_Min_Altan-NR-IR-LDI_NA'){
                        array_push($data['consultUF']['freeUnits']['ri'],array(
                            'totalAmt'=>$totalAmt,'unusedAmt'=>$unusedAmt,'freePercentage'=>$percentageFree,'name'=>'Minutos RI','description'=>'min','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }else if($freeUnits[$i]['name'] == 'FU_Redirect_Altan-RN'){
                        array_push($data['consultUF']['freeUnits']['extra'],array(
                            'totalAmt'=>$totalAmt/1024,'unusedAmt'=>$unusedAmt/1024,'freePercentage'=>$percentageFree,'name'=>'Navegación en Portal Cautivo','description'=>'MB','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }else if($freeUnits[$i]['name'] == 'FU_ThrMBB_Altan-RN_512kbps'){
                        array_push($data['consultUF']['freeUnits']['extra'],array(
                            'totalAmt'=>$totalAmt/1024,'unusedAmt'=>$unusedAmt/1024,'freePercentage'=>$percentageFree,'name'=>'Velocidad Reducida','description'=>'MB','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }
                    // else if($freeUnits[$i]['name'] == 'FU_Data_Altan-RN_RG18'){
                    //     array_push($data['consultUF']['freeUnits']['extra'],array('totalAmt'=>$totalAmt/1024,'unusedAmt'=>$unusedAmt/1024,'freePercentage'=>$percentageFree,'name'=>'no sabe','description'=>'MB','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate));

                    // }
                    // print_r($freeUnits[$i]['name'].'  --  ');
                    // print_r($freeUnits[$i]['freeUnit']['totalAmt'].'  --  ');
                    // print_r($freeUnits[$i]['freeUnit']['unusedAmt'].'<br>');
                    
                }

                if($data['consultUF']['offerID'] == 0){
                    $data['consultUF']['rate'] = 'PLAN NO CONTRATADO';    
                }else{
                    $rateData = Offer::where('offerID_second',$data['consultUF']['offerID'])->first();
                    $data['consultUF']['rate'] = $rateData->name_second;
                }

                if($status == 'Active'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'activo',
                        'traffic_outbound_incoming' => 'activo',
                        'status_altan' => 'activo'
                    ]);
                }else if($status == 'Suspend (B2W)'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'activo',
                        'traffic_outbound_incoming' => 'inactivo',
                        'status_altan' => 'activo'
                    ]);
                }else if($status == 'Predeactivate'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'activo',
                        'traffic_outbound_incoming' => 'activo',
                        'status_altan' => 'predeactivate'
                    ]);
                }else if($status == 'Barring (B1W) (Notified by client)'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'inactivo',
                        'traffic_outbound_incoming' => 'activo',
                        'status_altan' => 'activo'
                    ]);
                }
            }

            // return $data['consultUF']['freeUnits'];

        }else if($service == 'Conecta' || $service == 'Telmex'){
            $dataQuery = DB::table('instalations')
                            ->join('packs','packs.id','=','instalations.pack_id')
                            ->where('instalations.id',$id_act)
                            ->select('instalations.*','packs.name AS pack_name','packs.price AS pack_price')
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

    public function getInfoUF($msisdn){
        $bool = Number::where('MSISDN',$msisdn)->exists();

        if($bool){
            $numberData = Number::where('MSISDN',$msisdn)->first();
            $service = $numberData->producto;
            $number_id = $numberData->id;
            $service = trim($service);

            $consultUF = app('App\Http\Controllers\AltanController')->consultUF($msisdn);
            // return $consultUF;
            $responseSubscriber = $consultUF['responseSubscriber'];
            $information = $responseSubscriber['information'];
            $status = $responseSubscriber['status']['subStatus'];
            $freeUnits = $responseSubscriber['freeUnits'];

            $coordinates = $responseSubscriber['information']['coordinates'];
            $char = explode(',',$coordinates);

            if($service == 'HBB'){
                $lat_hbb = $char[0];
                $lng_hbb = $char[1];
            }else if($service == 'MIFI' || $service == 'MOV'){
                $lat_hbb = null;
                $lng_hbb = null;
            }

            $data = [];
            $data['status'] = $status;
            $data['imei'] = $information['IMEI'];
            $data['icc'] = $information['ICCID'];
            // return $status;
            if($status == 'Active'){
                $data['status_color'] = 'success';
            }else if($status == 'Suspend (B2W)' || $status == 'Barring (B1W) (Notified by client)'){
                $data['status_color'] = 'warning';
            }

            $data['service'] = $service;

            if($service == 'MIFI' || $service == 'HBB'){
                $data['FreeUnitsBoolean'] = 0;
                $data['FreeUnits2Boolean'] = 0;
                $data['consultUF']['offerID'] = 0;

                for ($i=0; $i < sizeof($freeUnits); $i++) {
                    if($freeUnits[$i]['name'] == 'Free Units' || $freeUnits[$i]['name'] == 'FU_Altan-RN'){
                        $totalAmt = $freeUnits[$i]['freeUnit']['totalAmt'];
                        $unusedAmt = $freeUnits[$i]['freeUnit']['unusedAmt'];
                        $percentageFree = ($unusedAmt/$totalAmt)*100;
                        $data['FreeUnits'] = array('totalAmt'=>$totalAmt/1024,'unusedAmt'=>$unusedAmt/1024,'freePercentage'=>$percentageFree);
                        $data['FreeUnitsBoolean'] = 1;

                        $detailOfferings = $freeUnits[$i]['detailOfferings'];

                        $data['effectiveDatePrimary'] = ClientController::formatDateConsultUF($detailOfferings[0]['effectiveDate']);
                        $data['expireDatePrimary'] = ClientController::formatDateConsultUF($detailOfferings[0]['expireDate']);
                        $expire_date = $detailOfferings[0]['expireDate'];
                        $expire_date = substr($expire_date,0,8);

                        $data['consultUF']['offerID'] = $detailOfferings[0]['offeringId'];
                    }

                    if($freeUnits[$i]['name'] == 'Free Units 2' || $freeUnits[$i]['name'] == 'FU_Altan-RN_P2'){
                        $totalAmt = $freeUnits[$i]['freeUnit']['totalAmt'];
                        $unusedAmt = $freeUnits[$i]['freeUnit']['unusedAmt'];
                        $percentageFree = ($unusedAmt/$totalAmt)*100;
                        $data['FreeUnits2'] = array('totalAmt'=>$totalAmt/1024,'unusedAmt'=>$unusedAmt/1024,'freePercentage'=>$percentageFree);
                        $data['FreeUnits2Boolean'] = 1;

                        $detailOfferings = $freeUnits[$i]['detailOfferings'];

                        $data['effectiveDateSurplus'] = ClientController::formatDateConsultUF($detailOfferings[0]['effectiveDate']);
                        $data['expireDateSurplus'] = ClientController::formatDateConsultUF($detailOfferings[0]['expireDate']);
                    }
                }

                $rateData = DB::table('numbers')
                                   ->leftJoin('activations','activations.numbers_id','=','numbers.id')
                                   ->leftJoin('rates','rates.id','=','activations.rate_id')
                                   ->where('numbers.MSISDN',$msisdn)
                                   ->select('rates.name AS rate_name')
                                   ->get();

                if($status == 'Suspend (B2W)'){
                    $data['consultUF']['rate'] = $rateData[0]->rate_name.'/Suspendido por falta de pago';    
                }else if($status == 'Active'){
                    $data['consultUF']['rate'] = $rateData[0]->rate_name;
                }

                if($status == 'Active'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'activo',
                        'traffic_outbound_incoming' => 'activo',
                        'status_altan' => 'activo'
                    ]);
    
                    if($service = 'MIFI'){
                        Activation::where('numbers_id',$number_id)->update(['expire_date'=>$expire_date]);
                    }
    
                    if($service = 'HBB'){
                        Activation::where('numbers_id',$number_id)->update(['expire_date'=>$expire_date,'lat_hbb'=>$lat_hbb,'lng_hbb'=>$lng_hbb]);
                    }
                }else if($status == 'Suspend (B2W)'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'activo',
                        'traffic_outbound_incoming' => 'inactivo',
                        'status_altan' => 'activo'
                    ]);
                    if($service = 'MIFI'){
                        Activation::where('numbers_id',$number_id)->update(['expire_date'=>$expire_date]);
                    }
                    if($service = 'HBB'){
                        Activation::where('numbers_id',$number_id)->update(['expire_date'=>$expire_date,'lat_hbb'=>$lat_hbb,'lng_hbb'=>$lng_hbb]);
                    }
                }else if($status == 'Predeactivate'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'activo',
                        'traffic_outbound_incoming' => 'activo',
                        'status_altan' => 'predeactivate'
                    ]);
                }else if($status == 'Barring (B1W) (Notified by client)'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'inactivo',
                        'traffic_outbound_incoming' => 'activo',
                        'status_altan' => 'activo'
                    ]);
                }

                if($data['FreeUnits2Boolean'] == 0){
                    $data['FreeUnits2'] = array('totalAmt'=>0,'unusedAmt'=>0,'freePercentage'=>0);
                    $data['effectiveDateSurplus'] = 'No se ha generado recarga.';
                    $data['expireDateSurplus'] = 'No se ha generado recarga.';
                }
            }else if($service == 'MOV'){
                $data['consultUF']['freeUnits']['extra'] = [];
                $data['consultUF']['freeUnits']['nacionales'] = [];
                $data['consultUF']['freeUnits']['ri'] = [];
                $data['consultUF']['offerID'] = 0;
                for ($i=0; $i < sizeof($freeUnits); $i++) {
                    $totalAmt = $freeUnits[$i]['freeUnit']['totalAmt'];
                    $unusedAmt = $freeUnits[$i]['freeUnit']['unusedAmt'];
                    $percentageFree = ($unusedAmt/$totalAmt)*100;
                    $indexDetailtOfferings = sizeof($freeUnits[$i]['detailOfferings']);
                    $indexDetailtOfferings = $indexDetailtOfferings-1;
                    $effectiveDate = ClientController::formatDateConsultUF($freeUnits[$i]['detailOfferings'][$indexDetailtOfferings]['effectiveDate']);
                    $expireDate = ClientController::formatDateConsultUF($freeUnits[$i]['detailOfferings'][$indexDetailtOfferings]['expireDate']);

                    if($freeUnits[$i]['name'] == 'FreeData_Altan-RN'){
                        $data['consultUF']['offerID'] = $freeUnits[$i]['detailOfferings'][$indexDetailtOfferings]['offeringId'];
                        array_push($data['consultUF']['freeUnits']['nacionales'],array(
                            'totalAmt'=>$totalAmt/1024,'unusedAmt'=>$unusedAmt/1024,'freePercentage'=>$percentageFree,'name'=>'Datos Nacionales','description'=>'MB','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }else if($freeUnits[$i]['name'] == 'FU_SMS_Altan-NR-LDI_NA'){
                        array_push($data['consultUF']['freeUnits']['nacionales'],array(
                            'totalAmt'=>$totalAmt,'unusedAmt'=>$unusedAmt,'freePercentage'=>$percentageFree,'name'=>'SMS Nacionales','description'=>'sms','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }else if($freeUnits[$i]['name'] == 'FU_Min_Altan-NR-LDI_NA'){
                        array_push($data['consultUF']['freeUnits']['nacionales'],array(
                            'totalAmt'=>$totalAmt,'unusedAmt'=>$unusedAmt,'freePercentage'=>$percentageFree,'name'=>'Minutos Nacionales','description'=>'min','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }else if($freeUnits[$i]['name'] == 'FU_Data_Altan-NR-IR_NA'){
                        array_push($data['consultUF']['freeUnits']['ri'],array(
                            'totalAmt'=>$totalAmt/1024,'unusedAmt'=>$unusedAmt/1024,'freePercentage'=>$percentageFree,'name'=>'Datos RI','description'=>'GB','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }else if($freeUnits[$i]['name'] == 'FU_SMS_Altan-NR-IR-LDI_NA'){
                        array_push($data['consultUF']['freeUnits']['ri'],array(
                            'totalAmt'=>$totalAmt,'unusedAmt'=>$unusedAmt,'freePercentage'=>$percentageFree,'name'=>'SMS RI','description'=>'sms','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }else if($freeUnits[$i]['name'] == 'FU_Min_Altan-NR-IR-LDI_NA'){
                        array_push($data['consultUF']['freeUnits']['ri'],array(
                            'totalAmt'=>$totalAmt,'unusedAmt'=>$unusedAmt,'freePercentage'=>$percentageFree,'name'=>'Minutos RI','description'=>'min','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }else if($freeUnits[$i]['name'] == 'FU_Redirect_Altan-RN'){
                        array_push($data['consultUF']['freeUnits']['extra'],array(
                            'totalAmt'=>$totalAmt/1024,'unusedAmt'=>$unusedAmt/1024,'freePercentage'=>$percentageFree,'name'=>'Navegación en Portal Cautivo','description'=>'MB','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }else if($freeUnits[$i]['name'] == 'FU_ThrMBB_Altan-RN_512kbps'){
                        array_push($data['consultUF']['freeUnits']['extra'],array(
                            'totalAmt'=>$totalAmt/1024,'unusedAmt'=>$unusedAmt/1024,'freePercentage'=>$percentageFree,'name'=>'Velocidad Reducida','description'=>'MB','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate
                        ));

                    }
                    // else if($freeUnits[$i]['name'] == 'FU_Data_Altan-RN_RG18'){
                    //     array_push($data['consultUF']['freeUnits']['extra'],array('totalAmt'=>$totalAmt/1024,'unusedAmt'=>$unusedAmt/1024,'freePercentage'=>$percentageFree,'name'=>'no sabe','description'=>'MB','effectiveDate'=>$effectiveDate,'expireDate'=>$expireDate));

                    // }
                    // print_r($freeUnits[$i]['name'].'  --  ');
                    // print_r($freeUnits[$i]['freeUnit']['totalAmt'].'  --  ');
                    // print_r($freeUnits[$i]['freeUnit']['unusedAmt'].'<br>');
                    
                }

                if($data['consultUF']['offerID'] == 0){
                    $data['consultUF']['rate'] = 'PLAN NO CONTRATADO';    
                }else{
                    $rateData = Offer::where('offerID_second',$data['consultUF']['offerID'])->first();
                    $data['consultUF']['rate'] = $rateData->name_second;
                }

                if($status == 'Active'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'activo',
                        'traffic_outbound_incoming' => 'activo',
                        'status_altan' => 'activo'
                    ]);
                }else if($status == 'Suspend (B2W)'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'activo',
                        'traffic_outbound_incoming' => 'inactivo',
                        'status_altan' => 'activo'
                    ]);
                }else if($status == 'Predeactivate'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'activo',
                        'traffic_outbound_incoming' => 'activo',
                        'status_altan' => 'predeactivate'
                    ]);
                }else if($status == 'Barring (B1W) (Notified by client)'){
                    Number::where('id',$number_id)->update([
                        'traffic_outbound' => 'inactivo',
                        'traffic_outbound_incoming' => 'activo',
                        'status_altan' => 'activo'
                    ]);
                }
            }

            return view('clients.consumptions',$data);
        }else{
            return view('home');
        }
    }

    public function formatDateConsultUF($date){
        $year = substr($date,0,4);
        $month = substr($date,4,2);
        $day = substr($date,6,2);
        $hour = substr($date,8,2);
        $minute = substr($date,10,2);
        $second = substr($date,12,2);
        $date = $day.'-'.$month.'-'.$year.' '.$hour.':'.$minute.':'.$second;
        return $date;
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

    public function consumos(Request $request){
        $num = $request['msisdn'];
        $msisdn= '52'.$num;
        $date_start = $request['date_start'];
        $date_end = $request['date_end'];
        $ano = substr($date_start, -4);
        $mes = substr($date_start, 0,2);
        $dia = substr($date_start, 3, -5);
        $dateStart = $ano. '-'. $mes.'-'.$dia;
        $anoEnd = substr($date_end, -4);
        $mesEnd = substr($date_end, 0,2);
        $diaEnd = substr($date_end, 3, -5);
        $dateEnd = $anoEnd. '-'. $mesEnd.'-'.$diaEnd;

        // return $dateStart.'  -  '.$dateEnd;
        $consumos = DB::select("CALL sftp_altan.consumos_datos('".$msisdn."','".$dateStart."','".$dateEnd."')");

        return $consumos;
    }
}
