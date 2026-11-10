<?php

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
