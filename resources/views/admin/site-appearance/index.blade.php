@extends('admin.layouts.app')

@section('title', 'Site appearance / Admin')

@section('content')
    <div class="admin-frame">
        <aside class="admin-sidebar">
            <a class="admin-brand" href="{{ route('home') }}"><span class="admin-brand-mark">+</span><span>medi<strong>sphere</strong></span></a>
            <span class="admin-side-label">Workspace</span>
            <a class="admin-side-link" href="{{ route('admin.medicines.index') }}"><span aria-hidden="true">▤</span> Medicine library</a>
            <a class="admin-side-link is-active" href="{{ route('admin.site-appearance.index') }}"><span aria-hidden="true">◉</span> Site appearance</a>
            <a class="admin-side-link" href="{{ route('admin.ora-training.index') }}"><span aria-hidden="true">✦</span> ORA training</a>
            <a class="admin-side-link" href="{{ route('medicines.index') }}"><span aria-hidden="true">↗</span> View public library</a>
            <div class="admin-side-bottom">
                <span class="admin-avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span class="admin-user"><strong>{{ auth()->user()->name }}</strong><small>Administrator</small></span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="admin-icon-button" type="submit" title="Sign out" aria-label="Sign out">↗</button>
                </form>
            </div>
        </aside>
        <main class="admin-main">
            <header class="admin-topbar"><span>Website settings</span><a href="{{ route('home') }}">Open website ↗</a></header>
            <section class="admin-content admin-editor-content">
                <div class="admin-page-heading">
                    <div><span class="admin-eyebrow">MEDISPHERE / APPEARANCE</span><h1>Site appearance</h1><p>Manage your website logo and the images shown on specialty and service pages.</p></div>
                </div>

                @if (session('status'))
                    <div class="admin-notice" role="status">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="admin-validation" role="alert"><strong>Please review the uploaded images.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif

                <form class="admin-editor" method="POST" enctype="multipart/form-data" action="{{ route('admin.site-appearance.update') }}">
                    @csrf
                    @method('PUT')
                    <section class="admin-editor-section">
                        <div class="admin-editor-section-heading"><span>01</span><div><h2>Website logo</h2><p>Shown in the website header and footer. Leave empty to keep the current logo.</p></div></div>
                        <div class="admin-field-grid">
                            <div class="admin-field admin-field-full">
                                <label for="logo">Logo image</label>
                                <label class="admin-upload" for="logo">
                                    <input id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp" data-image-preview="logo">
                                    <span class="admin-upload-icon" aria-hidden="true">↑</span>
                                    <strong>Choose a logo</strong>
                                    <small>PNG with transparency, JPG or WebP. Minimum 100 × 40 px, up to 2 MB.</small>
                                    @if ($logoPath)
                                        <img class="admin-site-logo-preview" src="{{ asset('storage/'.$logoPath) }}" alt="Current website logo" data-preview-image="logo">
                                    @else
                                        <img class="admin-site-logo-preview" alt="Selected website logo preview" hidden data-preview-image="logo">
                                    @endif
                                </label>
                                @error('logo')<small class="admin-error">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </section>
                    <section class="admin-editor-section">
                        <div class="admin-editor-section-heading"><span>02</span><div><h2>Homepage hero image</h2><p>Shown in the large image area at the top of the homepage. Leave empty to keep the current image.</p></div></div>
                        <div class="admin-field-grid">
                            <div class="admin-field admin-field-full">
                                <label for="home_hero">Homepage hero image</label>
                                <label class="admin-upload" for="home_hero">
                                    <input id="home_hero" name="home_hero" type="file" accept="image/jpeg,image/png,image/webp" data-image-preview="home-hero">
                                    <span class="admin-upload-icon" aria-hidden="true">↑</span>
                                    <strong>Choose homepage image</strong>
                                    <small>JPG, PNG or WebP. Minimum 600 × 400 px, up to 4 MB.</small>
                                    @if ($homeHeroPath)
                                        <img class="admin-specialty-image-preview" src="{{ asset('storage/'.$homeHeroPath) }}" alt="Current homepage hero image" data-preview-image="home-hero">
                                    @else
                                        <img class="admin-specialty-image-preview" alt="Selected homepage hero image preview" hidden data-preview-image="home-hero">
                                    @endif
                                </label>
                                @error('home_hero')<small class="admin-error">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </section>
                    <section class="admin-editor-section">
                        <div class="admin-editor-section-heading"><span>03</span><div><h2>Medicine specialty images</h2><p>These images replace the illustrated artwork on each specialty page.</p></div></div>
                        <div class="admin-specialty-assets">
                            @foreach ($specialties as $slug => $specialty)
                                <div class="admin-field">
                                    <label for="specialty_image_{{ $slug }}">{{ $specialty['title'] }}</label>
                                    <label class="admin-upload" for="specialty_image_{{ $slug }}">
                                        <input id="specialty_image_{{ $slug }}" name="specialty_images[{{ $slug }}]" type="file" accept="image/jpeg,image/png,image/webp" data-image-preview="{{ $slug }}">
                                        <span class="admin-upload-icon" aria-hidden="true">↑</span>
                                        <strong>Choose {{ strtolower($specialty['title']) }} image</strong>
                                        <small>JPG, PNG or WebP. Minimum 600 × 400 px, up to 4 MB.</small>
                                        @if ($specialtyImagePaths[$slug])
                                            <img class="admin-specialty-image-preview" src="{{ asset('storage/'.$specialtyImagePaths[$slug]) }}" alt="Current {{ strtolower($specialty['title']) }} image" data-preview-image="{{ $slug }}">
                                        @else
                                            <img class="admin-specialty-image-preview" alt="Selected {{ strtolower($specialty['title']) }} image preview" hidden data-preview-image="{{ $slug }}">
                                        @endif
                                    </label>
                                    @error('specialty_images.'.$slug)<small class="admin-error">{{ $message }}</small>@enderror
                                </div>
                            @endforeach
                        </div>
                    </section>
                    <section class="admin-editor-section">
                        <div class="admin-editor-section-heading"><span>04</span><div><h2>Care service images</h2><p>These images replace the illustrated artwork on each service page.</p></div></div>
                        <div class="admin-specialty-assets">
                            @foreach ($services as $slug => $service)
                                <div class="admin-field">
                                    <label for="service_image_{{ $slug }}">{{ $service['title'] }}</label>
                                    <label class="admin-upload" for="service_image_{{ $slug }}">
                                        <input id="service_image_{{ $slug }}" name="service_images[{{ $slug }}]" type="file" accept="image/jpeg,image/png,image/webp" data-image-preview="service-{{ $slug }}">
                                        <span class="admin-upload-icon" aria-hidden="true">↑</span>
                                        <strong>Choose {{ strtolower($service['title']) }} image</strong>
                                        <small>JPG, PNG or WebP. Minimum 600 × 400 px, up to 4 MB.</small>
                                        @if ($serviceImagePaths[$slug])
                                            <img class="admin-specialty-image-preview" src="{{ asset('storage/'.$serviceImagePaths[$slug]) }}" alt="Current {{ strtolower($service['title']) }} image" data-preview-image="service-{{ $slug }}">
                                        @else
                                            <img class="admin-specialty-image-preview" alt="Selected {{ strtolower($service['title']) }} image preview" hidden data-preview-image="service-{{ $slug }}">
                                        @endif
                                    </label>
                                    @error('service_images.'.$slug)<small class="admin-error">{{ $message }}</small>@enderror
                                </div>
                            @endforeach
                        </div>
                    </section>
                    <section class="admin-publish-row">
                        <span class="admin-field-hint">Only the images you select will be changed.</span>
                        <div class="admin-form-actions"><a class="admin-cancel" href="{{ route('admin.medicines.index') }}">Medicine library</a><button class="admin-button" type="submit">Save appearance <span aria-hidden="true">→</span></button></div>
                    </section>
                </form>
            </section>
        </main>
    </div>
@endsection
