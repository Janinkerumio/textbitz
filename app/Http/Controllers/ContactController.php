<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\ContactRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ContactsResource;

class ContactController extends Controller
{
    public function index()
    {
        return Inertia::render('Contacts/Main', [
            'tags' => Contact::allTags(),
            'hasData' => Contact::userHasSavedContacts(),
        ]);
    }

    public function load(Request $request)
    {
        $query = Contact::initiateQuery();

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

        $data = $query->orderBy('contact_name')->paginate(20);
        
        return ContactsResource::collection($data);
    }

    public function store(ContactRequest $request)
    {
        try {
            $contact = Contact::create([
                ...$request->validated(),
                'user_id' => Auth::id(),
            ]);

            return back()
                    ->with('success', 'Contact saved successfully')
                    ->with('newContact', new ContactsResource($contact));

        } catch (\Exception $e) {
            report($e);

            return back()->withErrors(['contact_name' => 'Something went wrong while saving the contact']);
        }
    }

    public function show(string $id)
    {
        try {
            $contact = Contact::findByHashId($id);

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
            $contact = Contact::findByHashId($id);
            $contact->update($request->validated());

            return back()
                ->with('success', 'Contact updated successfully')
                ->with('contactUpdated', new ContactsResource($contact));

        } catch (ModelNotFoundException $e) {
            return back()->withErrors(['contact_name' => 'Contact not found.']);

        } catch (\Exception $e) {
            report($e);

            return back()->withErrors(['contact_name' => 'Something went wrong while saving. Please try again.']);
        }
    }
}
