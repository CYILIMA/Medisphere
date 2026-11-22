<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
        $diseaseThumbnail = $validated['disease_thumbnail'];
        unset($validated['doctor_image'], $validated['disease_thumbnail']);

        $isPublished = (bool) ($validated['is_published'] ?? false);

        Medicine::create([
            ...$validated,
            'slug' => $this->uniqueSlug($validated['title']),
            'disease_thumbnail_path' => $diseaseThumbnail->store('disease-thumbnails', 'public'),
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
        $validated = $request->validate($this->rules(false, $medicine));
        $doctorImage = $validated['doctor_image'] ?? null;
        $diseaseThumbnail = $validated['disease_thumbnail'] ?? null;
        unset($validated['doctor_image'], $validated['disease_thumbnail']);

        $isPublished = (bool) ($validated['is_published'] ?? false);
        $previousDoctorImagePath = $medicine->doctor_image_path;
        $previousDiseaseThumbnailPath = $medicine->disease_thumbnail_path;
        $validated['is_published'] = $isPublished;
        $validated['published_at'] = $isPublished
            ? ($medicine->published_at ?? now())
            : null;

        if ($doctorImage !== null) {
            $validated['doctor_image_path'] = $doctorImage->store('doctor-profiles', 'public');
        }

        if ($diseaseThumbnail !== null) {
            $validated['disease_thumbnail_path'] = $diseaseThumbnail->store('disease-thumbnails', 'public');
        }

        $medicine->update($validated);

        if ($doctorImage !== null) {
            Storage::disk('public')->delete($previousDoctorImagePath);
        }

        if ($diseaseThumbnail !== null && $previousDiseaseThumbnailPath !== null) {
            Storage::disk('public')->delete($previousDiseaseThumbnailPath);
        }

        return redirect()->route('admin.medicines.index')->with('status', 'Medicine information updated.');
    }

    public function destroy(Medicine $medicine): RedirectResponse
    {
        Storage::disk('public')->delete($medicine->doctor_image_path);
        if ($medicine->disease_thumbnail_path !== null) {
            Storage::disk('public')->delete($medicine->disease_thumbnail_path);
        }

        $medicine->delete();

        return redirect()->route('admin.medicines.index')->with('status', 'Medicine information deleted.');
    }

    private function rules(bool $imageRequired, ?Medicine $medicine = null): array
    {
        $diseaseThumbnailRequired = $imageRequired || $medicine?->disease_thumbnail_path === null;

        return [
            'title' => ['required', 'string', 'max:180'],
            'medicine_name' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:100'],
            'specialty' => ['required', 'string', Rule::in(array_keys(config('health.specialties')))],
            'condition' => ['required', 'string', 'max:180'],
            'summary' => ['required', 'string', 'max:350'],
            'description' => ['required', 'string', 'max:20000'],
            'disease_thumbnail' => [$diseaseThumbnailRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096', 'dimensions:min_width=300,min_height=180'],
            'doctor_name' => ['required', 'string', 'max:160'],
            'doctor_specialty' => ['required', 'string', 'max:160'],
            'doctor_bio' => ['required', 'string', 'max:600'],
            'doctor_email' => ['required', 'email', 'max:255'],
            'doctor_phone' => ['required', 'string', 'max:40'],
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
