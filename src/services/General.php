<?php
namespace futureactivities\api\services;

use yii\base\Component;

use futureactivities\api\errors\ApiException;
use futureactivities\api\Plugin;

class General extends Component
{
    /**
     * Get details about a slug
     * 
     * @cache
     */
    public function uri($uri)
    {
        $element = \Craft::$app->elements->getElementByUri($uri);
        
        if (!$element)
            throw new ApiException('Unable to find element.');
        
        $reflect = new \ReflectionClass($element);
        
        $result = [
            'id' => $element->id,
            'type' => strtolower($reflect->getShortName()),
        ];
        
        if (isset($element->type))
            $result['handle'] = $element->type->handle;
        
        return $result;
    }
}