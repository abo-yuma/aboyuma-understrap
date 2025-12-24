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

// === ABOYUMA CUSTOM HELPER === //
// 🔍 Helper function to delete all files in a folder safely
function delete_files_in_folder($folder_path, $folder_path_normalized, $upload_basedir) {
  if (
    !file_exists($folder_path) || !is_dir($folder_path)
    || strpos($folder_path_normalized, $upload_basedir) !== 0
  ) {
    error_log("Folder missing or invalid: {$folder_path}");
    return;
  }

  $files = glob($folder_path . '*');
  if ($files === false) {
    error_log("Failed to read files from folder: {$folder_path}");
    return;
  }

  foreach ($files as $file) {
    if (is_file($file) && !unlink($file)) {
      error_log("Failed to delete file: {$file}");
    }
  }

  if (!rmdir($folder_path)) {
    error_log("Failed to remove directory: {$folder_path}");
  }
}
