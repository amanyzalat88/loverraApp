<?php

namespace App\Http\Controllers\Mobileapi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Mobile\Country;
use Illuminate\Support\Str;
use App\Http\Resources\ApiCountryResource;

class CountryController extends Controller
{
    public $successStatus = 200;

    
    public function index(Request $request)
    {
        
         $data=null;
         $message='';
         
           $item =Country::whereIn('id',['115','18','176','191','163'])->get();
        
       if ($item->count()>0) {
            $result=ApiCountryResource::collection($item);
           return response()->json(['status'=>true,'msg' => $message,'data'=>$result], $this->successStatus);
       } else {
           $message = "Not  Country yet";
           return response()->json(['status'=>false,'msg' => $message,'data'=>$data], $this->successStatus);
       }
    }
   
}
