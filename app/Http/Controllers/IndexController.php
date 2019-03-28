<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
    public function index()
    {
        return $this->view('index');
    }
}
