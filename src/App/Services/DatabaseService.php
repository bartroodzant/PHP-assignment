<?php


namespace App\Services;


use App\Model\PrimeNumber;
use SQLite3;

class DatabaseService
{
    private $database;

    public function __construct(String $databaseName) {
        $this->database = new SQLite3($databaseName.'.sqlite');
        $this->createTable();
    }

    private function createTable(): void {
        $sql = 'CREATE TABLE IF NOT EXISTS prime_numbers (
                        id INTEGER PRIMARY KEY,
                        value INTEGER NOT NULL,
                        count_from_zero INTEGER NOT NULL,
                        roman_literal TEXT NOT NULL,
                        UNIQUE(value)
                      )';

        $this->database->exec($sql);
    }

    public function insertPrimeNumber(PrimeNumber $primeNumber) {
        $sql = 'INSERT OR IGNORE INTO prime_numbers(value, count_from_zero, roman_literal) 
                VALUES(:value, :count_from_zero, :roman_literal)';
        $statement = $this->database->prepare($sql);
        $statement->bindValue(':value', $primeNumber->getValue());
        $statement->bindValue(':count_from_zero', $primeNumber->getCountFromZero());
        $statement->bindValue(':roman_literal', $primeNumber->getRomanLiteral());
        $statement->execute();
    }
}
