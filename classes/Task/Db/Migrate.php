<?php
 
class Task_Db_Migrate extends Minion_Task {

  protected function _execute(array $params)
  {
    if ( ! Migration::is_installed())
    {
      Migration::install();
    }

    Migration::migrate_all($messages);

    if (empty($messages))
    {
      Minion_CLI::write('Nothing to migrate');
    }
    else
    {
      foreach ($messages as $message)
      {
        Minion_CLI::write($message[0]);

        if (isset($message[1]))
        {
          Minion_CLI::write('ERROR: ' . $message[1]);
        }
        else
        {
          Minion_CLI::write('OK');
        }
      }
    }
  }

}