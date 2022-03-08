<?php

namespace App\Http\Controllers;

use App\User;
use App\Device;
use App\Number;
use App\Company;
use App\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{
    //

    public function index(){
        $data['companies'] = Company::all();
        return view('companies.index',$data);
    }

    public function chargeInventory(Request $request){
        if(request()->hasFile('file')){
            $user_id = auth()->user()->id;
            $type = $request->post('type');
            $company = $request->post('company');
            $csv = request()->file('file');
            // Apertura de archivo cargado con ICC's
            $fp = fopen ($csv,'r');

            $response = [];
            $response['records'] = [];
            $response['badRecords'] = 0;
            $response['goodRecords'] = 0;
            $numberLine = 0;
            $booleanExists = false;
            $description = '';
            $number_id = null;
            $device_id = null;
            
            while ($data = fgetcsv ($fp, 1000, ",")) {
                $numberLine++;
                $length = 'bad';
                $now = date('Y-m-d h:i:s');
                if(sizeof($data) == 1){
                    $length = 'good';
                    if($type == 'sim'){
                        $booleanExists = Number::where('icc_id',$data[0])->exists();
                        $description = $booleanExists ? '0' : 'La SIM no existe dentro de nuestros registros.';
                        $booleanExists ? $response['badRecords']+=0 : $response['badRecords']+=1;
                        if($booleanExists){
                            $productData = Number::where('icc_id',$data[0])->first();
                            $number_id = $productData->id;
                            $device_id = null;
                            $response['goodRecords']+=1;
                            $productExists = Inventory::where('number_id',$number_id)->exists();

                            if(!$productExists){
                                Inventory::insert([
                                    'company_id' => $company,
                                    'number_id' => $number_id,
                                    'device_id' => $device_id,
                                    'who_did_id' => $user_id,
                                    'date_created' => $now
                                ]);
                            }
                        }
                    }else if($type == 'device'){
                        $booleanExists = Device::where('no_serie_imei','LIKE','%'.$data[0].'%')->exists();
                        $description = $booleanExists ? '0' : 'El Dispositivo no existe dentro de nuestros registros.';
                        $booleanExists ? $response['badRecords']+=0 : $response['badRecords']+=1;
                        if($booleanExists){
                            $productData = Device::where('no_serie_imei','LIKE','%'.$data[0].'%')->first();
                            $number_id = null;
                            $device_id = $productData->id;
                            $response['goodRecords']+=1;
                            $productExists = Inventory::where('device_id',$device_id)->exists();

                            if(!$productExists){
                                Inventory::insert([
                                    'company_id' => $company,
                                    'number_id' => $number_id,
                                    'device_id' => $device_id,
                                    'who_did_id' => $user_id,
                                    'date_created' => $now
                                ]);
                            }
                        }
                    }

                }else{
                    $length = 'bad';
                    $description = 'Tamaño de filas inválido.';
                    $response['badRecords']+=1;
                }

                array_push($response['records'],array(
                    'length' => $length,
                    'number_line' => $numberLine,
                    'booleanExists' => $booleanExists,
                    'description' => $description,
                    'identifier' => $data[0]
                ));
            }

            return $response;
        }
    }

    public function store(Request $request){
        $request = request()->except('_token');
        Company::insert($request);
        return back();
    }

    public function storeDealer(Request $request){
        $password = $request->post('password');
        $request['password'] = Hash::make($password);
        $request['role_id'] = 8;
        $request = request()->except('_token');
        User::insert($request);
        return back();
    }
}
