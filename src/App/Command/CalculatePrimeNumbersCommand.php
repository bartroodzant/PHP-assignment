<?php

namespace App\Command;


use App\Model\PrimeNumber;
use App\Services\CalculatePrimeNumbersService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CalculatePrimeNumbersCommand extends Command {
    const START_INTEGER = 0;

    protected function configure() {
        $this->setName('calculate')
            ->setDescription('Calculates prime numbers between 0 and the given limit.')
            ->setHelp('This command allows you to calculate the prime numbers between 0 and the limit that is given.')
            ->addArgument('range', InputArgument::REQUIRED, 'The range to where the prime numbers should be calculated.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $calculatePrimeNumbersService = new CalculatePrimeNumbersService();

        $range = $input->getArgument('range');
        $primeNumberArray = [];

        $output->writeln('<info>Starting calculations</info>');

        $progressBar = new ProgressBar($output, $range);
        $progressBar->setBarCharacter('<fg=green>=</>');
        $progressBar->setProgressCharacter("\xF0\x9F\x8D\xBA");

        for($i = self::START_INTEGER; $i < $range; $i += 1) {
            if($calculatePrimeNumbersService->isPrimeNumber($i)) {
                $primeNumber = new PrimeNumber();
                $countFromZero = sizeof($primeNumberArray) + 1;
                $primeNumber->setValue($i)
                    ->setCountFromZero($countFromZero)
                    ->calculateAndsetRomanLiteral($i);

                $primeNumberArray[] = $primeNumber;
            }
            $progressBar->advance();
        }

        $output->writeln('');
        $outputString = $this->primeNumberArrayToRomanNumeralString($primeNumberArray);
        $output->writeln($outputString);
    }

    private function primeNumberArrayToRomanNumeralString(array $primeNumberArray) {
        $romanNumeralsString = '';
        $lastPrimeNumber = array_pop($primeNumberArray);

        foreach($primeNumberArray as $key => $primeNumber) {
            $romanNumeralsString .= $primeNumber->getRomanLiteral() . ', ';
        }

        $romanNumeralsString .= $lastPrimeNumber->getRomanLiteral();

        return $romanNumeralsString;
    }
}
