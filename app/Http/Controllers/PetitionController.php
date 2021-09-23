<?php

namespace App\Http\Controllers;

use App\Petition;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\User;
use App\Activation;

class PetitionController extends Controller
{
    public function index()
    {
        $x = DB::table('petitions')
               ->join('users', 'users.id', '=', 'petitions.sender')
               ->where('petitions.status', '=', 'solicitud')
               ->select('petitions.*', 'users.name AS name_sender', 'users.lastname AS lastname_sender')
               ->get();
        // return $x;
        $data['solicitudes'] = [];
        foreach($x as $x){
            $id = $x->id;
            $id_sender = $x->sender;
            $name_sender = $x->name_sender.' '.$x->lastname_sender;
            $status = $x->status;
            $date_sent = $x->date_sent;
            $comment = $x->comment;
            $product = $x->product;
            $client_id = $x->client_id;

            $client = User::where('id', $client_id)->select('name', 'lastname')->get();

            array_push($data['solicitudes'], array(
                'id'=>$id,
                'id_client'=>$client_id,
                'id_sent'=>$id_sender,
                'name_sender'=>$name_sender,
                'status'=>$status,
                'date_sent'=>$date_sent,
                'comment'=>$comment,
                'product'=>$product,
                'client'=>$client[0]->name.' '.$client[0]->lastname,
            ));

        }
        // return $data;
        return view('petitions/solicitudOperaciones', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Petition  $petition
     * @return \Illuminate\Http\Response
     */
    public function show(Petition $petition)
    {
        $currentRol = auth()->user()->role_id;
        if ($currentRol == 1) {
            $x = DB::table('petitions')
                   ->join('users', 'users.id', '=', 'petitions.sender')
                   ->where('petitions.status', '=', 'recibido')
                   ->orwhere('petitions.status', '=', 'activado')
                   ->select('petitions.*', 'users.name AS name_sender', 'users.lastname AS lastname_sender')
                   ->get();
        }elseif ($currentRol == 5) {
            $x = DB::table('petitions')
                   ->join('users', 'users.id', '=', 'petitions.sender')
                   ->where('petitions.status', '=', 'activado')
                   ->select('petitions.*', 'users.name AS name_sender', 'users.lastname AS lastname_sender')
                   ->get();
        }
        // return $x;
        $data['completadas'] = [];
        foreach($x as $y){
            $id = $y->id;
            $id_sender = $y->sender;
            $name_sender = $y->name_sender.' '.$y->lastname_sender;
            $status = $y->status;
            $comment = $y->comment;
            $product = $y->product;
            $client_id = $y->client_id;
            $cobroCpe = $y->collected_device;
            $cobroPlan = $y->collected_rate;
            $date_sent = $y->date_sent;
            $who_attended = $y->who_attended;
            $date_activated = $y->date_activated;
            $recibido = $y->who_received;
            $fechaRecibido = $y->date_received;

            $client = User::where('id', $client_id)->select('name', 'lastname')->get();
            $attended = User::where('id', $who_attended)->select('name', 'lastname')->get();

            if($y->who_received != null){
                $recibido1 = User::where('id', $recibido)->select('name', 'lastname')->get();
                $recibido = $recibido1[0]->name.' '.$recibido1[0]->lastname;
            }else{
                $recibido = 'Por recibir';
            }

            if ($status == 'recibido') {
                
                $badgeStatus = 'success';
                
            }else {
                $badgeStatus = 'warning';
            }

            if ($fechaRecibido != null) {
                $badgeFecha = 'success';
                
            }else{
                $fechaRecibido = 'Por Recibir';
                $badgeFecha = 'warning';
            }

            array_push($data['completadas'], array(
                'id'=>$id,
                'id_sendt'=>$id_sender,
                'id_client'=>$client_id,
                'client'=>$client[0]->name.' '.$client[0]->lastname,
                'product'=>$product,
                'status'=>$status,
                'cobroCpe'=> $cobroCpe,
                'cobroPlan'=>$cobroPlan,
                'fecha_solicitud'=>$date_sent,
                'activadoPor'=>$attended[0]->name.' '.$attended[0]->lastname,
                'date_activated'=>$date_activated,
                'recibido'=>$recibido,
                'dateRecibido'=>$fechaRecibido,
                'comment'=>$comment,
                'badgeFecha'=>$badgeFecha,
                'badgeStatus'=>$badgeStatus
            ));
        }
        // return $data['completadas'];
        
        return view('petitions/completadosOperaciones', $data);
    }

    public function recibidosFinance(Request $request){
        
        if(isset($request['start']) && isset($request['end'])){
            if($request['start'] != null && $request['end'] != null){
                $year =  substr($request['start'],6,4);
                $month = substr($request['start'],0,2);
                $day = substr($request['start'],3,2);
                $date_init = $year.'-'.$month.'-'.$day.' 00:00:00';

                $year =  substr($request['end'],6,4);
                $month = substr($request['end'],0,2);
                $day = substr($request['end'],3,2);
                $date_final = $year.'-'.$month.'-'.$day.' 23:59:59';

                // return $date_init.' y '.$date_final;
                $x = DB::table('petitions')
                        ->join('users', 'users.id', '=', 'petitions.sender')
                        ->where('petitions.status', '=', 'recibido')
                        ->whereBetween('petitions.date_received', [$date_init, $date_final])
                        ->select('petitions.*', 'users.name AS name_sender', 'users.lastname AS lastname_sender')
                        ->get();

                $data['fecha'] = 'Mostrando registros en el rango de '.substr($date_init,0,-8).' a '.substr($date_final,0,-8);
                // $data['fechaFinal'] = $;
            }
        }else{
            $x = DB::table('petitions')
                   ->join('users', 'users.id', '=', 'petitions.sender')
                   ->where('petitions.status', '=', 'recibido')
                   ->select('petitions.*', 'users.name AS name_sender', 'users.lastname AS lastname_sender')
                   ->get();

            $data['fecha'] = 'Mostrando todos los registros';
        }
        $data['totalcpe'] = 0;
        $data['totalplan'] = 0;
        $data['completadas'] = [];
        foreach($x as $y){
            $id = $y->id;
            $id_sender = $y->sender;
            $name_sender = $y->name_sender.' '.$y->lastname_sender;
            $status = $y->status;
            $comment = $y->comment;
            $product = $y->product;
            $client_id = $y->client_id;
            $cobroCpe = $y->collected_device;
            $cobroPlan = $y->collected_rate;
            $date_sent = $y->date_sent;
            $who_attended = $y->who_attended;
            $date_activated = $y->date_activated;
            $recibido = $y->who_received;
            $fechaRecibido = $y->date_received;

            $client = User::where('id', $client_id)->select('name', 'lastname')->get();
            $attended = User::where('id', $who_attended)->select('name', 'lastname')->get();

            if ($status == 'recibido') {
                $recibido1 = User::where('id', $recibido)->select('name', 'lastname')->get();
                $recibido = $recibido1[0]->name.' '.$recibido1[0]->lastname;
                $badgeStatus = 'success';
            }

            if ($fechaRecibido != null) {
                $badgeFecha = 'success';
            }

            array_push($data['completadas'], array(
                'id'=>$id,
                'id_sendt'=>$id_sender,
                'id_client'=>$client_id,
                'client'=>$client[0]->name.' '.$client[0]->lastname,
                'product'=>$product,
                'status'=>$status,
                'cobroCpe'=> $cobroCpe,
                'cobroPlan'=>$cobroPlan,
                'fecha_solicitud'=>$date_sent,
                'activadoPor'=>$attended[0]->name.' '.$attended[0]->lastname,
                'date_activated'=>$date_activated,
                'recibido'=>$recibido,
                'dateRecibido'=>$fechaRecibido,
                'comment'=>$comment,
                'badgeFecha'=>$badgeFecha,
                'badgeStatus'=>$badgeStatus
            ));
            $data['totalcpe']+= $y->collected_device;
            $data['totalplan'] = $y->collected_rate;
        }
        // return $data;
        return view('petitions/completadosFinanzas', $data);
    }

    public function activationOperaciones(Request $request){
        $id_client = $request['idClient'];

        $data = DB::table('clients')
                  ->join('users','users.id','=','clients.user_id')
                  ->where('clients.user_id', $id_client)
                  ->select('users.name','users.lastname','users.email','clients.address','clients.rfc','clients.ine_code', 'clients.date_born','clients.cellphone')
                  ->get();
        return $data;
    }

    public function collectMoney(Request $request){
        $collectedCPE = $request->post('collectedCPE');
        $collectedRate = $request->post('collectedRate');
        $petition_id = $request->post('petition_id');

        $data = DB::table('petitions')
                   ->join('activations','activations.petition_id','=','petitions.id')
                   ->where('petitions.id',$petition_id)
                   ->select('activations.amount_rate AS amount_rate','activations.amount_device AS amount_device', 'activations.id AS activation_id')
                   ->get();

        $amount_rate = $data[0]->amount_rate;
        $amount_device = $data[0]->amount_device;
        $activation_id = $data[0]->activation_id;

        $residuaryRate = $amount_rate-$collectedRate;
        $residuaryDevice = $amount_device-$collectedCPE;

        return response()->json([
            'residuaryRate' => $residuaryRate,
            'residuaryDevice' => $residuaryDevice,
            'petition_id' => $petition_id,
            'activation_id' => $activation_id,
            'amount_rate' => $amount_rate,
            'amount_device' => $amount_device,
        ]);
    }

    public function saveCollected(Request $request){
        $collected_amount_cpe = $request->post('received_amount_cpe');
        $collected_amount_rate = $request->post('received_amount_rate');
        $who_received = $request->post('who_received');
        $activation_id = $request->post('activation_id');
        $petition_id = $request->post('petition_id');
        

        $dataActivation = Activation::where('id',$activation_id)->first();
        $dataPetition = Petition::where('id',$petition_id)->first();

        $amount_rate = $dataActivation->amount_rate;
        $amount_device = $dataActivation->amount_device;
        $received_amount_rate = $dataActivation->received_amount_rate;
        $received_amount_device = $dataActivation->received_amount_device;

        $received_amount_rate = $received_amount_rate+$collected_amount_rate;
        $received_amount_device = $received_amount_device+$collected_amount_cpe;

        $statusPaymentActivation = 'pendiente';
        $statusPetition = 'activado';
        $date_received = date('Y-m-d H:i:s');

        if(($received_amount_rate >= $amount_rate) && ($received_amount_device >= $amount_device)){
            $statusPaymentActivation = 'completado';
            $statusPetition = 'recibido';
        }

        $x = Activation::where('id',$activation_id)->update([
                'received_amount_rate' => $received_amount_rate,
                'received_amount_device' => $received_amount_device,
                'payment_status' => $statusPaymentActivation
            ]);

        $y = Petition::where('id',$petition_id)->update([
                'collected_rate' => $received_amount_rate,
                'collected_device' => $received_amount_device,
                'who_received' => $who_received,
                'status' => $statusPetition,
                'date_received' => $date_received
            ]);

        if($x && $y){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Petition  $petition
     * @return \Illuminate\Http\Response
     */
    public function edit(Petition $petition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Petition  $petition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Petition $petition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Petition  $petition
     * @return \Illuminate\Http\Response
     */
    public function destroy(Petition $petition)
    {
        //
    }
}
