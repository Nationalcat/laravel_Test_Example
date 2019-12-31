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
     * @var LoggerService|\Illuminate\Contracts\Foundation\Application
     */
    protected $loggerService;

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

    public function batchWritingLogs(iterable $events) : void
    {
        foreach ($events as $event) {
            $country = $this->getCountry();
            $content ="{$event['content']}({$country})";
            $this->loggerService->save($event['userId'], $content);
        }
    }

    public function getCountry() : string
    {
        $response = app(Client::class)
            ->get('拿 IP 換國名的 api')
            ->getBody()
            ->getContents();

        return json_decode($response)->countryName;
    }
}
