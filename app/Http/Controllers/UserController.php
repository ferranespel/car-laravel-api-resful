<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\JwtAuth;
use App\User;

class UserController extends Controller
{
    public function register(Request $request){
    	
    	//Recoger post
    	$json = $request->input('json', null);	
    	$params = json_decode($json);

    	$email = (!is_null($json) && isset($params->email)) ? $params->email : null;
    	$name = (!is_null($json) && isset($params->name)) ? $params->name : null;
    	$surname = (!is_null($json) && isset($params->surname)) ? $params->surname : null;
    	$role = 'ROLE_USER';
    	$password = (!is_null($json) && isset($params->password)) ? $params->password : null;


    	if(!is_null($email) && !is_null($password) && !is_null($name)){
    		//Create user
    		$user = new User();
    		$user->email = $email;
    		$user->name = $name;
			$user->surname = $surname;
    		$user->role = $role;
    		
    		$pwd = hash('sha256', $password);
			$user->password = $pwd;

			// Check user exist
			$isset_user = User::where('email' , '=' , $email)->first();

			if(count($isset_user) == 0){
				//Save user
				$user->save();
	    		$data = array(
	    			'status'	=> 'success',
	    			'code'		=> 200,
	    			'message'	=> 'New user have been register.'
	    		);
			}else{
				// User exist
	    		$data = array(
	    			'status'	=> 'error',
	    			'code'		=> 400,
	    			'message'	=> 'Email User already exist, you have to register with different email.'
	    		);
			}

    	}else{
    		$data = array(
    			'status'	=> 'error',
    			'code'		=> 400,
    			'message'	=> 'User not created.'
    		);
    	}

    	return response()->json($data, 200);

    }

    public function login(Request $request){
    	$jwtAuth = new JwtAuth();

    	$json = $request->input('json', null);
    	$params = json_decode($json);

    	$email = (!is_null($json) && isset($params->email)) ? $params->email : null;
    	$password = (!is_null($json) && isset($params->password)) ? $params->password : null;
    	$getToken = (!is_null($json) && isset($params->gettoken)) ? $params->gettoken : null;

    	// Encode password
    	$pwd = hash('sha256', $password);

    	if(!is_null($email) && !is_null($password) && ($getToken == null || $getToken == 'false')){
    		$signup = $jwtAuth->signup($email, $pwd);

    	}elseif($getToken != null){
    		//var_dump($getToken);die();
    		$signup = $jwtAuth->signup($email, $pwd, $getToken);

    	}else{
    		$signup = array(
    			'status'	=> 'error',
    			'message'	=> 'User not created.'
    		);
    	}

		return response()->json($signup,200);
    }
}
