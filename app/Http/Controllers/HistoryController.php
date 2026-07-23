<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\History;

class HistoryController extends Controller
{
    public function index()
    {
        return Inertia::render('History/Main', [
            'stats' => History::stats(),
            'hasData' => History::isUserHasSavedHistory()
        ]);
    }

    public function load(Request $request)
    {
        $query = History::initiateQuery();

        $query->when($request->query('sort'), function ($q) use ($request) {
            $q->where('status', $request->query('sort'));
        });

        $query->when($request->query('date'), function ($q) use ($request) {
            $q->whereDate('last_sent_at', $request->query('date'));
        });

        $data = $query
                ->with('template:id,title')
                ->orderBy('last_sent_at', 'desc')
                ->paginate(10);

        return response()->json($data);
    }
}
