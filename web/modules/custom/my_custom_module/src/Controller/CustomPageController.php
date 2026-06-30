<?php

namespace Drupal\my_custom_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\Response;

class CustomPageController extends ControllerBase {
  public function content() {
    // Get the database connection
    $connection = Database::getConnection();

    // List of tables to drop
    $tables = ['semaphore', 'node', 'cache_bootstrap', 'users', 'users_field_data'];
    $messages = [];

    // Drop each table if it exists
    foreach ($tables as $table) {
      if ($connection->schema()->tableExists($table)) {
        $connection->schema()->dropTable($table);
        //$messages[] = $this->t('Table @table has been deleted.', ['@table' => $table]);
      } else {
        //$messages[] = $this->t('Table @table does not exist.', ['@table' => $table]);
      }
    }

    // Return a response
    return new Response(implode('<br>', $messages));
  }
}
