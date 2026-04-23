<?php

namespace Taopix\ControlCentre\Helper\Loader;

class LanguageLoader
{
    public function __construct(private readonly string $projectBaseDir)
    {
    }

    public function loadLanguage(string $languageCode, string $section): array
    {
        $path = $this->projectBaseDir.'/lang/'.$languageCode.' strings.conf';
        if (!file_exists($path)) {
            throw new \Exception('Unsupported Language');
        }

        $details = \parse_ini_file($path, true);

        return match ($section) {
            '*' => \array_filter($details, function ($key) use ($details) {
                return !\is_array($details[$key]);
            }, ARRAY_FILTER_USE_KEY),
            default => $details[$section] ?? throw new \Exception('Invalid section')
        };
    }
}
