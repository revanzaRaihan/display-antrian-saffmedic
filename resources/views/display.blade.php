@extends('layouts.app')

@section('title', 'Display Pendaftaran')

@section('content')
<!-- BARIS ATAS -->
<section class="grid grid-cols-4 gap-6">
    <!-- Antrian utama -->
    <div class="col-span-2 panel-main">
        <h2 class="tv-subtitle text-gray-600 mb-2">ANTRIAN PENDAFTARAN SAAT INI</h2>
        <div class="tv-number text-green-600 mb-2">{{ $currentQueue }}</div>
        <p class="text-gray-500">Silakan Menuju Ke Loket Pendaftaran</p>
    </div>

    <!-- Video -->
    <div class="col-span-2 panel-main p-4">
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
<section class="grid grid-cols-4 gap-6">
    <!-- Antrian Terlewat -->
    <div class="col-span-2 panel-secondary">
        <div class="flex justify-between items-center mb-3">
            <h3 class="tv-subtitle font-semibold">ANTRIAN TERLEWAT</h3>
        </div>
        <div class="missed-cards-container relative overflow-hidden">
            <div class="missed-cards flex space-x-2 transition-transform duration-500">
                @forelse ($missedQueues as $miss)
                <div class="missed-card flex-shrink-0 w-32 h-20 bg-gray-100 rounded-lg flex items-center justify-center text-xl font-semibold text-gray-800">
                    {{ $miss }}
                </div>
                @empty
                <div class="missed-card flex-shrink-0 w-32 h-20 bg-gray-50 rounded-lg flex items-center justify-center text-gray-500 italic">
                    Tidak ada antrian terlewat
                </div>
                @endforelse
            </div>
        </div>
    </div>

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
</section>
@endsection

@push('scripts')
<script>
    jQuery(function($) {
        function updateQueue() {
            $.getJSON("{{ route('ajax.queue') }}", function(data) {
                // Update nomor antrian utama
                document.querySelector('.panel-main .tv-number').textContent = data.currentQueue;

                // Update antrian terlewat
                let missedHtml = '';
                if (data.missedQueues && data.missedQueues.length > 0) {
                    data.missedQueues.forEach(function(num) {
                        missedHtml += `<div class="missed-card flex-shrink-0 w-32 h-20 bg-gray-100 rounded-lg flex items-center justify-center text-xl font-semibold text-gray-800">${num}</div>`;
                    });
                } else {
                    missedHtml = `<div class="missed-card flex-shrink-0 w-32 h-20 bg-gray-50 rounded-lg flex items-center justify-center text-gray-500 italic">Tidak ada antrian terlewat</div>`;
                }
                document.querySelector('.missed-cards').innerHTML = missedHtml;

                // Update pharmacy & billing
                document.getElementById('pharmacy-call').textContent =
                    data.pharmacyCall !== null ? data.pharmacyCall : '-';
                document.getElementById('billing-call').textContent =
                    data.billingCall !== null ? data.billingCall : '-';

                // Update running text
                const marqueeElement = document.querySelector('#running-text-display');
                if (marqueeElement) {
                    marqueeElement.textContent = data.marquee ?? '';
                }
                // Update video jika ada
                const videoFrame = document.querySelector('#display-video-frame');
                if (videoFrame) {
                    const currentUrl = new URL(videoFrame.src);
                    const currentId = currentUrl.pathname.split('/').pop(); // ambil ID video saat ini
                    const newYoutubeId = data.youtubeId;

                    if (newYoutubeId) {
                        if (currentId !== newYoutubeId) {
                            const newSrc = `https://www.youtube.com/embed/${newYoutubeId}?autoplay=1&loop=1&playlist=${newYoutubeId}&mute=1`;
                            videoFrame.src = newSrc;
                        }
                    } else {
                        if (videoFrame.src !== '') {
                            videoFrame.src = '';
                        }
                    }
                }
            });
        }

        updateQueue();
        setInterval(updateQueue, 5000);
    });
</script>
@endpush