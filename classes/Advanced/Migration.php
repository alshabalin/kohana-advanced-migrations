<?php

/**
 * Advanced Migrations module for Kohana 3.3
 *
 * Uses https://github.com/evopix/kohana-schema
 *
 * @package   Advanced_Migrations
 * @author    Alexei Shabalin <mail@alshabalin.com>
 */

class Advanced_Migration extends InitModel {

  public function up()
  {
    // Nothing by default
  }

  public function down()
  {
    // Nothing by default
  }


  public static function config($option = NULL)
  {
    static $config;

    if ($config === NULL)
    {
      $config = Kohana::$config->load('migrations');
    }

    if ($option !== NULL)
    {
      return Arr::get($config, $option);
    }

    return $config;
  }


  public static function install()
  {
    Schema::create('migrations', function($table)
    {
      $table->increments('id')->unsigned();
      $table->string('hash', 30);
      $table->string('name', 100);
      $table->timestamps();
    });
  }

  public static function uninstall_migrations()
  {
    Schema::drop('migrations');
  }

  public static function is_installed()
  {
    try
    {
      Migration::count_all();
    }
    catch (Database_Exception $ex)
    {
      return FALSE;
    }

    return TRUE;
  }


  public static function migrate_all(& $messages)
  {
    $migrations = static::get_migrations();
    $messages   = [];

    foreach (Migration::all() as $migration)
    {
      if (isset($migrations[$migration->hash]))
      {
        unset($migrations[$migration->hash]);
      }
    }

    $db = Database::instance();


    foreach ($migrations as $key => $info)
    {
      $message = sprintf('Executing migration: \'%s\' with hash: %s', $info['class'], $key);

      $db->begin();

      try 
      {
        static::resolve($info['file'], $info['class'])->up();

        if (Migration::create(['hash' => $key, 'name' => $info['class']])->saved())
        {
          $messages[] = [$message];
        }
        else
        {
          $messages[] = [$message, 'Failed to save migration to database'];
        }

        $db->commit();
      }
      catch (Exception $e)
      {
        $db->rollback();

        $messages[] = [$message, $e->getMessage()];

        return $messages;
      }
    }

    return $messages;
  }

  public static function rollback(& $messages) 
  {
    $migration = Migration::order_by('id', 'DESC')->first();
    $messages  = [];

    if ($migration->loaded()) 
    {
      $message = sprintf('Rolling back migration: \'%s\' with hash: %s', $migration->name, $migration->hash);

      try 
      {
        static::resolve($migration->get_class_filename(), $migration->name)->down();
        $migration->delete();
        $messages[] = [$message];
      }
      catch (Exception $e) 
      {
        $messages[] = [$message, $e->getMessage()];
      }
    }

    return $messages;
  }


  public static function generate($name, $columns = NULL) 
  {
    try
    {
      $data = ['migration_name' => $name];

      $name = strtolower($name);

      if (preg_match('#^create_(?<table>\w+)#', $name, $matches))
      {
        $data['table_name'] = $matches['table'];
      }
      else if (preg_match('#^drop_(?<table>\w+)#', $name, $matches))
      {
        $data['drop_table'] = $matches['table'];
      }
      else if (preg_match('#^add_(?<column>\w+)_to_(?<table>\w+)#U', $name, $matches))
      {
        $data['add_column'] = $matches['column'];
        $data['table_name'] = $matches['table'];
      }
      else if (preg_match('#^remove_(?<column>\w+)_from_(?<table>\w+)#U', $name, $matches))
      {
        $data['remove_column'] = $matches['column'];
        $data['table_name'] = $matches['table'];
      }

      if ($columns !== NULL)
      {
        $columns = explode(',', $columns);

        foreach ($columns as $i => $column)
        {
          $columns[$i] = explode(':', $column);
          if (empty($columns[$i][1]))
          {
            $columns[$i][1] = 'string';
          }
          if ($columns[$i][1] === 'int')
          {
            $columns[$i][1] = 'integer';
          }
        }

        $data['columns'] = $columns;
      }

      $view = View::factory('migration_template', $data);

      $filename = static::config('path') . date('YmdHis') . '_' . strtolower($name) . '.php';
      file_put_contents($filename, $view);
      chmod($filename, 0777);

      return TRUE;
    }
    catch (Exception $e)
    {
    }
    return FALSE;
  } 


  protected static function get_migration_files()  
  {
    $migrations = glob(static::config('path') . '*.php');

    if ($migrations !== FALSE && ! empty($migrations))
    {
      foreach ($migrations as $i => $migration)
      {
        $name = basename($migration);
        if ( ! is_file($migration) || ! isset($name[14]) || $name[14] !== '_')
        {
          unset($migrations[$i]);
        }
      }

      sort($migrations);

      return $migrations;
    }

    return [];
  }

  protected static function get_migrations() 
  {
    $migrations = [];
    foreach (static::get_migration_files() as $file)
    {
      $basename         = basename($file, '.php');
      $key              = substr($basename, 0, 14);
      $migrations[$key] = ['class' => substr($basename, 15), 'file' => $file];
    }
    return $migrations;
  }

  protected static function resolve($file, $class)
  {
    include_once $file;

    if ( ! class_exists($class, FALSE))
    {
      throw new Kohana_Exception('Class :class doesn\'t exists', [':class' => $class]);
    }

    return new $class;
  }


}
