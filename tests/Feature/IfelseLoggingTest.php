<?php

namespace Tests\Feature;

use Tests\TestCase;
use Faker\Generator;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IfelseLoggingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ao acessar a rota GET /soma/{num1}/{num2},
     * sistema deve logar uma INFORMAÇÃO com a frase 'Soma feita'
     * e retornar a soma dos dois números
     *
     * @return void
     */
    public function test_soma_info()
    {
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message) {
                return strpos($message, 'Soma feita') !== false;
            });

        $faker = app(Generator::class);
        $num1 = $faker->randomNumber(2);
        $num2 = $faker->randomNumber(2);

        $response = $this->get('/soma/' . $num1 . '/' . $num2);

        $response->assertSee(($num1 + $num2));
    }

    /**
     * Ao acessar a rota GET /sub/{num1}/{num2},
     * sistema deve logar um DEBUG com a frase 'Sub feita'
     * e adicione as informações adicionais: ['num1' => $num1, 'num2' => $num2, 'sub' => {a subtração dos números}]
     *
     * @return void
     */
    public function test_sub_debug()
    {
        $faker = app(Generator::class);
        $num1 = $faker->randomNumber(2);
        $num2 = $faker->randomNumber(2);
        $sub = $num1 - $num2;

        Log::shouldReceive('debug')
            ->once()
            ->withArgs(function ($message, $options) use ($num1, $num2, $sub) {
                return strpos($message, 'Sub feita') !== false &&
                    $options['num1'] == $num1 &&
                    $options['num2'] == $num2 &&
                    $options['sub'] == $sub;
            });

        $this->get('/sub/' . $num1 . '/' . $num2);
    }

    /**
     * Ao acessar a rota GET /div/{num1}/{num2},
     * sistema deve logar um ERRO com a frase 'Divisor zero!'
     * quando o num2 for zero
     *
     * @return void
     */
    public function test_div_error()
    {
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return strpos($message, 'Divisor zero!') !== false;
            });

        $faker = app(Generator::class);
        $num1 = $faker->randomNumber(2);
        $num2 = 0;

        $this->get('/div/' . $num1 . '/' . $num2);
    }

    /**
     * Ao acessar a rota GET /div/{num1}/{num2},
     * sistema deve logar um ERRO com a frase 'Divisor zero!'
     * quando o num2 for zero, ou logar uma INFORMAÇÃO 'Div feita'
     * quando o divisor não for zero
     *
     * @return void
     */
    public function test_div_normal()
    {
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message) {
                return strpos($message, 'Div feita') !== false;
            });

        $faker = app(Generator::class);
        $num1 = $faker->randomNumber(2);
        $num2 = 1;

        $this->get('/div/' . $num1 . '/' . $num2);
    }

    /**
     * Ao acessar a rota GET /mult/{num1}/{num2},
     * sistema deve logar um WARNING com a frase 'Negativo'
     * quando algum dos números passados for negativo
     *
     * @return void
     */
    public function test_mult_warning1()
    {
        $faker = app(Generator::class);
        $num1 = -1;
        $num2 = $faker->randomNumber(2);

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message) {
                return strpos($message, 'Negativo') !== false;
            });

        $this->get('/mult/' . $num1 . '/' . $num2);
    }

    /**
     * Ao acessar a rota GET /mult/{num1}/{num2},
     * sistema deve logar um WARNING com a frase 'Negativo'
     * quando algum dos números passados for negativo
     *
     * @return void
     */
    public function test_mult_warning2()
    {
        $faker = app(Generator::class);
        $num1 = $faker->randomNumber(2);
        $num2 = -1;

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message) {
                return strpos($message, 'Negativo') !== false;
            });

        $this->get('/mult/' . $num1 . '/' . $num2);
    }
}
