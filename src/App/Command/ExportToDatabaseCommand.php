<?php


namespace App\Command;


use App\Model\PrimeNumber;
use App\Services\DatabaseService;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportToDatabaseCommand extends Command {

    protected function configure() {
        $this->setName('export')
            ->setDescription('Exports the XML file from the calculate command to a database.')
            ->setHelp('This command allows you to export the generated XML file of the calculate command to a database.')
            ->addArgument('fileName', InputArgument::REQUIRED, 'The name of the XML file.')
            ->addArgument('databaseName', InputArgument::REQUIRED, 'The name of the SQLite database. (no .sqlite needed)');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $fileName = $input->getArgument('fileName');
        $databaseName = $input->getArgument('databaseName');

        $this->validateFile($fileName, $output);

        $output->writeln('<info>Parsing data ...</info>');

        $primeNumbers = $this->getPrimeNumbersFromXMLFile($fileName, $output);

        $databaseService = new DatabaseService($databaseName);

        $output->writeln('<info>Saving data ...</info>');

        $databaseService->insertAllPrimeNumbers($primeNumbers);

        $output->writeln('<info>All done!</info>');
    }

    /**
     * Documentation for DOMDocument: https://www.php.net/manual/en/class.domdocument.php
     * Using DOMXpath increases performance significantly.
     * Found based on: https://stackoverflow.com/questions/9367069/simplexml-vs-domdocument-performance
     * @param $fileName
     * @return array
     */
    private function getPrimeNumbersFromXMLFile($fileName, $output): array
    {
        $primeNumbers = [];
        $domDocument = new DOMDocument();
        $domDocument->load($fileName);
        $xpath = new DOMXpath($domDocument);
        $DOMElements = $xpath->query("primeNumber");

        foreach ($DOMElements as $DOMElement) {
            $primeNumbers[] = $this->createPrimeNumberFromDOMElement($DOMElement);
        }

        return $primeNumbers;
    }

    /**
     * @param DOMElement $DOMElement
     * @return PrimeNumber
     */
    private function createPrimeNumberFromDOMElement(DOMElement $DOMElement) {
        $value = $this->getElementFromXmlByTagName($DOMElement, 'value');
        $countFromZero = $this->getElementFromXmlByTagName($DOMElement, 'countFromZero');
        $romanLiteral = $this->getElementFromXmlByTagName($DOMElement, 'romanLiteral');

        $primeNumber = new PrimeNumber();
        $primeNumber->setValue($value)
            ->setCountFromZero($countFromZero)
            ->setRomanLiteral($romanLiteral);

        return $primeNumber;
    }

    /**
     * @param DOMElement $DOMElement
     * @param string $name
     * @return string
     */
    private function getElementFromXmlByTagName(DOMElement $DOMElement, string $name) {
        return $DOMElement->getElementsByTagName($name)->item(0)->nodeValue;
    }

    /**
     * @param string $fileName
     * @param OutputInterface $output
     */
    private function validateFile(string $fileName, OutputInterface $output) {
        if (!file_exists($fileName)) {
            $this->outputErrorMessageAndExit($output, "The file ".$fileName.".xml doesn't exist");
        }
    }

    /**
     * This function can be encapsulated in the future to remove double code
     * @param OutputInterface $output
     * @param string $message
     */
    private function outputErrorMessageAndExit(OutputInterface $output, string $message): void {
        $output->writeln('<error>'.$message.'</error>');
        exit(1);
    }

}
