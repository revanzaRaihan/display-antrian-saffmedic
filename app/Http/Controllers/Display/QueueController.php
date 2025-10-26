<?php

namespace App\Http\Controllers\Display;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\DisplaySetting;

class QueueController extends Controller
{
    protected $client;
    protected $baseUri;

    public function __construct()
    {
        $this->baseUri = config('displayqueue.base_url');

        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'cookies' => true
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
                'timeout' => 5,
                'allow_redirects' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal login ke API: ' . $e->getMessage());
        }
    }

    protected function fetchDisplaySettings($screenType)
    {
        $runningText = DisplaySetting::forScreen($screenType)
            ->where('type', 'running_text')
            ->value('value') ?? 'Selamat datang di layanan Saffmedic.';

        $videoLink = DisplaySetting::forScreen($screenType)
            ->where('type', 'video_link')
            ->value('value') ?? null;

        $youtubeId = $videoLink ? $this->extractYoutubeId($videoLink) : null;

        return [
            'marquee'   => $runningText,
            'youtubeId' => $youtubeId
        ];
    }

    protected function fetchQueueData()
    {
        return Cache::remember('queue_data_cache', 5, function () {
            $missedQueues = [];

            try {
                $response = $this->client->get('/ajax/antrian/queue', [
                    'headers' => ['Accept' => 'application/json'],
                    'timeout' => 5
                ]);
                $data = json_decode($response->getBody(), true);

                $missedQueues = $data['number_skip'] ?? [];
            } catch (\Exception $e) {
                Log::error('Gagal ambil antrian: ' . $e->getMessage());
            }

            return [
                'missedQueues' => $missedQueues,
            ];
        });
    }

    protected function extractYoutubeId($youtubeLink)
    {
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([\w\-]+)/', $youtubeLink, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function index()
    {
        $this->loginApi();
        $queueData = $this->fetchQueueData();
        $displaySettings = $this->fetchDisplaySettings('registration');

        return view('display/registration/display', array_merge($queueData, $displaySettings));
    }

    public function payment()
    {
        $queueData = $this->fetchQueueData();
        $displaySettings = $this->fetchDisplaySettings('payment');

        return view('display/payment/displayPayment', array_merge($queueData, $displaySettings));
    }

    public function pharmacy()
    {
        $queueData = $this->fetchQueueData();
        $displaySettings = $this->fetchDisplaySettings('pharmacy');

        return view('display/pharmacy/displayPharmacy', array_merge($queueData, $displaySettings));
    }

    public function ajaxQueue(Request $request)
    {
        $screenType = $request->get('screen', 'registration');
        $this->loginApi();
        $queueData = $this->fetchQueueData();
        $displaySettings = $this->fetchDisplaySettings($screenType);

        return response()->json(array_merge($queueData, $displaySettings));
    }
}
