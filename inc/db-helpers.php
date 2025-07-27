<?php
// 🔍 Helper function to check if a foreign key exists on a specific table
function fk_exists($table, $fk_name) {
    global $wpdb;
    $db = DB_NAME;
    $sql = "
    SELECT CONSTRAINT_NAME 
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = %s 
      AND TABLE_NAME = %s
      AND CONSTRAINT_NAME = %s
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
  ";
    return (bool) $wpdb->get_var($wpdb->prepare($sql, $db, $table, $fk_name));
}
