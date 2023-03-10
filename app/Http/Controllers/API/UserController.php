<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator as FacadesValidator;


class UserController extends BaseController
{
    public function register(Request $request)
    {
        $validator = FacadesValidator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.', 201);
    }

    public function login(Request $request)
    {

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User login successfully.', 200);
        } else {
            return $this->sendError('Invalid credentials.', ['error' => 'Email or password invalid']);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->sendResponse($request, 'User logged out successfully.', 200);
    }

    /*public function getLoggedUser()
    {
        $user = Auth::user();
        die(print_r($user->name));
    }*/
}
