<?php


namespace App\Services\Markdown;

class Parsedown implements MarkdownInterface
{
    protected $parsedown;

    public function __construct(\Parsedown $markdown)
    {
        $this->parsedown = $markdown;
    }

    public function text(string $text): string
    {
        return $this->parsedown->text($text);
    }
}
