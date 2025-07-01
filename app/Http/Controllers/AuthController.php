<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){
        dd($request->all());
    }
    public function registraion(Request $request){
        dd($request->all());
    }
}
