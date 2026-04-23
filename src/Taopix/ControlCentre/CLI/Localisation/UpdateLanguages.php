<?php

namespace Taopix\ControlCentre\CLI\Localisation;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Taopix\ControlCentre\Traits\CLI\Localisation\LoadStrings;

#[AsCommand(name: 'taopix:localisation:update', description: 'Update language files to match english file', hidden: true)]
class UpdateLanguages extends Command
{
    use LoadStrings;

    private InputInterface|null $input = null;
    private OutputInterface|null $output = null;
    private SymfonyStyle $ui;
    private array $defaultStrings = [];
    private string $baseFolder = '';
    private array $headers = ['Language Code', 'Name', 'Updated strings', 'Base strings', 'Status'];
    private array $rows = [];

    public function __construct(string|null $name, string|null $path)
    {
        parent::__construct($name);
        $this->baseFolder = $path.'/lang/';
    }

    public function configure(): void
    {
        parent::configure();
        $this->addOption(
            'releaseVersion',
            'r',
            InputOption::VALUE_OPTIONAL,
            'Update language file version number',
        )->addOption(
            'updateFolder',
            'f',
            InputOption::VALUE_OPTIONAL,
            'Folder with string updates'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $this->ui = new SymfonyStyle($input, $output);
        $this->ui->title('Language file updates');
        $this->ui->writeln('Loading language files from '. $this->baseFolder);
        $this->loadDefaultStrings('en');
        $this->ui->writeln('Loaded default strings from en strings.conf');
        $this->ui->progressStart(\count($this->languageList));
        $this->processOtherLanguages();
        return Command::SUCCESS;
    }

    private function processOtherLanguages(): void
    {
        $updateFolder = $this->input->getOption('updateFolder');
        $langaugeStringVersion = $this->input->getOption('releaseVersion');
        $versionString = null !== $langaugeStringVersion ? ['str_LangVersion' => '"'.$langaugeStringVersion.'"'] : [];
        foreach ($this->languageList as $langCode => $langName) {
            $updateStrings = [];
            $this->rows[$langCode] = [$langCode, $langName, 'Yes', 'Yes', 'Done'];
            if (null !== $updateFolder) { $updateStrings = $this->loadStrings($updateFolder, $langCode); }
            $baseStrings = $this->loadStrings($this->baseFolder, $langCode);
            $mergedStrings = \array_merge(
                $this->defaultStrings['baseStrings'],
                $baseStrings,
                $updateStrings,
                $versionString
            );
            if (empty($updateStrings)) { $this->rows[$langCode][2] = '<error>No</error>'; }
            if (empty($baseStrings)) { $this->rows[$langCode][3] = '<error>No</error>'; }
            $newContent = [];
            foreach ($this->defaultStrings['fileOrder'] as $line => $content) {
                $keyName = $content;
                if (\str_contains($content, '.')) {
                    list($section, $keyName) = \explode('.', $content, 2);
                }
                if (!\str_starts_with($keyName, 'str_') && !\str_starts_with($keyName, 'k')) {
                    $newContent[] = 0 === $line ? "# {$langName} Strings" : $content;
                } else {
                    $newString = $mergedStrings[$content];
                    if ($this->defaultStrings['baseStrings'][$content] === $mergedStrings[$content]) {
                        if (\str_contains($content, '.')) {
                            if (\array_key_exists($keyName, $updateStrings)) {
                                $newString = $updateStrings[$keyName];
                            }
                        }
                    }
                    $newContent[] = $keyName.' = '.$newString;
                }
            }
            \file_put_contents($this->baseFolder.$langCode.' strings.conf', \implode(PHP_EOL, $newContent));
            $this->ui->progressAdvance();
        }
        $this->ui->progressFinish();
        $this->ui->table($this->headers, $this->rows);
    }
}
