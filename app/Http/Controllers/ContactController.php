<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query = Contact::query();

        // if ($request->filled('search')) {
        //     $query->where(function ($q) use ($request) {
        //         $q->where('contact_name', 'like', "%{$request->search}%")
        //             ->orWhere('phone_num', 'like', "%{$request->search}%");
        //     });
        // }

        // if ($request->filled('tags')) {
        //     $query->where(function ($q) use ($request) {
        //         foreach ((array) $request->tags as $tag) {
        //             $q->orWhereJsonContains('tags', $tag);
        //         }
        //     });
        // }

    //     $contacts = $query->latest()->paginate(10);

    //     return Inertia::render('Contacts/Main', [
    //         'contacts' => Inertia::scroll($contacts),
    //         'filters' => [
    //             'search' => $request->search,
    //             'tags' => $request->tags ?? [],
    //         ],
    //         'tags' => Contact::allTags(),
    //     ]);
    // }

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
}
