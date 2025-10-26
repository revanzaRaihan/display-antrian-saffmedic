<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian - SaffMedic</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <!-- AOS (Animate On Scroll) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Poppins", sans-serif;
            min-height: 100vh;
            overflow: hidden;
            background: linear-gradient(135deg, #e3f2fd, #f8f9fa);
            position: relative;
        }

        /* Particles background */
        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        /* Intro Screen */
        #intro-screen {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #18A37E, #0e7b5f);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            transition: opacity 1s ease;
        }

        #intro-screen.fade-out {
            opacity: 0;
            pointer-events: none;
        }

        .intro-logo {
            font-size: 4rem;
            animation: fadeInUp 1.5s ease forwards;
        }

        .intro-text {
            opacity: 0;
            animation: fadeInUp 1.5s ease 0.8s forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Main content */
        .hero-title {
            color: #18A37E;
            animation: fadeInDown 1s ease;
        }

        .option-card {
            opacity: 0;
            transform: translateY(40px) scale(0.95);
            transition: all 0.6s ease;
            border-radius: 1.25rem;
            background: #ffffff;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        .option-card.show {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .option-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 35px rgba(24, 163, 126, 0.25);
        }

        .option-card i {
            transition: transform 0.4s ease;
        }

        .option-card:hover i {
            transform: rotate(10deg) scale(1.1);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .text-primary {
            color: #18A37E !important;
        }

        .border-primary {
            border-color: #18A37E !important;
        }

        .border-success {
            border-color: #18A37E !important;
        }

        .text-success {
            color: #18A37E !important;
        }

        .btn-primary {
            background-color: #18A37E;
            border-color: #18A37E;
        }

        .btn-primary:hover {
            background-color: #14936e;
            border-color: #14936e;
        }
    </style>
</head>

<body>
    <!-- Latar Partikel -->
    <div id="particles-js"></div>

    <!-- INTRO -->
    <div id="intro-screen">
        <div class="intro-logo fw-bold mb-3"><i class="fas fa-hospital-symbol"></i></div>
        <h1 class="intro-text fw-bold">SaffMedic Queue Display</h1>
    </div>

    <!-- KONTEN UTAMA -->
    <div class="container py-5 text-center" id="main-content" style="opacity: 0;">
        <h1 class="hero-title fw-bold mb-3" data-aos="fade-down">
            Display Antrian SaffMedic
        </h1>
        <p class="text-secondary mb-5" data-aos="fade-up" data-aos-delay="200">
            Pilih kategori antrian untuk menampilkan layar tampilan pasien, pembayaran, atau farmasi.
        </p>

        <div class="row g-4 justify-content-center">
            <!-- Antrian Pasien -->
            <div class="col-10 col-md-4" data-aos="zoom-in" data-aos-delay="200">
                <div class="option-card py-5 border-top border-5 border-primary"
                    data-delay="0"
                    data-link="{{ url('/display') }}">
                    <i class="fas fa-user-md fa-4x text-primary mb-3"></i>
                    <h5 class="fw-semibold text-dark">Antrian Pendaftaran</h5>
                    <p class="text-muted small mb-0">Lihat urutan pasien di ruang poli.</p>
                </div>
            </div>

            <!-- Antrian Pembayaran -->
            <div class="col-10 col-md-4" data-aos="zoom-in" data-aos-delay="400">
                <div class="option-card py-5 border-top border-5 border-success"
                    data-link="{{ url('/display/payment') }}">
                    <i class="fas fa-cash-register fa-4x text-success mb-3"></i>
                    <h5 class="fw-semibold text-dark">Antrian Pembayaran</h5>
                    <p class="text-muted small mb-0">Pantau giliran pasien di kasir.</p>
                </div>
            </div>

            <!-- Antrian Farmasi -->
            <div class="col-10 col-md-4" data-aos="zoom-in" data-aos-delay="600">
                <div class="option-card py-5 border-top border-5 border-success"
                    data-link="{{ url('/display/pharmacy') }}">
                    <i class="fas fa-pills fa-4x text-success mb-3"></i>
                    <h5 class="fw-semibold text-dark">Antrian Farmasi</h5>
                    <p class="text-muted small mb-0">Lihat progres pengambilan obat pasien.</p>
                </div>
            </div>

            <!-- Loop Poli -->
            @foreach ($polies as $index => $poly)
            <div class="col-10 col-md-4" data-aos="zoom-in" data-aos-delay="{{ 800 + ($index * 100) }}">
                <div class="option-card py-5"
                    data-delay="{{ $index * 200 }}"
                    data-link="{{ url('/display/poly/' . $poly['id']) }}"
                    style="border-top: 5px solid #18A37E;">
                    <i class="fas fa-clinic-medical fa-4x mb-3" style="color: #18A37E;"></i>
                    <h5 class="fw-semibold text-dark">{{ $poly['name'] }}</h5>
                    <p class="text-muted small mb-0">
                        Jam {{ substr($poly['open_time'], 0, 5) }} - {{ substr($poly['close_time'], 0, 5) }}
                    </p>
                </div>
            </div>
            @endforeach

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const intro = document.getElementById('intro-screen');
            const main = document.getElementById('main-content');
            const cards = document.querySelectorAll('.option-card');

            setTimeout(() => {
                intro.classList.add('fade-out');
                setTimeout(() => {
                    intro.style.display = 'none';
                    main.style.transition = 'opacity 1s ease';
                    main.style.opacity = 1;

                    cards.forEach(card => {
                        const delay = parseInt(card.dataset.delay) || 0;
                        setTimeout(() => card.classList.add('show'), delay);
                    });
                }, 1000);
            }, 2200);

            cards.forEach(card => {
                card.addEventListener('click', () => {
                    window.location.href = card.dataset.link;
                });
            });

            // Init AOS
            AOS.init({
                duration: 800,
                once: true
            });

            // Init Particles.js
            particlesJS('particles-js', {
                particles: {
                    number: {
                        value: 60
                    },
                    color: {
                        value: '#18A37E'
                    },
                    shape: {
                        type: 'circle'
                    },
                    opacity: {
                        value: 0.3
                    },
                    size: {
                        value: 3
                    },
                    line_linked: {
                        enable: true,
                        distance: 150,
                        color: '#18A37E',
                        opacity: 0.25,
                        width: 1
                    },
                    move: {
                        enable: true,
                        speed: 2,
                        direction: 'none',
                        out_mode: 'out'
                    }
                },
                interactivity: {
                    events: {
                        onhover: {
                            enable: true,
                            mode: 'grab'
                        },
                        onclick: {
                            enable: true,
                            mode: 'push'
                        }
                    },
                    modes: {
                        grab: {
                            distance: 140,
                            line_linked: {
                                opacity: 0.4
                            }
                        },
                        push: {
                            particles_nb: 4
                        }
                    }
                },
                retina_detect: true
            });
        });
    </script>
</body>

</html>