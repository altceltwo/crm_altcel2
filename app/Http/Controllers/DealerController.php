<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Dealer;
use App\Pack;
use App\Packsdealer;
use DB;

class DealerController extends Controller
{
    public function index(){
        $data['users'] = DB::table('users')
                         ->select('id','name','lastname','email')
                         ->orderBy('name', 'asc')
                         ->get();

        $dealers = DB::table('dealers')
                            ->join('users','users.id','=','dealers.user_id')
                            ->select('users.name','users.lastname','users.email','dealers.balance', 'dealers.who_created', 'dealers.id AS dealer_id')
                            ->get();
                        
        $data['dealers'] = array();
        foreach ($dealers as $dealer) {
            $user = $dealer->who_created;
            $userData = User::where('id',$user)->first();

            array_push($data['dealers'],array(
                'dealer' => $dealer->name.' '.$dealer->lastname,
                'email' => $dealer->email,
                'balance' => $dealer->balance,
                'who_created' => $userData->name.' '.$userData->lastname,
                'dealer_id' => $dealer->dealer_id
            ));
        }
// return $data['dealers'];
        return view('dealers.index',$data);
    }

    public function create(){
        return view('dealers.create');
    }

    public function store(Request $request){
        $userDealer = $request->post('user_id');
        $newDealerData = $request->post('newDealerData');

        $request = $request->except('_token','newDealerData');

        $exists = Dealer::where('user_id',$userDealer)->exists();

        if($exists){
            $status = 'warning';
            $message = 'El distribuidor <strong>'.$newDealerData.'</strong> que desea añadir ya existe.';
        }else{
            $x = Dealer::insert($request);

            if($x){
                $status = 'success';
                $message = 'Distribuidor añadido con éxito.';
            }else{
                $status = 'error';
                $message = 'Ocurrió un error, vuelve a intentarlo o consulta a Desarrollo.';
            }
        }

        return back()->with($status,$message);
    }

    public function update($dealer, Request $request){
        $request = $request->except('_token','_method');
        
        $x = Dealer::where('id',$dealer)->update($request);
        
        if($x){
            return response()->json(['http_code' => 1, 'message' => 'Actualizado con éxito.']);
        }else{
            return response()->json(['http_code' => 0, 'message' => 'Hubo un error, intente de nuevo o consulte a Desarrollo.']);
        }
    }

    public function show(Dealer $dealer){
        $data['dealer'] = $dealer;
        $data['dataDealer'] = User::where('id',$dealer->user_id)->first();
        $balance = $dealer->balance;

        if($balance == 0){
            $data['icon'] = "fa fa-frown-o";
        }else if($balance > 0 && $balance <= 30){
            $data['icon'] = "fa fa-meh-o";
        }else if($balance > 30){
            $data['icon'] = "fa fa-smile-o";
        }
        // return $data;
        return view('dealers.show',$data);
        
    }
}
