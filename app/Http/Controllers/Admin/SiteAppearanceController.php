<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteAsset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteAppearanceController extends Controller
{
    public function index(): View
    {
        $assets = SiteAsset::query()->pluck('path', 'asset_key');
        $specialtyImagePaths = collect(config('health.specialties'))
            ->keys()
            ->mapWithKeys(fn (string $slug): array => [$slug => $assets->get('specialty-'.$slug)]);
        $serviceImagePaths = collect(config('health.services'))
            ->keys()
            ->mapWithKeys(fn (string $slug): array => [$slug => $assets->get('service-'.$slug)]);

        return view('admin.site-appearance.index', [
            'logoPath' => $assets->get('logo'),
            'homeHeroPath' => $assets->get('home-hero'),
            'specialties' => config('health.specialties'),
            'specialtyImagePaths' => $specialtyImagePaths,
            'services' => config('health.services'),
            'serviceImagePaths' => $serviceImagePaths,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $imageRules = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp'];
        $rules = [
            'logo' => [...$imageRules, 'max:2048', 'dimensions:min_width=100,min_height=40'],
            'home_hero' => [...$imageRules, 'max:4096', 'dimensions:min_width=600,min_height=400'],
        ];

        foreach (array_keys(config('health.specialties')) as $slug) {
            $rules["specialty_images.{$slug}"] = [...$imageRules, 'max:4096', 'dimensions:min_width=600,min_height=400'];
        }

        foreach (array_keys(config('health.services')) as $slug) {
            $rules["service_images.{$slug}"] = [...$imageRules, 'max:4096', 'dimensions:min_width=600,min_height=400'];
        }

        $validated = $request->validate($rules);
        $uploads = [
            'logo' => $validated['logo'] ?? null,
            'home-hero' => $validated['home_hero'] ?? null,
        ];

        foreach (array_keys(config('health.specialties')) as $slug) {
            $uploads['specialty-'.$slug] = $validated['specialty_images'][$slug] ?? null;
        }

        foreach (array_keys(config('health.services')) as $slug) {
            $uploads['service-'.$slug] = $validated['service_images'][$slug] ?? null;
        }

        foreach ($uploads as $assetKey => $upload) {
            if ($upload === null) {
                continue;
            }

            $asset = SiteAsset::query()->firstOrNew(['asset_key' => $assetKey]);
            $previousPath = $asset->path;
            $asset->path = $upload->store('site-assets', 'public');
            $asset->save();

            if ($previousPath !== null) {
                Storage::disk('public')->delete($previousPath);
            }
        }

        return redirect()->route('admin.site-appearance.index')->with('status', 'Site appearance updated.');
    }
}
