<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Bill;
use Faker\Generator;
use App\Models\Client;
use App\Repositories\BillRepository;
use App\Repositories\ClientRepository;
use App\Services\CalculadoraService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IfelseServiceTest extends TestCase
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
     * Crie a service app/Services/CalculadoraService e ao acessar o método
     * sum($num1, $num2), resposta deve vir com o formato:
     * <code>['success' => true, 'message' => 'Soma feita com sucesso', 'data' => $result]</code>.
     * Caso aconteça alguma exception, retornar a resposta no formato:
     * <code>['success' => false, 'message' => 'Erro ao fazer soma']</code>.
     *
     * @return void
     */
    public function test_service_sum_success()
    {
        $response = app(CalculadoraService::class)->sum(1, 2);

        $this->assertTrue($response['success']);
        $this->assertEquals('Soma feita com sucesso', $response['message']);
        $this->assertEquals(3, $response['data']);
    }

    /**
     * Crie a service app/Services/CalculadoraService e ao acessar o método
     * sum($num1, $num2), resposta deve vir com o formato:
     * <code>['success' => true, 'message' => 'Soma feita com sucesso', 'data' => $result]</code>.
     * Caso aconteça alguma exception, retornar a resposta no formato:
     * <code>['success' => false, 'message' => 'Erro ao fazer soma']</code>.
     *
     * @return void
     */
    public function test_service_sum_error()
    {
        $response = app(CalculadoraService::class)->sum('amarelo', 2);

        $this->assertFalse($response['success']);
        $this->assertEquals('Erro ao fazer soma', $response['message']);
    }

    /**
     * Crie a service app/Services/CalculadoraService e ao acessar o método
     * div($num1, $num2), resposta deve vir com o formato:
     * <code>['success' => true, 'message' => 'Div feita com sucesso', 'data' => $result]</code>.
     * Caso a várivel $num2 for igual a zero, retornar no seguinte formato:
     * <code>['success' => false, 'message' => 'Divisão por zero']</code>.
     * Caso aconteça alguma exception, retornar a resposta no formato:
     * <code>['success' => false, 'message' => 'Erro ao fazer div']</code>.
     *
     * @return void
     */
    public function test_service_div_success()
    {
        $response = app(CalculadoraService::class)->div(1, 2);

        $this->assertTrue($response['success']);
        $this->assertEquals('Div feita com sucesso', $response['message']);
        $this->assertEquals(0.5, $response['data']);
    }

    /**
     * Crie a service app/Services/CalculadoraService e ao acessar o método
     * div($num1, $num2), resposta deve vir com o formato:
     * <code>['success' => true, 'message' => 'Div feita com sucesso', 'data' => $result]</code>.
     * Caso a várivel $num2 for igual a zero, retornar no seguinte formato:
     * <code>['success' => false, 'message' => 'Divisão por zero']</code>.
     * Caso aconteça alguma exception, retornar a resposta no formato:
     * <code>['success' => false, 'message' => 'Erro ao fazer div']</code>.
     *
     * @return void
     */
    public function test_service_div_zero()
    {
        $response = app(CalculadoraService::class)->div(1, 0);

        $this->assertFalse($response['success']);
        $this->assertEquals('Divisão por zero', $response['message']);
    }

    /**
     * Crie a service app/Services/CalculadoraService e ao acessar o método
     * div($num1, $num2), resposta deve vir com o formato:
     * <code>['success' => true, 'message' => 'Div feita com sucesso', 'data' => $result]</code>.
     * Caso a várivel $num2 for igual a zero, retornar no seguinte formato:
     * <code>['success' => false, 'message' => 'Divisão por zero']</code>.
     * Caso aconteça alguma exception, retornar a resposta no formato:
     * <code>['success' => false, 'message' => 'Erro ao fazer div']</code>.
     *
     * @return void
     */
    public function test_service_div_error()
    {
        $response = app(CalculadoraService::class)->div('amarelo', 2);

        $this->assertFalse($response['success']);
        $this->assertEquals('Erro ao fazer div', $response['message']);
    }

    /**
     * Crie a repository app/Repositories/ClientRepository e
     * vincule-a à Model Client.
     *
     * @return void
     */
    public function test_repository_client()
    {
        $data = $this->dataProvider();
        $client = $data['clients'][0];

        $response = app(ClientRepository::class)->find($client->id);

        $this->assertEquals($client->name, $response->name);
    }

    /**
     * Crie a repository app/Repositories/BillRepository e
     * vincule-a à Model Bill.
     *
     * @return void
     */
    public function test_repository_bill()
    {
        $data = $this->dataProvider();
        $client = $data['clients'][0];
        $bill = $data['bills'][$client->id][0];

        $response = app(BillRepository::class)->find($bill->id);

        $this->assertEquals($bill->invoice, $response->invoice);
    }
}