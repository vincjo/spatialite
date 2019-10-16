<?php
namespace Spatialite\Core;

use \Spatialite\SPL;

class Fetch extends SPL
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
		$this->row = explode("\n", $result[0]);
        return $this;
    }

    public function build()
    {
        $row = $this->row;
        foreach ($row as $value) {
            $key = trim(explode(" = ", $value)[0]);
            $val = isset(explode(" = ", $value)[1]) ? trim(explode(" = ", $value)[1]) : '';
            if ($key !== '') $values[$key] = $val;
        }
        if ($this->type === 'array') {
            $row = $values;
        } elseif ($this->type === 'object') {
            $row = (object) $values;
        } else {
            $row = [
                "FETCH_ASSOC" => $values,
                "FETCH_OBJ" => (object) $values,
            ];
        }
        $this->result = $row;
        return $this;
    }

    public function render()
    {
        if($this->row[0] === ''){
            return false;
        }
        unset($this->row);
        return $this->result;
    }
}