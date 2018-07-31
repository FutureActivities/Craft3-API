<?php 
namespace futureactivities\api\controllers;

use Craft;
use craft\web\Controller;

use futureactivities\api\Plugin;

class ApiController extends Controller
{
    protected $allowAnonymous = true;

    public function actionRequest($service, $method, $params = null)
    {
        if ($service == 'helper') return;
        
        $params = explode('/', $params);
        $cacheId = sha1(\Craft::$app->getRequest()->getQueryString());
        
        if (\Craft::$app->config->general->cacheElementQueries && $result = \Craft::$app->cache->get($cacheId))
            return $this->asJson(json_decode($result));

        $result = Plugin::getInstance()->$service->$method(...$params);
        
         \Craft::$app->cache->set($cacheId, json_encode($result));
         
        return $this->asJson($result);
    }
}