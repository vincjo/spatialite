<?php
namespace Spatialite\Core;

use \Spatialite\SPL;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Query extends SPL
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
            ->execute()
            ->rectify();
    }

    public function prepare() 
    {
        $this->query = "SELECT load_extension('mod_spatialite'); " . preg_replace('#\n|\t|\r#', ' ', trim(utf8_encode($this->query)));
        return $this;
    }

    public function execute()
    {
        $process = new Process([$this->bin->sqlite3, $this->db, ".mode line", $this->query]);
        $process->run();
		if (!$process->isSuccessful()) {
			throw new ProcessFailedException($process);
        }
        $this->output = $process->getOutput();
		return $this;  
    }

    public function rectify()
    {
        return trim(str_replace("load_extension('mod_spatialite') =", '', $this->output));
    }
}