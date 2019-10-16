<?php
namespace Spatialite\Core;

use \Spatialite\SPL;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FetchAll extends SPL
{
    protected $result;

    public function __construct(string $result, string $type = null)
    {
        $this->result = $result;
        $this->type = $type;
    }

    public function process()
    {
        return $this
            ->prepare()
            ->build()
            ->render();
    }

    public function prepare() 
    {
        $result = explode("\n\r", $this->result);
		foreach($result as $table){
			$this->rows[] = explode("\n", $table);
        }
        return $this;
    }

    public function build()
    {
        $rows = $this->rows;
		for ($i = 0; $i < count($rows); $i++) {
			foreach ($rows[$i] as $value) {
				$key = trim(explode(" = ", $value)[0]);
                $val = isset(explode(" = ", $value)[1]) ? trim(explode(" = ", $value)[1]) : '';
                if ($key !== '') $values[$key] = $val;
			}
			if ($this->type === 'array') {
				$rows[$i] = $values;
			} elseif ($this->type === 'object') {
				$rows[$i] = (object) $values;
			} else {
				$rows[$i] = [
					"FETCH_ASSOC" => $values,
					"FETCH_OBJ" => (object) $values,
				];
			}
		}
        $this->result = $rows;
        return $this;
    }

    public function render()
    {
        if($this->rows[0][0] === ''){
            return false;
        }
        unset($this->query, $this->rows);
        return $this->result;
    }
}