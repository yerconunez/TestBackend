<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\JWTException;
use GuzzleHttp\Client;
use Validator;
use JWTAuth;
use JWTFactory;
use Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {   
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails()) {
            return response()->json(['success'=> false, 'message'=> $validator->messages()], 400);
        }

        $url = 'http://5d5d8e0f6cf1330014feaf66.mockapi.io/api/v1/users?search='.$request->email;
        $client = new Client();
        $res = $client->request('GET', $url , []);
        $users = json_decode($res->getBody());
        if ( empty($users) || !Hash::check($request->password, $users[0]->password) ){
            return response()->json(['success'=> false, 'message'=> 'Unauthorized'], 401);
        }

        $now = Carbon::now()->toDateTimeString();
        $factory = JWTFactory::customClaims([
            'sub'   => [
                'email' => $request->email, 'date' => $now
            ],
        ]);
        $payload = $factory->make();
        $token = JWTAuth::encode($payload);
        $lastToken = $users[0]->token;
        if($lastToken!=''){
            try{
                JWTAuth::setToken($lastToken);
                JWTAuth::invalidate(JWTAuth::getToken());
            }
            catch (TokenExpiredException $e){}
            catch (TokenInvalidException $e) {}
            catch (TokenBlacklistedException $e){}
            catch (JWTException $e) {}
        }
        $parameters = [ 
            'token' => "{$token}"
        ];
        $url = 'http://5d5d8e0f6cf1330014feaf66.mockapi.io/api/v1/users/'.$users[0]->id;
        $client = new Client();
        $res = $client->request('PUT', $url, [
            'form_params' => $parameters
        ]);
        if($res->getStatusCode()==200 || $res->getStatusCode()==201 ){
            return response()->json(['success'=> true, 'message'=> "{$token}"], 200);
        }
        return response()->json(['success' => false,'message' => "Sorry, Login failed."], 500);
    }
    

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try{
            $token = $request->header('Authorization');
            $token = "{$token}";
            JWTAuth::setToken($token);
            $token = JWTAuth::getToken();
            if (! $claim = JWTAuth::getPayload()->get('sub') ) {
                return array('success'=>false,'message'=>'User not found.');
            }
            $email = JWTAuth::decode($token)->get('sub')->email;
            $url = 'http://5d5d8e0f6cf1330014feaf66.mockapi.io/api/v1/users?search='.$email;
            $client = new Client();
            $res = $client->request('GET', $url , []);
            if($res->getStatusCode()=='200' || $res->getStatusCode()=='201'){
                JWTAuth::invalidate(JWTAuth::getToken());
                return response()->json(['success'=>true,'message' => "Loggout successful."], 200);
            }
            return response()->json(['success'=>true,'message' => "Loggout failed. Token Invalid."], 401);
        }catch (TokenExpiredException $e){
            return response()->json(['success'=>false,'message' => "Loggout failed. Token expired."], 401);
        }catch (TokenInvalidException $e) {
            return response()->json(['success'=>false,'message' => "Loggout failed. Token Invalid."], 401);
        }catch (TokenBlacklistedException $e) {
            return response()->json(['success'=>false,'message' => "Loggout failed. Token Blacklisted."], 401);
        }catch (JWTException $e) {
            return response()->json(['success'=> false, 'message'=> 'Loggout failed.'], 401);
        }
    }

    
}
