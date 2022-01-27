<?php

namespace Tests\Feature;

use App\Models\Bill;
use Tests\TestCase;
use Faker\Generator;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IfelseTest extends TestCase
{
    use RefreshDatabase;

    public function dataProvider(
        $numClients = 1,
        $numBills = 1,
        $numInstallment = 1,
        $billMinValue = 1,
        $billMaxValue = 100
    ) {
        $faker = app(Generator::class);

        $clients = [];
        $bills = [];
        for ($i = 0; $i < $numClients; $i++) {
            $client = Client::create([
                'name' => $faker->name(),
                'email' => $faker->email(),
                'phone' => $faker->phoneNumber(),
                'id_number' => $faker->uuid()
            ]);

            $bills[$client->id] = [];

            for ($j = 0; $j < $numBills; $j++) {
                $invoice = $faker->randomNumber(4);

                for ($k = 0; $k < $numInstallment; $k++) {
                    $bills[$client->id][] = Bill::create([
                        'invoice' => $invoice,
                        'installment' => $k + 1,
                        'value' => $faker->randomFloat(2, $billMinValue, $billMaxValue),
                        'client_id' => $client->id,
                        'due_date' => $faker->dateTimeBetween('now', '+1 week'),
                        'payment_date' => $faker->dateTimeBetween('-1 week')
                    ]);
                }

            }

            $clients[] = $client;
        }


        return compact('clients', 'bills');
    }

    /**
     * Ao acessar a rota POST /clients/store,
     * sistema deve criar cliente com os dados passados
     *
     * @return void
     */
    public function test_store_client()
    {
        $faker = app(Generator::class);
        $client = [
            'name' => $faker->name(),
            'email' => $faker->email(),
            'phone' => $faker->phoneNumber(),
            'id_number' => $faker->uuid()
        ];

        $this->post('/clients/store', $client);

        $this->assertDatabaseHas('clients', [
            'name' => $client['name'],
            'email' => $client['email'],
            'phone' => $client['phone'],
            'id_number' => $client['id_number']
        ]);
    }

    /**
     * Ao acessar a rota GET /clients/show/{client},
     * sistema deve retornar um JSON com as informações do {client}
     *
     * @return void
     */
    public function test_client_info()
    {
        $data = $this->dataProvider();
        $client = $data['clients'][0];

        $response = $this->get('/clients/show/' . $client->id);

        $response->assertJson([
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'id_number' => $client->id_number
        ]);
    }

    /**
     * Ao acessar a rota GET /clients/name/{name},
     * sistema deve retornar um JSON com as informações do cliente com de nome {name}
     *
     * @return void
     */
    public function test_client_name()
    {
        $data = $this->dataProvider();
        $client = $data['clients'][0];

        $response = $this->get('/clients/name/' . $client->name);

        $response->assertJson([
            'id' => $client->id,
            'name' => $client->name,
            'email' => $client->email,
            'id_number' => $client->id_number
        ]);
    }

    /**
     * Ao acessar a rota GET /clients/search/{text},
     * sistema deve retornar um JSON com as informações dos clientes que tem {text} no nome
     *
     * @return void
     */
    public function test_client_search()
    {
        $faker = app(Generator::class);

        $client1 = Client::create([
            'name' => 'Joao Silva Souza',
            'email' => $faker->email(),
            'phone' => $faker->phoneNumber(),
            'id_number' => $faker->uuid()
        ]);

        $client2 = Client::create([
            'name' => 'Jose Silva Marques',
            'email' => $faker->email(),
            'phone' => $faker->phoneNumber(),
            'id_number' => $faker->uuid()
        ]);

        $client3 = Client::create([
            'name' => 'Maria Souza Marques',
            'email' => $faker->email(),
            'phone' => $faker->phoneNumber(),
            'id_number' => $faker->uuid()
        ]);

        $response = $this->get('/clients/search/Silva');

        $response->assertJsonFragment(['id' => $client1->id]);
        $response->assertJsonFragment(['id' => $client2->id]);
        $response->assertJsonMissing(['id' => $client3->id]);
    }

    /**
     * Ao acessar a rota GET /clients/bills/{client},
     * sistema deve retornar um JSON com as contas do {client}
     *
     * @return void
     */
    public function test_client_bills()
    {
        $data = $this->dataProvider(2, 2);
        $client = $data['clients'][0];
        $clientWrong = $data['clients'][1];
        $bills = $data['bills'][$client->id];
        $billsWrong = $data['bills'][$clientWrong->id];

        $response = $this->get('/clients/bills/' . $client->id);

        foreach ($bills as $bill) {
            $response->assertJsonFragment([
                'id' => $bill->id,
                'invoice' => $bill->invoice,
                'installment' => $bill->installment,
                'value' => $bill->value,
                'client_id' => $client->id
            ]);
        }

        foreach ($billsWrong as $bill) {
            $response->assertJsonMissing([
                'id' => $bill->id,
                'invoice' => $bill->invoice,
                'client_id' => $clientWrong->id
            ]);
        }
    }

    /**
     * Ao acessar a rota GET /bills/expensive/{value},
     * sistema deve retornar um JSON com todas as contas maiores que {value}
     *
     * @return void
     */
    public function test_bills_expensive()
    {
        $dataLess = $this->dataProvider(1, 2, 1, 1, 50);
        $clientLess = $dataLess['clients'][0];
        $billsLess = $dataLess['bills'][$clientLess->id];

        $dataGreater = $this->dataProvider(1, 2, 1, 60, 100);
        $clientGreater = $dataGreater['clients'][0];
        $billsGreater = $dataGreater['bills'][$clientGreater->id];

        $response = $this->get('/bills/expensive/55');

        foreach ($billsGreater as $bill) {
            $response->assertJsonFragment([
                'id' => $bill->id,
                'invoice' => $bill->invoice,
                'installment' => $bill->installment,
                'value' => $bill->value,
                'client_id' => $clientGreater->id
            ]);
        }

        foreach ($billsLess as $bill) {
            $response->assertJsonMissing([
                'id' => $bill->id,
                'invoice' => $bill->invoice,
                'client_id' => $clientLess->id
            ]);
        }
    }

    /**
     * Ao acessar a rota GET /bills/between/{value1}/{value2},
     * sistema deve retornar um JSON com todas as contas maiores que {value1} e menores que {value2}
     *
     * @return void
     */
    public function test_bills_between()
    {
        $dataLess = $this->dataProvider(1, 2, 1, 1, 20);
        $clientLess = $dataLess['clients'][0];
        $billsLess = $dataLess['bills'][$clientLess->id];

        $dataOk = $this->dataProvider(1, 2, 1, 25, 50);
        $clientOk = $dataOk['clients'][0];
        $billsOk = $dataOk['bills'][$clientOk->id];

        $dataGreater = $this->dataProvider(1, 2, 1, 60, 100);
        $clientGreater = $dataGreater['clients'][0];
        $billsGreater = $dataGreater['bills'][$clientGreater->id];

        $response = $this->get('/bills/between/21/55');

        foreach ($billsOk as $bill) {
            $response->assertJsonFragment([
                'id' => $bill->id,
                'invoice' => $bill->invoice,
                'installment' => $bill->installment,
                'value' => $bill->value,
                'client_id' => $clientOk->id
            ]);
        }

        foreach ($billsLess as $bill) {
            $response->assertJsonMissing([
                'id' => $bill->id,
                'invoice' => $bill->invoice,
                'client_id' => $clientLess->id
            ]);
        }

        foreach ($billsGreater as $bill) {
            $response->assertJsonMissing([
                'id' => $bill->id,
                'invoice' => $bill->invoice,
                'client_id' => $clientGreater->id
            ]);
        }
    }

    /**
     * Ao acessar a rota GET /clients/order,
     * sistema deve retornar um JSON com as informações dos dois primeiros clientes ordenados pelo nome
     *
     * @return void
     */
    public function test_client_order()
    {
        $faker = app(Generator::class);

        $client1 = Client::create([
            'name' => 'Joao Silva Souza',
            'email' => $faker->email(),
            'phone' => $faker->phoneNumber(),
            'id_number' => $faker->uuid()
        ]);

        $client2 = Client::create([
            'name' => 'Ana Silva Marques',
            'email' => $faker->email(),
            'phone' => $faker->phoneNumber(),
            'id_number' => $faker->uuid()
        ]);

        $client3 = Client::create([
            'name' => 'Maria Souza Marques',
            'email' => $faker->email(),
            'phone' => $faker->phoneNumber(),
            'id_number' => $faker->uuid()
        ]);

        $client4 = Client::create([
            'name' => 'Diego Souza Marques',
            'email' => $faker->email(),
            'phone' => $faker->phoneNumber(),
            'id_number' => $faker->uuid()
        ]);

        $response = $this->get('/clients/order');

        $response->assertJsonFragment(['id' => $client2->id]);
        $response->assertJsonFragment(['id' => $client4->id]);
        $response->assertJsonMissing(['id' => $client1->id]);
        $response->assertJsonMissing(['id' => $client3->id]);
    }
}
