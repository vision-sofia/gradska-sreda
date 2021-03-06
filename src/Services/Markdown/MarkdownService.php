<?php

namespace App\Services\Markdown;

class MarkdownService
{
    protected MarkdownInterface $markdown;

    public function __construct(MarkdownInterface $markdown)
    {
        $this->markdown = $markdown;
    }

    public function text(string $text): string
    {
        return $this->markdown->text($text);
    }
}
