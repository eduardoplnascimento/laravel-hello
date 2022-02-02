<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\PrimeFound;
use App\Services\CalculadoraService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FindMaxPrime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $limit;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $limit, int $userId)
    {
        $this->limit = $limit;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = app(CalculadoraService::class)->maxPrime($this->limit);

        if ($response['success']) {
            $user = User::find($this->userId);
            $user->notify(new PrimeFound(
                'https://i.imgur.com/wS0KgW8.png',
                'Primo Encontrado',
                'O maior primo é: ' . $response['data']
            ));
        }
    }
}
