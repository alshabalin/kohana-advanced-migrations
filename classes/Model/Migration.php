<?php

class Model_Migration extends ORM {

  protected $_table_columns = [
    'id'         => ['type' => 'int'],
    'hash'       => ['type' => 'string'],
    'name'       => ['type' => 'string'],
    'created_at' => ['type' => 'string'],
    'updated_at' => ['type' => 'string'],
  ];

  protected $_created_column = ['column' => 'created_at', 'format' => 'Y-m-d H:i:s'];

  protected $_updated_column = ['column' => 'updated_at', 'format' => 'Y-m-d H:i:s'];

  public function get_class_filename()
  {
    if ($this->loaded())
    {
      return Migration::config('path') . $this->hash . '_' . $this->name . '.php';
    }
  }

}
