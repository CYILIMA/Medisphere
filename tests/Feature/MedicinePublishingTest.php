<?php

use App\Models\Medicine;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function fakeDoctorImage(): UploadedFile
{
    $width = 200;
    $height = 200;
    $chunk = static fn (string $type, string $data): string => pack('N', strlen($data))
        .$type.$data.pack('N', crc32($type.$data));
    $pixels = str_repeat("\0".str_repeat("\xB8\xC4\xB8\xFF", $width), $height);
    $png = "\x89PNG\r\n\x1a\n"
        .$chunk('IHDR', pack('N2', $width, $height)."\x08\x06\x00\x00\x00")
        .$chunk('IDAT', gzcompress($pixels))
        .$chunk('IEND', '');

    return UploadedFile::fake()->createWithContent('doctor.png', $png);
}

it('redirects guests to admin login', function () {
    $this->get(route('admin.medicines.index'))
        ->assertRedirect(route('admin.login'));
});

it('denies dashboard access to authenticated non-admin users', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.medicines.index'))
        ->assertForbidden();
});

it('allows administrators to sign in', function () {
    $admin = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => 'correct-password',
        'is_admin' => true,
    ]);

    $this->post(route('admin.login.store'), [
        'email' => $admin->email,
        'password' => 'correct-password',
    ])->assertRedirect(route('admin.medicines.index'));

    $this->assertAuthenticatedAs($admin);
});

it('does not authenticate non-admin users with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => 'correct-password',
    ]);

    $this->post(route('admin.login.store'), [
        'email' => $user->email,
        'password' => 'correct-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('lets an administrator create a published medicine page with a doctor contact', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get(route('admin.medicines.index'))
        ->assertOk()
        ->assertSeeText('Medicine library');

    $this->actingAs($admin)->post(route('admin.medicines.store'), [
        'title' => 'Seasonal Allergy Care',
        'medicine_name' => 'Antihistamine',
        'category' => 'Allergy care',
        'condition' => 'Seasonal allergies',
        'summary' => 'Information about common seasonal allergy care.',
        'description' => 'General information reviewed by a doctor.',
        'doctor_name' => 'Dr. Morgan Lee',
        'doctor_specialty' => 'Family medicine',
        'doctor_email' => 'morgan@example.com',
        'doctor_phone' => '+1 555 0100',
        'doctor_image' => fakeDoctorImage(),
        'is_published' => '1',
    ])->assertRedirect(route('admin.medicines.index'));

    $this->assertDatabaseHas('medicines', [
        'slug' => 'seasonal-allergy-care',
        'is_published' => true,
        'doctor_name' => 'Dr. Morgan Lee',
    ]);

    $medicine = Medicine::firstOrFail();
    $this->assertTrue(Storage::disk('public')->exists($medicine->doctor_image_path));

    $this->get(route('medicines.show', $medicine->slug))
        ->assertOk()
        ->assertSeeText('Dr. Morgan Lee')
        ->assertSeeText('morgan@example.com')
        ->assertSeeText('Seasonal allergies');
});

it('keeps draft medicine pages out of the public library', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.medicines.store'), [
        'title' => 'Draft Medicine Page',
        'medicine_name' => 'Example medicine',
        'category' => 'General care',
        'condition' => 'Example condition',
        'summary' => 'A draft that is not ready for visitors.',
        'description' => 'Draft content.',
        'doctor_name' => 'Dr. Taylor',
        'doctor_specialty' => 'Primary care',
        'doctor_email' => 'doctor@example.com',
        'doctor_image' => fakeDoctorImage(),
    ])->assertRedirect(route('admin.medicines.index'));

    $medicine = Medicine::firstOrFail();

    $this->get(route('medicines.index'))->assertOk()->assertDontSeeText('Draft Medicine Page');
    $this->get(route('medicines.show', $medicine->slug))->assertNotFound();
});

it('allows administrators to replace the doctor photo and unpublish a page', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->post(route('admin.medicines.store'), [
        'title' => 'Medicine Information',
        'medicine_name' => 'Example medicine',
        'category' => 'General care',
        'condition' => 'Example condition',
        'summary' => 'An information summary.',
        'description' => 'Helpful details.',
        'doctor_name' => 'Dr. Taylor',
        'doctor_specialty' => 'Primary care',
        'doctor_email' => 'doctor@example.com',
        'doctor_image' => fakeDoctorImage(),
        'is_published' => '1',
    ])->assertRedirect(route('admin.medicines.index'));

    $medicine = Medicine::firstOrFail();
    $previousImagePath = $medicine->doctor_image_path;

    $this->actingAs($admin)->put(route('admin.medicines.update', $medicine), [
        'title' => 'Updated Medicine Information',
        'medicine_name' => 'Example medicine',
        'category' => 'General care',
        'condition' => 'Example condition',
        'summary' => 'Updated information summary.',
        'description' => 'Updated helpful details.',
        'doctor_name' => 'Dr. Taylor',
        'doctor_specialty' => 'Primary care',
        'doctor_email' => 'doctor@example.com',
        'doctor_image' => fakeDoctorImage(),
    ])->assertRedirect(route('admin.medicines.index'));

    $medicine->refresh();
    expect($medicine->is_published)->toBeFalse();
    $this->assertFalse(Storage::disk('public')->exists($previousImagePath));
    $this->assertTrue(Storage::disk('public')->exists($medicine->doctor_image_path));
    $this->get(route('medicines.show', $medicine->slug))->assertNotFound();
});
