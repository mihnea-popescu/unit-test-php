<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function orders(User $user)
    {
        return $user->orders->load('items.product');
    }
}
