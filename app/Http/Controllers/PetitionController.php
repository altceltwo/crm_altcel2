<?php

namespace App\Http\Controllers;

use App\Petition;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\User;

class PetitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        $x = DB::table('petitions')
                              ->join('users', 'users.id', '=', 'petitions.sender')
                              ->where('petitions.status', '=', 'recibido')
                              ->orwhere('petitions.status', '=', 'activado')
                              ->select('petitions.*', 'users.name AS name_sender', 'users.lastname AS lastname_sender')
                              ->get();
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
            $cobroCpe = $y->collected_rate;
            $cobroPlan = $y->collected_device;
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
                
            }else {
                $recibido = 'Por Recibir';
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
        // return $data;
        
        return view('petitions/completadosOperaciones', $data);
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
