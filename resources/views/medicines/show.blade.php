@extends('layouts.app')

@section('title', $medicine->title.' — MediSphere')
@section('meta_description', $medicine->summary)

@section('content')
    <section class="page-hero medicine-detail-hero">
        <div class="container medicine-detail-grid">
            <div class="page-hero-copy">
                <a class="back-link" href="{{ route('medicines.index') }}"><span aria-hidden="true">←</span> Medicine library</a>
                <span class="eyebrow"><span class="eyebrow-line"></span> {{ $medicine->category }}</span>
                <h1>{{ $medicine->title }}<span class="title-period">.</span></h1>
                <p>{{ $medicine->summary }}</p>
                @if ($medicine->disease_thumbnail_path)
                    <img class="medicine-detail-thumbnail" src="{{ asset('storage/'.$medicine->disease_thumbnail_path) }}" alt="{{ $medicine->condition }}" fetchpriority="high">
                @endif
            </div>
            <aside class="doctor-contact" aria-label="Doctor contact">
                    <img src="{{ asset('storage/'.$medicine->doctor_image_path) }}" alt="{{ $medicine->doctor_name }}">
                <span class="doctor-contact-label">A doctor to contact</span>
                <h2>{{ $medicine->doctor_name }}</h2>
                <p>{{ $medicine->doctor_specialty }}</p>
                @if ($medicine->doctor_bio)
                    <p class="doctor-bio">{{ $medicine->doctor_bio }}</p>
                @endif
                <a href="mailto:{{ $medicine->doctor_email }}">{{ $medicine->doctor_email }}</a>
                @if ($medicine->doctor_phone)
                    <a href="tel:{{ $medicine->doctor_phone }}">{{ $medicine->doctor_phone }}</a>
                @endif
            </aside>
        </div>
    </section>
    <section class="section detail-section">
        <div class="container medicine-information">
            <div class="medicine-facts">
                <span class="eyebrow"><span class="eyebrow-line"></span> At a glance</span>
                <dl>
                    <div><dt>Medicine</dt><dd>{{ $medicine->medicine_name }}</dd></div>
                    <div><dt>Condition</dt><dd>{{ $medicine->condition }}</dd></div>
                    <div><dt>Category</dt><dd>{{ $medicine->category }}</dd></div>
                    @if ($medicine->specialty)
                        <div><dt>Medical specialty</dt><dd>{{ config("health.specialties.{$medicine->specialty}.title", 'Other') }}</dd></div>
                    @endif
                </dl>
            </div>
            <article class="medicine-article">
                <h2>About {{ $medicine->title }}</h2>
                <div class="medicine-prose">{{ $medicine->description }}</div>
                <p class="info-note"><span aria-hidden="true">♡</span> This information is for learning only and does not replace professional medical advice. Contact a qualified health professional for advice about your situation.</p>
            </article>
        </div>
    </section>
@endsection
