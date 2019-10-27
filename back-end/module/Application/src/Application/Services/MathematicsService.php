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
        $xyzNumberList = [];

        for ($round = 1; $round <= 7; $round++) {
            $lastNumber = isset($xyzNumberList[$round - 1]) ? $xyzNumberList[$round - 1] : 1;
            $lastRound = ($round - 1 === 0) ? 1 : $round - 1;
            $thisRoundNumber = ($lastRound * 2) + $lastNumber;
            $xyzNumberList[$round] = $thisRoundNumber;
        }

        return $xyzNumberList;
    }
}