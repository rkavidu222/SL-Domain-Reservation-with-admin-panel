<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SmsTemplates;

class SmsTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SmsTemplates::create([
        'title' => 'Job Create Notification',
        'slug' => 'job-create-inquiry-user',
        'content' => 'Hello {client_name}, your job #{job_id} has been created by {user_name}. Call us at {client_phone}.',
    ]);
    }
}
