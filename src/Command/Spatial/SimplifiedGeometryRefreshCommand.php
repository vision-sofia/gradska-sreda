<?php

namespace App\Command\Spatial;

use App\Services\Geospatial\Simplify;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

class SimplifiedGeometryRefreshCommand extends Command
{
    protected static $defaultName = 'spatial:simplified:refresh';

    protected $simplify;
    protected $stopwatch;

    public function __construct(Simplify $simplify, Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
        $this->simplify = $simplify;
        parent::__construct();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Start: refresh simplified geometries');

        $this->stopwatch->start(self::$defaultName);

        $this->simplify->refresh();

        $stopwatchEvent = $this->stopwatch->stop(self::$defaultName);

        $io->success(sprintf('Complete in %.2f seconds', $stopwatchEvent->getDuration() / 1000));
    }
}
