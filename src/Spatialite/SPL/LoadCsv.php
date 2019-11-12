<?php
namespace Spatialite\SPL;

use \Spatialite\SPL;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class LoadCsv extends SPL
{
    private $filepath;
    private $tablename;
    private $separator;

    public function __construct(string $db, string $filepath, string $tablename, string $separator)
    {
        parent::__construct($db);
        $this->filepath = str_replace( '\\', '/', $filepath );
        $this->tablename = $tablename;
        $this->separator = $separator;
    }

    public function process()
    {
        $process = new Process([$this->bin->sqlite3, $this->db, '.mode csv', '.separator ' . $this->separator, '.import ' . $this->pathfile . ' ' . $tablename]);
		$process->run();
		if (!$process->isSuccessful()) {
			throw new ProcessFailedException($process);
        }
        $this->output = $process->getOutput();
		return $this;  
    }
}