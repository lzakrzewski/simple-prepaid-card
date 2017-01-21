<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in('src')
    ->in('tests');

$config = Symfony\CS\Config\Config::create();
$config->fixers([
    'align_double_arrow',
    'align_equals',
    'short_array_syntax',
    'ordered_use',
]);

$config->finder($finder);

return $config;