<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class TestCommand extends Command
{
    protected static $defaultName = 'test';

    protected $stopwatch;

    public function __construct(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->stopwatch->start('a');

        $item = [
            'style' => [
                '_s1'        => 'aaa',
                'collection' => 25,
                'complete'   => 1,

            ],
        ];

        $styles = [
            'aaa' => [
                'color'  => '#FF0000',
                'weight' => 1,
            ],
        ];

        $pre = [
            'collection' => [
                [
                    'c'   => 'eq',
                    'a'   => 25,
                    'v'   => 'color',
                    's'   => '#FFf0FF',
                    'key' => 'b',
                ],
            ],
            'complete'   => [
                [
                    'c'   => 'eq',
                    'a'   => 1,
                    'v'   => 'color',
                    's'   => 0.5,
                    'key' => 'g',
                ],
            ],
        ];


       # $set->find(1);
        for ($i = 0; $i < 10000; ++$i) {
            $s1 = [
                'key' => 0,
            ];



            foreach ($item['style'] as $k => $v) {
                if (!isset($pre[$k])) {
                    continue;
                }

                foreach ($pre[$k] as $z) {
                    if ($item['style'][$k] === $z['a']) {
                        $s1[$z['v']] = $z['s'];
                        $s1['key'] .= $z['key'];
                    }
                }
            }

            if (!empty($s1['key'])) {
                $os = $styles[$item['style']['_s1']];
                //dump(array_merge($os, $s1));
                $r = $item['style']['_s1'];
                $nn = $r . '-' . $s1['key'];
                //  dump($nn);
                //   dump(array_merge($os, $s1));
            }

            //  print_r($s1);
        }

        $d = $this->stopwatch->stop('a')->getDuration();

        echo $d . PHP_EOL;
    }
}
