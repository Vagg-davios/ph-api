<?php

namespace Database\Factories;

use App\Models\Pornstar;
use Illuminate\Database\Eloquent\Factories\Factory;

class PornstarFactory extends Factory
{
    protected $model = Pornstar::class;

    public function definition(): array
    {
        return [
            'external_id' => $this->faker->unique()->randomNumber(5),
            'name' => $this->faker->firstName,
            'slug' => $this->faker->slug,
            'license' => 'PH',
            'wl_status' => 'active',
            'link' => $this->faker->url,
            'attributes' => [
                'hairColor' => $this->faker->colorName,
                'ethnicity' => $this->faker->word,
            ],
            'aliases' => [$this->faker->firstName],
            'thumbnail_path' => 'thumbnails/'.$this->faker->uuid.'.jpg'
        ];
    }
}
