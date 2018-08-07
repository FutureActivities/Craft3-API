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
                
                $event->rules['api/user/token'] = 'fa-api/user/token';
                $event->rules['api/user/<method>'] = 'fa-api/user/request';
                $event->rules['api/user/<method>/<params:.+>'] = 'fa-api/user/request';
            }
        );
        
        // Register service classes
        $this->setComponents([
            'helper' => \futureactivities\api\services\Helper::class,
            'fields' => \futureactivities\api\services\Fields::class,
            'userAuth' => \futureactivities\api\services\UserAuth::class,
            'user' => \futureactivities\api\services\User::class,
            'entry' => \futureactivities\api\services\Entry::class,
            'category' => \futureactivities\api\services\Category::class,
        ]);
    }
}