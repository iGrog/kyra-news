<?php

use yii\db\Schema;
use yii\db\Migration;

// yii migrate/up --migrationPath=@vendor/kyra/sm/migrations

class m140708_122831_create_news_table extends Migration
{
    public function up()
    {
        $this->createTable('news', [
            'NID' => 'pk',
            'Title' => Schema::TYPE_STRING . ' NOT NULL',
            'SmallDesc' => Schema::TYPE_TEXT,
            'DateOf' => Schema::TYPE_DATETIME.' NOT NULL',
            'HeaderIID' => Schema::TYPE_INTEGER.' DEFAULT NULL',
            'ContentJSON' => Schema::TYPE_TEXT,
            'ContentHTML' => Schema::TYPE_TEXT,
            'IsVisible' => Schema::TYPE_SMALLINT.' DEFAULT 1'
        ]);
        $this->createIndex('hiid', 'news', 'HeaderIID');
        $this->addForeignKey('fk_news_image', 'news', 'HeaderIID', 'images', 'IID', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('news');
    }

}
