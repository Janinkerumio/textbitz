<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\History;
use App\Models\Recipients;
use App\Models\Contact;
use App\Models\Template;
use App\Http\Resources\HistoryResource;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Home/Main', [
            'recipients' => Recipients::sent()->forUser()->count(),
            'history' => History::initiateQuery()->sent()->count(),
            'contacts' => Contact::userHasSavedContacts(),
            'templates' => Template::totalTemplatesCount(),
        ]);
    }

    public function historyDashboard()
    {
        $query = History::initiateQuery();

        $data = $query->orderBy('last_sent_at', 'desc')->get()->take(3);

        return HistoryResource::collection($data);
    }
}
