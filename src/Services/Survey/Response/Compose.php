<?php

namespace App\Services\Survey\Response;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/** @deprecated */
class Compose
{
    public function build(array $answers, ?array $files, array $currentAnswers): array
    {
        $r = [];

        $questionsToClear = [];

        // Options
        if (isset($answers['option']) && \is_array($answers['option'])) {
            foreach ($answers['option'] as $question => $a) {
                if (\is_array($a)) {
                    foreach ($a as $item) {
                        if (empty($item) && isset($currentAnswers[$question])) {
                            $questionsToClear[$question] = $currentAnswers[$question];
                        } else {
                            $r[$item] = [];
                        }
                    }
                } else {
                    $r[$a] = [];
                }
            }
        }

        // Explanation
        if (isset($answers['explanation']) && \is_array($answers['explanation'])) {
            foreach ($answers['explanation'] as $key => $item) {
                if (isset($r[$key])) {
                    $r[$key]['explanation'] = $item;
                }
            }
        }

        // Photo
        if (isset($files['photo']) && \is_array($files['photo'])) {
            foreach ($files['photo'] as $key => $item) {
                if (isset($r[$key])) {
                    $r[$key]['photo'] = $item;
                }
            }
        }

        $d = [];
        foreach ($r as $key => $item) {
            $d[] = $key;
        }

        $flattenAnswers = $this->flatten($currentAnswers);
        $discardAnswers = $this->flatten($questionsToClear);

        $new = [];
        foreach (array_diff($d, $flattenAnswers) as $value) {
            $new[$value] = $r[$value];
        }

        $old = array_diff($flattenAnswers, $d);

        foreach ($discardAnswers as $discardAnswer) {
            if (isset($new[$discardAnswer])) {
                unset($new[$discardAnswer]);
            }

            if (false !== ($key = array_search($discardAnswer, $old, true))) {
                unset($old[$key]);
            }

            if (isset($r[$discardAnswer])) {
                unset($r[$discardAnswer]);
            }
        }

        return [
            'new' => $new,
            'old' => $old,
            'all' => $r,
            'clean' => array_keys($questionsToClear),
        ];
    }

    private function flatten(array $array): array
    {
        $result = [];
        $it = new RecursiveIteratorIterator(new RecursiveArrayIterator($array));

        foreach ($it as $v) {
            $result[] = $v;
        }

        return $result;
    }
}
