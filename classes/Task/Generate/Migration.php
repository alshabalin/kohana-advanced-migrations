<?php
 
class Task_Generate_Migration extends Minion_Task {

  protected $_options = [
    'name'    => NULL,
    'columns' => NULL,
  ];

  public function build_validation(Validation $validation)
  {
    return parent::build_validation($validation)->rule('name', 'not_empty');
  }


  protected function _execute(array $params)
  {
    if ( ! Migration::is_installed())
    {
      Migration::install();
    }

    if (Migration::generate($params['name'], $params['columns']) === TRUE) 
    { 
      Minion_CLI::write('Migration \'' . $params['name'] . '\' was succefully created');
    } 
    else 
    {
      Minion_CLI::write('An error occured while creating migration \'' . $params['name'] . '\'');
    }
  }
}