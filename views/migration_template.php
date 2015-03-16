<?= '<?php' ?>


class <?= $migration_name ?> extends Migration
{
  public function up()
  {
<?php if (!empty($add_column)) { ?>

    Schema::table('<?= $table_name ?>', function($table)
    {
      $table->string('<?= $add_column ?>');
    });

<?php } else if (!empty($remove_column)) { ?>

    Schema::table('<?= $table_name ?>', function($table)
    {
      $table->drop_column('<?= $remove_column ?>');
    });

<?php } else if (!empty($table_name)) { ?>

    Schema::create('<?= $table_name ?>', function($table)
    {
      $table->increments('id');
<?php if (!empty($columns)) { ?>
<?php foreach ($columns as list($name, $type)) { ?>
      $table-><?= $type ?: 'string' ?>('<?= $name ?>');
<?php } ?>
<?php } else { ?>

      $table->string('name');
      // ...
<?php } ?>

      $table->timestamps();
    });

<?php } else if (!empty($drop_table)) { ?>

    Schema::drop('<?= $drop_table ?>');

<?php } else { ?>

    // Schema::create('table_name', function($table)
    // {
    //   $table->increments('id');
    //   $table->timestamps();
    // });

    // Schema::table('table_name', function($table)
    // {
    //   $table->string('column_name')->after('id');
    // });

<?php } ?>
  }

  public function down()
  {
<?php if (!empty($add_column)) { ?>

    Schema::table('<?= $table_name ?>', function($table)
    {
      $table->drop_column('<?= $add_column ?>');
    });

<?php } else if (!empty($remove_column)) { ?>

    Schema::table('<?= $table_name ?>', function($table)
    {
      $table->drop_column('<?= $remove_column ?>');
    });

<?php } else if (!empty($table_name)) { ?>

    Schema::drop('<?= $table_name ?>');

<?php } else if (!empty($drop_table)) { ?>

    Schema::create('<?= $drop_table ?>', function($table)
    {
      $table->increments('id');
      $table->string('name');
      // ...

      $table->timestamps();
    });
<?php } else { ?>

    // Schema::table('table_name', function($table)
    // {
    //   $table->drop_column('column_name');
    // });

    // Schema::drop('table_name');
<?php } ?>
  }

}
