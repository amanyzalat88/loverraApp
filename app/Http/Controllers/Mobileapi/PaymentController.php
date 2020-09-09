<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Mobile\Customer;
use App\Models\Mobile\Payment;
use Illuminate\Support\Str;


class PaymentController extends Controller
{
    public $successStatus = 200;

    
    public function index(Request $request)
    {
        $rules = [
            
            'price' => 'required|int',
            'type' => 'required|in:bookeey,knet,credit',
            
        ];

        $messages = [
             
            'price.required' => 400,
            'price.int' => 405,

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
        $baseUrl = 'https://apps.bookeey.com/pgapi/api/payment/requestLink';
        $price = '"'.$request->price.'"';
        $merchantId = 'mer20000393';
        $secreatKey = '3650191';
        $furl = 'https://cp.myloverra.com/Mobileapi/paymentFail';
        $surl = 'https://cp.myloverra.com/Mobileapi/paymentSuccess';
        $tranid = $Time;
        $txntime = $Time;
        $hashMac = self::GenerateHashMac($request->price, $tranid);
        $paymentOptions = $request->type;

        /*$payment = new Payment();
        $payment->txnId = $tranid;
        //$payment->order_id = $request->price;
        $payment->custromer_id = $Member->id;
        
        $payment->save();*/

        


       $params = array(
            "DBRqst" => "PY_ECom",
            "Do_Appinfo" => array(
            "APIVer"=>"",
            "APPID"=>"",
            "APPTyp"=>"WEB",
            "AppVer"=>"V1.6",
            "ApiVer"=>"V1.6",
            "Country"=>"",
            "DevcType"=>"5",
            "HsCode"=>"",
            "IPAddrs"=>"",
            "MdlID"=>"",
            "OS"=>"Android",
            "UsrSessID"=>""
            ),
            "Do_TxnDtl"=>array(array(
                "SubMerchUID"=>"subm20000393",
                "Txn_AMT"=>'"'.$request->price.'"'
            )),
            "Do_MerchDtl"=>array(
                "BKY_PRDENUM"=>"ECom",
                "FURL"=>"https://cp.myloverra.com/Mobileapi/paymentFail",
                "MerchUID"=>"mer20000393",
                "SURL"=>"https://cp.myloverra.com/Mobileapi/paymentSuccess"
            ),
            "Do_TxnHdr"=>array(
                "BKY_Txn_UID"=>"",
                "Merch_Txn_UID"=>$Time,
                "PayFor"=>"ECom",
                "PayMethod"=> $request->type,
                "Txn_HDR"=>$Time,
                "hashMac"=>self::GenerateHashMac($request->price, $tranid)
                )
           
        );
       
        
        $result= self::httpPost($baseUrl,$params);
        
         
        $result = json_decode($result);
        var_dump($result);
        die();
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
    function httpPost($url,$params)
    {
        
        $data_string = json_encode($params);         
        
            $ch = curl_init();  
        
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($data_string))                                                                       
            );   
             
        
            $result=curl_exec($ch);
        
            curl_close($ch);
            $result = json_decode($result);
            var_dump($result);
            die();
            return $output;
    
    }
    public function GenerateHashMac($price, $time)
    {
        $mid = 'mer20000393';
        $secret_key = '3650191';

        $txRefNo = $time;
        $furl = 'https://cp.myloverra.com/Mobileapi/paymentFail';
        $surl = 'https://cp.myloverra.com/Mobileapi/paymentSuccess';
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
        $Payment->order_id = $res->ecomTxnId;
        $Subscription->amount = $res->amount;
        $Subscription->totalAmountDebittedFromCust = $res->totalAmountDebittedFromCust;
        $Subscription->totalAmountCreditedToMerchant = $res->totalAmountCreditedToMerchant;
        $Subscription->tranStatus = $res->tranStatus;
        $Subscription->walletTranStatus = $res->walletTranStatus;
        $Subscription->merchantTxnRefNo = $res->merchantTxnRefNo;
        $Subscription->txnRefNo = $res->txnRefNo;
        $Subscription->merchantID = $res->merchantID;
        $Subscription->successUrl = $res->successUrl;
        $Subscription->failureUrl = $res->failureUrl;
        $Subscription->crosscat = $res->crosscat;
        $Subscription->requestHashMac = $res->requestHashMac;
        $Subscription->paymentOptions = $res->paymentOptions;
        $Subscription->merchantName = $res->merchantName;
        $Subscription->payment_date = date('Y-m-d H:i:s');
        $Subscription->confirmed = 'true';
        $Subscription->statusDescription = $res->statusDescription;
        $Subscription->created_at = date('Y-m-d H:i:s');
        $Subscription->save();

        return view('payment.paymentSuccess');
    }

    

    public function paymentFail(Request $request)
    {
        return view('payment.paymentFail');
    }
   
}
