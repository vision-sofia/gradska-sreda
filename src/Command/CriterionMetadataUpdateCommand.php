<?php

namespace App\Command;

use App\Services\Survey\CriterionSubject\CriterionSubjectMetadata;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CriterionMetadataUpdateCommand extends Command
{
    protected static $defaultName = 'metadata:cr:update';

    protected $criterionSubjectMetadata;
    public function __construct(CriterionSubjectMetadata $criterionSubjectMetadata)
    {
        $this->criterionSubjectMetadata = $criterionSubjectMetadata;
        parent::__construct();
    }


    protected function configure()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->criterionSubjectMetadata->updateMaxPoints();
        $this->criterionSubjectMetadata->sync();

        $io = new SymfonyStyle($input, $output);
        $io->success('Done');
    }
}
