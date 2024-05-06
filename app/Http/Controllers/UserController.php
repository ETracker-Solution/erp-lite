<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return 90;
    }
    public function create()
    {
        return view('user.create');
    }
}
