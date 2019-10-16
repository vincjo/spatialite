<?php
namespace Spatialite\Geoprocessing;

use \Spatialite\SPL;

class Extent extends SPL
{
    private $db; 

    public function __construct(array $extent)
    {
        parent::__construct(__DIR__ . "/../bin/db.sqlite");
        $this->extent = $extent;
    }

    public function getPerimeter(){
		$width = $this->exec("
			SELECT ST_Distance(
				ST_Transform(SetSRID(MakePoint(" . $this->extent['xmin'] . ", " . $this->extent['ymin'] . "), " . $this->extent['srid'] . " ), 3857), 
				ST_Transform(SetSRID(MakePoint(" . $this->extent['xmax'] . ", " . $this->extent['ymin'] . "), " . $this->extent['srid'] . " ), 3857)
			)
		");
		$length = $this->exec("
			SELECT ST_Distance(
				ST_Transform(SetSRID(MakePoint(" . $this->extent['xmin'] . ", " . $this->extent['ymin'] . "), " . $this->extent['srid'] . " ), 3857), 
				ST_Transform(SetSRID(MakePoint(" . $this->extent['xmin'] . ", " . $this->extent['ymax'] . "), " . $this->extent['srid'] . " ), 3857)
			)
		");
		return [
			"width" => $width,
            "length" => $length,
            "ratio" => $width / $length,
            "bbox" => $this->toString()
        ];
    }
  
	public function expand(float $paddingInPercent){
        $perimeter = $this->getPerimeter();
        $expand = $paddingInPercent * min([$perimeter['width'], $perimeter['length']]);
        $AsText = $this->exec("
            SELECT AsText(
                ST_Expand(
                    SetSRID(MakePolygon(GeomFromText(
                        'LINESTRING(
                            " . $this->extent['xmin'] . " " . $this->extent['ymin'] . ",
                            " . $this->extent['xmax'] . " " . $this->extent['ymin'] . ",
                            " . $this->extent['xmax'] . " " . $this->extent['ymax'] . ",
                            " . $this->extent['xmax'] . " " . $this->extent['ymax'] . ",
                            " . $this->extent['xmin'] . " " . $this->extent['ymin'] . "
                        )'
                    )), " . $this->extent['srid'] . "
                ), $expand)
            );
        ");
        $coordinates = str_replace(array("POLYGON", ")", "(", ","), "", $AsText);
        $coordinates = explode(" ", $coordinates);
        $this->extent = [
            "srid" => $this->extent['srid'],
            "xmin" => $coordinates[0],
            "ymin" => $coordinates[1],
            "xmax" => $coordinates[2],
            "ymax" => $coordinates[5]
        ];
        return $this;
	}    

    public function transform(int $srid)
    {
        return $this->query("
            SELECT '" . $srid . "' AS srid,
                ST_X(ST_Transform(MakePoint(" . $this->extent['xmin'] . ", " . $this->extent['ymin'] . ", " . $this->extent['srid'] . "), " . $srid . ")) AS xmin,
                ST_Y(ST_Transform(MakePoint(" . $this->extent['xmin'] . ", " . $this->extent['ymin'] . ", " . $this->extent['srid'] . "), " . $srid . ")) AS ymin,
                ST_X(ST_Transform(MakePoint(" . $this->extent['xmax'] . ", " . $this->extent['ymax'] . ", " . $this->extent['srid'] . "), " . $srid . ")) AS xmax,
                ST_Y(ST_Transform(MakePoint(" . $this->extent['xmax'] . ", " . $this->extent['ymax'] . ", " . $this->extent['srid'] . "), " . $srid . ")) AS ymax
        ")->fetch(SPL::FETCH_ASSOC);
    }

    public function toString()
    {
        return $this->extent['xmin'] . ',' . $this->extent['ymin'] . ',' . $this->extent['xmax'] . ',' . $this->extent['ymax'];
    }
}