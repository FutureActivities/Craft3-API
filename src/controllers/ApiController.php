<?php 
namespace futureactivities\api\controllers;

use Craft;
use craft\web\Controller;

use futureactivities\api\Plugin;

class ApiController extends Controller
{
    protected $allowAnonymous = true;
    
    public function actionEntry($method, $params = null)
    {
        return $this->asJson($this->processRequest('entry', $method, $params));
    }
    
    public function actionCategory($method, $params = null)
    {
        return $this->asJson($this->processRequest('category', $method, $params));
    }
    
    protected function processRequest($service, $method, $params = null)
    {
        $params = explode('/', $params);
        $cacheId = sha1(\Craft::$app->getRequest()->getQueryString());
        
        if (\Craft::$app->config->general->cacheElementQueries && $result = \Craft::$app->cache->get($cacheId))
            return json_decode($result);

        $result = Plugin::getInstance()->$service->$method(...$params);
        
         \Craft::$app->cache->set($cacheId, json_encode($result));
         
        return $result;
    }
}