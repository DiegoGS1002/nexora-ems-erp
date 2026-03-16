<?php
require __DIR__.'/vendor/autoload.php';
use Illuminate\Support\Str;
$name = '163928_create_employees_table';
$parts = explode('_', $name);
$class = Str::studly(implode('_', array_slice($parts, 4)));
echo 'parts: '.implode(',', $parts)."\n";
echo 'class: '.$class."\n";
