@extends('layouts.registration')

@section('title', 'Display Pendaftaran')

@section('content')
<section class="grid grid-cols-2 gap-6">
    <!-- KIRI: Antrian utama + bawahnya (Terlewat & Selanjutnya) -->
    <div class="flex flex-col">
        <!-- Antrian utama -->
        <div class="panel-main w-full">
            <h2 class="tv-subtitle text-gray-600 mb-2 text-center">ANTRIAN PENDAFTARAN SAAT INI</h2>
            <div id="registration-call" class="tv-number text-green-600 mb-2 text-center">{{ $currentQueue ?? '-' }}</div>
            <p class="text-gray-500 text-center mb-2">Silakan Menuju Ke Loket Pendaftaran</p>
        </div>

        <!-- BAWAH: Terlewat dan Selanjutnya -->
        <div class="grid grid-cols-4">
            <!-- Kolom kiri: Antrian Terlewat -->
            <div class="col-span-3 panel-semi-secondary bg-green-100" id="skipped-queue">
                <h3 class="tv-sub-heading font-semibold mb-2 text-center">ANTRIAN TERLEWAT</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-center border-separate" style="border-spacing: 0 6px;" id="missed-table">
                        <tbody>
                            <tr>
                                <td class="px-3 py-3 text-gray-500 italic">Tidak ada antrian terlewat</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Kolom kanan: Antrian Selanjutnya -->
            <div class="panel-semi-secondary" id="next-queue">
                <h3 class="tv-sub-heading font-semibold mb-2 text-center">ANTRIAN SELANJUTNYA</h3>
                <div class="next-cards-container relative overflow-hidden">
                    <div class="next-cards flex space-x-2 transition-transform duration-500 justify-center items-center" id="next-cards">
                        <div id="next-number" class="next-card flex-shrink-0 w-32 h-20 bg-green-100 rounded-lg flex items-center justify-center text-2xl font-semibold text-green-800">
                            {{ $currentQueue ? $currentQueue + 1 : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KANAN: Video -->
    <div class="panel-main p-4 flex items-center justify-center">
        @if($youtubeId)
        <iframe
            id="display-video-frame"
            src="https://www.youtube.com/embed/{{ $youtubeId }}?autoplay=1&loop=1&playlist={{ $youtubeId }}&mute=1"
            frameborder="0"
            loading="lazy"
            allow="autoplay; fullscreen; encrypted-media"
            class="w-full h-full rounded-lg">
        </iframe>
        @else
        <p class="text-center text-gray-500">Video belum tersedia</p>
        @endif
    </div>
</section>

<!-- BARIS BAWAH -->
<section class="grid grid-cols-4 gap-6 mb-2">
    <!-- Kolom 1: Pendaftaran & Payment -->
    <div class="col-span-2 grid grid-cols-2 gap-6">
        <!-- Farmasi -->
        <div class="panel-secondary flex flex-col justify-center items-center text-center">
            <h3 class="tv-subtitle font-semibold mb-1">PEMANGGILAN FARMASI</h3>
            <div id="pharmacy-call" class="tv-number text-green-600">{{ $pharmacyCall ?? '-' }}</div>
            <p class="text-gray-500 text-sm">Nomor antrian farmasi</p>
        </div>

        <!-- Tagihan -->
        <div class="panel-secondary flex flex-col justify-center items-center text-center">
            <h3 class="tv-subtitle font-semibold mb-1">PEMANGGILAN TAGIHAN</h3>
            <div id="billing-call" class="tv-number text-green-600">{{ $billingCall ?? '-' }}</div>
            <p class="text-gray-500 text-sm">Nomor antrian pembayaran</p>
        </div>
    </div>

    <!-- Kolom 2: Informasi tambahan -->
    <div class="panel-secondary flex flex-col col-span-2 justify-center items-center text-center">
        <h3 class="tv-subtitle font-semibold mb-1">INFORMASI TAMBAHAN</h3>
        <p class="text-gray-500 text-sm">Tempat menaruh info umum, pengumuman, dsb.</p>
    </div>
</section>
@endsection

<!-- @push('scripts')
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script src="/js/echo.js"></script>
<script>
jQuery(function($) {
    let originalQueue = null;
    let timeoutHandle = null;
    let missedQueues = [];

    function showTemporarySkippedNumber(skippedNumber) {
        const registrationEl = document.getElementById('registration-call');
        if (!registrationEl) return;

        const numberValue = skippedNumber?.number ?? skippedNumber ?? '';
        if (!numberValue) return;

        if (originalQueue === null) originalQueue = registrationEl.textContent;
        registrationEl.textContent = numberValue;

        if (timeoutHandle) clearTimeout(timeoutHandle);

        timeoutHandle = setTimeout(() => {
            registrationEl.textContent = originalQueue;
            originalQueue = null;
        }, 5000); // tampilkan 5 detik
    }

    function updateMissedTable() {
        const missedTable = document.querySelector('#missed-table tbody');
        if (!missedTable) return;

        if (missedQueues.length === 0) {
            missedTable.innerHTML = `
                <tr>
                    <td class="px-3 py-3 text-gray-500 italic">Tidak ada antrian terlewat</td>
                </tr>
            `;
        } else {
            missedTable.innerHTML = missedQueues.map(num => `
                <tr class="bg-white rounded">
                    <td class="px-3 py-2 text-gray-800 font-semibold text-lg rounded">${num}</td>
                </tr>
            `).join('');
        }
    }

    // === WebSocket Listener ===
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: 'local',
        wsHost: window.location.hostname,
        wsPort: 6001,
        forceTLS: false,
        disableStats: true,
    });

    window.Echo.channel('queue-updates')
        .listen('QueueUpdated', (e) => {
            const data = e.queue;
            const regEl = document.getElementById('registration-call');
            const nextEl = document.getElementById('next-number');

            if (data.action === 'call') {
                if (regEl) regEl.textContent = data.number ?? '-';
                if (nextEl) nextEl.textContent = (parseInt(data.number) + 1) || '-';
            }

            if (data.action === 'call_again') {
                showTemporarySkippedNumber(data.number);
                if (!missedQueues.includes(data.number)) {
                    missedQueues.push(data.number);
                    updateMissedTable();
                }
            }
        });
});
</script>
@endpush -->
