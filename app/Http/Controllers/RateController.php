<?php

namespace App\Http\Controllers;
use App\Offer;
use App\Rate;
use App\Politic;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RatesExport;

class RateController extends Controller
{
    public function index(){
        $data['rates'] = DB::table('rates')
                            ->join('offers','rates.alta_offer_id','=','offers.id')
                            ->select('rates.*','offers.product AS product_offer','offers.name AS name_offer')
                            ->get();
        return view('rates.index',$data);
    }

    public function create(){
        $data['offers'] = Offer::all()->where('type','normal');
        return view('rates.create',$data);
    }

    public function store(Request $request) {
        // return $request;
        $time = time();
        $h = date("g", $time);
        $request = request()->except('_token');

        if(request()->hasFile('image')){
            $image = request()->file('image');
            $image->move('storage/uploads', date("Ymd")."_".$h.date("is", $time)."_".$image->getClientOriginalName());
            $request['image'] = date("Ymd")."_".$h.date("is", $time)."_".$image->getClientOriginalName();
            // return $image->getClientOriginalName();
        }
        $x = Rate::insert($request);
        if($x == 1){
            $message = "Guardado con éxito.";
            return back()->with('message',$message);
        }else{
            $message = "Parece que algo no salió bien. :(";
            return back()->with('error',$message);
        }
    }

    public function getRates(Request $request) {
        $product = $request->post('product');
        $client_id = $request->post('client_id');
        $msisdn_id = $request->post('msisdn_id');
        $rates = DB::table('activations')
                    ->join('rates','rates.id','=','activations.rate_id')
                    ->where('activations.numbers_id',$msisdn_id)
                    ->where('activations.client_id',$client_id)
                    ->select('rates.id','rates.name AS rate_name','rates.alta_offer_id','rates.multi_offer_id','rates.price')
                    ->get();
        return $rates;
    }

    public function getRatesAlta(Request $request) {
        $product = $request->post('product');
        $rates = DB::table('rates')
                    ->join('offers','offers.id','=','rates.alta_offer_id')
                    ->where('offers.product','=',$product)
                    ->where('rates.status','activo')
                    ->where('offers.type','normal')
                    ->select('rates.*','offers.name AS offer_name','offers.id AS offer_id','offers.product AS offer_product', 'offers.offerID')
                    ->get();
        return $rates;
    }

    public function getRatesAltaApi(Request $request) {
        $product = $request->post('product');
        $rates = DB::table('rates')
                    ->join('offers','offers.id','=','rates.alta_offer_id')
                    ->where('offers.product','=',$product)
                    ->where('rates.status','activo')
                    ->where('rates.type','publico')
                    ->where('offers.type','normal')
                    ->select('rates.*','offers.name AS offer_name','offers.id AS offer_id','offers.product AS offer_product', 'offers.offerID')
                    ->get();
        return $rates;
    }

    public function getPoliticsRates(Request $request) {
        // $request = request()->except('_token');
        $response = Politic::all();
        return $response;
    }

    public function edit(Rate $rate){
        $id = $rate->id;
        $response = DB::table('rates')
                       ->join('offers','offers.id','=','rates.alta_offer_id')
                       ->where('rates.id',$id)
                       ->select('rates.*','offers.price_c_iva AS price_c_iva_offer','offers.price_sale AS price_sale_offer')
                       ->get();
        return $response;
    }

    public function update(Request $request, Rate $rate){
        $id = $rate->id;
        
        Rate::where('id',$id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'alta_offer_id' => $request->offer_primary,
            'type' => $request->type,
            'price' => $request->price,
            'price_subsequent' => $request->price_subsequent,
            'recurrency' => $request->recurrency
        ]);
        
