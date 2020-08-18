<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// 

Route::post('/register','Api\AuthController@register')->name('api.register');

Route::post('/login','Api\AuthController@login')->name('api.login');

Route::middleware('auth:api')->post('/logout', 'Api\AuthController@logout');

Route::get('/email-verification', 'Api\VerificationController@verify')->name('verification.verify');

Route::post('/resend-email', 'Api\VerificationController@resendVerficationEmail')->name('verification.resend');



Route::post('/forgot-password','Api\ForgotPasswordController@sendResetLinkEmail');
Route::post('/reset-password','Api\ResetPasswordController@reset');