<?php

namespace App\Command\Survey;

use App\AppMain\Entity\Geospatial\StyleCondition;
use App\AppMain\Entity\Geospatial\StyleGroup;
use App\Event\Events;
use App\Services\Geospatial\StyleBuilder\StyleBuilder;
use Doctrine\ORM\EntityManagerInterface;
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

    protected $stopwatch;
    protected $styleBuilder;
    protected $eventDispatcher;

    public function __construct(StyleBuilder $matView, Stopwatch $stopwatch, EventDispatcherInterface $eventDispatcher)
    {
        $this->stopwatch = $stopwatch;
        $this->styleBuilder = $matView;
        $this->eventDispatcher = $eventDispatcher;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Start: style build');

        $this->stopwatch->start(self::$defaultName);

        $this->styleBuilder->build();

        $event = new GenericEvent();
        $this->eventDispatcher->dispatch(Events::STYLES_REBUILD, $event);

        $stopwatchEvent = $this->stopwatch->stop(self::$defaultName);

        $io->success(sprintf('Complete in %.2f seconds', $stopwatchEvent->getDuration() / 1000));
    }
}
