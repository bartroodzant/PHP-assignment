<?php


namespace App\Command;


use App\Model\PrimeNumber;
use App\Services\DatabaseService;
use DOMDocument;
use DOMElement;
use Symfony\Component\Console\Command\Command;
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

        $output->writeln('<info>Parsing and saving data</info>');

        $primeNumbers = $this->getPrimeNumbersFromXMLFile($fileName);

        $databaseService = new DatabaseService($databaseName);

        foreach ($primeNumbers as $primeNumber) {
            $databaseService->insertPrimeNumber($primeNumber);
        }

        $output->writeln('<info>All done!</info>');
    }

    /**
     * @param $fileName
     * @return array
     */
    private function getPrimeNumbersFromXMLFile($fileName): array
    {
        $primeNumbers = [];
        $domDocument = new DOMDocument();
        $domDocument->load($fileName);
        $DOMElements = $domDocument->getElementsByTagName('primeNumber');

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
        $value = $this->getElementFromXml($DOMElement, 'value');
        $countFromZero = $this->getElementFromXml($DOMElement, 'countFromZero');
        $romanLiteral = $this->getElementFromXml($DOMElement, 'romanLiteral');

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
    private function getElementFromXml(DOMElement $DOMElement, string $name) {
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
