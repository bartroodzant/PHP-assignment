<?php

namespace App\Model;


class PrimeNumber
{
    private $value;
    private $countFromZero;
    private $romanLiteral;

    /**
     * @return int
     */
    public function getValue(): int {
        return $this->value;
    }

    /**
     * @param int $value
     * @return PrimeNumber
     */
    public function setValue(int $value): PrimeNumber
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getCountFromZero(): int {
        return $this->countFromZero;
    }

    /**
     * @param int $countFromZero
     * @return PrimeNumber
     */
    public function setCountFromZero(int $countFromZero): PrimeNumber
    {
        $this->countFromZero = $countFromZero;
        return $this;
    }

    /**
     * @return string
     */
    public function getRomanLiteral(): string {
        return $this->romanLiteral;
    }

    public function setRomanLiteral(string $romanLiteral) {
        $this->romanLiteral = $romanLiteral;
    }

    /**
     * @param int $value
     * @return PrimeNumber
     */
    public function calculateAndSetRomanLiteral(int $value): PrimeNumber
    {
        $this->romanLiteral = $this->calculateNumberToRomanLiteral($value);
        return $this;
    }

    /**
     * @param int $number
     * @return string
     */
    private function calculateNumberToRomanLiteral(int $number) {
        // List of roman literals: https://www.tuomas.salste.net/doc/roman/numeri-romani.html
        $map = [
            "M\u{0305}" => 1000000, "D\u{0305}" => 500000, "C\u{0305}" => 100000,
            "L\u{0305}" => 50000, "X\u{0305}" => 10000, "V\u{0305}" => 5000,
            'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100,
            'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9,
            'V' => 5, 'IV' => 4, 'I' => 1
        ];
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }


}
