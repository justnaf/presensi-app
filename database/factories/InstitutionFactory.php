<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Institution>
 */
class InstitutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Menghasilkan nama perusahaan yang unik untuk setiap record.
            'name' => $this->faker->unique()->company(),

            // Memilih salah satu tipe secara acak dari daftar yang disediakan.
            'type' => $this->faker->randomElement([
                'Universitas',
                'Sekolah Menengah Atas',
                'Instansi Pemerintah',
                'Perusahaan Swasta',
                'Organisasi Non-Pemerintah'
            ]),
        ];
    }
}
