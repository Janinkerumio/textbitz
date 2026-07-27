<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TemplatesRequest;
use Inertia\Inertia;
use App\Models\Template;
use App\Http\Resources\TemplateResource;

class TemplatesController extends Controller
{
    public function index()
    {
        return Inertia::render('Templates/Main', [
            'hasData' => Template::totalTemplatesCount(),
            'categories' => Template::allCategories()
        ]);
    }

    public function load(Request $request)
    {
        $query = Template::query();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where(function ($sub) use ($request) {
                $sub->where('title', 'like', "%{$request->search}%")
                    ->orWhere('category', 'like', "%{$request->search}%");
            });
        });

        $data = $query->withCount('histories')
                    ->orderByDesc('histories_count')
                    ->paginate(20);

        return TemplateResource::collection($data);
    }

    public function store(TemplatesRequest $request)
    {
        try {
            Template::create([
                ...$request->validated(),
            ]);

            return back()->with('success', 'Template added successfully!');
                    
        } catch (\Exception $e) {
            report($e);

            return back()->withErrors(['message' => 'Something went wrong while saving']);
        }
    }

    public function getAndUseTemplate(string $id)
    {
        $template = Template::findByHashId($id);

        return Inertia::render('Blast/Main', [
            'messageTemplate' => $template->message
        ]);
    }
}
