<?PHP
/*
	Vijay Nair
	This will change the database encoding to utf8
 */

include("../init.php");

$collation = 'utf8_general_ci';

$osDB->query("ALTER DATABASE ".DB_NAME."  COLLATE $collation");
	
$tables = $osDB->getCol("SHOW TABLES");

foreach ($tables as $table) {
    $osDB->query("ALTER TABLE $table COLLATE $collation");
	
    $tablefields = $osDB->getAll("SHOW COLUMNS FROM $table");
    foreach ($tablefields as $tablefield) {
		if (preg_match('~char|text|enum|set~', $tablefield["Type"])) {
            $osDB->query("ALTER TABLE $table[0] MODIFY $tablefield[Field] $tablefield[Type] CHARACTER SET binary");
            $osDB->query("ALTER TABLE $table[0] MODIFY $tablefield[Field] $tablefield[Type] COLLATE $collation" . ($tablefield["Null"] ? "" : " NOT NULL") . ($tablefield["Default"] && $tablefield["Default"] != "NULL" ? " DEFAULT '$tablefield[Default]'" : ""));
        }
	}
}

?>
