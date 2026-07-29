<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CorporateInfo;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {

    }

    public function changeBusinessSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'business_name' => 'nullable|string|max:100',
                'sms_signature' => 'nullable|string|max:100'
            ]);

            CorporateInfo::updateOrCreate(
                ['user_id' => Auth::id()],
                $validated
            );

            return back()->with('success', 'Settings updated!');

        } catch (\Exception $e) {
            report($e);

            return back()->with('errors', 'Something went wrong');
        }
        
    }
}
