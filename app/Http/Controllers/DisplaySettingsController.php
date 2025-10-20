<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\DisplaySetting;

class DisplaySettingsController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'running_text' => 'nullable|string',
            'video_link'   => 'nullable|url',
        ]);

        try {
            DisplaySetting::updateOrCreate(
                ['type' => 'running_text'],
                ['value' => $request->input('running_text', '')]
            );

            DisplaySetting::updateOrCreate(
                ['type' => 'video_link'],
                ['value' => $request->input('video_link', '')]
            );

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Gagal simpan setting: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function get()
    {
        try {
            $runningText = DisplaySetting::where('type', 'running_text')->value('value') ?? '';
            $videoLink = DisplaySetting::where('type', 'video_link')->value('value') ?? '';

            return response()->json([
                'status'        => 'success',
                'running_text'  => $runningText,
                'video_link'    => $videoLink
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal ambil setting: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
