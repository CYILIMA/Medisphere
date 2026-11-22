<?php

use App\Models\OraLesson;
use App\Models\User;

it('restricts ORA training to administrators', function () {
    $this->get(route('admin.ora-training.index'))
        ->assertRedirect(route('admin.login'));

    $this->actingAs(User::factory()->create())
        ->get(route('admin.ora-training.index'))
        ->assertForbidden();
});

it('shows administrators the training question and answer form', function () {
    $this->actingAs(User::factory()->create(['is_admin' => true]))
        ->get(route('admin.ora-training.index'))
        ->assertOk()
        ->assertSeeText('Train ORA')
        ->assertSeeText('Training questions or trigger phrases')
        ->assertSeeText('Approved answer');
});

it('stores a lesson and uses it for a matching ORA question', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->post(route('admin.ora-training.store'), [
            'trigger_phrases' => 'hay fever, seasonal allergies',
            'answer' => 'Hay fever can often be managed with advice from a pharmacist or clinician.',
            'is_active' => true,
        ])
        ->assertRedirect(route('admin.ora-training.index'))
        ->assertSessionHas('status', 'ORA lesson saved and ready to use.');

    $this->assertDatabaseHas('ora_lessons', [
        'trigger_phrases' => 'hay fever, seasonal allergies',
        'answer' => 'Hay fever can often be managed with advice from a pharmacist or clinician.',
        'is_active' => true,
    ]);

    $this->postJson(route('ora.messages.store'), ['message' => 'How can you help with hay fever?'])
        ->assertOk()
        ->assertJsonPath('reply', 'Hay fever can often be managed with advice from a pharmacist or clinician.')
        ->assertJsonPath('link', null);
});

it('matches ORA training question words in any order and chooses the closest lesson', function () {
    OraLesson::factory()->create([
        'trigger_phrases' => 'allergies',
        'answer' => 'This is a broad allergy response.',
        'updated_at' => now(),
    ]);
    OraLesson::factory()->create([
        'trigger_phrases' => 'pollen allergies itchy eyes',
        'answer' => 'This is the response about itchy eyes from pollen allergies.',
        'updated_at' => now()->subMinute(),
    ]);

    $this->postJson(route('ora.messages.store'), [
        'message' => 'My eyes are itchy because of pollen allergies.',
    ])
        ->assertOk()
        ->assertJsonPath('reply', 'This is the response about itchy eyes from pollen allergies.');
});

it('does not match training keywords that occur only inside another word', function () {
    OraLesson::factory()->create([
        'trigger_phrases' => 'rash',
        'answer' => 'This is the approved rash response.',
    ]);

    $this->postJson(route('ora.messages.store'), ['message' => 'rash'])
        ->assertOk()
        ->assertJsonPath('reply', 'This is the approved rash response.');

    $this->postJson(route('ora.messages.store'), ['message' => 'What is crash safety?'])
        ->assertOk()
        ->assertJsonPath('reply', 'Hi, I’m ORA. I can help you find MediSphere information about health topics, pharmacy support, psychology, and food and wellbeing. What would you like to explore?');
});

it('does not match only one ordinary word from a multiword training question', function () {
    OraLesson::factory()->create([
        'trigger_phrases' => 'red eyes',
        'answer' => 'This is the approved red eyes response.',
    ]);

    $this->postJson(route('ora.messages.store'), ['message' => 'I see red flags.'])
        ->assertOk()
        ->assertJsonPath('reply', 'Hi, I’m ORA. I can help you find MediSphere information about health topics, pharmacy support, psychology, and food and wellbeing. What would you like to explore?');
});

it('updates an ORA lesson and can pause it', function () {
    $lesson = OraLesson::factory()->create(['is_active' => true]);
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->put(route('admin.ora-training.update', $lesson), [
            'trigger_phrases' => 'new question',
            'answer' => 'This is the newly approved ORA response.',
        ])
        ->assertRedirect(route('admin.ora-training.index'));

    $this->assertDatabaseHas('ora_lessons', [
        'id' => $lesson->id,
        'trigger_phrases' => 'new question',
        'answer' => 'This is the newly approved ORA response.',
        'is_active' => false,
    ]);
});

it('deletes an ORA lesson', function () {
    $lesson = OraLesson::factory()->create();
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->delete(route('admin.ora-training.destroy', $lesson))
        ->assertRedirect(route('admin.ora-training.index'));

    $this->assertModelMissing($lesson);
});
