<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerOpeningBalance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerOpeningBalance>
 */
class CustomerOpeningBalanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customer_ids = CustomerOpeningBalance::pluck('customer_id')->toArray();

        $random_id = $this->faker->randomElement(Customer::pluck('id')) ;
        $last_data = CustomerOpeningBalance::latest()->first();
        if(!in_array($random_id, $customer_ids))
        {
            return [
                'uid' => $last_data ? $last_data->uid + 1 : 1,
                'date' => $this->faker->dateTimeBetween('2024-01-01', '2024-12-31')->format('Y-m-d'),
                'amount' => $this->faker->randomFloat(2, 10, 1000),
                'customer_id' => $random_id,
                'coia_id' => null,
                'remarks' => $this->faker->sentence,
            ];
        }
    }

}
