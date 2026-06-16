<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MemoryMatchController extends Controller
{
    public function index()
    {
        return view('customer.memory-match.index');
    }
}
