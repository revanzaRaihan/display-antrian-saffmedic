<?php

namespace App\Http\Controllers\Display;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\DisplaySetting;

class QueuePolyController extends Controller
{
    protected $client;
    protected $baseUri;

    public function __construct()
    {
        $this->baseUri = config('displayqueue.base_url');

        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'cookies' => true,
            'timeout' => 5,
        ]);
    }

    protected function loginApi()
    {
        try {
            $loginPage = $this->client->get('/login');
            $html = (string) $loginPage->getBody();

            preg_match('/name="_token" value="([^"]+)"/', $html, $matches);
            $csrfToken = $matches[1] ?? null;

            if (!$csrfToken) {
                throw new \Exception('CSRF token tidak ditemukan.');
            }

            $this->client->post('/login', [
                'form_params' => [
                    '_token' => $csrfToken,
                    'email' => 'administrator@gmail.com',
                    'password' => 'administrator',
                ],
                'allow_redirects' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal login ke API: ' . $e->getMessage());
        }
    }

    protected function fetchPolyList()
    {
        try {
            $response = $this->client->get('/api/poly-list', [
                'headers' => ['Accept' => 'application/json'],
            ]);

            $data = json_decode($response->getBody(), true);

            return collect($data)
                ->map(fn($poly) => [
                    'id' => $poly['id'] ?? null,
                    'name' => $poly['name'] ?? 'Poli Tidak Dikenal',
                    'open_time' => $poly['open_time'] ?? '-',
                    'close_time' => $poly['close_time'] ?? '-',
                    'color' => $poly['color'] ?? '#cccccc',
                    'status' => $poly['status'] ?? 'inactive',
                ])
                ->toArray();
        } catch (\Exception $e) {
            Log::error("Gagal fetch data outpatient poly: " . $e->getMessage());
            return [];
        }
    }

    protected function fetchDisplaySettings()
    {
        $runningText = DisplaySetting::forScreen('poly')
            ->where('type', 'running_text')
            ->value('value') ?? 'Selamat datang di layanan Poli Saffmedic.';

        $videoLink = DisplaySetting::forScreen('poly')
            ->where('type', 'video_link')
            ->value('value') ?? null;

        $youtubeId = $videoLink ? $this->extractYoutubeId($videoLink) : null;

        return [
            'marquee'   => $runningText,
            'youtubeId' => $youtubeId,
        ];
    }

    protected function extractYoutubeId($youtubeLink)
    {
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([\w\-]+)/', $youtubeLink, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function show($polyId = 1)
    {
        $this->loginApi();
        $polies = $this->fetchPolyList();
        $selectedPoly = collect($polies)->firstWhere('id', $polyId) ?? null;

        $displaySettings = $this->fetchDisplaySettings();

        return view('display.poly.displayPoly', [
            'polyId' => $polyId,
            'polyName' => $selectedPoly['name'] ?? 'Poli Tidak Dikenal',
            'openTime' => substr($selectedPoly['open_time'] ?? '-', 0, 5),
            'closeTime' => substr($selectedPoly['close_time'] ?? '-', 0, 5),
            'marquee' => $displaySettings['marquee'],
            'youtubeId' => $displaySettings['youtubeId'],
            'polies' => $polies,
        ]);
    }

    public function index()
    {
        $this->loginApi();
        $polies = $this->fetchPolyList();

        return view('home', [
            'polies' => $polies,
        ]);
    }


    public function ajaxQueue(Request $request)
    {
        $polyId = $request->get('poly_id');

        $this->loginApi();
        $polies = $this->fetchPolyList();
        $selectedPoly = collect($polies)->firstWhere('id', $polyId) ?? null;

        $displaySettings = $this->fetchDisplaySettings();

        return response()->json([
            'polyName' => $selectedPoly['name'] ?? 'Poli Tidak Dikenal',
            'openTime' => substr($selectedPoly['open_time'] ?? '-', 0, 5),
            'closeTime' => substr($selectedPoly['close_time'] ?? '-', 0, 5),
            'marquee' => $displaySettings['marquee'],
            'youtubeId' => $displaySettings['youtubeId'],
        ]);
    }
}
