<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    /**
    * Reset the given user's password.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    */
   public function reset(ResetPasswordRequest $request)
   {
     //  $request->validate($this->rules(), $this->validationErrorMessages());

       // Here we will attempt to reset the user's password. If it is successful we
       // will update the password on an actual user model and persist it to the
       // database. Otherwise we will parse the error and return the response.
       $response = $this->broker()->reset(
           $this->credentials($request), function ($user, $password) {
               $this->resetPassword($user, $password);
           }
       );

       // If the password was successfully reset, we will redirect the user back to
       // the application's home authenticated view. If there is an error we can
       // redirect them back to where they came from with their error message.
       return $response == Password::PASSWORD_RESET
                   ? $this->sendResetResponse($request, $response)
                   : $this->sendResetFailedResponse($request, $response);
   }
   protected function credentials(Request $request)
   {
       return $request->only(
           'email', 'password', 'password_confirmation', 'token'
       );
   }


   protected function resetPassword($user, $password)
   {
      
       $user->password = Hash::make($password);
       $user->setRememberToken(Str::random(60));

       $user->save();

      // event(new PasswordReset($user));

      
   }

  
    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }



    protected function sendResetResponse(Request $request, $response)
    {
        return response()->json([
            "message" => "Contraseña restablecida correctamente",
            "response" => $response
        ],200);
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return response()->json([
            "message" => "falló el restablecimiento de contraseña",
            "response" => $response
        ],500);
    }

}

