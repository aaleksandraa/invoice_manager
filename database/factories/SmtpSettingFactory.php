<?php

namespace Database\Factories;

use App\Models\SmtpSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SmtpSetting>
 */
class SmtpSettingFactory extends Factory
{
    protected $model = SmtpSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => 587,
            'smtp_username' => fake()->email(),
            'smtp_password' => 'test-password',
            'from_email' => fake()->email(),
            'from_name' => fake()->company(),
            'encryption' => 'tls',
        ];
    }
}
