@extends('layouts.registration')

@section('title', 'Display Pendaftaran')

@section('content')
<section class="grid grid-cols-2 gap-6">
    <!-- KIRI: Antrian utama + bawahnya (Terlewat & Selanjutnya) -->
    <div class="flex flex-col">
        <!-- Antrian utama -->
        <div class="panel-main w-full">
            <h2 class="tv-subtitle text-gray-600 mb-2 text-center">ANTRIAN PENDAFTARAN SAAT INI</h2>
            <div id="registration-call" class="tv-number text-green-600 mb-2 text-center">{{ $currentQueue }}</div>
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
                            @forelse ($missedQueues as $index => $miss)
                            <tr class="bg-white rounded">
                                <td class="px-3 py-2 text-gray-800 font-semibold text-lg rounded">{{ $miss }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td class="px-3 py-3 text-gray-500 italic">Tidak ada antrian terlewat</td>
                            </tr>
                            @endforelse
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
                            {{ $currentQueue }}
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

@push('scripts')
<script>
    jQuery(function($) {
        let originalQueue = null;
        let timeoutHandle = null;
        let skipDisplayed = false;
        let lastSkippedNumber = null;

        function showTemporarySkippedNumber(skippedNumber) {
            const registrationEl = document.getElementById('registration-call');
            if (!registrationEl) return;

            const numberValue = (typeof skippedNumber === 'object') ?
                skippedNumber?.number ?? skippedNumber?.queue ?? '' :
                skippedNumber;

            if (!numberValue) return;

            if (originalQueue === null) originalQueue = registrationEl.textContent;
            registrationEl.textContent = numberValue;

            if (timeoutHandle) clearTimeout(timeoutHandle);

            timeoutHandle = setTimeout(() => {
                registrationEl.textContent = originalQueue;
                originalQueue = null;
            }, 5000);
        }

        let lastData = null;

        function updateQueue() {
            $.getJSON("{{ route('ajax.queue') }}?screen=registration", function(data) {
                // Jika data sama dengan sebelumnya, jangan render ulang
                if (JSON.stringify(data) === JSON.stringify(lastData)) return;
                lastData = data;

                const registrationEl = document.getElementById('registration-call');
                const nextEl = document.getElementById('next-number');

                if (originalQueue === null && registrationEl) {
                    registrationEl.textContent = data.currentQueue ?? '-';
                    if (nextEl) nextEl.textContent = (Number(data.currentQueue) + 1) ?? '-';
                }

                const pharmacyEl = document.getElementById('pharmacy-call');
                if (pharmacyEl && pharmacyEl.textContent !== String(data.pharmacyCall ?? '-'))
                    pharmacyEl.textContent = data.pharmacyCall ?? '-';

                const billingEl = document.getElementById('billing-call');
                if (billingEl && billingEl.textContent !== String(data.billingCall ?? '-'))
                    billingEl.textContent = data.billingCall ?? '-';

                // Render missed queues hanya jika berubah
                const missedTable = document.querySelector('#missed-table tbody');
                if (missedTable) {
                    const currentMissed = (data.missedQueues || []).join(',');
                    const lastMissed = (lastData?.missedQueues || []).join(',');
                    if (currentMissed !== lastMissed) {
                        if (data.missedQueues && data.missedQueues.length > 0) {
                            missedTable.innerHTML = data.missedQueues.map(num => `
                        <tr class="bg-white rounded">
                            <td class="px-3 py-2 text-gray-800 font-semibold text-lg rounded">${num}</td>
                        </tr>
                    `).join('');
                        } else {
                            missedTable.innerHTML = `
                        <tr>
                            <td class="px-3 py-3 text-gray-500 italic">Tidak ada antrian terlewat</td>
                        </tr>
                    `;
                        }
                    }
                }

                // Skip number logic tetap sama
                const currentSkipped = (typeof data.skippedNumber === 'object') ?
                    data.skippedNumber?.number ?? data.skippedNumber?.queue ?? null :
                    data.skippedNumber;

                if (currentSkipped && currentSkipped !== lastSkippedNumber) {
                    skipDisplayed = false;
                    lastSkippedNumber = currentSkipped;
                }

                if (!skipDisplayed && currentSkipped && currentSkipped !== '-') {
                    showTemporarySkippedNumber(currentSkipped);
                    skipDisplayed = true;
                }

                // Running text hanya update jika beda
                const marqueeEl = document.getElementById('running-text-display');
                if (marqueeEl && marqueeEl.textContent !== data.marquee)
                    marqueeEl.textContent = data.marquee ?? '';

                // Update video hanya jika ID berubah
                const videoFrame = document.getElementById('display-video-frame');
                if (videoFrame) {
                    const newYoutubeId = data.youtubeId;
                    const newSrc = newYoutubeId ? `https://www.youtube.com/embed/${newYoutubeId}?autoplay=1&loop=1&playlist=${newYoutubeId}&mute=1` : '';
                    if (videoFrame.src !== newSrc && newYoutubeId) videoFrame.src = newSrc;
                }
            });
        }

        updateQueue();
        setInterval(updateQueue, 5000);
    });
</script>
@endpush