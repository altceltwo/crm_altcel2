<?php

namespace App\Http\Controllers;

use App\Pay;
use Openpay;
use App\Offer;
use Exception;
use App\Number;
use App\Reference;
use App\Ethernetpay;
use OpenpayApiError;
use OpenpayApiAuthError;
use OpenpayApiRequestError;
use Illuminate\Http\Request;
use OpenpayApiConnectionError;
use OpenpayApiTransactionError;
use Illuminate\Http\JsonResponse;

require_once '../vendor/autoload.php';

class OpenPayController extends Controller
{
    // Generación de referencia de pago
    public function store(Request $request) {
        $name = $request->input('name');
        $lastname = $request->input('lastname');
        $email = $request->input('email');
        $cel_destiny_reference = $request->input('cel_destiny_reference');
        $amount = $request->input('amount');
        $concepto = $request->input('concepto');
        $type = $request->input('type');
        $channel = $request->input('channel');
        $user = $request->input('user_id');
        $client_id = $request->input('client_id');
        $pay_id = $request->input('pay_id');

        if($type == 1 || $type == 4 || $type == 5)
        {
            $number_id = $request->input('number_id');
            $offerID = $request->input('offer_id');
            $rate = $request->input('rate_id');

            $insert = 'offer_id';
            $insert_content = $offerID;
        }else if($type == 2){
            $pack_id = $request->input('pack_id');
        }
        try {
            // create instance OpenPay sandbox
            $openpay = Openpay::getInstance('mvtmmoafnxul8oizkhju', 'sk_e69bbf5d1e30448688b24670bcef1743');
            // create instance OpenPay production
            // $openpay = Openpay::getInstance('m3one5bybxspoqsygqhz', 'sk_1829d6a2ec22413baffb405b1495b51b');
            
            Openpay::setProductionMode(false);
            // Openpay::setProductionMode(true);
            
            // create object customer
            $customer = array(
                'name' => $name,
                'last_name' => $lastname,
                'email' => $email
            );

            // create object charge
            $chargeRequest =  array(
                'method' => 'store',
                'amount' => $amount,
                'currency' => 'MXN',
                'description' => $concepto,
                'customer' => $customer
            );
            
            $charge = $openpay->charges->create($chargeRequest);
            $responseJson = new \stdClass();
            $reference_id = $charge->id;
            $authorization = $charge->authorization;
            $operation_type = $charge->operation_type;
            $transaction_type = $charge->transaction_type;
            $status = $charge->status;
            $creation_date = $charge->creation_date;
            $operation_date = $charge->operation_date;
            $description = $charge->description;
            $error_message = $charge->error_message;
            $order_id = $charge->order_id;
            $payment_method = $charge->payment_method->type;
            $reference = $charge->payment_method->reference;
            $amount = $charge->amount;
            $currency = $charge->currency;
            $name = $charge->customer->name;
            $lastname = $charge->customer->last_name;
            $email = $charge->customer->email;

            $creation_date = substr($creation_date,0,19);
            $creation_date = str_replace("T", "", $creation_date);
            $creation_date = str_replace("-", "", $creation_date);
            $creation_date = str_replace(":", "", $creation_date);

            $operation_date = substr($operation_date,0,19);
            $operation_date = str_replace("T", "", $operation_date);
            $operation_date = str_replace("-", "", $operation_date);
            $operation_date = str_replace(":", "", $operation_date);
            // $user_id = auth()->user()->id;

            if($type == 1 || $type == 4 || $type == 5){
                $dataReference = [
                    'reference_id' => $reference_id,
                    'reference' => $reference,
                    'authorizacion' => $authorization,
                    'transaction_type' => $transaction_type,
                    'status' => $status,
                    'creation_date' => $creation_date,
                    'description' => $description,
                    'error_message' => $error_message,
                    'order_id' => $order_id,
                    'payment_method' => $payment_method,
                    'amount' => $amount,
                    'currency' => $currency,
                    'name' => $name,
                    'lastname' => $lastname,
                    'email' => $email,
                    'channel_id' => $channel,
                    'referencestype_id' => $type,
                    'number_id' => $number_id,
                    $insert => $insert_content,
                    'rate_id' => $rate,
                    'user_id' => $user = $user == 'null' ? null : $user,
                    'client_id' => $client_id
                ];
                // return $dataReference;
                Reference::insert($dataReference);
                if($type == 1){
                    Pay::where('id',$pay_id)->update(['reference_id' => $reference_id]);
                }else if($type == 4 || $type == 5){

                }
                
            }else if($type == 2){
                $dataReference = [
                    'reference_id' => $reference_id,
                    'reference' => $reference,
                    'authorizacion' => $authorization,
                    'transaction_type' => $transaction_type,
                    'status' => $status,
                    'creation_date' => $creation_date,
                    'description' => $description,
                    'error_message' => $error_message,
                    'order_id' => $order_id,
                    'payment_method' => $payment_method,
                    'amount' => $amount,
                    'currency' => $currency,
                    'name' => $name,
                    'lastname' => $lastname,
                    'email' => $email,
                    'channel_id' => $channel,
                    'referencestype_id' => $type,
                    'pack_id' => $pack_id,
                    'user_id' => $user = $user == 'null' ? null : $user,
                    'client_id' => $client_id
                ];
                Reference::insert($dataReference);
                Ethernetpay::where('id',$pay_id)->update(['reference_id' => $reference_id]);
            }
            return $dataReference;

        } catch (OpenpayApiTransactionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiRequestError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiConnectionError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiAuthError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (OpenpayApiError $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => [
                    'category' => $e->getCategory(),
                    'error_code' => $e->getErrorCode(),
                    'description' => $e->getMessage(),
                    'http_code' => $e->getHttpCode(),
                    'request_id' => $e->getRequestId()
                ]
            ]);
        }
    }

    public function cardPayment(){
        return view('clients.card_payment');
    }

    public function cardPaymentSend(Request $request){
        $name = $request->post('name');
        $lastname = $request->post('lastname');
        $email = $request->post('email');
        $cellphone = $request->post('cellphone');
        $amount = $request->post('amount');
        $concepto = $request->post('concepto');
        
        $openpay = Openpay::getInstance('mvtmmoafnxul8oizkhju', 'sk_e69bbf5d1e30448688b24670bcef1743');
            $customer = array(
                'name' => $name,
                'last_name' => $lastname,
                'phone_number' => $cellphone,
                'email' => $email);

            $chargeRequest = array(
                "method" => "card",
                'amount' => $amount,
                'description' => $concepto,
                'customer' => $customer,
                'send_email' => false,
                'confirm' => false,
                'redirect_url' => 'https://200.106.172.56/home');

            $charge = $openpay->charges->create($chargeRequest);
            return response()->json(['method'=>$charge->method, 'url'=>$charge->payment_method->url, 'type'=>$charge->payment_method->type]);
    }
}
