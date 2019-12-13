<?php


namespace App\Services;


class LoggerService
{
    public function save($userId) : void
    {
        var_dump('倒數五秒拉');
        sleep(1);
        var_dump('五');
        sleep(1);
        var_dump('四');
        sleep(1);
        var_dump('三');
        sleep(1);
        var_dump('二');
        sleep(1);
        var_dump('一');
        sleep(1);
    }
}