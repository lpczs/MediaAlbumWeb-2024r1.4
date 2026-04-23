<?php

namespace Taopix\ControlCentre\Enum\Experience;

enum ExperienceType: int
{
    case FULL = 0;
    case SETTINGS = 1;
    case WIZARD = 2;
    case EDITOR = 3;
}