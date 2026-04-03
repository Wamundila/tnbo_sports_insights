<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class GettingStartedController extends Controller
{
    public function index(): View
    {
        return view('admin.getting-started');
    }
}
