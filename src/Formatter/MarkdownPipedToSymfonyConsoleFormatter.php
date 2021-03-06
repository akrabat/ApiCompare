<?php
declare(strict_types=1);

namespace Roave\ApiCompare\Formatter;

use Roave\ApiCompare\Change;
use Roave\ApiCompare\Changes;
use Symfony\Component\Console\Output\OutputInterface;

final class MarkdownPipedToSymfonyConsoleFormatter implements OutputFormatter
{
    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function write(Changes $changes) : void
    {
        $arrayOfChanges = $changes->getIterator()->getArrayCopy();

        $this->output->writeln(
            "# Added\n"
            . implode('', $this->convertFilteredChangesToMarkdownBulletList(
                function (Change $change) : bool {
                    return $change->isAdded();
                },
                ...$arrayOfChanges
            ))
            . "\n# Changed\n"
            . implode('', $this->convertFilteredChangesToMarkdownBulletList(
                function (Change $change) : bool {
                    return $change->isChanged();
                },
                ...$arrayOfChanges
            ))
            . "\n# Removed\n"
            . implode('', $this->convertFilteredChangesToMarkdownBulletList(
                function (Change $change) : bool {
                    return $change->isRemoved();
                },
                ...$arrayOfChanges
            ))
        );
    }

    private function convertFilteredChangesToMarkdownBulletList(callable $filterFunction, Change ...$changes) : array
    {
        return array_map(
            function (Change $change) : string {
                return ' - ' . str_replace(['ADDED: ', 'CHANGED: ', 'REMOVED: '], '', trim((string)$change)) . "\n";
            },
            array_filter($changes, $filterFunction)
        );
    }
}
