<?php

namespace Application\Services;

class MathematicsService
{
    /**
     * X 5 9 15 23 Y Z
     * @return array
     */
    public function findXYZValue()
    {
        $sumPerRound = 2;
        $maxPosition = 6;
        $minPosition = 0;

        $baseValue = 9;
        $startPosition = 2;

        $arrayNum = range($minPosition, $maxPosition);//init array
        $arrayNum[$startPosition] = $baseValue;

        foreach ($arrayNum as $position => $number) {
            if ($position < $startPosition) {
                $arrayNum[$position] = $baseValue - ($sumPerRound * ($startPosition - ($position - 1)));
            } elseif ($position > $startPosition) {
                $arrayNum[$position] = $arrayNum[$position - 1] + ($sumPerRound * $position);
            }
        }

        return $arrayNum;
    }
}