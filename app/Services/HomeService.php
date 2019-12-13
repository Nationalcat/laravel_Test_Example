<?php


namespace App\Services;


use App\User;
use Illuminate\Database\Eloquent\Model;

class HomeService
{
    /**
     * @var User
     */
    private $user;
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $loggerService;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->loggerService = app(LoggerService::class);
    }

    public function getUserName(int $userId) : ?string
    {
        $this->loggerService->save($userId);
        $user = $this->user->find($userId);

        return $user->name ?? null;
    }
}
