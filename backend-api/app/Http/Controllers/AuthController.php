<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'refresh']]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::guard('api')->login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.string' => 'กรุณากรอกอีเมลเป็นตัวอักษร',
            'password.string' => 'กรุณากรอกรหัสผ่านเป็นตัวอักษร',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::guard('api')->attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::guard('api')->user();
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }


    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'authorisation' => [
                'token' => Auth::guard('api')->refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function me()
    {
        $array = [
            "id"=>Auth::user()->id,
            "email"=>Auth::user()->email,
            "name"=>Auth::user()->name,
            "image"=>url(Auth::user()->image),
            "created_at"=>Auth::user()->created_at->thaidate('j F Y เวลา H:i').' น.',
            "updated_at"=>Auth::user()->updated_at->thaidate('j F Y เวลา H:i').' น.',
        ];
        return response()->json($array);
    }
}
