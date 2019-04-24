<?php

namespace App\Command;

use App\AppMain\Entity\Geospatial\StyleCondition;
use App\AppMain\Entity\Geospatial\StyleGroup;
use App\Services\Geospatial\StyleBuilder\StyleBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

class StyleBuildCommand extends Command
{
    protected static $defaultName = 'style:build';

    protected $stopwatch;
    protected $styleBuilder;

    public function __construct(Stopwatch $stopwatch, StyleBuilder $styleBuilder)
    {
        $this->stopwatch = $stopwatch;
        $this->styleBuilder = $styleBuilder;
        parent::__construct();
    }

    protected function configure(): void
    {
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->stopwatch->start('build_styles');

        $this->styleBuilder->build();

        $duration = $this->stopwatch->stop('build_styles')->getDuration();

        $io = new SymfonyStyle($input, $output);
        $io->text(sprintf("Duration: %s\n", $duration));
        $io->success('Done');
    }
}
