<?php

namespace App\Services\FlashMessage;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class FlashMessage
{
    public const FLASH_TYPE_SUCCESS = 'success';
    public const FLASH_TYPE_INFO = 'info';
    public const FLASH_TYPE_WARNING = 'warning';
    public const FLASH_TYPE_ERROR = 'error';

    protected FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function addSuccess(?string $title, ?string $message): void
    {
        $this->add(self::FLASH_TYPE_SUCCESS, $title, $message);
    }

    public function addInfo(?string $title, ?string $message): void
    {
        $this->add(self::FLASH_TYPE_INFO, $title, $message);
    }

    public function addWarning(?string $title, ?string $message): void
    {
        $this->add(self::FLASH_TYPE_WARNING, $title, $message);
    }

    public function addError(?string $title, ?string $message): void
    {
        $this->add(self::FLASH_TYPE_ERROR, $title, $message);
    }

    private function add(string $flashType, ?string $title, ?string $message): void
    {
        $this->flashBag->add($flashType, $this->concatMessage($title, $message));
    }

    private function concatMessage(string $title, string $message): string
    {
        return sprintf('%s|%s', $message, $title);
    }
}
