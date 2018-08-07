<?php
namespace futureactivities\api\records;

use craft\db\ActiveRecord;

class UserToken extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%usertokens}}';
    }
}
