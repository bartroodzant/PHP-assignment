<?php

namespace App\Command;


use App\Model\PrimeNumber;
use DOMDocument;
use DOMNode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CalculatePrimeNumbersCommand extends Command {

    const START_INTEGER = 0;
    /**
     * @var ProgressBar
     */
    private $progressBar;

    protected function configure() {
        $this->setName('calculate')
            ->setDescription('Calculates prime numbers between 0 and the given limit.')
            ->setHelp('This command allows you to calculate the prime numbers between 0 and the limit that is given.')
            ->addArgument('range', InputArgument::REQUIRED, 'The range to where the prime numbers should be calculated.')
            ->addArgument('fileName', InputArgument::REQUIRED, 'The name of the XML file. (no .xml needed)');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $range = $input->getArgument('range');

        $domDocument = new DOMDocument('1.0', 'UTF-8');
        $xmlRoot = $domDocument->createElement("xml");
        $xmlRoot = $domDocument->appendChild($xmlRoot);

        $output->writeln('<info>Starting calculations</info>');

        $limit = intval(sqrt($range));

        $this->progressBar = new ProgressBar($output, $limit);
        $this->progressBar->setBarCharacter('<fg=green>=</>');
        $this->progressBar->setProgressCharacter("\xF0\x9F\x8D\xBA");

        $valueArray = $this->calculatePrimeNumbers($limit, $range);
        $primeNumberArray = $this->createPrimeNumberObjectsAndXMLObjectsFromArray($domDocument, $xmlRoot, $valueArray);

        $output->writeln('');

        $domDocument->formatOutput = true;
        $domDocument->save($input->getArgument('fileName').'.xml');
        $output->writeln('<info>Saved the XML file</info>');

        $outputString = $this->primeNumberArrayToRomanNumeralString($primeNumberArray);
        $output->writeln($outputString);
    }

    /**
     * @param int $limit
     * @param $range
     * @return array
     */
    private function calculatePrimeNumbers(int $limit, int $range): array
    {
        $valueArray = array_fill(0, $range, true);

        for ($i = 2; $i <= $limit; $i += 1) {
            if ($valueArray[$i - 1]) {
                for ($j = $i * $i; $j <= $range; $j += $i) {
                    $valueArray[$j - 1] = false;
                }
            }
            $this->progressBar->advance();
        }
        $valueArray[0] = false;
        return $valueArray;
    }

    /**
     * @param DOMDocument $domDocument
     * @param DOMNode $xmlRoot
     * @param array $valueArray
     * @return array
     */
    private function createPrimeNumberObjectsAndXMLObjectsFromArray(DOMDocument $domDocument, DOMNode $xmlRoot, array $valueArray): array
    {
        $primeNumberArray = [];
        $count = 0;
        foreach ($valueArray as $i => $is_prime) {
            if ($is_prime) {
                $count += 1;
                $value = $i + 1;

                $primeNumber = new PrimeNumber();
                $primeNumber->setValue($value)
                    ->setCountFromZero($count)
                    ->calculateAndSetRomanLiteral($value);

                $this->addPrimeNumberToXml($domDocument, $xmlRoot, $primeNumber);

                $primeNumberArray[] = $primeNumber;
            }
        }
        return $primeNumberArray;
    }

    /**
     * @param array $primeNumberArray
     * @return string
     */
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
