<?php
// database/factories/TransactionFactory.php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        $type = $this->faker->randomElement(['income', 'expense']);
        $currency = $this->faker->randomElement(['KSH', 'USD']);
        $amount = $this->faker->randomFloat(2, 100, 10000);
        $direction = $type == 'income' ? 'in' : 'out';

        return [
            'user_id' => User::where('role', 'customer')->inRandomOrder()->first()->id ?? 1,
            'transaction_number' => 'TXN-' . date('Ymd') . '-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'transaction_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'type' => $type,
            'description' => $this->faker->sentence,
            'amount' => $amount,
            'currency' => $currency,
            'direction' => $direction,
            'balance' => $amount,
            'reference' => $this->faker->optional()->bothify('INV-####-????'),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'created_by' => User::whereIn('role', ['admin', 'finance'])->inRandomOrder()->first()->id ?? 1,
            'payment_method' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'cash', 'digital_wallet']),
            'category' => $this->faker->randomElement(['invoice_payment', 'refund', 'fee', 'salary', 'rent', 'utilities']),
            'reference_number' => $this->faker->optional()->bothify('REF-####-????'),
            'notes' => $this->faker->optional()->sentence,
            'completed_at' => $this->faker->optional()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
