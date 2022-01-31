<?php

namespace Tests\Feature;

use Tests\TestCase;
use Faker\Generator;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IfelseFormRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ao acessar a rota POST /clients/store sem passar o nome,
     * sistema não deve criar cliente e retornar a mensagem: 'Um nome é obrigatório'
     *
     * @return void
     */
    public function test_not_store_client_without_name()
    {
        $faker = app(Generator::class);

        $user = User::factory()->create(['name' => 'Admin']);
        $this->actingAs($user);

        $input = [
            'email' => $faker->email(),
            'phone' => $faker->phoneNumber(),
            'id_number' => $faker->uuid()
        ];

        $response = $this->post('/clients/store', $input);

        $response->assertSessionHasErrors(['name' => 'Um nome é obrigatório']);
        $this->assertDatabaseMissing('clients', [
            'email' => $input['email'],
            'phone' => $input['phone'],
            'id_number' => $input['id_number']
        ]);
    }

    /**
     * Ao acessar a rota POST /clients/store passando o email vazio,
     * sistema não deve criar cliente e retornar a mensagem: 'E-mail não pode ser vazio'
     *
     * @return void
     */
    public function test_not_store_client_with_empty_email()
    {
        $faker = app(Generator::class);

        $user = User::factory()->create(['name' => 'Admin']);
        $this->actingAs($user);

        $input = [
            'name' => $faker->name(),
            'email' => '',
            'phone' => $faker->phoneNumber(),
            'id_number' => $faker->uuid()
        ];

        $response = $this->post('/clients/store', $input);

        $response->assertSessionHasErrors(['email' => 'E-mail não pode ser vazio']);
        $this->assertDatabaseMissing('clients', [
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'id_number' => $input['id_number']
        ]);
    }

    /**
     * Ao acessar a rota POST /bills/store sem passar a fatura,
     * sistema não deve criar conta e retornar a mensagem: 'Uma fatura é obrigatória'
     *
     * @return void
     */
    public function test_not_store_bill_without_invoice()
    {
        $faker = app(Generator::class);

        $user = User::factory()->create(['name' => 'Admin']);
        $this->actingAs($user);

        $client = Client::create([
            'name' => $faker->name(),
            'email' => $faker->email(),
            'phone' => $faker->phoneNumber(),
            'id_number' => $faker->uuid()
        ]);

        $input = [
            'installment' => 1,
            'value' => $faker->randomFloat(2, 1, 100),
            'client_id' => $client->id,
            'due_date' => $faker->dateTimeBetween('now', '+1 week'),
            'payment_date' => $faker->dateTimeBetween('-1 week')
        ];

        $response = $this->post('/bills/store', $input);

        $response->assertSessionHasErrors(['invoice' => 'Uma fatura é obrigatória']);
        $this->assertDatabaseMissing('bills', [
            'installment' => $input['installment'],
            'client_id' => $input['client_id'],
            'due_date' => $input['due_date'],
            'payment_date' => $input['payment_date']
        ]);
    }

    /**
     * Ao acessar a rota POST /bills/store,
     * sistema não deve autorizar usuários que tenham a palavra Guest no nome
     *
     * @return void
     */
    public function test_not_store_bill_when_unauthorized()
    {
        $faker = app(Generator::class);

        $user = User::factory()->create(['name' => 'João Guest Silva']);
        $this->actingAs($user);

        $client = Client::create([
            'name' => $faker->name(),
            'email' => $faker->email(),
            'phone' => $faker->phoneNumber(),
            'id_number' => $faker->uuid()
        ]);

        $input = [
            'invoice' => $faker->randomNumber(4),
            'installment' => 1,
            'value' => $faker->randomFloat(2, 1, 100),
            'client_id' => $client->id,
            'due_date' => $faker->dateTimeBetween('now', '+1 week'),
            'payment_date' => $faker->dateTimeBetween('-1 week')
        ];

        $response = $this->post('/bills/store', $input);

        $response->assertForbidden();
        $this->assertDatabaseMissing('bills', [
            'invoice' => $input['invoice'],
            'installment' => $input['installment'],
            'client_id' => $input['client_id'],
            'due_date' => $input['due_date'],
            'payment_date' => $input['payment_date']
        ]);
    }
}
