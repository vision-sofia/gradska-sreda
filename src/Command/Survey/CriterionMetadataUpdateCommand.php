<?php

namespace App\Command\Survey;

use App\Services\Survey\CriterionSubject\CriterionSubjectMetadata;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CriterionMetadataUpdateCommand extends Command
{
    protected static $defaultName = 'survey:metadata:update';

    protected CriterionSubjectMetadata $criterionSubjectMetadata;

    public function __construct(CriterionSubjectMetadata $criterionSubjectMetadata)
    {
        $this->criterionSubjectMetadata = $criterionSubjectMetadata;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Start: survey metadata update');

        $this->criterionSubjectMetadata->updateMaxPoints();
        $this->criterionSubjectMetadata->sync();

        $io->success('Complete');

        return 0;
    }
}
