@extends('admin.layouts.app')

@section('title', 'Medicine library / Admin')

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
            <header class="admin-topbar"><span>Content management</span><a href="{{ route('home') }}">Open website ↗</a></header>
            <section class="admin-content">
                <div class="admin-page-heading">
                    <div><span class="admin-eyebrow">MEDISPHERE / CONTENT</span><h1>Medicine library</h1><p>Publish helpful information and connect every page with a doctor.</p></div>
                    <a class="admin-button" href="{{ route('admin.medicines.create') }}"><span aria-hidden="true">＋</span> Add medicine</a>
                </div>

                @if (session('status'))
                    <div class="admin-notice" role="status">{{ session('status') }}</div>
                @endif

                <div class="admin-stats" aria-label="Medicine page totals">
                    <div><span>Total pages</span><strong>{{ $totalCount }}</strong><small>All medicine information</small></div>
                    <div><span>Published</span><strong>{{ $publishedCount }}</strong><small>Visible to visitors</small></div>
                    <div><span>Drafts</span><strong>{{ $draftCount }}</strong><small>Only visible to admins</small></div>
                </div>

                <section class="admin-table-section" aria-labelledby="admin-list-title">
                    <div class="admin-table-heading"><div><h2 id="admin-list-title">All medicine pages</h2><p>Manage content and doctor contact details.</p></div><span>{{ $medicines->total() }} records</span></div>
                    @if ($medicines->isEmpty())
                        <div class="admin-empty"><span aria-hidden="true">＋</span><h3>Your library starts here.</h3><p>Create a medicine page to share clear information and a doctor contact.</p><a class="admin-button" href="{{ route('admin.medicines.create') }}">Create first page <span aria-hidden="true">→</span></a></div>
                    @else
                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead><tr><th>Page</th><th>Doctor contact</th><th>Status</th><th>Updated</th><th><span class="sr-only">Actions</span></th></tr></thead>
                                <tbody>
                                    @foreach ($medicines as $medicine)
                                        <tr>
                                            <td><span class="admin-record-title">{{ $medicine->title }}</span><small>{{ $medicine->medicine_name }} · {{ $medicine->condition }}</small></td>
                                            <td><span class="admin-doctor-name">{{ $medicine->doctor_name }}</span><small>{{ $medicine->doctor_email }}</small></td>
                                            <td><span class="admin-status {{ $medicine->is_published ? 'is-published' : 'is-draft' }}"><i></i>{{ $medicine->is_published ? 'Published' : 'Draft' }}</span></td>
                                            <td class="admin-date">{{ $medicine->updated_at->format('M j, Y') }}</td>
                                            <td><div class="admin-row-actions"><a class="admin-icon-button" href="{{ route('admin.medicines.edit', $medicine) }}" title="Edit {{ $medicine->title }}" aria-label="Edit {{ $medicine->title }}">✎</a>
                                                <form method="POST" action="{{ route('admin.medicines.destroy', $medicine) }}" onsubmit="return confirm('Delete this medicine page?')">
                                                    @csrf @method('DELETE')
                                                    <button class="admin-icon-button is-danger" type="submit" title="Delete {{ $medicine->title }}" aria-label="Delete {{ $medicine->title }}">×</button>
                                                </form>
                                            </div></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="admin-pagination">{{ $medicines->links() }}</div>
                    @endif
                </section>
            </section>
        </main>
    </div>
@endsection
