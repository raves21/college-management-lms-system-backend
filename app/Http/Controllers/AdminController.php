<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Models\Admin;

class AdminController extends Controller
{
    public function index()
    {
        return AdminResource::collection(Admin::with('user')->paginate());
    }
}
