<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\SiteAsset;
use Illuminate\Contracts\View\View;

class HealthPageController extends Controller
{
    public function home(): View
    {
        $siteImagePaths = SiteAsset::query()
            ->whereIn(
                'asset_key',
                collect(config('health.specialties'))
                    ->keys()
                    ->map(fn (string $slug): string => 'specialty-'.$slug)
                    ->push('home-hero'),
            )
            ->pluck('path', 'asset_key');
        $homeHeroPath = $siteImagePaths->get('home-hero');

        $specialties = collect(config('health.specialties'))
            ->map(fn (array $page, string $slug): array => $page + [
                'slug' => $slug,
                'url' => route('medicine.show', ['specialty' => $slug]),
                'image_path' => $siteImagePaths->get('specialty-'.$slug),
            ])
            ->values();

        $services = collect(config('health.services'))
            ->map(fn (array $page, string $slug): array => $page + [
                'slug' => $slug,
                'url' => route('service.show', ['service' => $slug]),
            ])
            ->values();

        $medicines = Medicine::published()->latest('published_at')->take(3)->get();

        return view('home', compact('specialties', 'services', 'medicines', 'homeHeroPath'));
    }

    public function medicine(string $specialty): View
    {
        $page = config("health.specialties.{$specialty}");

        abort_if($page === null, 404);

        return view('pages.service', [
            'page' => $page + [
                'group' => 'Medicine',
                'url' => route('medicine.show', ['specialty' => $specialty]),
                'image_path' => SiteAsset::query()
                    ->where('asset_key', 'specialty-'.$specialty)
                    ->value('path'),
            ],
            'medicinePages' => Medicine::published()
                ->where('specialty', $specialty)
                ->latest('published_at')
                ->latest('id')
                ->paginate(9),
        ]);
    }

    public function service(string $service): View
    {
        $page = config("health.services.{$service}");

        abort_if($page === null, 404);

        return view('pages.service', [
            'page' => $page + [
                'group' => 'Our services',
                'url' => route('service.show', ['service' => $service]),
                'image_path' => SiteAsset::query()
                    ->where('asset_key', 'service-'.$service)
                    ->value('path'),
            ],
        ]);
    }

    public function information(string $topic): View
    {
        $page = config("health.information.{$topic}");

        abort_if($page === null, 404);

        return view('pages.information', compact('page'));
    }
}
