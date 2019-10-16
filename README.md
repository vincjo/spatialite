
# About
SpatiaLite implements spatial extensions following the specification of the Open Geospatial Consortium (OGC). <br>
At a very basic level, a DBMS that supports Spatial Data offers an SQL environment that has been extended with a set of geometry types, and thus may be usefully employed i.e. by some GIS application. <br>
A geometry-valued SQL column is implemented as a column that has a geometry type. The OGC specification describe a set of SQL geometry types, as well as functions on those types to create and analyze geometry values. <br><br>
[**SpatiaLite 4.2.0 - SQL functions reference list**](http://www.gaia-gis.it/gaia-sins/spatialite-sql-4.2.0.html)

<!-- # Install
```composer require vincjo/spatialite```

# Tests
```./vendor/bin/phpunit tests``` -->

# Basic usage
Create a new empty DB :
~~~php
use Spatialite\SPL;

$db = new SPL( SPL::CreateNewEmptyDB('test.sqlite') );
~~~
...Or connect an existing Spatialite DB :
~~~php
$db = new SPL( 'path/to/mydb.sqlite' );
~~~
Load shapfile :
~~~php
$db->loadShapefile('path/to/shapefile/commune', 'commune', [
    'srid' => 2154, 
    'charset' => 'UTF-8'
]);
~~~
Query :
~~~php
$result = $db->query("
    SELECT numero, nom_acc, statut, AsText(Centroid(geom)) 
    FROM commune 
    LIMIT 3
")->fetchAll(SPL::FETCH_OBJ);
~~~
Dump shapefile :
~~~php
$db->dumpShapefile('./shapefile', 'commune', [
    'charset' => 'UTF-8',
    'geomcolumn' => 'geom'
]);
~~~
