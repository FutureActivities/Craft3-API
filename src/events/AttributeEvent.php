<?php
namespace futureactivities\api\events;

use yii\base\Event;

class AttributeEvent extends Event
{
    public $entry;
    public $attributes = [];
}