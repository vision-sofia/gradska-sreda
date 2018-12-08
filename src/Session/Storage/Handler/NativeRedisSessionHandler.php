<?php

namespace App\Session\Storage\Handler;

class NativeRedisSessionHandler extends \SessionHandler
{
    public function __construct(string $sessionSavePath)
    {
        ini_set('session.save_path', $sessionSavePath);
    }
}
