<?php

namespace App\Http\Controllers;
use DB;
use App\Number;
use App\Numberstemporarie;
use Illuminate\Http\Request;

class NumberController extends Controller
{
    public function getNumber(Request $request) {
        $msisdn = $request->post('msisdn');
        $x = Number::where('icc_id','=',$msisdn)->first();
        return $x;
    }

    public function addDeleteNumberBulk(Request $request){
        $icc = $request->get('ICC');
        $type = $request->get('type');

        $x = Numberstemporarie::where('ICC',$icc)->exists();

        if($type == 'add'){
            $msisdn = $request->get('MSISDN');
            $producto = $request->get('Producto');
            $coordinates = $request->get('Coordinates');
            $rate_id = $request->get('rate_id');
            $offerID = $request->get('offerID');
            $client_id = $request->get('client_id');

            if(!$x){
                DB::table('numberstemporaries')->insert([
                    'ICC' => $icc,
                    'MSISDN' => $msisdn,
                    'Producto' => $producto,
                    'Coordinates' => $coordinates,
                    'rate_id' => $rate_id,
                    'offerID' => $offerID,
                    'client_id' => $client_id
                ]);
            }
        }else if($type == 'delete'){
            if($x){
                Numberstemporarie::where('ICC',$icc)->delete();
            }
        }
        
        
        $numbers = DB::table('numberstemporaries')
                      ->join('rates','rates.id','=','numberstemporaries.rate_id')
                      ->select('numberstemporaries.ICC','numberstemporaries.MSISDN','numberstemporaries.Producto','numberstemporaries.Coordinates',
                      'rates.name AS Rate')
                      ->get();
        $numberArray = [];

        foreach ($numbers as $number) {
            array_push($numberArray,array(
                'ICC' => $number->ICC,
                'MSISDN' => $number->MSISDN,
                'Producto' => $number->Producto,
                'Coordinates' => $number->Coordinates,
                'Rate' => $number->Rate,
                'Opcion' => '<button class="btn btn-danger btn-xs" data-icc="'.$number->ICC.'" onclick="deleteICC(this)"><i class="fa fa-times"></i></button>'
            ));
        }

        return $numberArray;
    }
}
