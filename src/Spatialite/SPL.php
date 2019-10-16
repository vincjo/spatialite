<?php
namespace Spatialite;

use \Spatialite\Core\{
	Query,
	FetchAll,
	Fetch,
	Exec,
	LoadShapefile,
	DumpShapefile,
	LoadCsv,
	DumpCsv,
	RunSqlScript
};

class SPL implements SpatialiteInterface
{
	const NEW_EMPTY_SPATIALITE = __DIR__ . "/../../bin/db.sqlite";
	const FETCH_OBJ = 'object';
	const FETCH_ASSOC = 'array';
	protected $db;
	protected $bin;
	protected $result;
	
	public function __construct($db)
	{
		$this->db = $db;
		$this->bin = $this->getBinaries();
	}

	/**
	 * Spatial functions enabled
	 * @param 	string		$query 	SQL, SELECT statement only.
	 */
	public function query(string $query)
	{
		$this->result = ( new Query($this->db, $query) )->process();
		return $this;
	}

	public function fetchAll($type = null)
	{
		return ( new FetchAll($this->result, $type) )->process();
	}

	public function fetch($type = null)
	{
		return ( new Fetch($this->result, $type) )->process();
	}

	public function exec($query)
	{
		return ( new Exec($this->db, $query) )->process();
	}

    /**
	 * Create new table from Shapefile.
	 * @param string  $filepath 				REQUIRED: Path to the Shapefile (without any extension)
	 * @param string  $tablename 				REQUIRED: Tablename in the Spatialite database
	 * @param string  $options['charset']		DEFAULT: 'UTF-8' 	Encoding file (UTF-8, CP1252...)
	 * @param integer $options['srid']	 		DEFAULT: 3857		EPSG - coordinate system code (3857, 4326, 2154 ...)
	 * @param string  $options['geomcolumn']  	DEFAULT: 'geom' 	Geometry column name
	 * @param string  $options['displayfield']	DEFAULT: 'PK_UID' 	Primary key (If it does not exist, specify a column name to do self-increment)
	 * @param string  $options['geomtype']		DEFAULT: 'AUTO' 	Geometry type (LINESTRING, POLYGON, POINT, MULTILINESTRING, MULTIPOLYGON, MULTIPOINT)
	 * @param string  $options['dimension']		DEFAULT: '2d' 		2d, 3d (default 2d)
	 * @param string  $options['compressed']	DEFAULT: 'compressed'
	 */
	public function loadShapefile(string $filepath, string $tablename, array $options = [])
	{
		return ( new LoadShapefile($this->db, $filepath, $tablename, $options) )->process();
	}

	/**
	 * Create Shapefile from table.
	 * @param string  $filepath 				REQUIRED: Path to the Shapefile (without any extension)
	 * @param string  $tablename 				REQUIRED: Tablename in the Spatialite database
	 * @param string  $options['charset']		DEFAULT: 'UTF-8' 	Encoding file (UTF-8, CP1252...)
	 * @param string  $options['geomcolumn']  	DEFAULT: 'geom' 	Geometry column name
	 */
	public function dumpShapefile(string $filepath, string $tablename, array $options = [])
	{
		return ( new DumpShapefile($this->db, $filepath, $tablename, $options) )->process();
	}

	/**
	 * Create new table from CSV. First row : columns name. Charset: UTF-8 mandatory
	 * @param 	string 		$filepath 		path to CSV file
	 * @param 	string 		$tablename 		table name
	 * @param 	string 		$separator		field separator character (";" or "," or "	" etc.)
	 */
	public function loadCsv($filepath, $tablename, $separator = ';')
	{
		return ( new LoadCsv($this->db, $filepath, $tablename, $separator) )->process();
	}

	/**
	 * @param string $filepath		path to the csv file in output
	 * @param string $query 		SQL, SELECT statement only
	 */
	public function dumpCsv($filepath, $query)
	{
		return ( new DumpCsv($this->db, $filepath, $query) )->process();
	}

	/**
	 * @param 	string 	$file 		path to SQL file
	 * @param 	bool 	$spatial	true if SQL script includes spatial function
	 */
	public function runSqlScript($filepath, $spatial = false)
	{
		return ( new RunSqlScript($this->db, $filepath, $spatial) )->process();
	}

	/**
	 * @param string $db 	path to the new Spatialite database
	 */
	public static function CreateNewEmptyDB($db)
	{
		if (copy(self::NEW_EMPTY_SPATIALITE, $db)) {
			return $db;
		}
		return false;
	}

	private function getBinaries()
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$folder = 'win';
		} else {
			$folder = 'linux';
		}	
		return (object) [
			"OS" => PHP_OS,
			"spatialite" => __DIR__ . "/../../bin/$folder/spatialite",
			"sqlite3" => __DIR__ . "/../../bin/$folder/sqlite3"
		];
	}
}