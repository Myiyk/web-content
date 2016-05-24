<?php

namespace Documentation;

use Latte;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Nette\Bridges\CacheLatte\CacheMacro;
use Nette\Bridges\FormsLatte\FormMacros;
use Nette\Utils\Finder;
use Tester\FileMock;

class Tester
{
    private $lastFile;
    private $lastLine;
    private $errorCount = 0;

    public function run($dir)
    {
        foreach (Finder::findFiles('*.texy')->from($dir) as $file) {
            /** @var \SplFileInfo $file */
            $this->processFile($file->getRealPath());
        }
    }

    public function testLatte($sourceCode)
    {
        $latte = new Latte\Engine;

        $compiler = $latte->getCompiler();

        FormMacros::install($compiler); // install form
        UIMacros::install($compiler); // install control and link
        $compiler->addMacro('cache', new CacheMacro());

        $source = FileMock::create($sourceCode, '.latte');

        try {
            $latte->compile($source);
        } catch (Latte\CompileException $e) {
            $this->fail($e);
        } catch (Latte\RegexpException $e) {
            $this->fail($e);
        }
    }

    private function fail(\Exception $exception)
    {
        $this->errorCount++;
        $file = $this->lastFile;
        $log = '';

        if ($file && $this->lastLine) {
            $log .= "Problem in file $file:{$this->lastLine}\n";
        }
        $log .= $exception->getMessage() . "\n";

        echo $log;
    }

    public function processFile($file)
    {
        $this->lastFile = $file;
        $handle = fopen($file, "r");

        if (!$handle) {
            throw new \Exception("Cannot open file $file");
        }

        $start = false;
        $sourceCode = '';
        for ($l = 1; ($line = fgets($handle)) !== false; $l++) {
            if (strpos($line, '/--latte') !== false || strpos($line, '/--html') !== false) {
                $this->lastLine = $l;
                $start = true;
            } elseif ($start && strpos($line, '\--') !== false) {
                $this->testLatte(trim($sourceCode));
                $sourceCode = '';
                $start = false;
            } elseif ($start) {
                $sourceCode .= $line;
            }
        }

        fclose($handle);
    }

    /**
     * @return int
     */
    public function getErrorCount()
    {
        return $this->errorCount;
    }
}