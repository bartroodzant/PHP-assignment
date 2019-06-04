<?php

namespace App\Services;

class CalculatePrimeNumbersService {

    /**
     * @param int $number
     * @return bool
     */
    public function isPrimeNumber(int $number) {
        // Source: https://stackoverflow.com/questions/16763322/a-formula-to-find-prime-numbers-in-a-loop
        if($number == 1)
            return false;

        if($number == 2)
            return true;

        if($number % 2 == 0) {
            return false;
        }

        $ceil = ceil(sqrt($number));
        for($i = 3; $i <= $ceil; $i = $i + 2) {
            if($number % $i == 0)
                return false;
        }

        return true;
    }

}
