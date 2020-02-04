<?php

namespace App\Command\Survey;

use App\Services\Survey\Denormalize\MatView;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

class DenormalizeCommand extends Command
{
    protected static $defaultName = 'survey:denormalize';

    protected Stopwatch $stopwatch;
    protected MatView $matView;

    public function __construct(Stopwatch $stopwatch, MatView $matView)
    {
        $this->stopwatch = $stopwatch;
        $this->matView = $matView;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Start: survey denormalize');

        $this->stopwatch->start(self::$defaultName);

        $this->matView->refresh();

        $stopwatchEvent = $this->stopwatch->stop(self::$defaultName);

        $io->success(sprintf('Complete in %.2f seconds', $stopwatchEvent->getDuration() / 1000));

        return 0;
    }
}
