<?php

namespace Taopix\ControlCentre\CLI\Localisation;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Taopix\ControlCentre\Traits\CLI\Localisation\LoadStrings;

#[AsCommand(name: 'taopix:localisation:nottranslated', description: 'Export untranslated strings for each language', hidden: true)]
class ExportUntranslated extends Command
{
    use LoadStrings;

    private InputInterface|null $input = null;
    private OutputInterface|null $output = null;
    private SymfonyStyle $ui;
    private array $defaultStrings = [];
    private string $baseFolder = '';
    private array $headers = ['Language Code', 'Name', 'Untranslated', 'Path'];
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
            'exportFolder',
            'f',
            InputOption::VALUE_OPTIONAL,
            'Folder to export untranslated strings',
            '/opt/taopix/MediaAlbumWeb/langUpdates/untranslated',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $this->ui = new SymfonyStyle($input, $output);
        $this->ui->title('Untranslated strings');
        $this->ui->writeln('Loading language files from '. $this->baseFolder);
        $this->loadDefaultStrings('en');
        $this->ui->writeln('Loaded default strings from en strings.conf');
        $this->ui->progressStart(\count($this->languageList));
        $this->processOtherLanguages();
        return Command::SUCCESS;
    }

    private function processOtherLanguages(): void
    {
        //'Language Code', 'Name', 'Untranslated', 'Path'
        $exportFolder = $this->input->getOption('exportFolder');
        if (!is_dir($exportFolder)) {
            mkdir($exportFolder, 0755, true);
        }
        foreach ($this->languageList as $langCode => $langName) {
            $this->rows[$langCode] = [$langCode, $langName, 0, ''];
            $baseStrings = $this->loadStrings($this->baseFolder, $langCode);
            $untranslated = [];
            foreach ($baseStrings as $stringKey => $stringValue) {
                if (\strtolower($stringValue) !== \strtolower($this->defaultStrings['baseStrings'][$stringKey])) { continue; }
                $untranslated[] = $stringKey.' = '.$stringValue;
            }
            if (!empty($untranslated)) {
                $this->rows[$langCode][2] = \count($untranslated);
                $this->rows[$langCode][3] = $exportFolder.'/'.$langCode.' strings.txt';
                \file_put_contents($exportFolder.'/'.$langCode.' strings.txt', \implode(PHP_EOL, $untranslated));
            }
            $this->ui->progressAdvance();
        }
        $this->ui->progressFinish();
        $this->ui->table($this->headers, $this->rows);
    }
}
