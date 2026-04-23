<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__."/src/Taopix/ControlCentre/")
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
;
