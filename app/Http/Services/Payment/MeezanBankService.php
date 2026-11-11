<?php

namespace App\Http\Services\Payment;

use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use App\Models\Order;

class MeezanBankService extends BasePaymentService
{
    public  $meezanBankClient;


    public  $gateway ;
    private $successUrl ;
    private $cancelUrl ;
    private $provider ;
    public $currency ;
    public  $meezan_port_address;
    public  $meezan_user_name;
    public  $meezan_user_password;
    public  $meezan_pkr_currency_code;
    public  $meezan_usd_currency_code;

    public function __construct($object)
    {
        if(isset($object['id'])){

            $this->cancelUrl = isset($object['cancelUrl ']) ? $object['cancelUrl '] : route('paymentCancel', $object['id']);
            $this->successUrl = isset($object['successUrl']) ? $object['successUrl'] : route('paymentNotify', $object['id']);
        }

        $this->meezan_port_address = get_option('meezan_port_address');
        $this->meezan_user_name = get_option('meezan_user_name');
        $this->meezan_user_password = get_option('meezan_user_password');
        $this->meezan_pkr_currency_code = 586;
        $this->meezan_usd_currency_code = 840;
    }

    public function makePayment($amount,$post_data = null,$order_id = null , $cart_id = null, $location = null, $trial_id = null, $live_enrollment = 0)
    {

      
    try {
            $data['success'] = false;
            $data['redirect_url'] = '';
            $data['payment_id'] = '';
            $data['message'] = '';
            $amount         =(int)$amount; 
            $amountUpdated         =$amount * 100; 
            $orderIDupdated = rand(1,99999).$order_id;

       
            if($location == 1){
                if($cart_id){
                    $returnURLUpdated = route('student.successfulMeezanWithCheckOut', ['order_id' => $order_id, 'cart_id' => $cart_id]);
                }
                else{
                    $returnURLUpdated = route('student.successfulMeezanWithCheckOut', ['order_id' => $order_id]);
                }
            }
            else{
                if($cart_id){
                    $returnURLUpdated = route('student.successfulMeezan', ['order_id' => $order_id, 'cart_id' => $cart_id]);
                }
                else{
                    $returnURLUpdated = route('student.successfulMeezan', ['order_id' => $order_id]);
                }
            }
        
          
            $tokenApiUrl =  $this->meezan_port_address.'register.do';
            $urlPostParams = array( 
                'userName'=>$this->meezan_user_name, 
                'password'=>$this->meezan_user_password, 
                'orderNumber'=>$orderIDupdated, 
                'amount'=>$amountUpdated, 
                'currency'=>$this->meezan_pkr_currency_code, 
                'returnUrl'=>$returnURLUpdated, 
            ); 
            $query=urldecode(http_build_query($urlPostParams));
            $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $tokenApiUrl); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'CURL/PHP Meezan API Example'); 
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch); 
        $arrayResult = json_decode($response, true);
        curl_close($ch); 
    
        $generatedErrorCode = $arrayResult['errorCode'];
        if($generatedErrorCode != 0){
            $generatedErrorMessage = $arrayResult['errorMessage'];
            return $data['errorMessage'] = "<strong>". $generatedErrorMessage."</strong><br/>";
            
        }
        else{
            $generatedOrderID = $arrayResult['orderId'];
            $generatedFormUrl = $arrayResult['formUrl'];
            $this->OrderId = $generatedOrderID;
           
            $getOrderData = Order::where('id',$order_id)->first();
            $getOrderData->meezan_transaction_id = $generatedOrderID;
            $getOrderData->update();
            // if ($payment->status == 'open') {
            //     $data['payment_id'] = $payment->id;
            //     $data['success'] = true;
            //     $data['redirect_url'] = $payment->url;
            // }
            // return $data;

            
            header("Location: $generatedFormUrl");
            exit();
               dd($generatedFormUrl);
        }
          
    } catch (\Exception $ex) {
            Log::info($ex);
            return $data['message'] = $ex->getMessage();
    }
    }

    public function paymentConfirmation($payment_id, $payer_id=NULL)
    {
        $data['data'] = null;
        $payment = $this->stripClient->checkout->sessions->retrieve($payment_id, []);
        Log::info(json_encode($payment));
        if ($payment->payment_status == 'paid') {
            $data['success'] = true;
            $data['data']['amount'] = $payment->amount_total / 100;
            $data['data']['currency'] = $payment->currency;
            $data['data']['payment_status'] =  'success';
            $data['data']['payment_method'] = STRIPE;
        } else {
            $data['success'] = false;
            $data['data']['amount'] = $payment->amount_total / 100;
            $data['data']['currency'] = $payment->currency;
            $data['data']['payment_status'] =  'unpaid';
            $data['data']['payment_method'] = STRIPE;
        }

        return $data;
    }
}
