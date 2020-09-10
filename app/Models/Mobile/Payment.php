<?php

namespace App\Models\Mobile;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Payment extends Model
{
    protected $table = 'payment';
    protected $hidden =  [];
    protected $fillable = ['id', 'txnId', 'order_id','custromer_id','amount','totalAmountDebittedFromCust','totalAmountCreditedToMerchant',
    'tranStatus','walletTranStatus','merchantTxnRefNo','txnRefNo','merchantID','successUrl','failureUrl','crosscat',
   'requestHashMac','paymentOptions','merchantName','payment_date','confirmed','statusDescription','created_at','updated_at'];
    
     
}
