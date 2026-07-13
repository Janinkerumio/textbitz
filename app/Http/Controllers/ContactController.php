<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Contacts/Main', [
            'tags' => Contact::allTags(),
            'hasData' => Contact::all()->count()
        ]);
    }

    public function load(Request $request)
    {
        $query = Contact::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('contact_name', 'like', "%{$request->search}%")
                    ->orWhere('phone_num', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('tags')) {
            $query->where(function ($q) use ($request) {
                foreach ((array) $request->tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        };

        return response()->json($query->latest()->paginate(10));
    }

    public function show(string $id)
    {
        try {
            $contact = Contact::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $contact
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found or does not exist'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
