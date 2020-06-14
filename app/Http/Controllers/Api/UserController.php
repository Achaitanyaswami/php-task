<?php
namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
// use Illuminate\Support\Facades\Auth; 
use Validator;
use DB;
use Hash;
use Auth;

use App\Provider_profiles;
use App\Provider_service;
use App\Role;
use App\Service_request;
use App\Service;
class UserController extends Controller 
{
    public $successStatus = 200;
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'email'     => 'required|email', 
            'password'  => 'required',  
        ]);
        $credentials = array('email' => $request->email, 'password' => $request->password);
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->accessToken; 
            return response()->json(['success' => $success], $this->successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        }
    }
    /** 
     * Register api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name'      => 'required', 
            'email'     => 'required|email', 
            'password'  => 'required', 
            'phone'     => 'required', 
            'latitude'  => 'required', 
            'longitude' => 'required', 
            'c_password'=> 'required|same:password', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all(); 
        $user_type = $request->user_type;
        $user = User::create([
                                'name'              => $request->name,
                                'email'             => $request->email,
                                'password'          => Hash::make($request->password),
                                'phone'             => $request->phone,
                                'latitude'          => $request->latitude,//28.663870,
                                'longitude'         => $request->longitude, //77.235161,
                                'created_at'        => date('Y-m-d H:i:s'),
                                'updated_at'        => date('Y-m-d H:i:s'),
                            ]);
        $success['token']   =  $user->createToken('MyApp')->accessToken; 
        $success['name']    =  $user->name;
        if ($user_type == 'provider') 
        {
            $description = $request->description;
            $role = Role::where('slug','provider')->first();
            $profile_data = array(  'user_id'       => $user->id, 
                                    'description'   => $description,
                                    'created_at'    => date('Y-m-d H:i:s'),
                                    'updated_at'    => date('Y-m-d H:i:s'));
            
            $provider_service   = new Provider_profiles($profile_data);
            $user->provider_profile()->save($provider_service);
        }else{
            $role = Role::where('slug', 'customer')->first();
        }
        $user->roles()->attach($role);
        return response()->json(['success'=>$success], $this->successStatus); 
    }
    /** 
     * details api 
     * 
     * @return \Illuminate\Http\Response 
     */ 
    public function details() 
    { 
        $user = Auth::user(); 
        if($user->hasRole('provider')) {
            $user_role = 'provider';
        }else{
            $user_role = 'customer';            
        }
        $result_arr['success']      = 1;
        $result_arr['user']         = $user;
        return response()->json($result_arr, $this->successStatus); 
    }

    public function post_services(Request $request) // for providers to post selected services
    {
        $ids        = $request->ids;
        $description= $request->description;
        $user       = Auth::user();
        $user_id    = Auth::user()->id;

        // provider services update start
        $user->provider_services()->delete();
        if (isset($ids) && sizeof($ids) > 0) 
        {
            $services = array();
            foreach ($ids as $service) 
            {
                $service_id = $service['id'];
                $price = null;
                if (isset($service['price'])) 
                {
                    $price = $service['price']; 
                }
                $services  =  array(  'user_id'    => $user_id,
                                        'service_id' => $service_id,
                                        'price'      => $price,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s') );

                $serviceModels[] = new Provider_service($services);
            }

            $user->provider_services()->saveMany($serviceModels);
            
            $result_arr['success']  = 1;
            $result_arr['services'] = $services;
            $result_arr['message']  = 'successful';
        }else{
            $result_arr['success'] = 1;
            $result_arr['message'] = 'no services selected by this Provider.';
        }
        // provider services update end

        // provider profile update start
        if (isset($description))
        {
            $user->provider_profile->update(['description' => $description,
                                             'updated_at' => date('Y-m-d H:i:s')
                                            ]);
        }

        $user_info = array();
        if (isset($request->name) && !empty($request->name) && $user->name!=$request->name)
        {
            $user_info['name'] = $request->name;
        }
        if (isset($request->email) && !empty($request->email) && $user->email!=$request->email)
        {
            $user_info['email'] = $request->email;
        }
        if (isset($request->phone) && !empty($request->phone) && $user->phone!=$request->phone)
        {
            $user_info['phone'] = $request->phone;
        }
        if (isset($request->latitude) && !empty($request->latitude) && $user->latitude!=$request->latitude)
        {
            $user_info['latitude'] = $request->latitude;
        }
        if (isset($request->longitude) && !empty($request->longitude) && $user->longitude!=$request->longitude)
        {
            $user_info['longitude'] = $request->longitude;
        }
        if (isset($request->password) && !empty($request->password))
        {
            $user_info['password'] = Hash::make($request->password);
        }

        if (sizeof($user_info) > 0) 
        {
            $user_info['updated_at'] = date('Y-m-d H:i:s');
        }
        $user->fill($user_info);
        $user->save();
        // provider profile update end

        return response()->json($result_arr);
    }

    public function get_services() // for provider user_type to get all services
    {
        $user_id            = Auth::user()->id;
        $user               = Auth::user();
        $service_id_price   = array();
        $services           = DB::table('services')->get();
        $selected_services  = $user->provider_services()->get();


        if (sizeof($services) > 0) 
        {
            foreach ($services as $service) 
            {
                $service_id_price[$service->id] = $service->price;
            }
        }

        if (sizeof($selected_services) > 0) 
        {
            foreach ($selected_services as $service) 
            {
                if (empty($service->price)) 
                {
                    $service->price = $service_id_price[$service->service_id];
                }
            }
        }
        $profile                            = $user->provider_profile()->get();
        $user->description                  = $profile[0]->description;
        $result_arr['success']              = 1;
        $result_arr['services']             = $services;
        $result_arr['selected_services']    = $selected_services;
        $result_arr['user_info']            = $user;
        return response()->json($result_arr);

    }

    public function find_nearest_provider($radius)
    {

        $user       = Auth::user(); 
        $latitude   = $user->latitude;
        $longitude  = $user->longitude;
        $result_arr = array();
        $providers  = User::selectRaw("id, name, email, latitude, longitude, phone ,
                     ( 6371 * acos( cos( radians(?) ) *
                       cos( radians( latitude ) )
                       * cos( radians( longitude ) - radians(?)
                       ) + sin( radians(?) ) *
                       sin( radians( latitude ) ) )
                     ) AS distance", [$latitude, $longitude, $latitude])
                    ->having("distance", "<=", $radius)
                    ->whereHas('roles', function($q){$q->where('name', 'provider');})
                    ->orderBy("distance",'asc')
                    ->get();

        if (sizeof($providers) > 0) 
        {
            foreach ($providers as $provider) 
            {
                $profile                       = $provider->provider_profile()->get();
                $provider->description         = $profile[0]->description;
                $selected_services             = $provider->provider_services()->get();
                if (sizeof($selected_services) > 0) 
                {
                    foreach ($selected_services as $service) 
                    {
                        $service_data = Service::find($service->service_id);
                        $service->title = $service_data->title;
                        if (empty($service->price)) 
                        {
                            $service->price = $service_data->price;
                        }
                    }
                }
                $provider->selected_services   = $selected_services;
            }
            $result_arr             = array();
            $result_arr['success']  = 1;
            $result_arr['providers']= $providers;

            return response()->json($result_arr, $this->successStatus); 
        }else{
            return response()->json(['success' => 0], $this->successStatus); 

        }
    }
    
    public function send_request_provider(Request $request)
    {
        $result_arr             = array();
        $data                   = array();
        $service_id             = $request->service_id;
        $provider_id            = $request->provider_id;
        $provider_service_data  = Provider_service::where('service_id',$service_id)
                                                  ->where('user_id',$provider_id)
                                                  ->get();
        if (sizeof($provider_service_data) > 0) 
        {
            if (isset($provider_service_data[0]->price) && !empty($provider_service_data[0]->price)) 
            {
                $price = $provider_service_data[0]->price;
            }else{
                $service_data   = Service::find($service_id);
                $price          = $service_data->price;
            }
            $customer_id          = Auth::user()->id;
            $data['service_id']   = $service_id;
            $data['customer_id']  = $customer_id;
            $data['provider_id']  = $provider_id;
            $data['price']        = $price;
            $data['status']       = 0;
            $data['created_at']   = date('Y-m-d H:i:s');
            $data['updated_at']   = date('Y-m-d H:i:s');
            Service_request::create($data);
            $result_arr['success']= 1;
            return response()->json($result_arr, $this->successStatus); 
        }else{
            $result_arr['success']= 0;
            $result_arr['Message']= 'Check provider_id or service_id';
            return response()->json($result_arr, 401); 
        }
    }

    public function get_providers_request()
    {
        $requests  = Service_request::where('provider_id',Auth::user()->id)
                                      ->where('status', 0)
                                      ->get();
        $result_arr['success']  = 1;
        $result_arr['requests'] = $requests;
        return response()->json($result_arr, $this->successStatus); 
    }

    public function update_request(Request $request)
    {
        $ids                    = $request->ids;
        $result_arr['success']  = 1;
        $result_arr['ids']      = $ids;

        foreach ($ids as $service_id) 
        {
            Service_request::where('provider_id',Auth::user()->id)
                           ->where('id',$service_id['id'])
                           ->update(['status' => $service_id['status'],
                                     'updated_at' => date('Y-m-d H:i:s')
                                    ]);
        }
        return response()->json($result_arr, $this->successStatus); 
    }

    public function clear_rejected()
    {
        $max_date = date('Y-m-d H:i:s', strtotime("-1 days"));
        Service_request::where('created_at','<=',$max_date)
                       ->where('status',2)
                       ->delete();
        $result_arr['success']  = 1;
        return response()->json($result_arr, $this->successStatus); 
    }


}