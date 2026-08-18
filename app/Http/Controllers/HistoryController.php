<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\History;
use App\Http\Resources\HistoryResource;

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
                ->latest()
                ->paginate(20);

        return HistoryResource::collection($data);
    }

    public function delete(Request $request, string $id)
    {
        $isRedirect = $request->query('redirect', false);

        try {
            $history = History::findByHashId($id);

            if (!$history) {
                return back()->with('error', 'Blast history not found.');
            }

            $history->delete();

            if($isRedirect)
            {
                return redirect()->route('app.blast.history')->with('success', 'Blast history removed');
            }

            return back()->with('success', 'Blast history removed');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Something went wrong while processing. Please try again.');
        }
    }

    public function show(Request $request)
    {

    }

    public function update(Request $request, string $uuid)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', [
                History::STATUS_FAILED,
                History::STATUS_SENT,
            ]),
        ]);

        $history = History::initiateQuery()->where('uuid', $uuid)->first();
        $history->update($validated);

        return response()->json(['success' => true]);
    }

    public function pendingBlasts()
    {
        $blasts = History::initiateQuery()
            ->whereNotIn('status', [
                History::STATUS_SENT,
                History::STATUS_FAILED,
                History::STATUS_CANCELLED,
            ])
            ->select('uuid')
            ->get();

        return response()->json([
            'blasts' => $blasts,
        ]);
    }
}
