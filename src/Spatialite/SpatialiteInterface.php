<?php
namespace Spatialite;

interface SpatialiteInterface
{
    public function query(string $request);

    public function fetchAll(string $type);

    public function fetch(string $type);

    public function exec(string $request);

    public function loadShapefile(string $filepath, string $tablename, array $options);

    public function dumpShapefile(string $filepath, string $tablename, array $options);

    public function loadCsv(string $filepath, string $tablename, string $separator);

    public function dumpCsv(string $filepath, string $query);

    public function runSqlScript(string $filepath, bool $spatial);

    public static function CreateNewEmptyDB($db);
}