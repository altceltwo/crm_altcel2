<?php

namespace App\Http\Controllers;
use App\Pack;
use App\Radiobase;
use App\Rate;
use App\Politic;
use App\Pay;
use App\Ethernetpay;
use App\User;
use App\Number;
use App\Device;
use App\Assignment;
use DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index() {
        $data['packs'] = Pack::all();
        $data['radiobases'] = Radiobase::all();
        return view('ethernet.administration',$data);
    }

    public function createPack(Request $request) {
        Pack::insert([
            'name' => $request->post('name'),
            'description' => $request->post('description'),
            'service_name' => $request->post('service_name'),
            'recurrency' => $request->post('recurrency'),
            'price' => $request->post('price'),
            'price_s_iva' => $request->post('price_s_iva'),
            'price_install' => $request->post('price_install')
        ]);
    }

    public function createRadiobase(Request $request) {
        Radiobase::insert([
            'name' => $request->post('name'),
            'address' => $request->post('address'),
            'ip_address' => $request->post('ip_address'),
            'lat' => $request->post('lat'),
            'lng' => $request->post('lng')
        ]);
    }

    public function createPoliticRate() {
        $data['politics'] = Politic::all();
        return view('rates.politics',$data);
    }

    public function insertPoliticRate(Request $request) {
        $request = request()->except('_token');
        $x = Politic::insert($request);
        if($x){
            return 1;
        }else{
            return 0;
        }
    }

    public function destroy($politic_id){
        Politic::where('id', $politic_id)->delete();
        return back();
    }

    public function getPolitic($politic_id){
        $response = Politic::find($politic_id);;
        return $response;
    }

    public function updatePolitic(Request $request,Politic $politic){
        $id = $politic->id;
        $request = request()->except('_method','_token');
        $x = Politic::where('id',$id)->update($request);

        if($x){
            $message = 'Cambios guardados.';
            return back()->with('message',$message);
        }else{
            $message = 'Parece que ha ocurrido un error, intente de nuevo.';
            return back()->with('error',$message);
        }
    }

    public function changeStatusPacksRates(Request $request){
        $status = $request['status'];
        $id = $request['id'];
        $type = $request['type'];

        if($type == 'ethernet'){
            if($status == 'activo'){
                Pack::where('id',$id)->update(['status' => 'inactivo']);
            }else if($status == 'inactivo'){
                Pack::where('id',$id)->update(['status' => 'activo']);
            }
        }else if($type == 'altan'){
            if($status == 'activo'){
                Rate::where('id',$id)->update(['status' => 'inactivo']);
            }else if($status == 'inactivo'){
                Rate::where('id',$id)->update(['status' => 'activo']);
            }
        }
    }

    public function getPackEthernet(Request $request){
        $response = Pack::find($request->get('id'));
        return $response;
    }
    
    public function updatePackEthernet(Pack $pack_id, Request $request){
        $id = $pack_id->id;
        $price_install = $request->post('price_install');
        $request = request()->except('_token','id');
        if($price_install == null){
            $price_install = 0;
            $request['price_install'] = $price_install;
        }
        Pack::where('id',$id)->update($request);
        return back();
    }

    public function checkOverduePayments(Request $request){
        $response = Pay::all();
        return $response;
    }

    
}
