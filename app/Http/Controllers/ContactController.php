<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Contacts/Main', [
            'tags' => Contact::allTags(),
            'hasData' => Contact::all()->count(),
            'newContact' => fn () => session('contact')
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

        return response()->json($query->latest()->paginate(20));
    }

    public function store(ContactRequest $request)
    {
        try {
            $contact = Contact::create([
                ...$request->validated(),
                'user_id' => Auth::id(),
            ]);

            return back()->withSuccess()->with('contact', $contact);

        } catch (\Exception $e) {
            report($e);

            return back()->withErrors(['contact_name' => 'Something went wrong while saving the contact']);
        }
    }

    public function show(string $id)
    {
        try {
            $contact = Contact::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $contact
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found or does not exist'
            ], 404);

        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong getting data'
            ], 500);
        }
    }

    public function update(ContactRequest $request, string $id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->update($request);

            return back()->with('success', 'Contact updated successfully.');

        } catch (ModelNotFoundException $e) {
            return back()->withErrors(['contact_name' => 'Contact not found.']);

        } catch (\Exception $e) {
            report($e);

            return back()->withErrors(['contact_name' => 'Something went wrong while saving. Please try again.']);
        }
    }
}
