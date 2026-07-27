<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Template;

class BlastController extends Controller
{
    public function index()
    {
        return Inertia::render('Blast/Main', [
            'templates' => Template::mostUsed()
        ]);
    }
}
