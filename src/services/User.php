<?php
namespace futureactivities\api\services;

use yii\base\Component;
use craft\elements\Entry AS CraftEntry;

use futureactivities\api\Plugin;

class User extends Component
{
    /**
     * Get a users account details
     */
    public function account($user)
    {
        return Plugin::getInstance()->helper->parseAttributes($user);
    }
}