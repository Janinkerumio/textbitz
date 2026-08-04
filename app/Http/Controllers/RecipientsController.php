<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipients;
use App\Models\History;
use App\Http\Resources\HistoryResource;
use App\Http\Resources\RecipientResource;
use Inertia\Inertia;
use Inertia\Response;

class RecipientsController extends Controller
{
    public function index(string $id): Response
    {
        $history = History::findByHashId($id)->load('template:id,title');

        return Inertia::render('Recipients/Main', [
            'history' => fn () => HistoryResource::make($history)
        ]);
    }

    public function loadByHistory(Request $request, string $history_id)
    {
        $history = History::findByHashId($history_id);

        $query = Recipients::query();

        $query->when($request->query('sort'), function ($q) use ($request) {
            $q->where('status', $request->query('sort'));
        });

        $recipients = $query->where('history_id', $history->id)
                        ->latest()
                        ->paginate(20);

        return RecipientResource::collection($recipients);
    }
}
