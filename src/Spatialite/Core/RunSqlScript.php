<?php
namespace Spatialite\Core;

use \Spatialite\SPL;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class RunSqlScript extends SPL
{
    private $filepath;
    private $spatial;
    private $command;

    public function __construct(string $db, string $filepath, bool $spatial)
    {
        parent::__construct($db);
        $this->filepath = $filepath;
        $this->spatial = $spatial;
    }

    public function process()
    {
        return $this
            ->prepare()
            ->execute();
    }

    public function prepare()
    {
        $this->command = $this->db . ' < ' . $filepath;
        $this->executable = $this->spatial ? $this->bin->spatialite : $this->bin->sqlite3;
        return $this;
    }

    public function execute()
    {
        $process = new Process([$this->executable, $this->command]);
		$process->run();
		if (!$process->isSuccessful()) {
			throw new ProcessFailedException($process);
        }
        return $process->getOutput();
    }
}