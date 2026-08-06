<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BlastRequest;
use App\Http\Resources\RecipientResource;
use App\Http\Resources\TemplateMiniResource;
use Inertia\Inertia;
use App\Models\Template;
use Illuminate\Support\Facades\DB;
use App\Services\SMSBlastService;
use App\Models\History;

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

        try 
        {
            $response = SMSBlastService::processBlastRequest($data, $request); 
            $result = SMSBlastService::resolveSendMode($response['blast'], $response['recipients'], $data);

            if ($result['success']) {
                DB::commit();
                return redirect()->route('app.blast.history')
                    ->with('success', 'SMS blast is being processed');
            } else {
                DB::rollBack();
                return back()->withErrors([
                    'message' => 'Failed to process SMS blast: ' . ($result['message'] ?? 'Unknown error'),
                ]);
            }

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()
                ->with('errors', 'Critical server error')
                ->withErrors(['message' => 'Something went wrong. Please try again later']);
        }
    }

    public function duplicateBlast(string $id)
    {
        $blast = History::findByHashId($id)->load('recipients');
        $template = Template::findOrFail($blast->template_id);
        $recipients = $blast->recipients()->with('contact')->get();

        return Inertia::render('Blast/Main', [
            'messageTemplate' => TemplateMiniResource::make($template),
            'preSelectedRecipients' => $recipients->pluck('contact.hash_id')
        ]);
    }

    public function resend(string $id)
    {
        
    }
}
