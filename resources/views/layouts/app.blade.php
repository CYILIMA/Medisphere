<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f8f8f3">
    <meta name="description" content="@yield('meta_description', 'Explore thoughtful health information and care services with MediSphere.')">
    <title>@yield('title', 'MediSphere — care, made more human')</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <div class="announcement">
        <span class="announcement-dot" aria-hidden="true"></span>
        A more thoughtful way to find your next step in care
    </div>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="{{ route('home') }}" aria-label="MediSphere home">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 42 42" fill="none">
                        <path d="M21 5v32M5 21h32" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
                        <circle cx="21" cy="21" r="17" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </span>
                <span class="brand-name">medi<span>sphere</span></span>
            </a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" data-menu-toggle>
                <span></span><span></span>
                <span class="sr-only">Toggle navigation</span>
            </button>
            <nav class="primary-navigation" id="primary-navigation" aria-label="Main navigation">
                <details class="nav-dropdown">
                    <summary>Medicine <span class="chevron" aria-hidden="true"></span></summary>
                    <div class="dropdown-menu medicine-menu">
                        <span class="dropdown-heading">Explore specialties</span>
                        <div class="dropdown-grid">
                            @foreach (config('health.specialties') as $slug => $specialty)
                                <a href="{{ route('medicine.show', ['specialty' => $slug]) }}">{{ $specialty['title'] }}</a>
                            @endforeach
                        </div>
                    </div>
                </details>
                <a href="{{ route('service.show', ['service' => 'psychologists']) }}">Psychologists</a>
                <a href="{{ route('service.show', ['service' => 'pharmacy']) }}">Pharmacy</a>
                <a href="{{ route('service.show', ['service' => 'food-fruits']) }}">Food & fruits</a>
                <details class="nav-dropdown">
                    <summary>More <span class="chevron" aria-hidden="true"></span></summary>
                    <div class="dropdown-menu more-menu">
                        <a href="{{ route('about') }}">About us</a>
                        <a href="{{ route('about.show', ['topic' => 'mission']) }}">Our mission</a>
                        <a href="{{ route('about.show', ['topic' => 'vision']) }}">Our vision</a>
                    </div>
                </details>
            </nav>
            <a class="button button-small header-cta" href="{{ route('home') }}#services">
                Find care <span aria-hidden="true">→</span>
            </a>
        </div>
    </header>

    <main id="main-content">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container footer-main">
            <div class="footer-brand">
                <a class="brand brand-light" href="{{ route('home') }}">
                    <span class="brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 42 42" fill="none">
                            <path d="M21 5v32M5 21h32" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
                            <circle cx="21" cy="21" r="17" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </span>
                    <span class="brand-name">medi<span>sphere</span></span>
                </a>
                <p>Care, made more human. Find thoughtful health information and a clearer path to support.</p>
            </div>
            <div class="footer-links">
                <div>
                    <h2>Explore care</h2>
                    <a href="{{ route('medicine.show', ['specialty' => 'internal']) }}">Internal medicine</a>
                    <a href="{{ route('service.show', ['service' => 'psychologists']) }}">Psychologists</a>
                    <a href="{{ route('service.show', ['service' => 'pharmacy']) }}">Pharmacy</a>
                    <a href="{{ route('service.show', ['service' => 'food-fruits']) }}">Food & fruits</a>
                </div>
                <div>
                    <h2>Get to know us</h2>
                    <a href="{{ route('about') }}">About MediSphere</a>
                    <a href="{{ route('about.show', ['topic' => 'mission']) }}">Our mission</a>
                    <a href="{{ route('about.show', ['topic' => 'vision']) }}">Our vision</a>
                </div>
            </div>
        </div>
        <div class="container footer-bottom">
            <span>© {{ date('Y') }} MediSphere. Made with care.</span>
            <span>Health information is for learning and does not replace professional medical advice.</span>
        </div>
    </footer>
</body>
</html>
