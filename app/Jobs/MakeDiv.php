<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\DivMade;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class MakeDiv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $num1;
    protected $num2;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($num1, $num2, $userId)
    {
        $this->num1 = $num1;
        $this->num2 = $num2;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::find($this->userId);

        if ($this->num2 == 0) {
            return $user->notify(new DivMade('Erro', 'Divisão por zero'));
        }

        $div = $this->num1 / $this->num2;
        return $user->notify(new DivMade('Sucesso', 'Div = ' . $div));
    }
}
