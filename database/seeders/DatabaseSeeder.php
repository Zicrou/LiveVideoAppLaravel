<?php

namespace Database\Seeders;

use App\Models\Like;
use App\Models\Save;
use App\Models\User;
use App\Models\Comment;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Like::factory()->create([
            'user_id' => 'b4bf210c-83b1-4034-86e7-68e710a7f1bd',
            'video_id' => '9694d240-999b-4dce-b527-3ee9c6c1a426',
            
        ]);
        Like::factory()->create([
            'user_id' => 'b4bf210c-83b1-4034-86e7-68e710a7f1bd',
            'video_id' => '23fd9b69-d33d-4552-9af1-74def0063a27',
        ]);

        Like::factory()->create([
            'user_id' => 'edecc2ae-4eb5-4581-9424-b42e77c40392',
            'video_id' => '9694d240-999b-4dce-b527-3ee9c6c1a426', 
        ]);
        Like::factory()->create([
            'user_id' => 'edecc2ae-4eb5-4581-9424-b42e77c40392',
            'video_id' => '23fd9b69-d33d-4552-9af1-74def0063a27',
        ]);
        Like::factory()->create([
            'user_id' => 'edecc2ae-4eb5-4581-9424-b42e77c40392',
            'video_id' => 'd1da033a-1351-467c-93a2-492dc44fcf71',
        ]);

        Save::factory()->create([
            'user_id' => 'edecc2ae-4eb5-4581-9424-b42e77c40392',
            'video_id' => 'd1da033a-1351-467c-93a2-492dc44fcf71',
        ]);

        Save::factory()->create([
            'user_id' => 'edecc2ae-4eb5-4581-9424-b42e77c40392',
            'video_id' => '23fd9b69-d33d-4552-9af1-74def0063a27',
        ]);

        Save::factory()->create([
            'user_id' => 'edecc2ae-4eb5-4581-9424-b42e77c40392',
            'video_id' => '9694d240-999b-4dce-b527-3ee9c6c1a426',
        ]);

        Save::factory()->create([
            'user_id' => 'b4bf210c-83b1-4034-86e7-68e710a7f1bd',
            'video_id' => '9694d240-999b-4dce-b527-3ee9c6c1a426',
            
        ]);
        Save::factory()->create([
            'user_id' => 'b4bf210c-83b1-4034-86e7-68e710a7f1bd',
            'video_id' => '23fd9b69-d33d-4552-9af1-74def0063a27',
        ]);

        Comment::factory()->create([
            'user_id' => 'edecc2ae-4eb5-4581-9424-b42e77c40392',
            'video_id' => 'd1da033a-1351-467c-93a2-492dc44fcf71',
        ]);

        Comment::factory()->create([
            'user_id' => 'edecc2ae-4eb5-4581-9424-b42e77c40392',
            'video_id' => '23fd9b69-d33d-4552-9af1-74def0063a27',
        ]);

        Comment::factory()->create([
            'user_id' => 'edecc2ae-4eb5-4581-9424-b42e77c40392',
            'video_id' => '9694d240-999b-4dce-b527-3ee9c6c1a426',
        ]);

        Comment::factory()->create([
            'user_id' => 'b4bf210c-83b1-4034-86e7-68e710a7f1bd',
            'video_id' => '9694d240-999b-4dce-b527-3ee9c6c1a426',
            
        ]);
        Comment::factory()->create([
            'user_id' => 'b4bf210c-83b1-4034-86e7-68e710a7f1bd',
            'video_id' => '23fd9b69-d33d-4552-9af1-74def0063a27',
        ]);
    }
}
