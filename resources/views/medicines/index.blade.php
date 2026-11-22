@extends('layouts.app')

@section('title', 'Medicine library — MediSphere')
@section('meta_description', 'Browse medicine and disease information published by the MediSphere care team.')

@section('content')
    <section class="page-hero medicine-library-hero">
        <div class="container page-hero-copy">
            <a class="back-link" href="{{ route('home') }}"><span aria-hidden="true">←</span> MediSphere home</a>
            <span class="eyebrow"><span class="eyebrow-line"></span> Medicine library</span>
            <h1>Clear information.<br><span>Thoughtful care.</span></h1>
            <p>Explore medicine and condition information, with a doctor contact on every page for your next questions.</p>
        </div>
    </section>
    <section class="section published-section">
        <div class="container">
            @if ($medicines->isEmpty())
                <div class="empty-library">
                    <span class="eyebrow"><span class="eyebrow-line"></span> A growing library</span>
                    <h2>New information is on its way.</h2>
                    <p>There are no published medicine pages yet. Please check back soon.</p>
                </div>
            @else
                <div class="published-grid published-grid-large">
                    @foreach ($medicines as $medicine)
                        <a class="published-card" href="{{ route('medicines.show', $medicine->slug) }}">
                            @if ($medicine->disease_thumbnail_path)
                                <img class="published-card-thumbnail" src="{{ asset('storage/'.$medicine->disease_thumbnail_path) }}" alt="{{ $medicine->condition }}" loading="lazy">
                            @endif
                            <span class="published-card-content">
                                <span class="published-category">{{ $medicine->category }} · {{ $medicine->condition }}</span>
                                <strong>{{ $medicine->title }}</strong>
                                <span class="published-summary">{{ $medicine->summary }}</span>
                                <span class="published-link">Read information <span aria-hidden="true">→</span></span>
                            </span>
                        </a>
                    @endforeach
                </div>
                <div class="medicine-pagination">{{ $medicines->links() }}</div>
            @endif
        </div>
    </section>
@endsection
