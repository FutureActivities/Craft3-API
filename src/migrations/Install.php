<?php
namespace futureactivities\api\migrations;

use craft\db\Migration;
use craft\db\Query;
use craft\models\CategoryGroup;
use craft\models\CategoryGroup_SiteSettings;
use craft\elements\Category;

class Install extends Migration
{
    public function safeUp()
    {
        if (!$this->db->tableExists('{{%usertokens}}')) {
            
            $this->createTable('{{%usertokens}}', [
                'id' => $this->primaryKey(),
                'token' => $this->text(),
                'userId' => $this->integer()->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);
        }
    }

    public function safeDown()
    {
        
    }
}