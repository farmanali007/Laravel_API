<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class APIuserController extends Controller
{
    public function show_all_users()
    {
        $usersData = User::select('id','name','email')->get();
        return response()->json(['users' => $usersData],200);
    }
    public function userslist(Request $request){
        $header = $request->header('Authorization');
        if(empty($header)){
            $Message = "header Authorization is missing";
            return response()->json(['status' => false,'message' => $Message],422);

        }else{
            if($header == "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6ImZhcm1hbmFsaSIsImlhdCI6MTUxNjIzOTAyMn0.vjxrYh0E6vhXd7LEWly4aO2BD58jbAQ4p6aoHpMdsbM"){
                $users = User::get();
                return response()->json(['users' => $users],200);
            }else{
                $Message = "header Authorization is missing";
                return response()->json(['status' => false,'message' => $Message],422);    
            }
        }
             
    }
    public function show_single_user_data(Request $request)
    {
        if ($request->isMethod('post')) {
            $userId = $request->input('id');
            $userData = User::find($userId);
            // dd($userData);
            if ($userData) {
                return response()->json([
                    'users' => $userData
                ],200);
            } else {
                return response()->json(["message" => "no data found"]);
            }
        }
    }



    public function add_single_user_data(Request $request)
    {
        if ($request->isMethod('post')) {
            $userData = $request->input();
            //==>simple validations

            //check user detail
            // if(empty($userData['name']) || empty($userData['email'])|| empty($userData['password']))
            // {
            //     $error_message = "please enter complete user details";
             
            // }

            // //check if valid email
            // if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            //     $error_message = "please enter valid email address";
            //   }

            //   //check if email already exist
            //   $userCount = User::where('email', '=' , $userData['email'])->count();
            //   if($userCount > 0){
            //     $error_message = "email already Exist";
            //   }
            //   if(isset($error_message) && !empty($error_message)){
            //     return response()->json([
            //         "status" => false,
            //         "message" => $error_message
            //     ],422);
            //   }



            //==> Advance validations in Laravel


            $rules = [
                "name" => "required",
                "email" => "required|email|unique:users",
                "password" => "required"
            ];

          //CustomeMEssages for validation errors

          $customeMessage = [
               'name.required' => 'Name field is required',
            //    'name.regex' => 'Only characters are allowed',
               'email.email' => 'Please enter valid email Address',
               'email.required' => 'Emial field is required',
               'email.unique' => 'This email address already exist in Db',
               'password.required' => 'password field is requried'
          ];

            $validator = Validator::make($userData,$rules,$customeMessage);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }


            $userData['password'] = bcrypt($userData['password']);
            $user = User::create($userData);
            return response()->json(["message" => "add user success fully!"],201);
        }
    }

    //Register API - Add users with API token
    public function registerUser(Request $request){
     if($request->isMethod('post')){
        $userData = $request->input();
        $Api_token = Str::random(60);

        $rules = [
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required"
        ];

        //CustomeMEssages for validation errors

        $customeMessage = [
            'name.required' => 'Name field is required',
         //    'name.regex' => 'Only characters are allowed',
            'email.email' => 'Please enter valid email Address',
            'email.required' => 'Emial field is required',
            'email.unique' => 'This email address already exist in Db',
            'password.required' => 'password field is requried'
       ];

       $validator = Validator::make($userData,$rules,$customeMessage);
       if($validator->fails()){
           return response()->json($validator->errors(),422);
       }
      $user = new User;
      $user->name = $userData['name'];
      $user->email = $userData['email'];
      $user->password = bcrypt($userData['password']);
      $user->Api_Token = $Api_token;
      $user->save();
      return response()->json([
        "status" => true,
        "message" => "register user success fully",
        "token" => $Api_token
    ],201);

     }

    }

    //Passport: Register User with Passport
    public function registerUserWithPassport(Request $request){
        if($request->isMethod('post')){
           $userData = $request->input();
        //    $Api_token = Str::random(60);
   
           $rules = [
               "name" => "required",
               "email" => "required|email|unique:users",
               "password" => "required"
           ];
   
           //CustomeMEssages for validation errors
   
           $customeMessage = [
               'name.required' => 'Name field is required',
               'email.email' => 'Please enter valid email Address',
               'email.required' => 'Emial field is required',
               'email.unique' => 'This email address already exist in Db',
               'password.required' => 'password field is requried'
          ];
   
          $validator = Validator::make($userData,$rules,$customeMessage);
          if($validator->fails()){
              return response()->json($validator->errors(),422);
          }
         $user = new User;
         $user->name = $userData['name'];
         $user->email = $userData['email'];
         $user->password = bcrypt($userData['password']);
         $user->save();
        // $input = $request->all();
        // $input['password'] = bcrypt($input['password']);
        // $user = User::create($input);
        // $apiToken = $user->createToken('SomeThing')->accessToken;
        // dd($apiToken);
            if(Auth::attempt(['email' => $userData['email'],'password' => $userData['password']])){
                $user = User::where('email',$userData['email'])->first();
                
                // generate Api-access_torken
                $apiToken = $user->createToken($userData['email'])->accessToken;
                // dd($apiToken);
                // update Api-access_token in users Table
                User::where('email',$userData['email'])
                ->update(['Api_Token' => $apiToken]);
                return response()->json([
                    'status' => true,
                    'message'=>'user registered successfully',
                    'token'=> $apiToken
                ],201);
            }
   
        }
   
       }

    



    //Login Users API - Login users after Registration

    public function loginUsers(Request $request){
        if($request->isMethod('post')){
            $userData = $request->input();
        $rules = [
            "email" => "required|email|exists:users",
            "password" => "required"
        ];

        //CustomeMEssages for validation errors

        $customeMessage = [
            'email.email' => 'Please enter valid email Address',
            'email.required' => 'Emial field is required',
            'email.exists' => 'This email address not exist',
            'password.required' => 'password field is requried'
       ];

       $validator = Validator::make($userData,$rules,$customeMessage);
       if($validator->fails()){
           return response()->json($validator->errors(),422);
       }
       //fetch user detail
       $userDetails = User::where('email', $userData['email'])->first();
       //verify the password
       if(password_verify($userData['password'], $userDetails->password)){
            //update API -  Token
            $apiToken = Str::random(60);
            User::where('email', $userData['email'])->update(['api_Token' => $apiToken]);

            return response()->json([
                'status' => true,
                'message'=> 'user loged in successfully',
                'token' => $apiToken
            ],201);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'incorrect password'
            ],422);
        }

        }
    }

    //Passport: Login User and regenrate Passport Token

    public function loginUserWithPassport(Request $request){
        if($request->isMethod('post')){
            $userData = $request->input();
        $rules = [
            "email" => "required|email|exists:users",
            "password" => "required"
        ];

        //CustomeMEssages for validation errors

        $customeMessage = [
            'email.email' => 'Please enter valid email Address',
            'email.required' => 'Emial field is required',
            'email.exists' => 'This email address not exist',
            'password.required' => 'password field is requried'
       ];

       $validator = Validator::make($userData,$rules,$customeMessage);
       if($validator->fails()){
           return response()->json($validator->errors(),422);
       }
       //fetch user detail
       $user = User::where('email', $userData['email'])->first();
       //verify the password
       if(password_verify($userData['password'], $user->password)){
            //update API Token with passport
            $apiToken = $user->createToken($userData['email'])->accessToken;
            // dd($apiToken);
            User::where('email', $userData['email'])->update(['api_Token' => $apiToken]);

            return response()->json([
                'status' => true,
                'message'=> 'user loged in successfully',
                'token' => $apiToken
            ],201);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'incorrect password'
            ],422);
        }

        }
    }
  
