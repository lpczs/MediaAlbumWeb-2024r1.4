<?php

namespace Taopix\ControlCentre\Common;

class URLFormatting
{
    public function correctURL(string $url, string $separator = "/", bool $trailing = true): string
    {
        $lastChar = substr($url, -1, 1);

        if (($trailing == true) && ($lastChar != $separator))
        {
            $url = $url . $separator;
        }
        elseif (($trailing == false) && ($lastChar == $separator))
        {
            $url = substr($url, 0, strlen($url) - 1);
        }

        return $url;
    }    
}
