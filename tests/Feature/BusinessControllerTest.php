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
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        return $user;
    }


    private function mediaCreate()
    {
        //Create fake file system
        Storage::fake('public');
        //Simulate fake file on disk
        return UploadedFile::fake()->image('fake-image.jpg');
    }

    private function getBusinessData(UploadedFile $media)
    {
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
            'covered_areas' => ['Granada', 'Jaén']
        ];
        if ($media) {
            $businessData['media'] = [
                [
                    'file' => $media,
                    'type' => 'image',
                    'caption' => 'Example image'
                ]
            ];
        }
        return $businessData;
    }

    public function test_create_business()
    {
        $user = $this->getUser();

        $media = $this->mediaCreate();
        $businessData = $this->getBusinessData($media);

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

    public function test_show_business()
    {
        $user = $this->getUser();

        $media = $this->mediaCreate();
        $businessData = $this->getBusinessData($media);
        $business = Business::create(array_merge($businessData, ['user_id' => $user->id]));

        $response = $this->getJson("/api/businesses/{$business->id}");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'result' => [
                    'id',
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
                    'media',
                ],
            ]);
    }
}
