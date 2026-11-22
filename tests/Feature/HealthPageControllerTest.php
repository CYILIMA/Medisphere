<?php

use App\Models\SiteAsset;
use Illuminate\Testing\TestResponse;

it('renders the home page with every medical specialty', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSeeText('Good health starts with')
        ->assertSeeText('Dental medicine')
        ->assertSeeText('Internal medicine')
        ->assertSeeText('Eye medicine')
        ->assertSeeText('Nose medicine')
        ->assertSeeText('Skin medicine')
        ->assertSeeText('Bones medicine')
        ->assertSeeText('Ear medicine')
        ->assertSeeText('Mental medicine')
        ->assertSeeText('Psychologists')
        ->assertSeeText('Pharmacy')
        ->assertSeeText('Food & fruits');
});

it('shows admin-uploaded specialty thumbnails on homepage cards', function () {
    SiteAsset::query()->create([
        'asset_key' => 'specialty-dental',
        'path' => 'site-assets/dental.png',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('src="'.asset('storage/site-assets/dental.png').'"', false)
        ->assertSee('class="specialty-thumbnail"', false)
        ->assertSeeText('Dental medicine');
});

it('uses the admin-uploaded homepage hero image', function () {
    SiteAsset::query()->create([
        'asset_key' => 'home-hero',
        'path' => 'site-assets/home-hero.png',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('src="'.asset('storage/site-assets/home-hero.png').'"', false)
        ->assertDontSee('images.unsplash.com/photo-1559839734-2b71ea197ec2', false);
});

it('renders each specialty, service and information page', function () {
    $pages = [
        [route('medicine.show', ['specialty' => 'dental']), 'Dental medicine'],
        [route('medicine.show', ['specialty' => 'internal']), 'Internal medicine'],
        [route('medicine.show', ['specialty' => 'eye']), 'Eye medicine'],
        [route('medicine.show', ['specialty' => 'nose']), 'Nose medicine'],
        [route('medicine.show', ['specialty' => 'skin']), 'Skin medicine'],
        [route('medicine.show', ['specialty' => 'bones']), 'Bones medicine'],
        [route('medicine.show', ['specialty' => 'ear']), 'Ear medicine'],
        [route('medicine.show', ['specialty' => 'mental']), 'Mental medicine'],
        [route('service.show', ['service' => 'psychologists']), 'Psychologists'],
        [route('service.show', ['service' => 'pharmacy']), 'Pharmacy'],
        [route('service.show', ['service' => 'food-fruits']), 'Food & fruits'],
        [route('about'), 'About MediSphere'],
        [route('about.show', ['topic' => 'mission']), 'Our mission'],
        [route('about.show', ['topic' => 'vision']), 'Our vision'],
    ];

    foreach ($pages as [$url, $title]) {
        /** @var TestResponse $response */
        $response = $this->get($url);

        $response->assertOk()->assertSeeText($title);
    }
});

it('returns a not found response for an unknown specialty', function () {
    $this->get('/medicine/unknown')->assertNotFound();
});

it('keeps the introductory section on non-medicine service pages', function () {
    $this->get(route('service.show', ['service' => 'pharmacy']))
        ->assertOk()
        ->assertSeeText('A good place to start')
        ->assertSeeText('How we can help');
});
