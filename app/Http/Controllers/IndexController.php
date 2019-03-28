<?php

namespace App\Http\Controllers;

use App\Entities\User;

class IndexController extends Controller
{
    public function index()
    {
        $users = $this->em->getRepository(User::class)->findAll();

        return $this->view('index', [
            'users' => $users
        ]);
    }
}
