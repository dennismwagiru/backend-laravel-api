<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = array_filter(
            config('settings.sources'),
            fn($el) => array_key_exists('service', $el) && class_exists($el['service'])
        );

        foreach ($sources as $key => $source) {
            Source::updateOrCreate([
                'name' => $source['label']
            ], [
                'api_key' => $source['api-key'],
                'service_class' => $source['service']
            ]);
        }
    }
}
