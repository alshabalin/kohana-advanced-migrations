<?php
 
class Task_Db_Seed extends Minion_Task {

  protected $_options = [
    'name' => NULL,
  ];

  protected function _execute(array $params)
  {
    $mask = '*.php';

    if ($params['name'] !== NULL)
    {
      $mask = $params['name'] . '.php';
    }

    $seeds = glob(Migration::config('seeds') . $mask);

    if ($seeds !== FALSE && count($seeds))
    {
      foreach ($seeds as $seed)
      {
        Minion_CLI::write('Seeding \'' . basename($seed, '.php') . '\'');
        include $seed;
        Minion_CLI::write('OK');
      }
    }
    else
    {
      Minion_CLI::write('Nothing to seed');
    }
  }
}