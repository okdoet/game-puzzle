<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Game2048Controller extends Controller
{
    public function index()
    {
        return view('customer.2048.index');
    }
}
