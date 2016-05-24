<?php

array_shift($argv);

if ($argv) {
    include __DIR__ . '/../vendor/autoload.php';

    $tester = new \Documentation\Tester;

    foreach ($argv as $dir) {
        $tester->run($dir);
    }
} else {
    echo 'Write tested directory as parameter' . PHP_EOL;
    die(1);
}
