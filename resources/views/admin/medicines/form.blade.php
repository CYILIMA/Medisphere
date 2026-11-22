@extends('admin.layouts.app')

@php($isEditing = $medicine->exists)

@section('title', ($isEditing ? 'Edit medicine' : 'Add medicine').' / Admin')

@section('content')
    <div class="admin-frame">
        <aside class="admin-sidebar">
            <a class="admin-brand" href="{{ route('home') }}"><span class="admin-brand-mark">+</span><span>medi<strong>sphere</strong></span></a>
            <span class="admin-side-label">Workspace</span>
            <a class="admin-side-link is-active" href="{{ route('admin.medicines.index') }}"><span aria-hidden="true">▤</span> Medicine library</a>
            <a class="admin-side-link" href="{{ route('admin.site-appearance.index') }}"><span aria-hidden="true">◉</span> Site appearance</a>
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
            <header class="admin-topbar"><a href="{{ route('admin.medicines.index') }}">← Medicine library</a><a href="{{ route('home') }}">Open website ↗</a></header>
            <section class="admin-content admin-editor-content">
                <div class="admin-page-heading">
                    <div><span class="admin-eyebrow">MEDISPHERE / CONTENT</span><h1>{{ $isEditing ? 'Edit medicine page' : 'New medicine page' }}</h1><p>Write clear information and add the doctor visitors can contact.</p></div>
                </div>
                @if ($errors->any())
                    <div class="admin-validation" role="alert"><strong>Please review the highlighted fields.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form class="admin-editor" method="POST" enctype="multipart/form-data" action="{{ $isEditing ? route('admin.medicines.update', $medicine) : route('admin.medicines.store') }}">
                    @csrf
                    @if ($isEditing) @method('PUT') @endif
                    <section class="admin-editor-section">
                        <div class="admin-editor-section-heading"><span>01</span><div><h2>Medicine information</h2><p>Information visitors will see on the public page.</p></div></div>
                        <div class="admin-field-grid">
                            <div class="admin-field admin-field-full"><label for="title">Page title <b>*</b></label><input id="title" name="title" value="{{ old('title', $medicine->title) }}" maxlength="180" required placeholder="e.g. Understanding seasonal allergies">@error('title')<small class="admin-error">{{ $message }}</small>@enderror</div>
                            <div class="admin-field"><label for="medicine_name">Medicine name <b>*</b></label><input id="medicine_name" name="medicine_name" value="{{ old('medicine_name', $medicine->medicine_name) }}" maxlength="180" required placeholder="Generic or common name">@error('medicine_name')<small class="admin-error">{{ $message }}</small>@enderror</div>
                            <div class="admin-field"><label for="category">Category <b>*</b></label><input id="category" name="category" value="{{ old('category', $medicine->category) }}" maxlength="100" required placeholder="e.g. Allergy care">@error('category')<small class="admin-error">{{ $message }}</small>@enderror</div>
                            <div class="admin-field admin-field-full"><label for="specialty">Medical specialty <b>*</b></label><select id="specialty" name="specialty" required><option value="">Choose a specialty</option>@foreach (config('health.specialties') as $slug => $specialty)<option value="{{ $slug }}" @selected(old('specialty', $medicine->specialty) === $slug)>{{ $specialty['title'] }}</option>@endforeach</select><small class="admin-field-hint">The page will appear under this specialty on the public site.</small>@error('specialty')<small class="admin-error">{{ $message }}</small>@enderror</div>
                            <div class="admin-field admin-field-full"><label for="condition">Related condition <b>*</b></label><input id="condition" name="condition" value="{{ old('condition', $medicine->condition) }}" maxlength="180" required placeholder="Condition this information covers">@error('condition')<small class="admin-error">{{ $message }}</small>@enderror</div>
                            <div class="admin-field admin-field-full"><label for="summary">Short summary <b>*</b></label><textarea id="summary" name="summary" rows="2" maxlength="350" required placeholder="A brief introduction for the medicine library">{{ old('summary', $medicine->summary) }}</textarea><small class="admin-field-hint">Up to 350 characters.</small>@error('summary')<small class="admin-error">{{ $message }}</small>@enderror</div>
                            <div class="admin-field admin-field-full"><label for="description">Full information <b>*</b></label><textarea id="description" name="description" rows="9" maxlength="20000" required placeholder="Add clear, reviewed information. Avoid giving personalized diagnosis or treatment instructions.">{{ old('description', $medicine->description) }}</textarea><small class="admin-field-hint">Plain text. Use short paragraphs for readability.</small>@error('description')<small class="admin-error">{{ $message }}</small>@enderror</div>
                            <div class="admin-field admin-field-full"><label for="disease_thumbnail">Disease thumbnail <b>*</b></label><label class="admin-upload" for="disease_thumbnail"><input id="disease_thumbnail" name="disease_thumbnail" type="file" accept="image/jpeg,image/png,image/webp" {{ $medicine->disease_thumbnail_path ? '' : 'required' }} data-image-preview="disease"><span class="admin-upload-icon" aria-hidden="true">↑</span><strong>Choose a disease photo</strong><small>JPG, PNG or WebP. Minimum 300 × 180 px, up to 4 MB.</small>@if ($medicine->disease_thumbnail_path)<img class="admin-upload-thumbnail" src="{{ asset('storage/'.$medicine->disease_thumbnail_path) }}" alt="Current disease thumbnail" data-preview-image="disease">@else<img class="admin-upload-thumbnail" alt="Selected disease thumbnail preview" hidden data-preview-image="disease">@endif</label>@error('disease_thumbnail')<small class="admin-error">{{ $message }}</small>@enderror</div>
                        </div>
                    </section>
                    <section class="admin-editor-section">
                        <div class="admin-editor-section-heading"><span>02</span><div><h2>Doctor contact</h2><p>Every public medicine page includes this contact profile.</p></div></div>
                        <div class="admin-field-grid">
                            <div class="admin-field"><label for="doctor_name">Doctor name <b>*</b></label><input id="doctor_name" name="doctor_name" value="{{ old('doctor_name', $medicine->doctor_name) }}" maxlength="160" required>@error('doctor_name')<small class="admin-error">{{ $message }}</small>@enderror</div>
                            <div class="admin-field"><label for="doctor_specialty">Specialty <b>*</b></label><input id="doctor_specialty" name="doctor_specialty" value="{{ old('doctor_specialty', $medicine->doctor_specialty) }}" maxlength="160" required>@error('doctor_specialty')<small class="admin-error">{{ $message }}</small>@enderror</div>
                            <div class="admin-field admin-field-full"><label for="doctor_bio">Short doctor biography <b>*</b></label><textarea id="doctor_bio" name="doctor_bio" rows="3" maxlength="600" required placeholder="Share a brief introduction to the doctor's experience and approach.">{{ old('doctor_bio', $medicine->doctor_bio) }}</textarea><small class="admin-field-hint">Up to 600 characters.</small>@error('doctor_bio')<small class="admin-error">{{ $message }}</small>@enderror</div>
                            <div class="admin-field"><label for="doctor_email">Contact email <b>*</b></label><input id="doctor_email" name="doctor_email" type="email" autocomplete="email" value="{{ old('doctor_email', $medicine->doctor_email) }}" required>@error('doctor_email')<small class="admin-error">{{ $message }}</small>@enderror</div>
                            <div class="admin-field"><label for="doctor_phone">Phone <b>*</b></label><input id="doctor_phone" name="doctor_phone" type="tel" autocomplete="tel" value="{{ old('doctor_phone', $medicine->doctor_phone) }}" required>@error('doctor_phone')<small class="admin-error">{{ $message }}</small>@enderror</div>
                            <div class="admin-field admin-field-full"><label for="doctor_image">Doctor profile photo <b>*</b></label>
                                    <label class="admin-upload" for="doctor_image"><input id="doctor_image" name="doctor_image" type="file" accept="image/jpeg,image/png,image/webp" {{ $isEditing ? '' : 'required' }} data-image-preview="doctor"><span class="admin-upload-icon" aria-hidden="true">↑</span><strong>Choose a photo</strong><small>JPG, PNG or WebP. Minimum 200 × 200 px, up to 4 MB.</small>@if ($medicine->doctor_image_path)<img src="{{ asset('storage/'.$medicine->doctor_image_path) }}" alt="Current doctor photo" data-preview-image="doctor">@else<img alt="Selected doctor photo preview" hidden data-preview-image="doctor">@endif</label>
                                @error('doctor_image')<small class="admin-error">{{ $message }}</small>@enderror
                            </div>
                        </div>
                    </section>
                    <section class="admin-publish-row"><label class="admin-publish-toggle"><input type="checkbox" name="is_published" value="1" @checked((bool) old('is_published', $medicine->is_published))><span class="admin-toggle-track"></span><span><strong>Publish page</strong><small>Published pages are visible in the public medicine library.</small></span></label><div class="admin-form-actions"><a class="admin-cancel" href="{{ route('admin.medicines.index') }}">Cancel</a><button class="admin-button" type="submit">{{ $isEditing ? 'Save changes' : 'Save medicine' }} <span aria-hidden="true">→</span></button></div></section>
                </form>
            </section>
        </main>
    </div>
@endsection
