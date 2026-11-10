@extends('layouts.app')

@section('title', 'MediSphere — care, made more human')
@section('meta_description', 'Explore medical specialties, pharmacy support, psychology and everyday wellbeing with MediSphere.')

@section('content')
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-copy">
                <span class="eyebrow"><span class="eyebrow-line"></span> A little more care in every step</span>
                <h1>Good health starts with <span>feeling heard.</span></h1>
                <p class="hero-description">Find thoughtful health information, explore care that fits your life, and feel more confident about what comes next.</p>
                <div class="hero-actions">
                    <a class="button" href="#services">Explore our care <span aria-hidden="true">→</span></a>
                    <a class="text-link" href="{{ route('about') }}">Get to know us <span aria-hidden="true">→</span></a>
                </div>
                <div class="hero-note">
                    <div class="avatar-stack" aria-hidden="true"><span>M</span><span>+</span><span>♡</span></div>
                    <p><strong>Here for your whole health</strong><br>From everyday questions to finding the right specialist.</p>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-image-wrap">
                    <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&amp;fit=crop&amp;w=1100&amp;q=85" alt="A doctor ready to listen and help" fetchpriority="high">
                    <div class="image-label"><span class="label-icon">♡</span><span><strong>Care that starts with you</strong><small>Explore at your own pace</small></span></div>
                </div>
                <div class="hero-sticker" aria-hidden="true"><span>your</span><strong>health<br>journey</strong><span>matters</span><i>✳</i></div>
                <div class="hero-sparkle sparkle-one" aria-hidden="true">✳</div>
                <div class="hero-sparkle sparkle-two" aria-hidden="true">✦</div>
            </div>
        </div>
    </section>

    <section class="trust-strip" aria-label="Our approach">
        <div class="container trust-inner">
            <span><i>✦</i> People-first care</span>
            <span><i>✦</i> Clear, helpful information</span>
            <span><i>✦</i> Support for every next step</span>
        </div>
    </section>

    <section class="section services-section" id="services">
        <div class="container">
            <div class="section-heading">
                <div>
                    <span class="eyebrow"><span class="eyebrow-line"></span> Find your kind of care</span>
                    <h2>Where would you<br><span>like to begin?</span></h2>
                </div>
                <p>Explore a specialty or service to find helpful information and a place to start.</p>
            </div>
            <div class="specialty-grid">
                @foreach ($specialties as $specialty)
                    <a class="specialty-card tone-{{ $specialty['tone'] }}" href="{{ $specialty['url'] }}">
                        <span class="specialty-icon" aria-hidden="true">{{ $specialty['icon'] }}</span>
                        <span class="specialty-title">{{ $specialty['title'] }}</span>
                        <span class="card-arrow" aria-hidden="true">→</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section care-section">
        <div class="container care-layout">
            <div class="care-art">
                <div class="care-art-circle circle-back"></div>
                <div class="care-art-circle circle-front"></div>
                <div class="care-art-copy"><span>one you.</span><strong>many ways<br>to feel well.</strong><i>✳</i></div>
                <div class="care-art-leaf leaf-one">✿</div>
                <div class="care-art-leaf leaf-two">✽</div>
            </div>
            <div class="care-copy">
                <span class="eyebrow"><span class="eyebrow-line"></span> More than a moment</span>
                <h2>Wellbeing is made<br>up of <span>little things.</span></h2>
                <p>Health is personal, and support can take many forms. Meet our psychologists, find everyday pharmacy guidance, or discover simple inspiration for nourishing meals.</p>
                <div class="service-list">
                    @foreach ($services as $service)
                        <a class="service-list-item" href="{{ $service['url'] }}">
                            <span class="service-list-icon tone-{{ $service['tone'] }}" aria-hidden="true">{{ $service['icon'] }}</span>
                            <span><strong>{{ $service['title'] }}</strong><small>{{ $service['eyebrow'] }}</small></span>
                            <span class="service-arrow" aria-hidden="true">→</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="mission-band">
        <div class="container mission-inner">
            <span class="mission-mark" aria-hidden="true">“</span>
            <div>
                <span class="eyebrow eyebrow-light">Our promise to you</span>
                <h2>Health care should feel<br><em>human, clear and close.</em></h2>
            </div>
            <a class="button button-light" href="{{ route('about.show', ['topic' => 'mission']) }}">What guides us <span aria-hidden="true">→</span></a>
        </div>
    </section>

    <section class="section final-cta">
        <div class="container final-cta-inner">
            <div>
                <span class="eyebrow"><span class="eyebrow-line"></span> Your next step can start here</span>
                <h2>Curious about a<br><span>specialty or service?</span></h2>
            </div>
            <a class="button" href="#services">Find the care you need <span aria-hidden="true">→</span></a>
        </div>
    </section>
@endsection
