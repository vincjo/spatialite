<?php
namespace Spatialite\SPL;

use \Spatialite\SPL;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class LoadShapefile extends SPL
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
            ->execute()
            ->rectifyGeometryPoint();
    }

    public function setOptions() 
    {
        $options = [
            'charset' => 'UTF-8',  
            'srid' => 3857, 
            'geomcolumn' => 'geom', 
            'displayfield' => 'PK_UID', 
            'geomtype' => 'AUTO', 
            'dimension' => '2d', 
            'compressed' => 'compressed'
        ];
        $this->options = array_merge($options, $this->options);
        return $this;
    }

    public function prepare()
    {
        $this->command = '.loadshp ' . $this->filepath . ' ' . $this->tablename . ' ' . implode(' ', $this->options);
        return $this;
    }
    
    public function execute()
    {
        $process = new Process([$this->bin->spatialite, $this->db, $this->command]);
		$process->run();
		if (!$process->isSuccessful()) {
			throw new ProcessFailedException($process);
        }
        $this->output = $process->getOutput();
		return $this;  
    }

    /**
     * POINT types are automatically converted to MULTIPOINT. They must be converted back into POINT
     */
    public function rectifyGeometryPoint()
    {
		if($this->options['geomtype'] === 'POINT'){
			$this->exec("
				UPDATE geometry_columns SET geometry_type = 1 WHERE f_table_name = '".$table_name."';
				UPDATE " . $table_name . " SET " . $geom_column . " = CastToPoint(" . $geom_column . ");
			");
        }
        return $this;  
    }
}