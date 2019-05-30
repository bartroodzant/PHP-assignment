<?php

namespace App\Services;

class CalculatePrimeNumbersService {
    public function isPrimeNumber(int $number) {
        //1 is not prime. See: http://en.wikipedia.org/wiki/Prime_number#Primality_of_one
        if($number == 1)
            return false;

        //2 is prime (the only even number that is prime)
        if($number == 2)
            return true;

        /**
         * if the number is divisible by two, then it's not prime and it's no longer
         * needed to check other even numbers
         */
        if($number % 2 == 0) {
            return false;
        }

        /**
         * Checks the odd numbers. If any of them is a factor, then it returns false.
         * The sqrt can be an aproximation, hence just for the sake of
         * security, one rounds it to the next highest integer value.
         */
        $ceil = ceil(sqrt($number));
        for($i = 3; $i <= $ceil; $i = $i + 2) {
            if($number % $i == 0)
                return false;
        }

        return true;
    }

}
