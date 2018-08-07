<?php
namespace futureactivities\api;

use yii\base\Event;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

class Plugin extends \craft\base\Plugin
{
    public function init()
    {
        parent::init();

        // Register our site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                
                $event->rules['api/entry/<method>'] = 'fa-api/api/entry';
                $event->rules['api/entry/<method>/<params:.+>'] = 'fa-api/api/entry';
                
                $event->rules['api/category/<method>'] = 'fa-api/api/category';
                $event->rules['api/category/<method>/<params:.+>'] = 'fa-api/api/category';
            }
        );
        
        // Register service classes
        $this->setComponents([
            'helper' => \futureactivities\api\services\Helper::class,
            'search' => \futureactivities\api\services\Helper::class,
            'entry' => \futureactivities\api\services\Entry::class,
            'category' => \futureactivities\api\services\Category::class,
        ]);
    }
}