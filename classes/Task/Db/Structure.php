<?php
 
class Task_Db_Structure extends Minion_Task {

  protected function _execute(array $params)
  {
    $db = Database::instance(Database::$default);

    Minion_CLI::write('Exporting table structure to structure.sql');

    try
    {
      $rows = $db->query(Database::SELECT, 'show tables', FALSE);

      $tables = [];
      $views  = [];

      foreach ($rows as $row)
      {
        $table = reset($row);

        $res = $db->query(Database::SELECT, 'show create table `' . $table . '`', FALSE);
        $res = $res[0];

        $schema = Arr::get($res, 'Create Table', NULL);

        if ($schema === NULL)
        {
          $schema = Arr::get($res, 'Create View', NULL);
          $schema = preg_replace('#^CREATE.*VIEW `#U', 'CREATE VIEW `', $schema);

          if ($schema === NULL)
          {
            continue;
          }

          $views[] = $schema . ';';
          continue;
        }

        $tables[] = $schema . ';';
      }

      file_put_contents(Migration::config('dump') . 'structure.sql', implode("\n\n", $tables) . "\n\n" . implode("\n\n", $views));

      Minion_CLI::write('OK');
    }
    catch (Exception $ex)
    {
      Minion_CLI::write('ERROR: ' . $ex->getMessage());
    }
  }

}