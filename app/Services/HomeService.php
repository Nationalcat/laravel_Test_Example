<?php


namespace App\Services;


use App\User;
use GuzzleHttp\Client;


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
    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    private $client;

    public function __construct(User $user)
    {
        $this->user          = $user;
        $this->loggerService = app(LoggerService::class);
    }

    public function getUserName(int $userId) : ?string
    {
        $this->loggerService->save($userId);
        $user = $this->user->find($userId);

        return $user->name ?? null;
    }

    public function getWeather()
    {
        $response = app(Client::class)
            ->get('天氣預報 api')
            ->getBody()
            ->getContents();

        return json_decode($response)->today;
    }
}
