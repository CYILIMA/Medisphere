@extends('layouts.app')

@section('title', $page['title'].' — MediSphere')
@section('meta_description', $page['description'])

@section('content')
    <section class="page-hero">
        <div class="container page-hero-grid">
            <div class="page-hero-copy">
                <a class="back-link" href="{{ route('home') }}"><span aria-hidden="true">←</span> Back to home</a>
                <span class="eyebrow"><span class="eyebrow-line"></span> More about us</span>
                <h1>{{ $page['title'] }}<span class="title-period">.</span></h1>
                <p>{{ $page['description'] }}</p>
                <a class="button" href="#learn-more">Learn more <span aria-hidden="true">↓</span></a>
            </div>
            <div class="page-hero-card tone-{{ $page['tone'] }}">
                <span class="page-hero-icon" aria-hidden="true">{{ $page['icon'] }}</span>
                <span class="page-hero-caption">{{ $page['eyebrow'] }}</span>
                <span class="page-card-decoration" aria-hidden="true">✳</span>
            </div>
        </div>
    </section>
    <section class="section detail-section" id="learn-more">
        <div class="container detail-layout">
            <div>
                <span class="eyebrow"><span class="eyebrow-line"></span> {{ $page['eyebrow'] }}</span>
                <h2>{{ $page['title'] }}<span>.</span></h2>
            </div>
            <div class="detail-copy">
                <p class="detail-intro">{{ $page['intro'] }}</p>
                <h2>What matters to us</h2>
                <ul class="highlight-list">
                    @foreach ($page['highlights'] as $highlight)
                        <li><span aria-hidden="true">✓</span>{{ $highlight }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
    <section class="detail-cta">
        <div class="container detail-cta-inner">
            <div><span class="eyebrow eyebrow-light">Explore MediSphere</span><h2>Find a little more care<br>in every next step.</h2></div>
            <a class="button button-light" href="{{ route('home') }}#services">Explore our care <span aria-hidden="true">→</span></a>
        </div>
    </section>
@endsection
