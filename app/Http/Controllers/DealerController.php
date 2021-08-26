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
        $data['dealers'] = DB::table('dealers')
                              ->join('users','users.id','=','dealers.user_id')
                              ->select('dealers.*','users.name AS user_name')
                              ->get();
        return view('dealers.index',$data);
    }

    public function create(){
        return view('dealers.create');
    }

    public function store(Request $request){
        $time = time();
        $h = date("g", $time);
        $pass =  'dealer'.date("Ymd").$h.date("is", $time);
        $pass1 = Hash::make($pass);
        User::insert([
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'password' => $pass1,
            'role_id' => 5,
            'remember_token' => $pass
        ]);

        $user_id = User::where('email',$request->post('email'))->first();
        $user_id = $user_id->id;

        Dealer::insert([
            'name' => $request->post('asociation'),
            'address' => $request->post('address'),
            'phone' => $request->post('phone'),
            'rfc' => $request->post('rfc'),
            'user_id' => $user_id,
            'who_did_id' => $request->post('who_did_id')
        ]);
        // Falta envío de correos
        return redirect('/dealers');
    }

    public function show($dealer){
        $data['dealer'] = Dealer::where('id',$dealer)->first();
        $data['packs'] = Pack::all()->where('service_name','Conecta');
        $data['myPacks'] = DB::table('dealers')
                              ->join('packsdealers','packsdealers.dealer_id','=','dealers.id')
                              ->join('packs','packs.id','=','packsdealers.pack_id')
                              ->where('dealers.id',$dealer)
                              ->select('packs.name AS pack_name','packs.price AS pack_price','packsdealers.comission AS pack_comission','packsdealers.myMoney AS pack_total')
                              ->get();
        return view('dealers.show',$data);
    }

    public function addPackDealer(Request $request){
        $route = '/dealers'.'/'.$request->post('dealer_id');
        $request = request()->except('_token');
        Packsdealer::insert($request);
        return redirect($route);
    }
}
