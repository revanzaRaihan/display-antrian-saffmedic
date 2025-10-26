<?php

namespace App\Http\Controllers\Display;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\DisplaySetting;

class DisplaySettingsController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'screen_type' => 'required|string|in:registration,pharmacy,payment,poly',
            'type'        => 'required|in:running_text,video_link',
            'value'       => 'nullable|string',
        ]);

        $screenType = $request->input('screen_type');
        $type = $request->input('type');
        $value = $request->input('value');

        try {
            DisplaySetting::updateOrCreate(
                ['type' => $type, 'screen_type' => $screenType],
                ['value' => $value]
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
            // Ambil semua setting, lalu kelompokkan berdasarkan screen_type
            $allSettings = DisplaySetting::all()
                ->groupBy('screen_type')
                ->map(function ($items) {
                    return [
                        'running_text' => optional($items->firstWhere('type', 'running_text'))->value ?? '',
                        'video_link'   => optional($items->firstWhere('type', 'video_link'))->value ?? '',
                    ];
                });

            return response()->json([
                'status'   => 'success',
                'settings' => $allSettings
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal ambil setting: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
