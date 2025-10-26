@extends('layouts.app')

@section('title', 'Display Farmasi')

@section('content')
<!-- BARIS ATAS -->
<section class="grid grid-cols-2 gap-6">
    <!-- Antrian utama (Farmasi) -->
    <div class="panel-main p-4 flex flex-col justify-center items-center">
        <h2 class="tv-subtitle text-gray-600 mb-2">ANTRIAN FARMASI SAAT INI</h2>
        <div id="pharmacy-call" class="tv-number text-green-600 mb-2">-</div>
        <p class="text-gray-500">Silakan Menuju Ke Loket Farmasi</p>
    </div>

    <!-- Video -->
    <div class="panel-main p-4">
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
<section class="grid grid-cols-4 gap-6 mt-6">
    <!-- Kolom 1: Pendaftaran & Payment -->
    <div class="col-span-2 grid grid-cols-2 gap-6">
        <!-- Pendaftaran -->
        <div class="panel-secondary flex flex-col justify-center items-center text-center">
            <h3 class="tv-subtitle font-semibold mb-1">PEMANGGILAN PENDAFTARAN</h3>
            <div id="registration-call" class="tv-number text-green-600">-</div>
            <p class="text-gray-500 text-sm">Nomor antrian pendaftaran</p>
        </div>

        <!-- Tagihan / Payment -->
        <div class="panel-secondary flex flex-col justify-center items-center text-center">
            <h3 class="tv-subtitle font-semibold mb-1">PEMANGGILAN PEMBAYARAN</h3>
            <div id="billing-call" class="tv-number text-green-600">-</div>
            <p class="text-gray-500 text-sm">Nomor antrian kasir / tagihan</p>
        </div>
    </div>

    <!-- Kolom 2: Informasi tambahan -->
    <div class="panel-secondary flex flex-col col-span-2 justify-center items-center text-center">
        <h3 class="tv-subtitle font-semibold mb-1">INFORMASI TAMBAHAN</h3>
        <p class="text-gray-500 text-sm">Tempat menaruh info umum, pengumuman, dsb.</p>
    </div>
</section>
@endsection

@push('scripts')
<script>
jQuery(function($) {
    // Variables
    let currentQueueNumber = 0;
    let tempMissedNumber = null;

    // Render queue display
    function renderQueue() {
        const currentQueueEl = document.getElementById('registration-call');
        const nextNumberEl = document.getElementById('next-number');

        if (currentQueueEl) currentQueueEl.textContent =
            (tempMissedNumber !== null) ? tempMissedNumber : currentQueueNumber;
    }

    // Normal queue event
    Echo.channel('queue-registration')
        .listen('.queue.registration.called', (data) => {
            tempMissedNumber = null;
            currentQueueNumber = Number(data.number) || 0;
            renderQueue();
        });

    // Skipped queue event
    Echo.channel('queue-skipped')
        .listen('.queue.skipped.called', (data) => {
            tempMissedNumber = Number(data.number) || null;
            renderQueue();
        });

    // Pharmacy queue
    Echo.channel('queue-pharmacy')
        .listen('.queue.pharmacy.called', (data) => {
            document.getElementById('pharmacy-call').textContent = data.number;
        });

    // Payment queue
    Echo.channel('queue-payment')
        .listen('.queue.payment.called', (data) => {
            document.getElementById('billing-call').textContent = data.number;
        });

    // Polling for table, video and running text
    function updateQueue() {
        $.getJSON("{{ route('ajax.queue') }}?screen=pharmacy", function(data) {

            if ((currentQueueNumber === 0 || currentQueueNumber == null) && data.currentQueue) {
                currentQueueNumber = Number(data.currentQueue) || 0;
                renderQueue();
            }

            // Missed queue table
            const missedTableBody = document.getElementById('missed-body');
            if (missedTableBody) {
                if (data.missedQueues && data.missedQueues.length > 0) {
                    missedTableBody.innerHTML = data.missedQueues.map(num => `
                        <tr class="bg-white rounded">
                            <td class="px-3 py-2 text-gray-800 font-semibold text-lg rounded">${num}</td>
                        </tr>
                    `).join('');
                } else {
                    missedTableBody.innerHTML = `
                        <tr>
                            <td class="px-3 py-3 text-gray-500 italic">No missed queue</td>
                        </tr>
                    `;
                }
            }

            // Running text
            const marqueeEl = document.getElementById('running-text-display');
            if (marqueeEl) marqueeEl.textContent = data.marquee ?? '';

            // Video
            const videoFrame = document.getElementById('display-video-frame');
            if (videoFrame) {
                const newYoutubeId = data.youtubeId;
                const newSrc = newYoutubeId
                    ? `https://www.youtube.com/embed/${newYoutubeId}?autoplay=1&loop=1&playlist=${newYoutubeId}&mute=1`
                    : '';

                if (videoFrame.src !== newSrc) videoFrame.src = newSrc;
            }

        }).fail(() => {});
    }

    renderQueue();
    updateQueue();
    setInterval(updateQueue, 5000);
});
</script>
@endpush