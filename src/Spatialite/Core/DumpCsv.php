<?php
namespace Spatialite\Core;

use \Spatialite\SPL;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DumpCsv extends SPL
{
    private $filepath;
    private $query;

    public function __construct(string $db, string $filepath, string $query)
    {
        parent::__construct($db);
        $this->filepath = $filepath;
        $this->query = $query;
    }

    public function process()
    {
        return $this
            ->prepare()
            ->execute();
    }

    public function prepare()
    {
        $this->query = "SELECT load_extension('mod_spatialite'); " . preg_replace('#\n|\t|\r#', ' ', trim(utf8_encode($this->query)));
        return $this;
    }

    public function execute()
    {
        $process = new Process([$this->bin->sqlite3, $this->db, '.headers on', '.mode csv', '.separator ;', '.output ' . $this->filepath, $this->query]);
		$process->run();
		if (!$process->isSuccessful()) {
			throw new ProcessFailedException($process);
        }
        return $process->getOutput();
    }
}