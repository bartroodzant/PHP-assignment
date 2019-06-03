<?php

namespace App\Command;


use App\Model\PrimeNumber;
use App\Services\CalculatePrimeNumbersService;
use DOMDocument;
use DOMNode;
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
            ->addArgument('fileName', InputArgument::REQUIRED, 'The name of the XML file. (no .xml needed)');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $calculatePrimeNumbersService = new CalculatePrimeNumbersService();

        $range = $input->getArgument('range');
        $primeNumberArray = [];
        $domDocument = new DOMDocument('1.0', 'UTF-8');
        $xmlRoot = $domDocument->createElement("xml");
        $xmlRoot = $domDocument->appendChild($xmlRoot);

        $output->writeln('<info>Starting calculations</info>');

        $progressBar = new ProgressBar($output, $range);
        $progressBar->setBarCharacter('<fg=green>=</>');
        $progressBar->setProgressCharacter("\xF0\x9F\x8D\xBA");

        for($i = self::START_INTEGER; $i < $range; $i += 1) {
            if($calculatePrimeNumbersService->isPrimeNumber($i)) {
                $primeNumber = $this->createPrimeNumberObject($primeNumberArray, $i);

                $this->addPrimeNumberToXml($domDocument, $xmlRoot, $primeNumber);

                $primeNumberArray[] = $primeNumber;
            }
            $progressBar->advance();
        }

        $output->writeln('');

        $domDocument->formatOutput = true;
        $domDocument->save($input->getArgument('fileName').'.xml');
        $output->writeln('<info>Saved the XML file</info>');

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

    /**
     * @param array $primeNumberArray
     * @param int $value
     * @return PrimeNumber
     */
    private function createPrimeNumberObject(array $primeNumberArray, int $value): PrimeNumber {
        $primeNumber = new PrimeNumber();
        $countFromZero = sizeof($primeNumberArray) + 1;
        $primeNumber->setValue($value)
            ->setCountFromZero($countFromZero)
            ->calculateAndsetRomanLiteral($value);
        return $primeNumber;
    }

    /**
     * @param DOMDocument $domDocument
     * @param DOMNode $xmlRoot
     * @param PrimeNumber $primeNumber
     */
    private function addPrimeNumberToXml(DOMDocument $domDocument, DOMNode $xmlRoot, PrimeNumber $primeNumber): void
    {
        $currentTrack = $domDocument->createElement("primeNumber");
        $currentTrack = $xmlRoot->appendChild($currentTrack);
        $currentTrack->appendChild($domDocument->createElement('value', $primeNumber->getValue()));
        $currentTrack->appendChild($domDocument->createElement('countFromZero', $primeNumber->getCountFromZero()));
        $currentTrack->appendChild($domDocument->createElement('romanLiteral', $primeNumber->getRomanLiteral()));
    }
}