//Logout API
    public function logoutUsers(Request $request){
        $apiToken = $request->header('Authorization');
        if(empty($apiToken)){
            return response()->json([
                'status' => false,
                'message' => 'api token is missing in API header'
            ],422);
        }else{
            $usercount = User::where('Api_Token',$apiToken)->count();
            if($usercount > 0){
                User::where('Api_Token',$apiToken)->update(['Api_Token' => null]);
                return response()->json([
                    'status' => true,
                    'message' => 'user logged out successfully'
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'token does not exist'
                ]);
            }
        }

    }



    public function add_multiple_users_data(Request $request)
    {
        if ($request->isMethod('post')) {
            $userData = $request->input();
            
            //Validation on array in laravel
          
            $rules = [
                "users.*.name" => "required",
                "users.*.email" => "required|email|unique:users",
                "users.*.password" => "required"
            ];
              //Custome message for validation on array data
            $Message = [
                'users.*.name.required' => 'Name field is required',
                'users.*.email.email' => 'Please enter valid email Address',
                'users.*.email.required' => 'Emial field is required',
                'users.*.email.unique' => 'This email address already exist in Db',
                'users.*.password.required' => 'password field is must requried'
           ];

            $validator = Validator::make($userData,$rules,$Message);
            if($validator->fails()){
                return response()->json($validator->errors(),422);
            }


                $userData['password'] = bcrypt($userData['password']);
            foreach($userData['users'] as $key => $value){
                $users = User::insert($value);
            }
         

            return response()->json(["message" => "users data inserted successfully"],201);
        }
    }


    public function update_users_data(Request $request,$id){
        if($request->isMethod('put')){
            $userData = $request->input();

            //Validation for single update
            $rules = [
                "name" => "required",
                "email" => "required|email|unique:users",
                "password" => "required"
            ];

            //Custome Messgae for single update validation
            $customeMessage = [
                'name.required' => 'Name field is required',
             //    'name.regex' => 'Only characters are allowed',
                'email.email' => 'Please enter valid email Address',
                'email.required' => 'Emial field is required',
                'email.unique' => 'This email address already exist in Db',
                'password.required' => 'password field is requried'
           ];
         $validator = Validator::make($userData,$rules,$customeMessage);
         if($validator->fails()){
            return response()->json($validator->errors(),422);
         } 

            // dd($userData);
            User::where('id',$id)
            ->update([
                 'name' => $userData['name'],
                 'email' => $userData['email'],
                 'password' => bcrypt($userData['password'])
            ]);
            return response()->json(["message" => "user updated successfully!"],202);
        }
    }

    public function update_single_user_record(Request $request,$id){
        if($request->isMethod('patch')){
            $userData = $request->input();
            // dd($userData);
            User::where('id',$id)
            ->update([
                'name' => $userData['name']
            ]);
            return response()->json(["message" => "user updated successfully!"],202);
        }
    }

    public function deleteSingleUser(Request $request){
      if($request->isMethod('delete')){
        $userData = $request->all();
        User::where('id',$userData['id'])->delete();
        return response()->json(['message' => 'delete user successfully!'],202);
      }
    }
    public function deleteMultipleUsers($ids){
        $ids = explode(",",$ids);
        User::whereIn('id',$ids)->delete();
        return response()->json(['message' => 'delete users successfully'],202);
    }
    public function deleteMultipleUsersWithJson(Request $request){
        $userData = $request->all();
        // dd($userData['ids']);
        User::whereIn('id',$userData['ids'])->delete();
        return response()->json(['message' => 'user deleted successfully']);
          
    }
}
