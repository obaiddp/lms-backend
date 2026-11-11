<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ZoomSetting;
use App\Traits\General;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;

class ZoomAccountController extends Controller
{
    use General;
    public function index()
    {
        $data['title'] = 'Zoom Setting';
        $data['navApplicationSettingParentActiveClass'] = 'mm-active';
        $data['subNavGlobalSettingsActiveClass'] = 'mm-active';
        $data['gzoomSettingsActiveClass'] = 'active';
        $checkUpdate =check_and_update_zoom_account_minutes();
        $data['zoomAccounts'] = ZoomSetting::orderBy('id','ASC')->get();

     

        return view('admin.application_settings.general.zoom_accounts', $data);
    }


    public function store(Request $request)
    {
        $currentStatus = $request->status;
     
            $request->validate([
            //    'account_id' => 'required',
               'api_key' => 'required',
               'api_secret' => 'required',
               'api_jwt_token' => 'required',
               'sdk_key' => 'required',
               'sdk_secret' => 'required',
            ]);
        
            $checkExists = ZoomSetting::where('sdk_key',$request->sdk_key)->first();
            if($checkExists){
                $this->showToastrMessage('error', __('Zoom Account With Given SDK Key already exists!!'));
                return redirect()->route('settings.zoom_account.index');
            }
            else{
                $checkExists2 = ZoomSetting::where('api_key',$request->api_key)->first();
                 if($checkExists2){
                $this->showToastrMessage('error', __('Zoom Account With Given Api Key already exists!!'));
                return redirect()->route('settings.zoom_account.index');
                }
            }


             ///////////////////////////// NEW CODE ////////////////////////////////////
             if($request->api_jwt_token != null && $request->api_jwt_token){
                $zoomVideoSDKJWT = $request->api_jwt_token;
                $zoomResult = $this->zoomCurl($zoomVideoSDKJWT);
            
                if(empty($zoomResult)){
                    $this->showToastrMessage('error', __('Invalid Or Expired Zoom Access Token Entered'));
                    return redirect()->route('settings.zoom_account.index');
                }
                else{
                    if(isset($zoomResult['code']) && isset($zoomResult['message'])){
                        $zoomErrorMessage =__($zoomResult['message']);
                        $this->showToastrMessage('error',  $zoomErrorMessage);
                        return redirect()->route('settings.zoom_account.index');
                     
                    }
                    else{
                        $zoomResultArray = $zoomResult;

                        $zoom = new ZoomSetting();
                        $zoom->user_id = Auth::user()->id;
                        $zoom->api_key = $request->api_key;
                        $zoom->api_secret = $request->api_secret;
                        $zoom->sdk_key = $request->sdk_key;
                        $zoom->sdk_secret = $request->sdk_secret;
                        $zoom->api_jwt_token = $request->api_jwt_token;
                        $zoom->status = $request->status;
                        $zoom->meeting_sessions_used = $zoomResultArray['Total_Sessions'];
                        $zoom->meeting_minutes_used = $zoomResultArray['Total_Minutes'];
                        $zoom->save();
                        if($currentStatus == 1){
                            $values['ZOOM_API_KEY'] = $request->api_key;
                            $values['ZOOM_API_SECRET'] = $request->api_secret;
                            $values['ZOOM_SDK_KEY'] = $request->sdk_key;
                            $values['ZOOM_SDK_SECRET'] = $request->sdk_secret;
                            $values['ZOOM_JWT_TOKEN'] = $request->api_jwt_token;
                            if (!updateEnv($values)) {     
                            }
                    
                            Artisan::call('optimize:clear');
                            
                            $getOtherAccounts = ZoomSetting::where('id','<>',$zoom->id)->get();
                            if(!$getOtherAccounts->isEmpty()){
                                foreach ($getOtherAccounts as $key => $value) {
                                    $value->status = 0;
                                    $value->update();
                                }
                            }
                        }

                        $this->showToastrMessage('success', __('Zoom Account Created Successfully'));
                        return redirect()->route('settings.zoom_account.index');
                    }
                }
            }
            else{
                $this->showToastrMessage('error', __('Please Enter Jwt Token'));
                return redirect()->route('settings.zoom_account.index');
            }
            /////////////////////////////// NEW CODE ////////////////////////////////////

     
    }


