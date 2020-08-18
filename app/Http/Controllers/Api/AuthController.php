<?php

namespace App\Http\Controllers\Api;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
  
    public function register(Request $request){
        $user = User::create([
            'name' => $request->name,
            'email'=> $request->email,
            // 'role' => $request->role,
            'password'=>Hash::make($request->password) 

        ]);

      if(!$user){
          return response()->json(["success"=>false, "message"=>"Falló el Registro"],500);
      }

         $user->sendEmailVerificationNotification();

        return response()->json(["success"=>true, "message"=>"Registrado Correctamente"]);
    }


    
   public function login(Request $request){

     $user = User::where('email', $request->email)->first();

     $verificacion = User::where('email', $request->email)
                 ->where('email_verified_at', '<>' , null)
                 ->first();
  


        if($user && !$verificacion){
            return response()->json(["success"=>false, "message"=>"Correo no verificado"],500);
        }else{

    $http = new \GuzzleHttp\Client;
    try {
        
        $response = $http->post(config('services.passport.login_endpoint'), [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'username' => $request->email,
                'password' => $request->password,
            ]
        ]);
    
        return $response->getBody();
    
    } catch (\GuzzleHttp\Exception\BadResponseException $e) {
       
        if ($e->getCode() === 400) {
            return response()->json(["message"=>'Tus credenciales no se encuentran en nuestro registros'], $e->getCode());
        } else if ($e->getCode() === 401) {
            return response()->json(["message"=>'Tus credenciales son incorrectas, por favor intente de nuevo'], $e->getCode());
        }
    
        return response()->json(["message"=>'Ocurrió un error en el servidor'], $e->getCode());
    }

}

   }


   public function logout()
   {
       auth()->user()->tokens->each(function ($token, $key) {
           $token->delete();
       });
       
       return response()->json('Logged out successfully', 200);
   }


  


}







