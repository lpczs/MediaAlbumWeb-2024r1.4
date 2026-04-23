<?php

namespace Taopix\ControlCentre\Enum\Product;

enum Type: int
{
	case PHOTO_BOOK = 0;
	case PROOF_BOOK = 1;
	case PHOTO_PRINT = 2;
	case CALENDAR = 3;
	case YEAR_BOOK = 4;
	case CANVAS = 5;
	case CARD = 6;
	case NO_PAGE = 100;
	case EASY_MODE_PHOTO_BOOK = 200;
}