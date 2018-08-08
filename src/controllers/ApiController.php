<?php 
namespace futureactivities\api\controllers;

use Craft;
use craft\web\Controller;

use futureactivities\api\errors\ApiException;
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
    
    public function actionUser($method, $params = null)
    {
        return $this->asJson($this->processRequest('user', $method, $params));
    }
    
    protected function processRequest($service, $method, $params = null)
    {
        $params = explode('/', $params);
        $cacheId = sha1(\Craft::$app->getRequest()->getQueryString());
        
        // Read the comments for the selected method
        $class = new \ReflectionMethod(Plugin::getInstance()->$service, $method);
        $docComment = $class->getDocComment();
        
        // Check if this methods results should be cached
        $cache = false;
        if (strpos($docComment, '@cache') !== false)
            $cache = true;
        
        // Check if this has already been cached
        if ($cache && \Craft::$app->config->general->cacheElementQueries && $result = \Craft::$app->cache->get($cacheId))
            return json_decode($result);

        // Attempt to run method
        try {
            $result = Plugin::getInstance()->$service->$method(...$params);
        } catch (ApiException $e) {
            \Craft::$app->response->setStatusCode(400);
            return $e;
        }
        
        if ($cache)
            \Craft::$app->cache->set($cacheId, json_encode($result));
         
        return $result;
    }
}