<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Business;
use App\Traits\HandleMediaTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;

class BusinessControllerTest extends TestCase
{

    use DatabaseTransactions, HandleMediaTrait;

    private function getUser()
    {
        return User::factory()->create();
    }


    public function test_create_business()
    {
        Storage::fake('public');

        $user = $this->getUser();
        Sanctum::actingAs($user, ['*']);

        $media = UploadedFile::fake()->image('image.jpg');
        $businessData = [
            'name' => 'Business Test',
            'description' => 'Business created for automatic tests',
            'direction' => 'c/test nº test',
            'phone' => '111222333',
            'email' => 'test@example.com',
            'hours' => ['Monday' => '9am - 9pm', 'Tuesday' => '10am - 9pm'],
            'website' => 'https://www.example.com',
            'social_networks' => ['facebook' => '@test', 'instagram' => '@test'],
            'characteristics' => ['innovator', 'modern'],
            'covered_areas' => ['Granada', 'Jaén'],
            'media' => [
                [
                    'file' => $media,
                    'type' => 'image',
                    'caption' => 'Example image'
                ]
            ]
        ];

        $response = $this->postJson('/api/businesses', $businessData);

        // Check status response and structure of data
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'result' => [
                    'name',
                    'description',
                    'direction',
                    'phone',
                    'email',
                    'hours',
                    'website',
                    'social_networks',
                    'characteristics',
                    'covered_areas',
                ],
                'message'
            ]);

        $business = Business::latest()->first();

        // Check business created sucessfully
        $this->assertDatabaseHas('businesses', [
            'id' => $business->id,
            'name' => 'Business Test',
            'email' => 'test@example.com'
        ]);

        //Check file is associated with business
        $this->assertDatabaseHas('media', [
            'type' => 'image',
            'caption' => 'Example image',
            'mediaable_type' => Business::class,
            'mediaable_id' => $business->id,
        ]);
        $media = $business->media->first();

        // Check the file was stored
        Storage::disk('public')->assertExists($media->file_path);

        // Check business is associated with the correct user
        $this->assertEquals($user->id, $business->user_id);
    }
}
