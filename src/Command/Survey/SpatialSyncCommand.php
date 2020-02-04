<?php

namespace App\Command\Survey;

use App\Services\Survey\Spatial\Sync;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

class SpatialSyncCommand extends Command
{
    protected static $defaultName = 'survey:spatial:sync';

    protected Sync $spatialSync;
    protected Stopwatch $stopwatch;

    public function __construct(Sync $sync, Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
        $this->spatialSync = $sync;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Start: spatial sync');

        $this->stopwatch->start(self::$defaultName);

        $this->spatialSync->syncGeoObjects();

        $stopwatchEvent = $this->stopwatch->stop(self::$defaultName);

        $io->success(sprintf('Complete in %.2f seconds', $stopwatchEvent->getDuration() / 1000));
        $io->note(sprintf('Run "%s" if you expect new objects in survey', StyleBuildCommand::getDefaultName()));

        return 0;
    }
}
