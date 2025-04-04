<?php
session_start();

// Redirect logged in users to dashboard
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Gestor de Cotizaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="/Cotizaciones/public/css/style.css">
</head>
<body class="index-body">
    <header class="header-index">
        <div class="container">
            <div class="header-content py-4">
                <h1 class="header-title text-center mb-4">
                    <i class="fas fa-file-invoice-dollar header-icon"></i>
                    Sistema Gestor de Cotizaciones
                </h1>
                <nav class="modern-nav">
                    <ul class="nav justify-content-center align-items-center flex-wrap">
                        <li class="nav-item">
                            <a href="auth/login.php" class="btn-light">
                                <span class="icon"><i class="fas fa-sign-in-alt"></i></span>
                                <span>Iniciar Sesión</span>
                            </a>
                        </li>
                        <li class="nav-item ms-3">
                            <a href="auth/register.php" class="btn-light">
                                <span class="icon"><i class="fas fa-user-plus"></i></span>
                                <span>Registrarse</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <main class="landing-main">
        <section class="hero-section section-container">
            <div class="container-fluid px-4 px-md-5">
                <div class="row align-items-center g-5">
                    <div class="col-12 col-lg-6" data-aos="fade-right">
                        <div class="text-center text-lg-start">
                            <h1 class="hero-title display-4 fw-bold mb-4">Gestión de Cotizaciones Simplificada</h1>
                            <p class="hero-text lead mb-4">Automatiza y optimiza el proceso de cotizaciones para tu negocio</p>
                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center justify-content-lg-start">
                                <a href="auth/register.php" class="btn btn-primary btn-lg px-4 gap-3">
                                    <i class="fas fa-rocket"></i> Comenzar Ahora
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6" data-aos="fade-left">
                        <img src="public/img/1_index.png" alt="Gestión-de-Cotizaciones-Simplificada" class="hero-image img-fluid">
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="features-section section-container">
            <div class="container px-4 px-md-5">
                <h2 class="section-title text-center display-5 mb-5" data-aos="fade-up">Características Principales</h2>
                <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">
                    <div class="col" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-card h-100">
                            <i class="fas fa-users feature-icon"></i>
                            <h3 class="h4 mt-4">Gestión de Clientes</h3>
                            <p class="mt-3">Administra tu base de clientes de forma eficiente y mantén un registro detallado.</p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-card h-100">
                            <i class="fas fa-box-open feature-icon"></i>
                            <h3 class="h4 mt-4">Catálogo de Productos</h3>
                            <p class="mt-3">Mantén actualizado tu inventario y precios en un solo lugar.</p>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-card h-100">
                            <i class="fas fa-file-invoice-dollar feature-icon"></i>
                            <h3 class="h4 mt-4">Cotizaciones Profesionales</h3>
                            <p class="mt-3">Genera cotizaciones personalizadas en segundos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="carousel-section section-container">
            <div class="container px-4 px-md-5">
                <h2 class="section-title text-center display-5 mb-5" data-aos="fade-up">Vista Previa</h2>
                <div class="swiper carousel-slider" data-aos="fade-up">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="ratio ratio-16x9">
                                <img src="public/img/carousel1.png" alt="Imagen 1" class="carousel-image object-fit-cover">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="ratio ratio-16x9">
                                <img src="/public/img/carousel2.jpg" alt="Imagen 2" class="carousel-image object-fit-cover">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="ratio ratio-16x9">
                                <img src="public/img/carousel3.jpg" alt="Imagen 3" class="carousel-image object-fit-cover">
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="ratio ratio-16x9">
                                <img src="public/img/carousel4.jpg" alt="Imagen 4" class="carousel-image object-fit-cover">
                            </div>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>

        <section class="cta-section section-container">
            <div class="container px-4 px-md-5">
                <div class="cta-content text-center py-5" data-aos="zoom-in">
                    <h2 class="display-5 mb-4">
                        <i class="fas fa-check-circle me-2"></i> ¿Listo para empezar?
                    </h2>
                    <p class="lead mb-4">
                        <i class="fas fa-users me-2"></i> Únete a nuestra comunidad y aprovecha todas las ventajas de este gran sistema
                    </p>
                    <a href="auth/register.php" class="btn btn-lg btn-primary cta-button px-5 py-3">
                        <i class="fas fa-paper-plane me-2"></i> Registrarse Gratis
                    </a>
                </div>
            </div>
        </section>
    </main>
    <?php include 'layout/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });

        const swiper = new Swiper('.carousel-slider', {
            slidesPerView: 1,
            spaceBetween: 30,
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            },
            breakpoints: {
                768: {
                    slidesPerView: 2
                },
                1024: {
                    slidesPerView: 3
                }
            }
        });
    </script>
</body>
</html>
