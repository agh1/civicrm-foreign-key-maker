<?php
/**
 * @file
 * Generate "add constraint" foreign key statements from schema.
 *
 * To use, run the following command first:
 *   grep -rl '<foreignKey' path-to-civicrm/civicrm/xml > civicrm-foreign-key-maker/fklist.txt
 * Then, run this script:
 *   php fkmaker.php > constraints.sql
 */

$files = file_get_contents('fklist.txt');
$files = explode("\n", $files);
foreach ($files as $file) {
  $file = trim($file);
  if (!strlen($file)) {
    continue;
  }
  $xmlstr = trim(file_get_contents($file));
  $tableXML = simplexml_load_string($xmlstr);
  $name = trim((string) $tableXML->name);
  foreach ($tableXML->foreignKey as $foreignXML) {
    $foreignKeys = array();
    getForeignKey($foreignXML, $foreignKeys, $name);
  }
  foreach ($foreignKeys as $foreignKey) {
    $row = "ALTER TABLE $name ADD CONSTRAINT {$foreignKey['uniqName']} FOREIGN KEY (`{$foreignKey['name']}`) REFERENCES `{$foreignKey['table']}`(`{$foreignKey['key']}`)";
    if ($foreignKey['onDelete']) {
      $row .= " ON DELETE {$foreignKey['onDelete']}";
    }
    $row .= ";\n";
    echo $row;
  }
}

/**
 * Process foreign key.
 *
 * Lifted from CRM_Core_CodeGen_Specification::getForeignKey.
 */
function getForeignKey(&$foreignXML, &$foreignKeys, &$currentTableName) {
  $name = trim((string ) $foreignXML->name);

  /** need to check for existence of table and key **/
  $table = trim((string) $foreignXML->table);
  $foreignKey = array(
    'name' => $name,
    'table' => $table,
    'uniqName' => "FK_{$currentTableName}_{$name}",
    'key' => trim((string) $foreignXML->key),
    'import' => empty($foreignXML->import) ? FALSE : $foreignXML->import,
    'export' => empty($foreignXML->export) ? FALSE : $foreignXML->export,
    // we do this matching in a separate phase (resolveForeignKeys)
    'className' => NULL,
    'onDelete' => empty($foreignXML->onDelete) ? FALSE : $foreignXML->onDelete,
  );
  $foreignKeys[$name] = &$foreignKey;
}
