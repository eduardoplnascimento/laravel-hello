<?php

namespace App\Jobs;

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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $limit)
    {
        $this->limit = $limit;
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
            logger()->info($response['message'], ['max' => $response['data']]);
        }
    }
}
