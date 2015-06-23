<?php
/**
 * @file
 * Generate "add constraint" foreign key statements from schema.
 */

/**
 * Process foreign key.
 *
 * Lifted from CRM_Core_CodeGen_Specification::getForeignKey.
 */
function getForeignKey(&$foreignXML, &$fields, &$foreignKeys, &$currentTableName) {
  $name = trim((string ) $foreignXML->name);

  /** need to make sure there is a field of type name */
  if (!array_key_exists($name, $fields)) {
    echo "foreign $name in $currentTableName does not have a field definition, ignoring\n";
    return;
  }

  /** need to check for existence of table and key **/
  $table = trim($this->value('table', $foreignXML));
  $foreignKey = array(
    'name' => $name,
    'table' => $table,
    'uniqName' => "FK_{$currentTableName}_{$name}",
    'key' => trim($this->value('key', $foreignXML)),
    'import' => $this->value('import', $foreignXML, FALSE),
    'export' => $this->value('import', $foreignXML, FALSE),
    // we do this matching in a separate phase (resolveForeignKeys)
    'className' => NULL,
    'onDelete' => $this->value('onDelete', $foreignXML, FALSE),
  );
  $foreignKeys[$name] = &$foreignKey;
}
