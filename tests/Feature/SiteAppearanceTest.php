<?php

use App\Models\SiteAsset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function fakeAppearanceImage(int $width, int $height, string $filename): UploadedFile
{
    $chunk = static fn (string $type, string $data): string => pack('N', strlen($data))
        .$type.$data.pack('N', crc32($type.$data));
    $pixels = str_repeat("\0".str_repeat("\xB8\xC4\xB8\xFF", $width), $height);
    $png = "\x89PNG\r\n\x1a\n"
        .$chunk('IHDR', pack('N2', $width, $height)."\x08\x06\x00\x00\x00")
        .$chunk('IDAT', gzcompress($pixels))
        .$chunk('IEND', '');

    return UploadedFile::fake()->createWithContent($filename, $png);
}

it('restricts site appearance management to administrators', function () {
    $this->get(route('admin.site-appearance.index'))
        ->assertRedirect(route('admin.login'));

    $this->put(route('admin.site-appearance.update'))
        ->assertRedirect(route('admin.login'));

    $this->actingAs(User::factory()->create())
        ->get(route('admin.site-appearance.index'))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->put(route('admin.site-appearance.update'))
        ->assertForbidden();
});

it('lets administrators upload images and displays them on the public specialty page', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($admin);

    $this->get(route('admin.site-appearance.index'))
        ->assertOk()
        ->assertSeeText('Site appearance')
        ->assertSeeText('Website logo')
        ->assertSeeText('Homepage hero image')
        ->assertSeeText('Medicine specialty images')
        ->assertSeeText('Care service images')
        ->assertSeeText('Dental medicine');

    $this->put(route('admin.site-appearance.update'), [
        'logo' => fakeAppearanceImage(120, 60, 'logo.png'),
        'home_hero' => fakeAppearanceImage(640, 420, 'home-hero.png'),
        'specialty_images' => [
            'dental' => fakeAppearanceImage(640, 420, 'dental.png'),
        ],
        'service_images' => [
            'psychologists' => fakeAppearanceImage(640, 420, 'psychologists.png'),
            'pharmacy' => fakeAppearanceImage(640, 420, 'pharmacy.png'),
            'food-fruits' => fakeAppearanceImage(640, 420, 'food-fruits.png'),
        ],
    ])->assertRedirect(route('admin.site-appearance.index'))
        ->assertSessionHas('status', 'Site appearance updated.');

    $logo = SiteAsset::query()->where('asset_key', 'logo')->firstOrFail();
    $homeHero = SiteAsset::query()->where('asset_key', 'home-hero')->firstOrFail();
    $dentalImage = SiteAsset::query()->where('asset_key', 'specialty-dental')->firstOrFail();
    $serviceImages = [
        'psychologists' => SiteAsset::query()->where('asset_key', 'service-psychologists')->firstOrFail(),
        'pharmacy' => SiteAsset::query()->where('asset_key', 'service-pharmacy')->firstOrFail(),
        'food-fruits' => SiteAsset::query()->where('asset_key', 'service-food-fruits')->firstOrFail(),
    ];

    $this->assertTrue(Storage::disk('public')->exists($logo->path));
    $this->assertTrue(Storage::disk('public')->exists($homeHero->path));
    $this->assertTrue(Storage::disk('public')->exists($dentalImage->path));
    foreach ($serviceImages as $serviceImage) {
        $this->assertTrue(Storage::disk('public')->exists($serviceImage->path));
    }

    $this->get(route('admin.site-appearance.index'))
        ->assertOk()
        ->assertSee(asset('storage/'.$logo->path), false)
        ->assertSee(asset('storage/'.$homeHero->path), false)
        ->assertSee(asset('storage/'.$dentalImage->path), false)
        ->assertSee(asset('storage/'.$serviceImages['psychologists']->path), false)
        ->assertSee(asset('storage/'.$serviceImages['pharmacy']->path), false)
        ->assertSee(asset('storage/'.$serviceImages['food-fruits']->path), false);

    $this->put(route('admin.site-appearance.update'))
        ->assertRedirect(route('admin.site-appearance.index'));

    $this->assertDatabaseHas('site_assets', [
        'asset_key' => 'logo',
        'path' => $logo->path,
    ]);
    $this->assertDatabaseHas('site_assets', [
        'asset_key' => 'home-hero',
        'path' => $homeHero->path,
    ]);
    $this->assertDatabaseHas('site_assets', [
        'asset_key' => 'specialty-dental',
        'path' => $dentalImage->path,
    ]);
    foreach ($serviceImages as $slug => $serviceImage) {
        $this->assertDatabaseHas('site_assets', [
            'asset_key' => 'service-'.$slug,
            'path' => $serviceImage->path,
        ]);
    }

    $this->put(route('admin.site-appearance.update'), [
        'logo' => fakeAppearanceImage(140, 70, 'replacement-logo.png'),
    ])->assertRedirect(route('admin.site-appearance.index'));

    $replacementLogo = SiteAsset::query()->where('asset_key', 'logo')->firstOrFail();

    $this->assertNotSame($logo->path, $replacementLogo->path);
    $this->assertFalse(Storage::disk('public')->exists($logo->path));
    $this->assertTrue(Storage::disk('public')->exists($replacementLogo->path));
    $this->assertTrue(Storage::disk('public')->exists($dentalImage->path));

    $this->get(route('medicine.show', ['specialty' => 'dental']))
        ->assertOk()
        ->assertSee(asset('storage/'.$replacementLogo->path), false)
        ->assertSee(asset('storage/'.$dentalImage->path), false)
        ->assertSee('page-hero-image', false);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(asset('storage/'.$homeHero->path), false);

    foreach ($serviceImages as $slug => $serviceImage) {
        $this->get(route('service.show', ['service' => $slug]))
            ->assertOk()
            ->assertSee(asset('storage/'.$serviceImage->path), false)
            ->assertSee('page-hero-image', false);
    }
});

it('rejects undersized logo and specialty images without saving them', function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create(['is_admin' => true]))
        ->put(route('admin.site-appearance.update'), [
            'logo' => fakeAppearanceImage(80, 30, 'small-logo.png'),
            'home_hero' => fakeAppearanceImage(300, 200, 'small-home-hero.png'),
            'specialty_images' => [
                'dental' => fakeAppearanceImage(300, 200, 'small-dental.png'),
            ],
            'service_images' => [
                'psychologists' => fakeAppearanceImage(300, 200, 'small-service.png'),
            ],
        ])
        ->assertSessionHasErrors(['logo', 'home_hero', 'specialty_images.dental', 'service_images.psychologists']);

    $this->assertDatabaseCount('site_assets', 0);
    $this->assertSame([], Storage::disk('public')->allFiles('site-assets'));
});
