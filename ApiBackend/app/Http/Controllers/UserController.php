<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use JWTAuth;
use JWTFactory;
use Validator;
class UserController extends Controller
{

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
        
        if ($validator->fails()) {
            return response()->json([ 'success' => false, 'message' => "Please, complete all fields." ], 400);
        }
        $users = $this->getByEmail($request->email);
        if (!empty($users)) {
            return response()->json([ 'success' => false,'message' => "Sorry, the email is already registered."], 401);
        }
        
        $parameters = ['name' => $request->name, 'username' => $request->username, 'email' => $request->email,
                       'password' => bcrypt($request->password), 'token' => ''];
        $url = 'http://5d5d8e0f6cf1330014feaf66.mockapi.io/api/v1/users';
        $client = new Client();
        $res = $client->request('POST', $url , [ "form_params"=>$parameters ]);
        if($res->getStatusCode()==200 || $res->getStatusCode()==201 ){
            return response()->json([ 'success' => true,'message' => "Register Successful."],200);
        }
        return response()->json(['success' => false,'message' => "Sorry, Register failed."], 500);
    }


    public function edit(Request $request)
    {
        $token = $request->header('Authorization');
        $autenticate = $this->autenticate($request->header('Authorization'));
        if($autenticate['success']){
            $user = $autenticate['message'];
            return response()->json(['success' => true,'message' => $user], 200);
        }
        return response()->json($autenticate, 401);
    }


    public function update(Request $request)
    {
        $token = $request->header('Authorization');
        $autenticate = $this->autenticate($request->header('Authorization'));
        if($autenticate['success']){
            $user = $autenticate['message'];
        }
        else{
            return response()->json($autenticate, 401);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([ 'success' => false, 'message' => "Please, complete all fields." ], 400);     
        }
        if($request->email != $user->email){
            $users = $this->getByEmail($request->email);
            if(!empty($users)){
                return response()->json([ 'success' => false,'message' => "Sorry, the email is already registered."], 400);
            }
        }

        $parameters = [ 
            'name' => $request->name, 
            'username' => $request->username, 
            'email' => $request->email,
            'password' => $request->password
        ];

        $url = 'http://5d5d8e0f6cf1330014feaf66.mockapi.io/api/v1/users/'.$user->id;
        $client = new Client();
        $res = $client->request('PUT', $url, [
            'form_params' => $parameters
        ]);
        if($res->getStatusCode()==200 || $res->getStatusCode()==201 ){
            return response()->json([ 'success' => true,'message' => $user], 200);
        }
        return response()->json(['success' => false,'message' => "Sorry, Register failed."], 500);
        

    }

    public function delete(Request $request)
    {
        $token = $request->header('Authorization');
        $autenticate = $this->autenticate($request->header('Authorization'));
        if($autenticate['success']){
            $user = $autenticate['message'];
        }
        else{
            return response()->json($autenticate, 200);
        }
        $url = 'http://5d5d8e0f6cf1330014feaf66.mockapi.io/api/v1/users/'.$user->id;
        $client = new Client();
        $res = $client->request('DELETE', $url , []);
        if($res->getStatusCode()==200 || $res->getStatusCode()==201 ){
            return response()->json([ 'success' => true,'message' => $user], 200);
        }
        return response()->json(['success' => false,'message' => "Sorry, Delete user failed."], 500);
    }

    public function autenticate($token)
    {
        try { 
            JWTAuth::setToken($token);
            $token = JWTAuth::getToken();
            if (! $claim = JWTAuth::getPayload()->get('sub')) {
                return array('success'=>false,'message'=>'User not found.');
            }
            $email = JWTAuth::decode($token)->get('sub')->email;
            $users = $this->getByEmail($email);
            $token = "{$token}";
            if(!empty($users) && $users[0]->token==$token){
                return array('success'=>true,'message' => $users[0]);
            }
            return array('success'=>false,'message'=>'User not found.');
        }catch (TokenExpiredException $e) {
            return array('success'=>false,'message'=>'Token expired.');
        }catch (TokenInvalidException $e) {
            return array('success'=>false,'message'=>'Token invalid.');
        }catch (TokenBlacklistedException $e) {
            return array('success'=>false,'message'=>'Token blacklisted.');
        }catch (JWTException $e) {
            return array('success'=>false,'message'=>'Token absent.');
        }
    }


    public function getByEmail($email)
    {
        $url = 'http://5d5d8e0f6cf1330014feaf66.mockapi.io/api/v1/users?search='.$email;
        $client = new Client();
        $res = $client->request('GET', $url , []);
        return json_decode($res->getBody());
    }
}
