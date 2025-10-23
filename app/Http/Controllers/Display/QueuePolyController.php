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
            'timeout' => 5
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
                'allow_redirects' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal login ke API: ' . $e->getMessage());
        }
    }
    // FETCH DATA ANTRIAN POLI
    protected function fetchQueueByPoly($polyId)
    {
        try {
            $today = now('Asia/Jakarta')->toDateString();

            $response = $this->client->get('/api/poli-queues', [
                'query' => ['poly_id' => $polyId],
                'headers' => ['Accept' => 'application/json']
            ]);

            $data = json_decode($response->getBody(), true);

            $filtered = collect($data)
                ->filter(fn($q) => isset($q['in_date']) && str_starts_with($q['in_date'], $today))
                ->sortByDesc(fn($q) => strtotime($q['in_date']))
                ->values();

            return $filtered->map(fn($q) => [
                'poly_id' => $q['poly_id'] ?? null,
                'poli_name' => $q['poli_name'] ?? null,
                'reference_queue' => $q['reference_queue'] ?? null,
                'open_time' => $q['open_time'] ?? null,
                'close_time' => $q['close_time'] ?? null,
            ])->toArray();
        } catch (\Exception $e) {
            Log::error("Gagal fetch antrian poli $polyId: " . $e->getMessage());
            return [];
        }
    }

    //FETCH DISPLAY SETTINGS (RUNNING TEXT & VIDEO)
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
            'youtubeId' => $youtubeId
        ];
    }

    protected function extractYoutubeId($youtubeLink)
    {
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([\w\-]+)/', $youtubeLink, $matches)) {
            return $matches[1];
        }
        return null;
    }


    // TAMPILAN DISPLAY POLI
    public function show($polyId = 1)
{
    $polyId = $polyId ?: 1;

    $this->loginApi();
    $queues = $this->fetchQueueByPoly($polyId);
    $queues = array_values($queues);

    $displaySettings = $this->fetchDisplaySettings();

    // Ambil currentQueue dari API current-call/poli
    $currentQueueNumber = null;
    try {
        $response = $this->client->get("/api/current-call/poli", [
            'query' => ['poly_id' => $polyId],
            'headers' => ['Accept' => 'application/json']
        ]);
        $data = json_decode($response->getBody(), true);
        if (isset($data['number']) && $data['number']) {
            $currentQueueNumber = $data['number'];
        }
    } catch (\Exception $e) {
        Log::error("Gagal ambil current queue untuk poly_id $polyId: " . $e->getMessage());
    }

    // Ambil queue berikutnya (tetap dari fetchQueueByPoly)
    $nextQueue = null;
    if (count($queues) > 1) {
        $nextQueue = $queues[1]['reference_queue'] ?? null;
    }

    // Ambil jam buka & tutup (format HH:MM)
    $openTime = isset($queues[0]['open_time']) ? substr($queues[0]['open_time'], 0, 5) : '-';
    $closeTime = isset($queues[0]['close_time']) ? substr($queues[0]['close_time'], 0, 5) : '-';

    return view('display.poly.displayPoly', [
        'queues' => $queues,
        'polyId' => $polyId,
        'polyName' => $queues[0]['poli_name'] ?? 'Poli Tidak Dikenal',
        'currentQueue' => $currentQueueNumber ?? ($queues[0]['reference_queue'] ?? '-'),
        'nextQueue' => $nextQueue ?? '-',
        'openTime' => $openTime,
        'closeTime' => $closeTime,
        'marquee' => $displaySettings['marquee'],
        'youtubeId' => $displaySettings['youtubeId'],
    ]);
}

    // ENDPOINT UNTUK AJAX REFRESH POLI
    public function ajaxQueue(Request $request)
    {
        $polyId = $request->get('poly_id');

        $this->loginApi();
        $queues = $this->fetchQueueByPoly($polyId);
        $queues = array_values($queues);

        $displaySettings = $this->fetchDisplaySettings();

        $current = $queues[0] ?? null;
        $nextQueue = $queues[1]['reference_queue'] ?? '-';
        $openTime = isset($current['open_time']) ? substr($current['open_time'], 0, 5) : '-';
        $closeTime = isset($current['close_time']) ? substr($current['close_time'], 0, 5) : '-';

        return response()->json([
            'polyName' => $current['poli_name'] ?? 'Poli Tidak Dikenal',
            'currentQueue' => $current['reference_queue'] ?? '-',
            'nextQueue' => $nextQueue,
            'openTime' => $openTime,
            'closeTime' => $closeTime,
            'marquee' => $displaySettings['marquee'],
            'youtubeId' => $displaySettings['youtubeId'],
        ]);
    }
}
