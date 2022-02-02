<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Notifications\PrimeFound;
use App\Services\CalculadoraService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ConvertCelsius implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $farenheit;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $farenheit, int $userId)
    {
        $this->farenheit = $farenheit;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $response = app(CalculadoraService::class)->celsius($this->farenheit);

        if ($response['success']) {
            logger()->info('Celsius = ' . $response['data']);
        }
    }
}
