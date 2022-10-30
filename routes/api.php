<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\APIuserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListingController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('show_all_users',[APIuserController::class,'show_all_users']);
//Secure GET API to show all users list
Route::post('users-list',[APIuserController::class,'userslist']);

Route::get('show_all_users',[APIuserController::class,'show_all_users']);
Route::post('show_single_user_data',[APIuserController::class,'show_single_user_data']);
Route::post('add_single_user_data',[APIuserController::class,'add_single_user_data']);
//Secure Register API - Register User with API Token
Route::post('register-user',[APIuserController::class,'registerUser']);
// Passport: Register API - Register User with API Token WIth Passport
Route::post('register-user-with-passport',[APIuserController::class,'registerUserWithPassport']);
//Passport: Login API - Login user and regenerate Passport Token
Route::post('login-user-with-passport',[APIuserController::class,'loginUserWithPassport']);

//Secure Login API -  Login Users after Register
Route::post('login-user',[APIuserController::class,'loginUsers']);
// Logout API - Logout user and delete api_token
Route::post('logout-user',[ApiuserController::class,'logoutUsers']);
Route::post('add_multiple_users_data',[APIuserController::class,'add_multiple_users_data']);
//Put API for updating single or multiple records of specific single user by user id
Route::put('update_users_data/{id}', [APIuserController::class,'update_users_data']);

//Patch API for updating single record of specific single user by user id
Route::patch('update_single_user_record/{id}',[APIuserController::class,'update_single_user_record']);
//DELETE API for deleting single record of specific single user by user_id
Route::delete('delete_single_user_record',[APIuserController::class,'deleteSingleUser']);
//DELETE  API for deleting multiple users with param
Route::delete('delete-multiple-users/{ids}',[APIuserController::class,'deleteMultipleUsers']);
//DELETE API for deleting multiple users with json
Route::delete('delete-multiple-users-withJson',[APIuserController::class,'deleteMultipleUsersWithJson']);