<?php

use App\Models\KnowledgeDocument;

it('renders the ORA chat launcher on the public website', function () {
    $this->get(route('home'))->assertOk()->assertSeeText('Chat with ORA')->assertSeeText('ORA shares general information, not medical advice.');
});

it('returns pharmacy guidance for medicine questions', function () {
    $this->postJson(route('ora.messages.store'), ['message' => 'Can I change my medication dose?'])
        ->assertOk()
        ->assertJsonPath('reply', 'For medicine questions, a pharmacist or clinician can help you check what is safe for you. Do not start, stop, or change a prescribed medicine based only on a chat response.')
        ->assertJsonPath('link.label', 'Explore pharmacy support');
});

it('returns urgent guidance for emergency language', function () {
    $this->postJson(route('ora.messages.store'), ['message' => 'I have chest pain and cannot breathe.'])
        ->assertOk()
        ->assertJsonPath('reply', 'This could need urgent help. Please contact your local emergency service or go to the nearest emergency department now. ORA cannot assess emergencies.')
        ->assertJsonPath('link', null);
});

it('explains MediSphere when a visitor asks what it is', function () {
    $this->postJson(route('ora.messages.store'), ['message' => 'what is medisphere'])
        ->assertOk()
        ->assertJsonPath('reply', 'MediSphere is a health-information and care-support website. You can explore medical specialties, read general health information, and find services such as pharmacy support, psychology, and food and wellbeing. The information is educational and does not replace advice from a healthcare professional.')
        ->assertJsonPath('link.label', 'Explore care options')
        ->assertJsonPath('link.url', route('home').'#services');
});

it('returns an approved answer from a matching knowledge record', function () {
    $document = KnowledgeDocument::factory()->create(['status' => 'ready']);
    $document->chunks()->create(['position' => 0, 'content' => "ENTRY ID: CON-015\nTRIGGER PHRASES\nwhat is ACL\nACL injury\nAPPROVED RESPONSE\nACL means anterior cruciate ligament. It is a major knee ligament that helps provide stability.\nRELATED KEYWORDS\nACL, knee"]);

    $this->postJson(route('ora.messages.store'), ['message' => 'what is ACL?'])
        ->assertOk()
        ->assertJsonPath('reply', "ACL means anterior cruciate ligament. It is a major knee ligament that helps provide stability.\n\nSource: ora-knowledge-guide.pdf");
});

it('matches a visitor question with similar knowledge trigger words', function () {
    $document = KnowledgeDocument::factory()->create(['status' => 'ready']);
    $document->chunks()->create(['position' => 0, 'content' => "ENTRY ID: ENT-001\nTRIGGER PHRASES\nobject in a child's nose\nbead stuck in nose\nAPPROVED RESPONSE\nDo not try to push the object deeper. Seek urgent clinical advice, especially if there is trouble breathing, bleeding, or a battery is involved.\nRELATED KEYWORDS\nnose, child, bead"]);

    $this->postJson(route('ora.messages.store'), ['message' => 'Can my child put a bead in their nose?'])
        ->assertOk()
        ->assertJsonPath('reply', "Do not try to push the object deeper. Seek urgent clinical advice, especially if there is trouble breathing, bleeding, or a battery is involved.\n\nSource: ora-knowledge-guide.pdf");
});

it('does not use a manual overview as a knowledge answer', function () {
    $document = KnowledgeDocument::factory()->create(['status' => 'ready']);
    $document->chunks()->create(['position' => 0, 'content' => 'This manual explains ENTRY ID, CATEGORY, TRIGGER PHRASES, APPROVED RESPONSE, and RELATED KEYWORDS for every database entry. APPROVED RESPONSE This is not an answer. RELATED KEYWORDS database, entry.']);

    $this->postJson(route('ora.messages.store'), ['message' => 'database entry'])
        ->assertOk()
        ->assertJsonPath('reply', 'Hi, I’m ORA. I can help you find MediSphere information about health topics, pharmacy support, psychology, and food and wellbeing. What would you like to explore?');
});

it('does not match knowledge triggers inside another word', function () {
    $document = KnowledgeDocument::factory()->create(['status' => 'ready']);
    $document->chunks()->create(['position' => 0, 'content' => "ENTRY ID: SKN-001\nTRIGGER PHRASES\nrash\nAPPROVED RESPONSE\nThis is the approved rash response.\nRELATED KEYWORDS\nskin"]);

    $this->postJson(route('ora.messages.store'), ['message' => 'What is crash safety?'])
        ->assertOk()
        ->assertJsonPath('reply', 'Hi, I’m ORA. I can help you find MediSphere information about health topics, pharmacy support, psychology, and food and wellbeing. What would you like to explore?');
});

it('returns 422 when an ORA message is missing', function () {
    $this->postJson(route('ora.messages.store'), [])->assertUnprocessable()->assertJsonValidationErrors(['message']);
});
