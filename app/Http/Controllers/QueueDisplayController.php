<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\DisplaySetting;


class QueueDisplayController extends Controller
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

    protected function fetchDisplaySettings()
    {
        $runningText = DisplaySetting::where('type', 'running_text')->value('value') ?? 'Selamat datang di layanan Saffmedic.';
        $videoLink   = DisplaySetting::where('type', 'video_link')->value('value') ?? null;

        // Jika ada video link, ambil YouTube ID
        $youtubeId = $videoLink ? $this->extractYoutubeId($videoLink) : null;

        return [
            'marquee'   => $runningText,
            'youtubeId' => $youtubeId
        ];
    }

    protected function fetchQueueData()
    {
        $currentQueue = 0;
        $missedQueues = [];
        $pharmacyCall = '-';
        $billingCall = '-';

        try {
            $response = $this->client->get('/ajax/antrian/queue', [
                'headers' => ['Accept' => 'application/json'],
                'timeout' => 5
            ]);
            $data = json_decode($response->getBody(), true);

            $currentQueue = $data['number'] ?? 0;
            $missedQueues = $data['number_skip'] ?? [];

            $farmasi = $this->client->get('/api/current-call?type=call_to_apotik', [
                'headers' => ['Accept' => 'application/json'],
                'timeout' => 5
            ]);
            $pharmacyCall = json_decode($farmasi->getBody(), true)['number'] ?? '-';

            $payment = $this->client->get('/api/current-call?type=call_to_payment', [
                'headers' => ['Accept' => 'application/json'],
                'timeout' => 5
            ]);
            $billingCall = json_decode($payment->getBody(), true)['number'] ?? '-';
        } catch (\Exception $e) {
            Log::error('Gagal ambil antrian: ' . $e->getMessage());
        }

        return [
            'currentQueue' => $currentQueue,
            'missedQueues' => $missedQueues,
            'pharmacyCall' => $pharmacyCall,
            'billingCall' => $billingCall
        ];
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
        $displaySettings = $this->fetchDisplaySettings();

        return view('display', array_merge($queueData, $displaySettings));
    }

    public function payment()
    {
        $this->loginApi();
        $queueData = $this->fetchQueueData();
        $displaySettings = $this->fetchDisplaySettings();

        return view('displayPayment', array_merge($queueData, $displaySettings));
    }

    public function pharmacy()
    {
        $this->loginApi();
        $queueData = $this->fetchQueueData();
        $displaySettings = $this->fetchDisplaySettings();

        return view('displayPharmacy', array_merge($queueData, $displaySettings));
    }

    public function ajaxQueue()
    {
        $this->loginApi();
        $queueData = $this->fetchQueueData();
        $displaySettings = $this->fetchDisplaySettings();

        return response()->json(array_merge($queueData, $displaySettings));
    }
}
