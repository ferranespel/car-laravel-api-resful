<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\JwtAuth;
use App\Car;

class CarController extends Controller
{

	// List all cars
    public function index(){

    	$cars = Car::all()->load('user');
    	return response()->json(array(
    		'cars' => $cars,
    		'status' => 'success'
    	),200);

    }


    // Show details of a car

    public function show($id){
    	$car = Car::find($id)->load('user');

    	return response()->json(array(
    		'car' => $car,
    		'status' => 'success'
    	),200);
    }

    // Register one car
    public function store(Request $request){
    	$hash = $request->header('Authorization', null);

    	$jwtAuth = new JwtAuth();
    	$checkToken = $jwtAuth->checkToken($hash);

    	if($checkToken){
    		// Get data by POST
    		$json = $request->input('json',null);
    		$params = json_decode($json);
    		$params_array = json_decode($json,true);

			// Get user authenticated
			$user = $jwtAuth->checkToken($hash, true);

			// Validation

			$validate = \Validator::make($params_array, [
				'title' 		=> 'required|min:5',
				'description' 	=> 'required',
				'price' 		=> 'required', 
				'status' 		=> 'required', 
			]);

			if($validate->fails()){
				return response()->json($validate->errors(),400);

			}


			// Save Car
			$car = new Car();
			$car->user_id = $user->sub;
			$car->title = $params->title;
			$car->description = $params->description;
			$car->price = $params->price;
			$car->status = $params->status;

			$car->save();

			$data = array(
				'car' => $car,
				'status' => 'status',
				'code' => 200
			);

    	}else{
    		// Error output
			$data = array(
				'message' => 'Login Error',
				'status' => 'error',
				'code' => 400
			);
    	}
    	return response()->json($data,200);
    }

    //Update Car information

    public function update($id, Request $request){
    	$hash = $request->header('Authorization', null);

    	$jwtAuth = new JwtAuth();
    	$checkToken = $jwtAuth->checkToken($hash);

    	if($checkToken){
    		// Get params by POST
    		$json = $request->input('json',null);
    		$params = json_decode($json);
    		$params_array = json_decode($json,true);

			// Validation

			$validate = \Validator::make($params_array, [
				'title' 		=> 'required|min:5',
				'description' 	=> 'required',
				'price' 		=> 'required', 
				'status' 		=> 'required', 
			]);

			if($validate->fails()){
				return response()->json($validate->errors(),400);

			}

    		// Update the car

			$car = Car::where('id', $id)->update($params_array);

			$data = array(
				'title'			=> $params,
				'status'		=> 'success',
				'code'			=> 200
			);

    	}else{
    		// Error output
			$data = array(
				'message' => 'Login Error',
				'status' => 'error',
				'code' => 400
			);
    	}
		return response()->json($data,200);

    }

    // Delete car from DB
    public function destroy($id, Request $request){
    	$hash = $request->header('Authorization', null);

    	$jwtAuth = new JwtAuth();
    	$checkToken = $jwtAuth->checkToken($hash);

    	if($checkToken){
    		// Check Car Registration Exist

    		$car = Car::find($id);

    		// Delete Car
    		$car->delete();

    		// Response output

			$data = array(
				'car'			=> $car,
				'status'		=> 'success',
				'code'			=> 200
			);

    	
    	}else{
    		// Error output
			$data = array(
				'message' => 'Login Error',
				'status' => 'error',
				'code' => 400
			);
    	}
		return response()->json($data,200);

    }

}
