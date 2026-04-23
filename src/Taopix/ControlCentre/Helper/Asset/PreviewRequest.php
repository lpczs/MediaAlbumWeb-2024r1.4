<?php

namespace Taopix\ControlCentre\Helper\Asset;

class PreviewRequest
{
    public function __construct(private string $webUrl, private array $config)
    {
    }

    public function getAssetPath($code, $type): string
    {
        $hashedPath = \md5($code);
        $folderPath = \implode(\DIRECTORY_SEPARATOR, [
            $this->correctPath($this->config['CONTROLCENTREPREVIEWSPATH'], \DIRECTORY_SEPARATOR, false),
            $type,
            $hashedPath
        ]);

        if (!\is_dir($folderPath)) {
            return '';
        }

        $files = \array_diff(\scandir($folderPath), ['..', '.']);
        if (empty($files)) {
            return '';
        }

        return $this->webUrl.'/previews/'.$type.'/'.$hashedPath.'/'.$files[2].'?version='.time();
    }

    private function correctPath(string $path, string $separator, bool $trailing = true): string
    {
        return match($trailing) {
            true => \str_ends_with($path, $separator) ? $path : $path.$separator,
            false => !\str_ends_with($path, $separator) ? $path : \substr($path, (0-\strlen($separator)))
        };
    }

}
