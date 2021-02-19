<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ReactPageController extends Controller
{
    /**
     * Returns default view with loaded React app
     *
     */
    public function index(): View
    {
        return view("app");
    }
}
