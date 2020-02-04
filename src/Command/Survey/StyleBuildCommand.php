<?php

namespace App\Command\Survey;

use App\Event\Events;
use App\Services\Geospatial\StyleBuilder\StyleBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Stopwatch\Stopwatch;

class StyleBuildCommand extends Command
{
    protected static $defaultName = 'survey:style:build';

    protected Stopwatch $stopwatch;
    protected StyleBuilder $styleBuilder;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        StyleBuilder $styleBuilder,
        Stopwatch $stopwatch,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->stopwatch = $stopwatch;
        $this->styleBuilder = $styleBuilder;
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Start: style build');

        $this->stopwatch->start(self::$defaultName);

        $this->styleBuilder->build();

        $event = new GenericEvent();
        $this->eventDispatcher->dispatch($event, Events::STYLES_REBUILD);

        $stopwatchEvent = $this->stopwatch->stop(self::$defaultName);

        $io->success(sprintf('Complete in %.2f seconds', $stopwatchEvent->getDuration() / 1000));

        return 0;
    }
}
