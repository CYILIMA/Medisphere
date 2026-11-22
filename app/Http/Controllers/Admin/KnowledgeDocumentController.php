<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessKnowledgeDocument;
use App\Models\KnowledgeDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class KnowledgeDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.knowledge-documents.index', ['documents' => KnowledgeDocument::query()->latest()->paginate(12)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['document' => ['required', 'file', 'mimes:pdf,docx', 'max:10240']]);
        $upload = $validated['document'];
        $document = KnowledgeDocument::query()->create([
            'name' => pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME),
            'original_filename' => $upload->getClientOriginalName(),
            'mime_type' => $upload->getMimeType(),
            'path' => $upload->store('ora-knowledge'),
            'size_bytes' => $upload->getSize(),
        ]);

        ProcessKnowledgeDocument::dispatchSync($document);

        return redirect()->route('admin.knowledge-documents.index')->with('status', 'Document uploaded. ORA is preparing its knowledge.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KnowledgeDocument $knowledgeDocument): RedirectResponse
    {
        Storage::disk('local')->delete($knowledgeDocument->path);
        $knowledgeDocument->delete();

        return redirect()->route('admin.knowledge-documents.index')->with('status', 'Knowledge document deleted.');
    }

    public function retry(KnowledgeDocument $knowledgeDocument): RedirectResponse
    {
        $knowledgeDocument->update(['status' => 'processing', 'error_message' => null]);
        ProcessKnowledgeDocument::dispatchSync($knowledgeDocument);

        return redirect()->route('admin.knowledge-documents.index')->with('status', 'ORA is retrying this document.');
    }
}
