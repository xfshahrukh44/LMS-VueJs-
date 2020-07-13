<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class MainController extends Controller
{
    public function dashboard()
    {
        if(auth()->user() == NULL)
        {
            return redirect()->route('login');
        }
        if(auth()->user()->type == 'admin')
        {
            return redirect()->route('home');
        }
        if(auth()->user()->type == 'teacher' || auth()->user()->type == 'student' || auth()->user()->type == 'user')
        {
            return redirect()->route('profile');
        }

    }

    public function guest()
    {
        return view('home');
    }
}
