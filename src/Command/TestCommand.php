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

        $a = [
            'style' => [
                'c' => 1,
                't' => 1,
            ],
        ];

        $pre = [
            'qe' => [
                'p' => 'c',
                'c' => 'eq',
                'a' => 1,
                'v' => 'color',
                's' => '#FF00FF',
            ],
            'za' => [
                'p' => 'c',
                'c' => 'eq',
                'a' => 2,
                'v' => 'color',
                's' => '#FF0000',
            ],
            'vv' => [
                'p' => 'c',
                'c' => 'eq',
                'a' => 3,
                'v' => 'color',
                's' => '#FFFF00',
            ],
            'zz' => [
                'p' => 'c',
                'c' => 'eq',
                'a' => 3,
                'v' => 'color',
                's' => '#FFFFFF',
            ],
            'z1z' => [
                'p' => 'c',
                'c' => 'eq',
                'a' => 3,
                'v' => 'color',
                's' => '#FFFFFF',
            ],
            'zz2' => [
                'p' => 'c',
                'c' => 'eq',
                'a' => 3,
                'v' => 'color',
                's' => '#FFFFFF',
            ],
            'z3z' => [
                'p' => 'c',
                'c' => 'eq',
                'a' => 3,
                'v' => 'color',
                's' => '#FFFFFF',
            ],
            'z4z' => [
                'p' => 'c',
                'c' => 'eq',
                'a' => 3,
                'v' => 'color',
                's' => '#FFFFFF',
            ],
            'z5z' => [
                'p' => 'c',
                'c' => 'eq',
                'a' => 3,
                'v' => 'color',
                's' => '#FFFFFF',
            ],
            'z6z' => [
                'p' => 'c',
                'c' => 'eq',
                'a' => 3,
                'v' => 'color',
                's' => '#FFFFFF',
            ],
            'z7z' => [
                'p' => 'c',
                'c' => 'eq',
                'a' => 3,
                'v' => 'color',
                's' => '#FFFFFF',
            ],
        ];

        for ($i = 0; $i < 10000; ++$i) {
            foreach ($a as $item) {
                $s1 = [];

                foreach ($pre as $k => $c) {
                    if ($c['a'] === $item[$c['p']]) {
                        $s1[$c['v']]['v'] = $c['s'];
                        $s1[$c['v']]['s'] = $k;
                    }
                }

                $st = [];
                $o = [];
                $w = '';
                foreach ($s1 as $k => $ss) {
                    $w .= $ss['s'];
                    $o[$k] = $ss['v'];
                }

                $st[$w] = $o;
            }
        }

        $d = $this->stopwatch->stop('a')->getDuration();

        echo $d . PHP_EOL;
    }
}
