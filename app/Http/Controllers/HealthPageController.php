<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HealthPageController extends Controller
{
    public function home(): View
    {
        $specialties = collect(config('health.specialties'))
            ->map(fn (array $page, string $slug): array => $page + [
                'slug' => $slug,
                'url' => route('medicine.show', ['specialty' => $slug]),
            ])
            ->values();

        $services = collect(config('health.services'))
            ->map(fn (array $page, string $slug): array => $page + [
                'slug' => $slug,
                'url' => route('service.show', ['service' => $slug]),
            ])
            ->values();

        return view('home', compact('specialties', 'services'));
    }

    public function medicine(string $specialty): View
    {
        $page = config("health.specialties.{$specialty}");

        abort_if($page === null, 404);

        return view('pages.service', [
            'page' => $page + [
                'group' => 'Medicine',
                'url' => route('medicine.show', ['specialty' => $specialty]),
            ],
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
