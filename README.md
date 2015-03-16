## Kohana Advanced Migrations

Advanced Migrations is a migration module for kohana, the true Rails way.

It uses SQL schemas written by Evopix https://github.com/evopix/kohana-schema

Requirements:

* PHP 5.4
* Kohana 3.3 with Database, ORM modules
* Kohana Advanced ORM module
* Kohana Schema module

Features:

* Commands to generate, execute and rollback migrations.


## How to use

A typical migration file looks like:

```php
<?
class Create_Comments extends Migration
{
  public function up()
  {
    Schema::create('comments', function($table)
    {
      $table->increments('id');
      $table->integer('user_id')->unsigned();
      $table->integer('article_id')->unsigned();
      $table->text('comment');
      $table->enum('status', ['new', 'published', 'banned'])->default('new');
      $table->timestamps();
    });

    Schema::table('articles', function($table)
    {
      $table->datetime('last_commented_at')->after('content');
      $table->integer('comments_count')->unsigned()->after('content');
    });
  }

  public function down()
  {
    Schema::drop('comments');

    Schema::table('articles', function($table)
    {
      $table->drop_column('last_commented_at');
      $table->drop_column('comments_count');
    });
  }
}
?>
```

You may want to create a new migration this way:

```
./minion generate:migration --name=Create_Comments
```

After all migrations are done, you need to apply all pending migrations:

```
./minion db:migrate
```


## License

MIT License
(c) Alexei Shabalin, 2015