        return back();
    }

    public function exportRatesActives(){
        return Excel::download(new RatesExport, 'tarifas-activas.xlsx');
    }

    public function getOffersRatesDiff(Request $request){
        $msisdn = $request->get('msisdn');
        $dataActivation = DB::table('numbers')
                             ->join('activations','activations.numbers_id','=','numbers.id')
                             ->join('rates','rates.id','=','activations.rate_id')
                             ->join('offers','offers.id','=','activations.offer_id')
                             ->where('MSISDN',$msisdn)
                             ->select('numbers.*','activations.offer_id','activations.rate_id','offers.name AS offer_name','rates.name AS rate_name','activations.lat_hbb AS lat','activations.lng_hbb AS lng')
                             ->get();
        
        $offer_id = $dataActivation[0]->offer_id;
        $offer_name = $dataActivation[0]->offer_name;
        $rate_id = $dataActivation[0]->rate_id;
        $rate_name = $dataActivation[0]->rate_name;
        $lat = $dataActivation[0]->lat;
        $lng = $dataActivation[0]->lng;
        $producto = $dataActivation[0]->producto;
        $producto = trim($producto);

        $response['dataMSISDN'] = array(
            'offer_name' => $offer_name,
            'offer_id' => $offer_id,
            'rate_name' => $rate_name,
            'rate_id' => $rate_id,
            'producto' => $producto,
            'lat' => $lat,
            'lng' => $lng
        );

        $response['offersAndRates'] = DB::table('offers')
                             ->join('rates','rates.alta_offer_id','=','offers.id')
                             ->where('rates.id','!=',$rate_id)
                             ->where('offers.product',$producto)
                             ->select('offers.id AS offer_id','offers.offerID AS offerID','offers.name AS offer_name','rates.id AS rate_id','rates.name AS rate_name','rates.price_subsequent AS rate_price')
                             ->get();

        return response()->json($response);
    }

    public function getOffersRatesDiffAPI(Request $request){
        $msisdn = $request->get('msisdn');
        $dataActivation = DB::table('numbers')
                             ->join('activations','activations.numbers_id','=','numbers.id')
                             ->join('rates','rates.id','=','activations.rate_id')
                             ->join('offers','offers.id','=','activations.offer_id')
                             ->where('MSISDN',$msisdn)
                             ->select('numbers.*','activations.offer_id','activations.rate_id','offers.name AS offer_name','rates.name AS rate_name','activations.lat_hbb AS lat','activations.lng_hbb AS lng')
                             ->get();
        
        $offer_id = $dataActivation[0]->offer_id;
        
        $offer_name = $dataActivation[0]->offer_name;
        $rate_id = $dataActivation[0]->rate_id;
        $rate_name = $dataActivation[0]->rate_name;
        $lat = $dataActivation[0]->lat;
        $lng = $dataActivation[0]->lng;
        $producto = $dataActivation[0]->producto;
        $producto = trim($producto);

        $response['dataMSISDN'] = array(
            'offer_name' => $offer_name,
            'offer_id' => $offer_id,
            'rate_name' => $rate_name,
            'rate_id' => $rate_id,
            'producto' => $producto,
            'lat' => $lat,
            'lng' => $lng
        );

        $response['offersAndRates'] = DB::table('offers')
                             ->join('rates','rates.alta_offer_id','=','offers.id')
                             ->where('offers.id','!=',$offer_id)
                             ->where('offers.product',$producto)
                             ->select('offers.id AS offer_id','offers.offerID AS offerID','offers.name AS offer_name','rates.id AS rate_id','rates.name AS rate_name','rates.price_subsequent AS rate_price')
                             ->get();

        return response()->json($response);
    }

    public function getOffersRatesDiffAPIPublic(Request $request){
        $msisdn = $request->get('msisdn');
        $dataActivation = DB::table('numbers')
                             ->join('activations','activations.numbers_id','=','numbers.id')
                             ->join('rates','rates.id','=','activations.rate_id')
                             ->join('offers','offers.id','=','activations.offer_id')
                             ->join('users','users.id','=','activations.client_id')
                             ->join('clients','user_id','=','users.id')
                             ->where('MSISDN',$msisdn)
                             ->select('numbers.*','activations.offer_id','activations.rate_id','offers.name AS offer_name','rates.name AS rate_name','activations.lat_hbb AS lat','activations.lng_hbb AS lng',
                             'users.name AS name_user','users.lastname AS lastname_user','users.email AS email_user',
                             'clients.cellphone AS cellphone_user','users.id AS id_user','numbers.MSISDN','numbers.id AS number_id')
                             ->get();
        
        $offer_id = $dataActivation[0]->offer_id;
        $offer_name = $dataActivation[0]->offer_name;
        $rate_id = $dataActivation[0]->rate_id;
        $rate_name = $dataActivation[0]->rate_name;
        $lat = $dataActivation[0]->lat;
        $lng = $dataActivation[0]->lng;
        $producto = $dataActivation[0]->producto;
        $name_user = $dataActivation[0]->name_user;
        $lastname_user = $dataActivation[0]->lastname_user;
        $email_user = $dataActivation[0]->email_user;
        $cellphone_user = $dataActivation[0]->cellphone_user;
        $id_user = $dataActivation[0]->id_user;
        $number_id = $dataActivation[0]->number_id;
        $producto = trim($producto);

        $response['dataMSISDN'] = array(
            'offer_name' => $offer_name,
            'offer_id' => $offer_id,
            'rate_name' => $rate_name,
            'rate_id' => $rate_id,
            'producto' => $producto,
            'lat' => $lat,
            'lng' => $lng,
            'name_user' => $name_user,
            'lastname_user' => $lastname_user,
            'email_user' => $email_user,
            'cellphone_user' => $cellphone_user,
            'id_user' => $id_user,
            'number_id' => $number_id
        );

        $response['offersAndRates'] = DB::table('offers')
                             ->join('rates','rates.alta_offer_id','=','offers.id')
                             ->where('offers.id','!=',$offer_id)
                             ->where('offers.product',$producto)
                             ->where('rates.type','publico')
                             ->where('rates.status','activo')
                             ->where('rates.price_subsequent','!=','0')
                             ->select('offers.id AS offer_id','offers.offerID AS offerID','offers.name AS offer_name','rates.id AS rate_id','rates.name AS rate_name','rates.price_subsequent AS rate_price')
                             ->get();

        return response()->json($response);
    }

    public function getOffersRatesSurplus(Request $request){
        $msisdn = $request->get('msisdn');
        $dataActivation = DB::table('numbers')
                             ->join('activations','activations.numbers_id','=','numbers.id')
                             ->join('rates','rates.id','=','activations.rate_id')
                             ->join('offers','offers.id','=','activations.offer_id')
                             ->join('users','users.id','=','activations.client_id')
                             ->join('clients','user_id','=','users.id')
                             ->where('numbers.MSISDN',$msisdn)
                             ->select('numbers.MSISDN','numbers.id AS number_id','activations.offer_id','activations.rate_id',
                             'offers.name AS offer_name','rates.name AS rate_name',
                             'activations.lat_hbb AS lat','activations.lng_hbb AS lng','offers.offerID AS offerID',
                             'users.name AS name_user','users.lastname AS lastname_user','users.email AS email_user',
                             'clients.cellphone AS cellphone_user','users.id AS id_user')
                             ->get();

        $response['dataMSISDN'] = $dataActivation[0];
        $offerID = $dataActivation[0]->offerID;

        $response['packsSurplus'] = DB::table('offers')
                                       ->where('type','excedente')
                                       ->where('offerID_excedente',$offerID)
                                       ->select('offers.*')
                                       ->get();

        return $response;
    }
}
