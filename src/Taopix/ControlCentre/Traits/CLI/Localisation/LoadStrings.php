<?php

namespace Taopix\ControlCentre\Traits\CLI\Localisation;

trait LoadStrings
{
    private array $languageList = [
        'cs' => 'Czech',
        'da' => 'Danish',
        'de' => 'German',
        'el' => 'Greek',
        'es' => 'Spanish',
        'fi' => 'Finnish',
        'fr' => 'French',
        'hr' => 'Croatian',
        'it' => 'Italian',
        'ja' => 'Japanese',
        'ko' => 'Korean',
        'mk' => 'Macedonian',
        'nl' => 'Dutch',
        'no' => 'Norwegian',
        'pl' => 'Polish',
        'pt' => 'Portuguese Brazilian',
        'ro' => 'Romanian',
        'ru' => 'Russian',
        'sl' => 'Slovenian',
        'sv' => 'Swedish',
        'th' => 'Thai',
        'zh_cn' => 'Chinese Simplified',
        'zh_tw' => 'Chinese Traditional',
    ];

    private function loadDefaultStrings(string $langCode): void
    {
        $return = [
            'fileOrder' => [],
            'baseStrings' => [],
        ];
        $fileName = $this->baseFolder.$langCode.' strings.conf';
        if (!\file_exists($fileName)) {
            $this->defaultStrings = $return;
            return;
        }

        $lines = \explode(PHP_EOL, \file_get_contents($fileName));
        $currentSection = '';
        foreach ($lines as $lineNumber => $lineContent) {
            $hasKey = null;
            $trimmedContent = \trim($lineContent);
            if ('' === $trimmedContent || \str_starts_with($trimmedContent, '#')) {
                $currentSection = '';
            } elseif (\str_starts_with($lineContent, '[') && \str_ends_with($lineContent, ']')) {
                $currentSection = \substr($lineContent, 1, -1).'.';
            } elseif (\str_contains($lineContent, '=')) {
                list ($stringKey, $stringValue) = $this->getString($lineContent);
                $return['baseStrings'][$currentSection.$stringKey] = $stringValue;
                $hasKey = $stringKey;
            }

            $return['fileOrder'][$lineNumber] = null === $hasKey ? $lineContent : $currentSection.$hasKey;
        }
        $this->defaultStrings = $return;
    }

    private function getString(string $content): array
    {
        $details = \explode('=', $content, 2);
        $stringKey = \trim($details[0]);
        $stringValue = \trim($details[1]);
        if (!\str_starts_with($stringValue, '"')) {
            $stringValue = '"'.$stringValue;
        }
        if (!\str_ends_with($stringValue, '"')) {
            $stringValue .= '"';
        }
        return [
            $stringKey,
            $stringValue,
        ];
    }

    private function loadStrings(string $folder, string $langCode = 'en'): array
    {
        $fileName = $folder.$langCode.' strings.conf';
        if (!\file_exists($fileName)) {
            return [];
        }
        $lines = \explode(PHP_EOL, \file_get_contents($fileName));
        $strings = [];
        $currentSection = '';
        foreach ($lines as $lineNumber => $lineContent) {
            $trimmedContent = \trim($lineContent);
            if ('' === $trimmedContent || \str_starts_with($trimmedContent, '#')) {
                $currentSection = '';
                continue;
            } elseif (\str_starts_with($lineContent, '[') && \str_ends_with($lineContent, ']')) {
                $currentSection = \substr($lineContent, 1, -1).'.';
                continue;
            } elseif (!\str_contains($lineContent, '=')) {
                continue;
            }
            list ($stringKey, $stringValue) = $this->getString($lineContent);
            $strings[$currentSection.$stringKey] = $stringValue;
        }

        return $strings;
    }
}
