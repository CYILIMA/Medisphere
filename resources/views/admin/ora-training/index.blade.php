@extends('admin.layouts.app')

@section('title', 'ORA training / Admin')

@section('content')
    <div class="admin-frame">
        <aside class="admin-sidebar">
            <a class="admin-brand" href="{{ route('home') }}"><span class="admin-brand-mark">+</span><span>medi<strong>sphere</strong></span></a>
            <span class="admin-side-label">Workspace</span>
            <a class="admin-side-link" href="{{ route('admin.medicines.index') }}"><span aria-hidden="true">▤</span> Medicine library</a>
            <a class="admin-side-link" href="{{ route('admin.site-appearance.index') }}"><span aria-hidden="true">◉</span> Site appearance</a>
            <a class="admin-side-link is-active" href="{{ route('admin.ora-training.index') }}"><span aria-hidden="true">✦</span> ORA training</a>
            <a class="admin-side-link" href="{{ route('admin.knowledge-documents.index') }}"><span aria-hidden="true">▣</span> Knowledge files</a>
            <a class="admin-side-link" href="{{ route('home') }}"><span aria-hidden="true">↗</span> View public website</a>
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
            <header class="admin-topbar"><span>ORA knowledge base</span><a href="{{ route('home') }}">Open website ↗</a></header>
            <section class="admin-content admin-editor-content">
                <div class="admin-page-heading">
                    <div><span class="admin-eyebrow">MEDISPHERE / ORA</span><h1>Train ORA</h1><p>Add approved knowledge that ORA should use when visitors ask similar questions.</p></div>
                </div>

                @if (session('status'))
                    <div class="admin-notice" role="status">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="admin-validation" role="alert"><strong>Please review the lesson.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif

                <form class="admin-editor" method="POST" action="{{ route('admin.ora-training.store') }}">
                    @csrf
                    <section class="admin-editor-section">
                        <div class="admin-editor-section-heading"><span>01</span><div><h2>New ORA lesson</h2><p>Add example questions or alternate phrases separated by commas. ORA compares the important words in a visitor’s question and uses the closest matching lesson.</p></div></div>
                        <div class="admin-field-grid">
                            <div class="admin-field admin-field-full"><label for="trigger_phrases">Training questions or trigger phrases <b>*</b></label><input id="trigger_phrases" name="trigger_phrases" value="{{ old('trigger_phrases') }}" maxlength="500" required placeholder="e.g. What is hay fever?, seasonal allergies">@error('trigger_phrases')<small class="admin-error">{{ $message }}</small>@enderror</div>
                            <div class="admin-field admin-field-full"><label for="answer">Approved answer <b>*</b></label><textarea id="answer" name="answer" rows="6" maxlength="4000" required placeholder="Write the clear, safe answer ORA should give.">{{ old('answer') }}</textarea><small class="admin-field-hint">Avoid diagnosis, emergency assessment, or instructions to change prescribed treatment.</small>@error('answer')<small class="admin-error">{{ $message }}</small>@enderror</div>
                        </div>
                    </section>
                    <section class="admin-publish-row"><label class="admin-publish-toggle"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))><span class="admin-toggle-track"></span><span><strong>Use this lesson now</strong><small>Active lessons become available to ORA immediately.</small></span></label><div class="admin-form-actions"><button class="admin-button" type="submit">Save ORA lesson <span aria-hidden="true">→</span></button></div></section>
                </form>

                <section class="admin-table-section" aria-labelledby="ora-lessons-title">
                    <div class="admin-table-heading"><div><h2 id="ora-lessons-title">Saved lessons</h2><p>Edit, pause, or remove ORA’s approved knowledge.</p></div><span>{{ $lessons->total() }} records</span></div>
                    @if ($lessons->isEmpty())
                        <div class="admin-empty"><span aria-hidden="true">✦</span><h3>ORA is ready to learn.</h3><p>Add the first approved answer above to strengthen its knowledge.</p></div>
                    @else
                        <div class="ora-training-list">
                            @foreach ($lessons as $lesson)
                                <form class="ora-training-card" method="POST" action="{{ route('admin.ora-training.update', $lesson) }}">
                                    @csrf @method('PUT')
                                    <div class="admin-field"><label for="phrases-{{ $lesson->id }}">Training questions or trigger phrases</label><input id="phrases-{{ $lesson->id }}" name="trigger_phrases" value="{{ $lesson->trigger_phrases }}" maxlength="500" required></div>
                                    <div class="admin-field"><label for="answer-{{ $lesson->id }}">Approved answer</label><textarea id="answer-{{ $lesson->id }}" name="answer" rows="4" maxlength="4000" required>{{ $lesson->answer }}</textarea></div>
                                    <div class="ora-training-actions"><label class="admin-publish-toggle"><input type="checkbox" name="is_active" value="1" @checked($lesson->is_active)><span class="admin-toggle-track"></span><span><strong>{{ $lesson->is_active ? 'Active' : 'Paused' }}</strong><small>Updated {{ $lesson->updated_at->format('M j, Y') }}</small></span></label><button class="admin-button" type="submit">Save</button></div>
                                </form>
                                <form class="ora-training-delete" method="POST" action="{{ route('admin.ora-training.destroy', $lesson) }}" onsubmit="return confirm('Delete this ORA lesson?')">
                                    @csrf @method('DELETE')
                                    <button class="admin-icon-button is-danger" type="submit" title="Delete ORA lesson" aria-label="Delete ORA lesson">×</button>
                                </form>
                            @endforeach
                        </div>
                        <div class="admin-pagination">{{ $lessons->links() }}</div>
                    @endif
                </section>
            </section>
        </main>
    </div>
@endsection
