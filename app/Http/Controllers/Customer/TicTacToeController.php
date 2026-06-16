<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

class TicTacToeController extends Controller
{
    public function index()
    {
        return view('customer.tictactoe.index');
    }
}
