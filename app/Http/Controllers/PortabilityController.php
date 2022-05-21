<?php

namespace App\Http\Controllers;

use App\Rate;
use App\User;
use App\Offer;
use App\Number;
use App\Activation;
use App\Portability;
use Illuminate\Http\Request;
use App\Http\Controllers\AltanController;

class PortabilityController extends Controller
{
    public function index()
    {
        $pendings = Portability::all()->where('status','pendiente');
        $activateds = Portability::all()->where('status','activado');
        $completeds = Portability::all()->where('status','completado');
        $arrayPending = [];
        $arrayActivated = [];
        $arrayCompleted = [];

        foreach ($pendings as $pending) {
            $who_did_it = User::where('id',$pending->who_did_it)->first();
            $who_attended = User::where('id',$pending->who_attended)->first();
            $client = User::where('id','=',$pending->client_id)->first();
            $rate = Rate::where('id','=',$pending->rate_id)->first();
            array_push($arrayPending,array(
                'id' => $pending->id,
                'msisdnPorted' => $pending->msisdnPorted,
                'icc' => $pending->icc,
                'msisdnTransitory' => $pending->msisdnTransitory,
                'date' => $pending->date,
                'nip' => $pending->nip,
                'client' => $client->name.' '.$client->lastname,
                'who_did_it' => $who_did_it->name.' '.$who_did_it->lastname,
                'who_attended' => $who_attended = $who_attended == null ? 'N/A' : $who_attended->name.' '.$who_attended->lastname,
                'rate' => $rate->name.' - $'.number_format($rate->price,2)
            ));
        }

        foreach ($activateds as $activated) {
            $who_did_it = User::where('id',$activated->who_did_it)->first();
            $who_attended = User::where('id',$activated->who_attended)->first();
            $client = User::where('id','=',$activated->client_id)->first();
            $rate = Rate::where('id','=',$activated->rate_id)->first();
            array_push($arrayActivated,array(
                'id' => $activated->id,
                'msisdnPorted' => $activated->msisdnPorted,
                'icc' => $activated->icc,
                'msisdnTransitory' => $activated->msisdnTransitory,
                'date' => $activated->date,
                'nip' => $activated->nip,
                'client' => $client->name.' '.$client->lastname,
                'who_did_it' => $who_did_it->name.' '.$who_did_it->lastname,
                'who_attended' => $who_attended = $who_attended == null ? 'N/A' : $who_attended->name.' '.$who_attended->lastname,
                'rate' => $rate->name.' - $'.number_format($rate->price,2)
            ));
        }

        foreach ($completeds as $completed) {
            $who_did_it = User::where('id',$completed->who_did_it)->first();
            $who_attended = User::where('id',$completed->who_attended)->first();
            $client = User::where('id',$completed->client_id)->first();
            $rate = Rate::where('id','=',$completed->rate_id)->first();
            array_push($arrayCompleted,array(
                'msisdnPorted' => $completed->msisdnPorted,
                'icc' => $completed->icc,
                'msisdnTransitory' => $completed->msisdnTransitory,
                'date' => $completed->date,
                'nip' => $completed->nip,
                'client' => $client->name.' '.$client->lastname,
                'who_did_it' => $who_did_it->name.' '.$who_did_it->lastname,
                'who_attended' => $who_attended = $who_attended == null ? 'N/A' : $who_attended->name.' '.$who_attended->lastname,
                'rate' => $rate->name.' - $'.number_format($rate->price,2)
            ));
        }

        $data['pendings'] = $arrayPending;
        $data['activateds'] = $arrayActivated;
        $data['completeds'] = $arrayCompleted;

        return view('portabilities.index',$data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Portability $portability)
    {
        //
    }

    public function edit(Portability $portability)
    {
        //
    }

    public function update(Request $request, Portability $portability)
    {
        //
    }

    public function destroy(Portability $portability)
    {
        //
    }

    public function doActivationPort(Request $request){
        $portability = Portability::where('id',$request['id'])->first();
        $client_id = $portability->client_id;
        $rate_id = $portability->rate_id;
        $rate = Rate::where('id',$rate_id)->first();
        $offer_id = $rate->alta_offer_id;
        $offer = Offer::where('id',$offer_id)->first();
        $offerID = $offer->offerID;
        $product = $offer->product;
        $product = trim($product);
        $msisdn = $portability->msisdnTransitory;
        $msisdn = trim($msisdn);
        $icc = $portability->icc;
        $number = Number::where('icc_id',$icc)->first();
        $number_id = $number->id;
        $who_did_id = $request['user_id'];
        $amount = $rate->price;
        $date_activation = date('Y-m-d');
        $scheduleDate = date('Ymd');

        $insertActivation = [
            "client_id" => $client_id,
            "numbers_id" => $number_id,
            "who_did_id" => $who_did_id,
            "offer_id" => $offer_id,
            "rate_id" => $rate_id,
            "amount" => $amount,
            "received_amount_rate" => $amount,
            "date_activation" => $date_activation,
            "rate_subsequent" => $rate_id,
            "flag_rate" => 1
        ];

        $accessToken = app('App\Http\Controllers\AltanController')->accessTokenRequestPost();
        if($accessToken['status'] == 'approved'){
            $accessToken = $accessToken['accessToken'];

            $consultUF = app('App\Http\Controllers\AltanController')->consultUF($msisdn);
            $responseSubscriber = $consultUF['responseSubscriber'];
            $status = $responseSubscriber['status']['subStatus'];

            if($status == 'Idle'){
                $activationAltan = app('App\Http\Controllers\AltanController')->activationRequestPost($accessToken,$msisdn,$offerID,'','',$product,$scheduleDate);
            }else{
                $activationAltan['msisdn'] = $msisdn;
                $activationAltan['order']['id'] = null;
            }
            
            if(isset($activationAltan['msisdn']) && $activationAltan['msisdn'] == $msisdn){
                $activation = Activation::insert($insertActivation);

                if($activation){
                    $order_id = $activationAltan['order']['id'];
                    Portability::where('id',$request['id'])->update(['status'=>'activado','who_attended'=>$who_did_id,'order_id'=>$order_id]);
                    return response()->json(['http_code' => 200,'message' => 'Activación hecha con éxito.'],200);
                }else{
                    return response()->json(['http_code' => 500,'message' => 'La activación se realizó correctamente, pero no se guardo en nuestra DB, NOTIFIQUE A DESARROLLO.']);
                }
            }else{
                return response()->json(['http_code' => 500,'message' => 'No se realizó la activación, intente de nuevo o consulte a Desarrollo.']);
            }
        }else{
            return response()->json(['http_code' => 500,'message' => 'El token de acceso no fue aprobado, intente de nuevo o consulte a Desarrollo.']);
        }

        return $request;
    }

    public function importAllPorts(Request $request){
        $portabilities = Portability::all()->where('import_to_altan',0);
        $errors = [];
        $messages = [];
        $errorBoolean = 0;
        foreach ($portabilities as $port) {
            $date = $port->date;
            $date = str_replace("-","",$date);
            $altan = new AltanController();
            $portInRequest = $altan->importPortInToAltan($port->msisdnTransitory,$port->msisdnPorted,$port->imsi,$date,$port->dida,$port->rida,$port->dcr,$port->rcr);
            if(isset($portInRequest['msisdnPorted'])){
                array_push($messages,array(json_decode($portInRequest)));
                Portability::where('id',$port->id)->update(['import_to_altan' => 1]);
            }else{
                $errorBoolean = 1;
                $portInRequest = json_decode($portInRequest);
                array_push($errors,array('msisdn'=>array('msisdnPorted'=>$port->msisdnPorted,'response'=>$portInRequest)));
            }
            
        }
        return response()->json(['message'=>'Importación terminada','error'=>$errorBoolean,'errors'=>$errors,'messages'=>$messages]);
    }
}
