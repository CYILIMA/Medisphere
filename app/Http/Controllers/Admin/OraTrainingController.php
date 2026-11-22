<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OraLesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OraTrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('admin.ora-training.index', [
            'lessons' => OraLesson::query()->latest('updated_at')->paginate(12),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->boolean('is_active');

        OraLesson::query()->create($validated);

        return redirect()->route('admin.ora-training.index')->with('status', 'ORA lesson saved and ready to use.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OraLesson $oraTraining): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->boolean('is_active');

        $oraTraining->update($validated);

        return redirect()->route('admin.ora-training.index')->with('status', 'ORA lesson updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OraLesson $oraTraining): RedirectResponse
    {
        $oraTraining->delete();

        return redirect()->route('admin.ora-training.index')->with('status', 'ORA lesson deleted.');
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function rules(): array
    {
        return [
            'trigger_phrases' => ['required', 'string', 'min:2', 'max:500'],
            'answer' => ['required', 'string', 'min:10', 'max:4000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