    public function zoomCurl($zoomVideoSDKJWT)
    {
    
        $result_array = array();
        $currentMonth = date('m');
        $currentMonth = (int)$currentMonth;
        $currentYear = date('Y');
        $currentYear = (int)$currentYear;
     
        $curl = curl_init();
        curl_setopt_array($curl, [
            // CURLOPT_URL => "https://api.zoom.us/v2/videosdk/report/daily",
            CURLOPT_URL => "https://api.zoom.us/v2/videosdk/report/daily?year=$currentYear&month=$currentMonth",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $zoomVideoSDKJWT,
          
            ],
            // CURLOPT_HTTPHEADER=>[
            //     "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhdWQiOm51bGwsImlzcyI6IllvM18tZzFDVG4ycDMxbk4wTndsNVEiLCJleHAiOjIwNTEyMDQxMDAsImlhdCI6MTczMDExOTIwNH0.seU7u-7VUEKrMjP4d0nzVtooLdlMJq2TiW7afLNgal8",
            // ],
          ]);

          $response = curl_exec($curl);
          $err = curl_error($curl);
          
          curl_close($curl);
          $result = json_decode($response);
     
          if(isset($result->code) && isset($result->message)){
             
            $result_array = [
                'code' => $result->code,
                'message' => $result->message,
            ];
            return $result_array;
          }
          else{
            if(isset($result->dates) && !empty($result->dates)){
                $TotalSessions = 0;
                $TotalMinutes = 0;
                foreach ($result->dates as $key => $value) {
                    $TotalSessions += $value->sessions;
                    $TotalMinutes += $value->session_minutes;
                }
                $result_array = [
                    'Total_Sessions'=> $TotalSessions,
                    'Total_Minutes'=> $TotalMinutes,
                ]; 
                return $result_array;  
            }
            else{
                $result_array = [
                    'Total_Sessions'=> 0,
                    'Total_Minutes'=> 0,
                ]; 
                return $result_array;   
            }
          }
          dd($result->code);
    
    }


    public function edit($id)
    {
        $data['title'] = 'Edit Zoom Setting';
        $data['navApplicationSettingParentActiveClass'] = 'mm-active';
        $data['subNavGlobalSettingsActiveClass'] = 'mm-active';
        $data['gzoomSettingsActiveClass'] = 'active';
        $data['zoomAccount'] = ZoomSetting::findOrFail($id);
        return view('admin.application_settings.general.zoom-account-edit', $data);
    }



    public function update(Request $request, $id)
    {
        $currentStatus = $request->status;
        if ($request->status == 1){
            $request->validate([
            //    'account_id' => 'required',
               'api_key' => 'required',
               'api_secret' => 'required',
               'api_jwt_token' => 'required',
               'sdk_key' => 'required',
               'sdk_secret' => 'required',
            ]);
        }

        $zoom = ZoomSetting::where('id',$id)->first();
        if (!$zoom) {
            $zoom = new ZoomSetting();
        }

        // $checkExists = ZoomSetting::where('sdk_key',$request->sdk_key)->where('api_key',$request->api_key)
        // ->where('id','<>',$id)->first();
        // if($checkExists){
        //     $this->showToastrMessage('error', __('Zoom Account With Given Api and SDK Key already exists!!'));
        //     return redirect()->route('settings.zoom_account.edit', [$id]);
        // }


           $checkExists = ZoomSetting::where('sdk_key',$request->sdk_key)->where('id','<>',$id)->first();
            if($checkExists){
                $this->showToastrMessage('error', __('Zoom Account With Given SDK Key already exists!!'));
               return redirect()->route('settings.zoom_account.edit', [$id]);
            }
            else{
                $checkExists2 = ZoomSetting::where('api_key',$request->api_key)->where('id','<>',$id)->first();
                 if($checkExists2){
                $this->showToastrMessage('error', __('Zoom Account With Given Api Key already exists!!'));
              return redirect()->route('settings.zoom_account.edit', [$id]);
                }
            }

               ///////////////////////////// NEW CODE ////////////////////////////////////
            if($request->api_jwt_token != null && $request->api_jwt_token){
                $zoomVideoSDKJWT = $request->api_jwt_token;
                $zoomResult = $this->zoomCurl($zoomVideoSDKJWT);
            
                if(empty($zoomResult)){
                    $this->showToastrMessage('error', __('Invalid Or Expired Zoom Access Token Entered'));
                    return redirect()->route('settings.zoom_account.index');
                }
                else{
                    if(isset($zoomResult['code']) && isset($zoomResult['message'])){
                        $zoomErrorMessage =__($zoomResult['message']);
                        $this->showToastrMessage('error',  $zoomErrorMessage);
                        return redirect()->route('settings.zoom_account.index');
                     
                    }
                    else{
                        $zoomResultArray = $zoomResult;

                        $zoom->user_id = Auth::user()->id;
                        $zoom->api_key = $request->api_key;
                        $zoom->api_secret = $request->api_secret;
                        $zoom->sdk_key = $request->sdk_key;
                        $zoom->sdk_secret = $request->sdk_secret;
                        $zoom->api_jwt_token = $request->api_jwt_token;
                        $zoom->status = $request->status;
                        $zoom->meeting_sessions_used = $zoomResultArray['Total_Sessions'];
                        $zoom->meeting_minutes_used = $zoomResultArray['Total_Minutes'];
                        $zoom->update();
                        // $zoom->save();

                        if($currentStatus == 1){
                            $values['ZOOM_API_KEY'] = $request->api_key;
                            $values['ZOOM_API_SECRET'] = $request->api_secret;
                            $values['ZOOM_SDK_KEY'] = $request->sdk_key;
                            $values['ZOOM_SDK_SECRET'] = $request->sdk_secret;
                            $values['ZOOM_JWT_TOKEN'] = $request->api_jwt_token;
                            if (!updateEnv($values)) {     
                            }
                    
                            Artisan::call('optimize:clear');
                            
                            $getOtherAccounts = ZoomSetting::where('id','<>',$zoom->id)->get();
                            if(!$getOtherAccounts->isEmpty()){
                                foreach ($getOtherAccounts as $key => $value) {
                                    $value->status = 0;
                                    $value->update();
                                }
                            }
                        }

                        $this->showToastrMessage('success', 'Zoom Account Successfully Updated');
                        return redirect()->route('settings.zoom_account.index');
                    }
                }
            }
            else{
                $this->showToastrMessage('error', __('Please Enter Jwt Token'));
                return redirect()->route('settings.zoom_account.index');
            }
            /////////////////////////////// NEW CODE ////////////////////////////////////

    }




    public function statusChange($zoom_id){

        $zoom = ZoomSetting::where('id',$zoom_id)->first();
        if (!$zoom) {
            $this->showToastrMessage('error', __('Given Zoom Account ID Does Not Exists In Records'));
            return redirect()->back();
        }
        $currentStatus = $zoom->status;
        if($currentStatus == 1){
            $zoom->status = 0;
            $zoom->update();
        }
        else{
            $zoom->status = 1;
            $zoom->update(); 
            $values['ZOOM_API_KEY'] = $zoom->api_key;
            $values['ZOOM_API_SECRET'] = $zoom->api_secret;
            $values['ZOOM_SDK_KEY'] = $zoom->sdk_key;
            $values['ZOOM_SDK_SECRET'] = $zoom->sdk_secret;
            $values['ZOOM_JWT_TOKEN'] = $zoom->api_jwt_token;
            if (!updateEnv($values)) {
            }
    
            Artisan::call('optimize:clear');
            $getOtherAccounts = ZoomSetting::where('id','<>',$zoom_id)->get();
            if(!$getOtherAccounts->isEmpty()){
                foreach ($getOtherAccounts as $key => $value) {
                    $value->status = 0;
                    $value->update();
                }
            }
        }
    
   
        $this->showToastrMessage('success', __('Status has been changed'));
        return redirect()->back();

    }

}
