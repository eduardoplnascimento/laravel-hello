<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Jobs\MakeDiv;
use App\Jobs\MakeSum;
use App\Jobs\ConvertCelsius;
use App\Notifications\DivMade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IfelseQueueTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Crie o Job app/Jobs/MakeSum que faça a soma de dois números passados para a Classe, caso o cálculo seja feito com sucesso,
     * faça o log de uma INFO com a mensagem: 'Soma = $soma', onde $soma seja o resultado da operação.
     *
     * @return void
     */
    public function test_job_make_sum()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message) {
                return strpos($message, 'Soma = 3') !== false;
            });

        MakeSum::dispatch(1, 2);
    }

    /**
     * Crie o Job app/Jobs/ConvertCelsius que faça a conversão de Farenheit para Celsius, a fórmula é: <code>($farenheit - 32) * 5 / 9</code>.
     * Ao acessar a rota GET /celsius/{farenheit}, o sistema deve colocar o Job na fila e caso o cálculo seja feito com sucesso,
     * faça o log de uma INFO com a mensagem: 'Celsius = $celsius', onde $celsius seja o resultado da conversão.
     *
     * @return void
     */
    public function test_job_convert_celsius()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Queue::fake();
        $this->get('/celsius/32');
        Queue::assertPushed(ConvertCelsius::class);
    }

    /**
     * Crie o Job <code>app/Jobs/MakeDiv</code> que faça a divisão de dois números passados para a Classe (passe o id do usuário logado também),
     * caso o cálculo seja feito com sucesso, envie uma notificação <code>App\Notifications\DivMade</code> para o usuário logado com o formato: 
     * <br><pre>[<br>    'title' => 'Sucesso',<br>    'description' => 'Div = $div'<br>]</pre><br>, onde $div seja o resultado da operação. 
     * Caso o segundo número for zero, envie uma notificação <code>App\Notifications\DivMade</code> para o usuário logado com o formato: 
     * <br><pre>[<br>    'title' => 'Erro',<br>    'description' => 'Divisão por zero'<br>]</pre><br><br>
     * <strong>Importante: deixe todos os atributos <code>$title</code> e <code>$description</code> na classe <code>DivMade</code> como públicos.</strong>
     *
     * @return void
     */
    public function test_job_make_div()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Notification::fake();

        MakeDiv::dispatch(1, 2, $user->id);

        Notification::assertSentTo(
            $user,
            function (DivMade $notification) {
                return $notification->title == 'Sucesso' && $notification->description == 'Div = 0.5';
            }
        );
    }

    /**
     * Crie o Job <code>app/Jobs/MakeDiv</code> que faça a divisão de dois números passados para a Classe (passe o id do usuário logado também),
     * caso o cálculo seja feito com sucesso, envie uma notificação <code>App\Notifications\DivMade</code> para o usuário logado com o formato: 
     * <br><pre>[<br>    'title' => 'Sucesso',<br>    'description' => 'Div = $div'<br>]</pre><br>, onde $div seja o resultado da operação. 
     * Caso o segundo número for zero, envie uma notificação <code>App\Notifications\DivMade</code> para o usuário logado com o formato: 
     * <br><pre>[<br>    'title' => 'Erro',<br>    'description' => 'Divisão por zero'<br>]</pre><br><br>
     * <strong>Importante: deixe todos os atributos <code>$title</code> e <code>$description</code> na classe <code>DivMade</code> como públicos.</strong>
     *
     * @return void
     */
    public function test_job_make_div_zero()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Notification::fake();

        MakeDiv::dispatch(1, 0, $user->id);

        Notification::assertSentTo(
            $user,
            function (DivMade $notification) {
                return $notification->title == 'Erro' && $notification->description == 'Divisão por zero';
            }
        );
    }
}