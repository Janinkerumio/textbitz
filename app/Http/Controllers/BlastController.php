<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BlastRequest;
use App\Http\Resources\TemplateMiniResource;
use Inertia\Inertia;
use App\Models\Template;
use Illuminate\Support\Facades\DB;
use App\Services\SMSBlastService;

class BlastController extends Controller
{
    public function index()
    {
        $templates = Template::mostUsed();

        return Inertia::render('Blast/Main', [
            'templates' => TemplateMiniResource::collection($templates)
        ]);
    }

    public function store(BlastRequest $request)
    {
        $data = $request->validated();

        DB::beginTransaction();

        try {
            $dateTimeEx = $data['scheduled_date'] . ' ' . $data['scheduled_time'];


        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return back()
                ->with('errors', 'Critical server error')
                ->withErrors(['message' => 'Something went wrong. Please try again later']);
        }
    }
}
