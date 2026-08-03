<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TemplatesRequest;
use App\Http\Resources\TemplateMiniResource;
use Inertia\Inertia;
use App\Models\Template;
use App\Http\Resources\TemplateResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            $template = Template::create([
                ...$request->validated(),
            ]);

            return back()->with([
                'success' => 'Template added successfully!',
                'newTemplate' => new TemplateResource($template)
            ]);
                    
        } catch (\Exception $e) {
            report($e);

            return back()->withErrors(['message' => 'Something went wrong while saving']);
        }
    }

    public function show (string $id)
    {
        try {
            $template = Template::findByHashId($id);

            return response()->json([
                'success' => true,
                'data' => $template
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Template not found or does not exist'
            ], 404);
            
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong getting data'
            ], 500);
        }
    }

    public function update(TemplatesRequest $request, string $id)
    {
        try {
            $template = Template::findByHashId($id);
            $template->update($request->validated());

            return back()
                ->with('success', 'Template updated successfully')
                ->with('templateUpdated', new TemplateResource($template));

        } catch (ModelNotFoundException $e) {
            return back()->withErrors(['title' => 'Template not found.']);

        } catch (\Exception $e) {
            report($e);

            return back()->withErrors(['title' => 'Something went wrong while saving. Please try again.']);
        }
    }

    public function delete(string $id)
    {
        try {
            Template::findByHashId($id)->delete();

            return back()->with('success', 'Template removed');
            
        } catch (ModelNotFoundException $e) {
            return back()->with('error', 'Template not found.');

        } catch (\Exception $e) {
            report($e);

            return back()->with('error', 'Something went wrong while processing. Please try again.');
        }
    }

    public function getAndUseTemplate(string $id)
    {
        $template = Template::findByHashId($id);

        return Inertia::render('Blast/Main', [
            'messageTemplate' => $template
        ]);
    }
}
