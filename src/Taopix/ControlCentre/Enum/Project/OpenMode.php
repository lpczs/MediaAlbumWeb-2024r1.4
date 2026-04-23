<?php

namespace Taopix\ControlCentre\Enum\Project;

enum OpenMode : int
{
    case New = 2;
    case Exisiting = 3;
    case PreviewExisting = 5;

    public static function endPoint(int $openMode): string
    {
        return match (self::tryFrom($openMode)) {
            OpenMode::New => '/api/project/create',
            OpenMode::Exisiting, OpenMode::PreviewExisting  => '/api/project/open',
        };
    }
}