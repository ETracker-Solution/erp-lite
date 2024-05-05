<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Create a size attribute
        Attribute::create([
            'name'          =>  'Size',
        ]);
        // Create a color attribute
        Attribute::create([
            'name'          =>  'Color',
        ]);
        $sizes = ['small', 'medium', 'large'];
        $colors = ['black', 'blue', 'red', 'orange'];

        foreach ($sizes as $size)
        {
            AttributeOption::create([
                'attribute_id'      =>  1,
                'value'             =>  $size
            ]);
        }

        foreach ($colors as $color)
        {
            AttributeOption::create([
                'attribute_id'      =>  2,
                'value'             =>  $color
            ]);
        }
    }
}
