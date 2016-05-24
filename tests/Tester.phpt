<?php

use Tester\Assert;

include __DIR__ . '/bootstrap.php';


$tester = new Documentation\Tester();
$tester->testLatte('{var $a = 10}');
Assert::same(0, $tester->getErrorCount());


$tester = new Documentation\Tester();
$tester->testLatte('{wrongCode}');
Assert::same(1, $tester->getErrorCount());


$tester = new Documentation\Tester();
$file = \Tester\FileMock::create('
/--latte
{control name}
{cache}{/cache}
{form name}{/form}
{link}
<a n:href=""></a>
\--
', 'texy');
$tester->processFile($file);
Assert::same(0, $tester->getErrorCount());


$tester = new Documentation\Tester();
$file = \Tester\FileMock::create('
/--latte
{variable $a}
\--
', 'texy');
$tester->processFile($file);
Assert::same(1, $tester->getErrorCount());
