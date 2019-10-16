<?php
namespace Spatialite\Core;

use \Spatialite\SPL;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Exec extends SPL
{
    private $query;

    public function __construct(string $db, string $query)
    {
        parent::__construct($db);
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
        $this->query = preg_replace('#\n|\t|\r#', ' ', trim(utf8_encode($this->query)));
        return $this;
    }

    public function execute()
    {
        $process = new Process([$this->bin->spatialite, $this->db, $this->query]);
        $process->run();
		if (!$process->isSuccessful()) {
			throw new ProcessFailedException($process);
        }
        return $process->getOutput();
    }
}