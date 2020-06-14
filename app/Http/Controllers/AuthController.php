<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use DB;
use App\User;
use Hash;
use Auth;

use App\Role;
// use App\Permission;

class AuthController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function create_user(Request $request)
    {
        $user_type = 'provider';
        $user = User::create([
                    'name'              => 'Chaitanya',
                    'email'             => 'chaitanyas082@gmail.com',
                    'password'          => Hash::make('123456'),
                    'phone'             => 8010915556,
                    'latitude'          => 28.663870,
                    'longitude'         => 77.235161,
                    'created_at'  		=> date('Y-m-d H:i:s'),
                    'updated_at'       	=> date('Y-m-d H:i:s'),
                ]);
        
        if ($user_type == 'provider') 
        {
            $role = Role::where('slug','provider')->first();
            DB::table('provider_profiles')->insert(['user_id'        => $user->id, 
                                                    'description'  => $user->email,
                                                    'created_at'    => date('Y-m-d H:i:s'),
                                                    'updated_at'    => date('Y-m-d H:i:s'),
                                                    ]);
        }else{
            $role = Role::where('slug', 'customer')->first();
        }
        $user->roles()->attach($role);
		$result_arr['success'] = 1;
        return response()->json($result_arr);
    }

    public function login()
    {
        $credentials = array('email' => "chaitanyas082@gmail.com", 'password' => '123456');
        if (Auth::attempt($credentials, true))
        {
            $user_id = Auth::user()->id;
            echo "<pre>"; print_r(Auth::user()); echo "</pre>";

        }
    }

    public function user_info()
    {
        $user = Auth::user();
        if($user->hasRole('provider')) {
            echo "<pre>"; print_r('user is provider'); echo "</pre>";
        }else{
            echo "<pre>"; print_r('user is not provider'); echo "</pre>";
        }

        if($user->hasRole('customer')) {
            echo "<pre>"; print_r('user is customer'); echo "</pre>";
        }else{
            echo "<pre>"; print_r('user is not a customer'); echo "</pre>";
        }
    }

    public function get_services() // for provider to get all services
    {
        $service_id_price   = array();
        $services           = DB::table('services')->get();
        $selected_services  = DB::table('provider_services')->get();
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
        $result_arr['success']              = 1;
        $result_arr['services']             = $services;
        $result_arr['selected_services']    = $selected_services;
        // echo "<pre>"; print_r($services); echo "</pre>";
        // echo "<pre>"; print_r($selected_services); echo "</pre>";
        return response()->json($result_arr);

    }

    public function post_services(Request $request) // for providers to post selected services
    {
        $ids        = $request->ids;
        $user_id    = 2;
        DB::table('provider_services')->where('user_id', $user_id)->delete();
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
                $services[]  =  array(  'user_id'    => $user_id,
                                        'service_id' => $service_id,
                                        'price'      => $price,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'updated_at' => date('Y-m-d H:i:s') );
            }            

            DB::table('provider_services')->where('user_id', $user_id)->delete();
            DB::table('provider_services')->insert($services);
            $result_arr['success']  = 1;
            $result_arr['services'] = $services;
            $result_arr['message']  = 'successful';
        }else{
            $result_arr['success'] = 1;
            $result_arr['message'] = 'no services selected to this Provider.';

        }
        return response()->json($result_arr);

    }

    public function get_token(){
        $result_arr['token'] = csrf_token();
        return response()->json($result_arr);
    }





}
