<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Auth\Events\Verified;
use Auth;



class VerificationController extends Controller
{
    //
    public function verify(Request $request){
      $user = User::where('id',$request->id)->first();

     
        // if (! hash_equals((string) $request->id, (string) $request->user()->getKey())) {
        //     throw new AuthorizationException;
        // }

        if (! hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        if ($user->hasVerifiedEmail()) {

      return response()->json([
          'message' =>"Este correo ya se encuentra verificado"
               ]);

          }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        if ($response = $this->verified($request)) {
            return $response;
        }

      return response()->json([
          'message' => "Correo verificado correctamente"
      ]);


    }

 /**
     * The user has been verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function verified(Request $request)
    {
        //
    }
    

    public function resendVerficationEmail(Request $request){

     
    $user = User::where('email', $request->email)->first();

      if(!$user){
        return response()->json([
            "message" => "No se encuentra este usuario"
        ]);

      }
 
        $user->sendEmailVerificationNotification();
    }

}
