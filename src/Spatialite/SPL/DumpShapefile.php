<?php
namespace Spatialite\SPL;

use \Spatialite\SPL;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DumpShapefile extends SPL
{
    private $filepath;
    private $tablename;
    private $options;
    private $command;

    public function __construct(string $db, string $filepath, string $tablename, array $options)
    {
        parent::__construct($db);
        $this->filepath = str_replace( '\\', '/', $filepath );
        $this->tablename = $tablename;
        $this->options = $options;
    }

    public function process()
    {
        return $this
            ->setOptions()
            ->prepare()
            ->execute();
    }

    public function setOptions() 
    {
        $options = [
            'charset' => 'UTF-8',  
            'geomcolumn' => 'geom'
        ];
        $this->options = array_merge($options, $this->options);
        return $this;
    }

    public function prepare()
    {
        $this->command = '.dumpshp ' . $this->tablename . ' ' . $this->options['geomcolumn'] . ' ' . $this->filepath . ' ' . $this->options['charset'];
        return $this;
    }

    public function execute()
    {
        $process = new Process([$this->bin->spatialite, $this->db, $this->command]);
		$process->run();
		if (!$process->isSuccessful()) {
			throw new ProcessFailedException($process);
        }
        return $process->getOutput();
    }
}