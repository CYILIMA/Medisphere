@extends('layouts.app')

@section('title', $page['title'].' — MediSphere')
@section('meta_description', $page['description'])

@section('content')
    <section class="page-hero">
        <div class="container page-hero-grid">
            <div class="page-hero-copy">
                <a class="back-link" href="{{ route('home') }}#services"><span aria-hidden="true">←</span> Explore all care</a>
                <span class="eyebrow"><span class="eyebrow-line"></span> {{ $page['group'] }}</span>
                <h1>{{ $page['title'] }}<span class="title-period">.</span></h1>
                <p>{{ $page['description'] }}</p>
                <a class="button" href="{{ isset($medicinePages) ? '#medicine-information' : '#learn-more' }}">{{ isset($medicinePages) ? 'Explore health information' : 'Explore this specialty' }} <span aria-hidden="true">↓</span></a>
            </div>
            <div class="page-hero-card tone-{{ $page['tone'] }}">
                @if (! empty($page['image_path']))
                    <img class="page-hero-image" src="{{ asset('storage/'.$page['image_path']) }}" alt="{{ $page['title'] }}" fetchpriority="high">
                @else
                    <span class="page-hero-icon" aria-hidden="true">{{ $page['icon'] }}</span>
                    <span class="page-hero-caption">{{ $page['eyebrow'] }}</span>
                    <span class="page-card-decoration" aria-hidden="true">✳</span>
                @endif
            </div>
        </div>
    </section>
    @unless (isset($medicinePages))
        <section class="section detail-section" id="learn-more">
            <div class="container detail-layout">
                <div>
                    <span class="eyebrow"><span class="eyebrow-line"></span> A good place to start</span>
                    <h2>{{ $page['eyebrow'] }}<span>.</span></h2>
                </div>
                <div class="detail-copy">
                    <p class="detail-intro">{{ $page['intro'] }}</p>
                    <h2>How we can help</h2>
                    <ul class="highlight-list">
                        @foreach ($page['highlights'] as $highlight)
                            <li><span aria-hidden="true">✓</span>{{ $highlight }}</li>
                        @endforeach
                    </ul>
                    <div class="info-note"><span aria-hidden="true">♡</span><p>This page offers general information only. A qualified health professional can help with advice that is right for you.</p></div>
                </div>
            </div>
        </section>
    @endunless
    @isset($medicinePages)
        <section class="section published-section" id="medicine-information">
            <div class="container">
                <div class="section-heading">
                    <div>
                        <span class="eyebrow"><span class="eyebrow-line"></span> {{ $page['title'] }}</span>
                        <h2>Conditions and<br><span>medicine information.</span></h2>
                    </div>
                    <p>Read information published by our care team and find a doctor to contact on each page.</p>
                </div>
                @if ($medicinePages->isEmpty())
                    <div class="empty-library">
                        <h2>Information is on its way.</h2>
                        <p>There are no published pages for this specialty yet. Please check back soon.</p>
                    </div>
                @else
                    <div class="published-grid">
                        @foreach ($medicinePages as $medicine)
                            <a class="published-card" href="{{ route('medicines.show', $medicine->slug) }}">
                                @if ($medicine->disease_thumbnail_path)
                                    <img class="published-card-thumbnail" src="{{ asset('storage/'.$medicine->disease_thumbnail_path) }}" alt="{{ $medicine->condition }}" loading="lazy">
                                @endif
                                <span class="published-card-content">
                                    <span class="published-category">{{ $medicine->condition }} · {{ $medicine->category }}</span>
                                    <strong>{{ $medicine->title }}</strong>
                                    <span class="published-summary">{{ $medicine->summary }}</span>
                                    <span class="published-link">Read more and find a doctor <span aria-hidden="true">→</span></span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                    <div class="medicine-pagination">{{ $medicinePages->links() }}</div>
                @endif
            </div>
        </section>
    @endisset
    <section class="detail-cta">
        <div class="container detail-cta-inner">
            <div><span class="eyebrow eyebrow-light">Keep exploring</span><h2>There is a next step<br>that feels right for you.</h2></div>
            <a class="button button-light" href="{{ route('home') }}#services">See all our care <span aria-hidden="true">→</span></a>
        </div>
    </section>
@endsection
