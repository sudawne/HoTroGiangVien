<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'password' => Hash::make('123456'),
            'role_id'  => 3,
        ];
    }

    // 👨‍🏫 Lecturer
    public function lecturer()
    {
        return $this->state(fn () => [
            'role_id' => 2,
        ]);
    }

    // 🎓 Student
    public function student()
    {
        return $this->state(fn () => [
            'role_id' => 3,
        ]);
    }
}
