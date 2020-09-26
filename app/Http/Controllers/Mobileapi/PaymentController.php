<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Mobile\Customer;
use App\Models\Mobile\Payment;
use App\Models\Mobile\Cart;
use Illuminate\Support\Str;


class PaymentController extends Controller
{
    public $successStatus = 200;

    
    public function index(Request $request)
    {
        $item =Cart::where('customer_id',$request->user()->id)->count();  
        if ($item >0 ) {
        $rules = [
            
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'type' => 'required|in:bookeey,knet,credit',
            
        ];

        $messages = [
             
            'price.required' => 400,
            'price.regex' => 405,

            'type.required' => 400,
            'type.in' => 405,

           
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json(['status' => (int) $validator->errors()->first(), 'error' => $validator->errors()]);
        }

        $Member = $request->user();
        if ($Member == null) {
            return response()->json(['status' => 401]);
        }
       

        $Time = (string) str_random(10).time();
        $baseUrl = 'https://demo.bookeey.com/portal/mobileBookeeyPg';
        $price = '"'.$request->price.'"';
       /* $merchantId = 'mer20000393';
        $secreatKey = '3650191';*/
        $merchantId = 'mer2000013';
        $secreatKey = '5083930';
        $furl = 'https://cp.myloverra.com/mobileapi/paymentFail';
        $surl = 'https://cp.myloverra.com/mobileapi/paymentSuccess';
        $tranid = $Time;
        $txntime = $Time;
        $hashMac = self::GenerateHashMac($request->price, $tranid);
        $paymentOptions = $request->type;

        $Payment = new Payment();
        $Payment->txnId = $tranid;
        $Payment->custromer_id = $request->user()->id;
        $Payment->amount =  $request->price;
        $Payment->save();

        $Url = $baseUrl."?data={\"price\":\"{$request->price}\",\"merchantId\":\"{$merchantId}\",\"secreatKey\":\"{$secreatKey}\",\"surl\":\"{$surl}\",\"furl\":\"{$furl}\",\"tranid\":\"{$tranid}\",\"txntime\":\"{$txntime}\",\"hashMac\":\"{$hashMac}\",\"paymentOptions\":\"{$paymentOptions}\"}";

        $Curl = curl_init();
        curl_setopt($Curl, CURLOPT_URL, $Url);
        curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($Curl);
        curl_close($Curl);

        $result = json_decode($result);
        if (isset($result->bookeeyUrl)) {
            $url = $result->bookeeyUrl;
        }
        if (isset($result->knetUrl)) {
            $url = $result->knetUrl;
        }
        if (isset($result->ccUrl)) {
            $url = $result->ccUrl;
        }
        if ($result->g_status == -1) {
            return response()->json(['status' => 405, 'data' => 'payment failed']);
        }

        return response()->json(['status' => 200, 'data' => $url]);
    }
    else{
        $message = "Cart Empty ";
        return response()->json(['status'=>false,'msg' => $message,'data'=>null], 503);
    }
    }
    public function GenerateHashMac($price, $time)
    {
    
       /* $mid = 'mer20000393';
        $secret_key = '3650191';*/
        $mid = 'mer2000013';
        $secret_key = '5083930';
        $txRefNo = $time;
        $furl = 'https://cp.myloverra.com/mobileapi/paymentFail';
        $surl = 'https://cp.myloverra.com/mobileapi/paymentSuccess';
        $crossCat = 'GEN';
        $txTime = $time;
        $amt = (string) $price;
        // "mid|txnRefNo|su|fu|amt|txnTime|crossCat|secret_key"
        $hstring = $mid.'|'.$txRefNo.'|'.$surl.'|'.$furl.'|'.
            $amt.'|'.$txTime.'|'.$crossCat.'|'.$secret_key;

        $sig = hash('sha512', $hstring);

        return $sig;
    }
    public static function requery($txnId)
    {
        $URL = "https://demo.bookeey.com/portal/paymentrequery?txnId={$txnId}";

        $Curl = curl_init();
        curl_setopt($Curl, CURLOPT_URL, $URL);
        curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($Curl);
        curl_close($Curl);

        return $result;
    }
    public function paymentSuccess(Request $request)
    {
        $res = self::requery($request->merchantTxnId);
        $res = json_decode($res);

        $Payment = Payment::where('txnId', $request->merchantTxnId)->first();
        $Payment->totalAmountDebittedFromCust = $res->totalAmountDebittedFromCust;
        $Payment->totalAmountCreditedToMerchant = $res->totalAmountCreditedToMerchant;
        $Payment->tranStatus = $res->tranStatus;
        $Payment->walletTranStatus = $res->walletTranStatus;
        $Payment->merchantTxnRefNo = $res->merchantTxnRefNo;
        $Payment->txnRefNo = $res->txnRefNo;
        $Payment->merchantID = $res->merchantID;
        $Payment->successUrl = $res->successUrl;
        $Payment->failureUrl = $res->failureUrl;
        $Payment->crosscat = $res->crosscat;
        $Payment->requestHashMac = $res->requestHashMac;
        $Payment->paymentOptions = $res->paymentOptions;
        $Payment->merchantName = $res->merchantName;
        $Payment->payment_date = date('Y-m-d H:i:s');
        $Payment->confirmed = 'true';
        $Payment->statusDescription = $res->statusDescription;
        $Payment->created_at = date('Y-m-d H:i:s');
        $Payment->save();
        $mess="congratulations";
        return response()->json(['status'=>true,'msg' => $mess,'data'=>$request->merchantTxnId], $this->successStatus);
    }

    

    public function paymentFail(Request $request)
    {
        $mess="Payment Failed Reason:  ".$request->errorMessage;
        return response()->json(['status'=>false,'msg' => $mess,'data'=>null], $this->successStatus);
    }
   
}
