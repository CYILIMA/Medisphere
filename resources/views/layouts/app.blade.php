<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                @if ($siteLogoPath)
                    <img class="brand-logo" src="{{ asset('storage/'.$siteLogoPath) }}" alt="">
                @else
                    <span class="brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 42 42" fill="none">
                            <path d="M21 5v32M5 21h32" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
                            <circle cx="21" cy="21" r="17" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </span>
                @endif
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
                        <a href="{{ route('medicines.index') }}">Medicine library</a>
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

    <section class="ora-chat" aria-label="Chat with ORA">
        <div class="ora-panel" id="ora-panel" hidden>
            <div class="ora-panel-header">
                <div><span class="ora-name">ORA</span><span class="ora-status">Here to help you find your next step</span></div>
                <button class="ora-close" type="button" aria-label="Close ORA chat" data-ora-close>&times;</button>
            </div>
            <div class="ora-messages" aria-live="polite" data-ora-messages>
                <p class="ora-message ora-message-assistant">Hi, I’m ORA. Ask a quick question about MediSphere care, medicine information, wellbeing, or support.</p>
            </div>
            <form class="ora-form" data-ora-form>
                <label class="sr-only" for="ora-message">Your question</label>
                <input id="ora-message" name="message" type="text" maxlength="500" placeholder="Write your question..." autocomplete="off" required data-ora-input>
                <button type="submit">Send <span class="sr-only">your question to ORA</span></button>
            </form>
            <p class="ora-disclaimer">ORA shares general information, not medical advice. In an emergency, contact local emergency services.</p>
        </div>
        <button class="ora-trigger" type="button" aria-expanded="false" aria-controls="ora-panel" data-ora-trigger>
            <span class="ora-trigger-mark" aria-hidden="true">✦</span> Chat with ORA
        </button>
    </section>

    <footer class="site-footer">
        <div class="container footer-main">
            <div class="footer-brand">
                <a class="brand brand-light" href="{{ route('home') }}">
                    @if ($siteLogoPath)
                        <img class="brand-logo" src="{{ asset('storage/'.$siteLogoPath) }}" alt="">
                    @else
                        <span class="brand-mark" aria-hidden="true">
                            <svg viewBox="0 0 42 42" fill="none">
                                <path d="M21 5v32M5 21h32" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
                                <circle cx="21" cy="21" r="17" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </span>
                    @endif
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
