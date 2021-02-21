<?php

namespace App\Jobs;

use App\Repositories\User\PasswordResetRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InvalidateResetToken implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var string
     */
    private $token;

    /**
     * InvalidateResetToken constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @param PasswordResetRepositoryInterface $resetRepository
     * @return void
     *
     */
    public function handle(PasswordResetRepositoryInterface $resetRepository)
    {
        $resetRepository->deleteById($this->token);
    }
}
