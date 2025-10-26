@extends('layouts.app')

@section('title', 'Display Poli')

@section('content')
<!-- BARIS ATAS -->
<section class="grid grid-cols-2 gap-6">
    <!-- Antrian utama (Poli) -->
    <div class="panel-main p-4 flex flex-col justify-center items-center">
        <h2 class="tv-subtitle text-gray-600 mb-2 font-bold">
            POLI {{ strtoupper($polyName ?? 'POLI') }}
        </h2>
        <div id="current-queue" class="tv-number text-green-600 mb-2">-</div>
        <p class="text-gray-500">Silakan Menuju Ke Ruang Poli</p>
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
    <div class="col-span-2 grid grid-cols-2 gap-6">
        <!-- Jadwal Poli -->
        <div class="panel-secondary flex flex-col justify-center items-center text-center">
            <h3 class="tv-subtitle font-semibold mb-1">JADWAL POLI</h3>
            <div id="schedule-time" class="text-green-600 text-4xl font-bold">
                {{ $openTime ?? '-' }} - {{ $closeTime ?? '-' }}
            </div>
            <p class="text-gray-500 text-sm">Jam pelayanan hari ini</p>
        </div>

        <!-- Antrian Berikutnya -->
        <div class="panel-secondary flex flex-col justify-center items-center text-center">
            <h3 class="tv-subtitle font-semibold mb-1">ANTRIAN BERIKUTNYA</h3>
            <div id="next-queue" class="text-4xl font-bold text-green-600">-</div>
            <p class="text-gray-500 text-sm">Nomor antrian setelah saat ini</p>
        </div>
    </div>

    <!-- Kolom Informasi -->
    <div class="panel-secondary flex flex-col col-span-2 justify-center items-center text-center">
        <h3 class="tv-subtitle font-semibold mb-1">INFORMASI TAMBAHAN</h3>
        <p class="text-gray-500 text-sm">Tempat menaruh info umum, pengumuman, dsb.</p>
    </div>
</section>
@endsection

@push('scripts')
<script>
jQuery(function ($) {
    const polyId = "{{ $polyId ?? 1 }}";

    Echo.channel('queue-poly-' + polyId)
    .listen('.queue.poly.called', (data) => {
        $('#current-queue').text(data.number);
    });

    function refreshDisplay() {
        $.getJSON("{{ route('ajax.poli-queue') }}?poly_id=" + polyId, function (data) {
            // Update judul POLI
            const title = document.querySelector('.tv-subtitle');
            if (title) title.textContent = 'POLI ' + (data.polyName ?? 'POLI');

            // Update jam buka/tutup
            $('#schedule-time').text(`${data.openTime ?? '-'} - ${data.closeTime ?? '-'}`);

            // Update teks berjalan
            $('#running-text-display').text(data.marquee ?? '');

            // Update video YouTube jika berubah
            const iframe = $('#display-video-frame');
            if (iframe.length && data.youtubeId) {
                const newSrc = `https://www.youtube.com/embed/${data.youtubeId}?autoplay=1&loop=1&playlist=${data.youtubeId}&mute=1`;
                if (iframe.attr('src') !== newSrc) {
                    iframe.attr('src', newSrc);
                }
            }
        }).fail(function () {
            console.warn('Gagal memuat data dari endpoint poli.');
        });
    }

    // Jalankan pertama kali
    refreshDisplay();

    // Jalankan ulang setiap 5 detik
    setInterval(refreshDisplay, 5000);
});
</script>
@endpush
