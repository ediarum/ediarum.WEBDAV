<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsersController extends Controller
{

    public function show(Request $request): View
    {
        return view('users', [
            'users' => User::orderBy('name')->get(),
        ]);
    }

    //
}
