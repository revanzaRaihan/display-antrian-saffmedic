@extends('layouts.app')

@section('title', 'Display Farmasi')

@section('content')
<!-- BARIS ATAS -->
<section class="grid grid-cols-2 gap-6">
    <!-- Antrian utama (Farmasi) -->
    <div class="panel-main p-4 flex flex-col justify-center items-center">
        <h2 class="tv-subtitle text-gray-600 mb-2">ANTRIAN FARMASI SAAT INI</h2>
        <div class="tv-number text-green-600 mb-2">{{ $pharmacyCall }}</div>
        <p class="text-gray-500">Silakan Menuju Ke Loket Farmasi</p>
    </div>

    <!-- Video -->
    <div class="panel-main p-4">
        @if($youtubeId)
        <iframe
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
            <div id="pharmacy-call" class="tv-number text-green-600">{{ $currentQueue ?? '-' }}</div>
            <p class="text-gray-500 text-sm">Nomor antrian pendaftaran</p>
        </div>

        <!-- Tagihan / Payment -->
        <div class="panel-secondary flex flex-col justify-center items-center text-center">
            <h3 class="tv-subtitle font-semibold mb-1">PEMANGGILAN PEMBAYARAN</h3>
            <div id="billing-call" class="tv-number text-green-600">{{ $billingCall ?? '-' }}</div>
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
        function updateQueue() {
            $.getJSON("{{ route('ajax.queue') }}", function(data) {
                // Update antrian utama
                document.querySelector('.panel-main .tv-number').textContent = data.pharmacyCall;

                // Update pendaftaran & payment
                document.getElementById('pharmacy-call').textContent =
                    data.currentQueue !== null ? data.currentQueue : '-';
                document.getElementById('billing-call').textContent =
                    data.billingCall !== null ? data.billingCall : '-';
            });
        }
        updateQueue();
        setInterval(updateQueue, 5000);
    });
</script>
@endpush