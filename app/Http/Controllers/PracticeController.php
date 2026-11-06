<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PracticeController extends Controller
{
    /**
     * Display CLI setup and session instructions.
     */
    public function setup(): Response
    {
        return Inertia::render('setup/index');
    }
}
