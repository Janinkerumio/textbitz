<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Template;

class TemplatesController extends Controller
{
    public function index()
    {
        return Inertia::render('Templates/Main', [
            'hasData' => Template::totalTemplatesCount()
        ]);
    }

    public function load(Request $request)
    {
        $query = Template::query();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('title', 'like', "%{$request->search}%")
                ->orWhere('category', 'like', "%{$request->search}%");
        });

        $data = $query->withCount('histories')
                    ->orderByDesc('histories_count')
                    ->paginate(20);

        return response()->json($data);
    }
}
