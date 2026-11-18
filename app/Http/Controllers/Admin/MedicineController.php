<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MedicineController extends Controller
{
    public function index(): View
    {
        return view('admin.medicines.index', [
            'medicines' => Medicine::query()->latest('updated_at')->paginate(10),
            'totalCount' => Medicine::count(),
            'publishedCount' => Medicine::where('is_published', true)->count(),
            'draftCount' => Medicine::where('is_published', false)->count(),
        ]);
    }

    public function create(): View
    {
        return view('admin.medicines.form', ['medicine' => new Medicine]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules(true));
        $doctorImage = $validated['doctor_image'];
        unset($validated['doctor_image']);

        $isPublished = (bool) ($validated['is_published'] ?? false);

        Medicine::create([
            ...$validated,
            'slug' => $this->uniqueSlug($validated['title']),
            'doctor_image_path' => $doctorImage->store('doctor-profiles', 'public'),
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        return redirect()->route('admin.medicines.index')->with('status', 'Medicine information saved.');
    }

    public function edit(Medicine $medicine): View
    {
        return view('admin.medicines.form', compact('medicine'));
    }

    public function update(Request $request, Medicine $medicine): RedirectResponse
    {
        $validated = $request->validate($this->rules(false));
        $doctorImage = $validated['doctor_image'] ?? null;
        unset($validated['doctor_image']);

        $isPublished = (bool) ($validated['is_published'] ?? false);
        $previousImagePath = $medicine->doctor_image_path;
        $validated['is_published'] = $isPublished;
        $validated['published_at'] = $isPublished
            ? ($medicine->published_at ?? now())
            : null;

        if ($doctorImage !== null) {
            $validated['doctor_image_path'] = $doctorImage->store('doctor-profiles', 'public');
        }

        $medicine->update($validated);

        if ($doctorImage !== null) {
            Storage::disk('public')->delete($previousImagePath);
        }

        return redirect()->route('admin.medicines.index')->with('status', 'Medicine information updated.');
    }

    public function destroy(Medicine $medicine): RedirectResponse
    {
        Storage::disk('public')->delete($medicine->doctor_image_path);
        $medicine->delete();

        return redirect()->route('admin.medicines.index')->with('status', 'Medicine information deleted.');
    }

    private function rules(bool $imageRequired): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'medicine_name' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:100'],
            'condition' => ['required', 'string', 'max:180'],
            'summary' => ['required', 'string', 'max:350'],
            'description' => ['required', 'string', 'max:20000'],
            'doctor_name' => ['required', 'string', 'max:160'],
            'doctor_specialty' => ['required', 'string', 'max:160'],
            'doctor_email' => ['required', 'email', 'max:255'],
            'doctor_phone' => ['nullable', 'string', 'max:40'],
            'doctor_image' => [$imageRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=200,min_height=200'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    private function uniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title) ?: 'medicine';
        $slug = $baseSlug;
        $suffix = 2;

        while (Medicine::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
