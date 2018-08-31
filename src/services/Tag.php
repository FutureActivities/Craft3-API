<?php
namespace futureactivities\api\services;

use yii\base\Component;
use craft\elements\Tag AS CraftTag;

use futureactivities\api\errors\ApiException;
use futureactivities\api\Plugin;

class Tag extends Component
{
    /**
     * Get an tag by ID
     * 
     * @cache
     */
    public function id($id)
    {
        $tag = CraftTag::find()
            ->id($id)
            ->one();
            
        if (!$tag)
            throw new ApiException('Entry not found');
            
        return Plugin::getInstance()->helper->parseAttributes($tag);
    }
    
    /**
     * Get a collection of tags
     * 
     * @cache
     */
    public function collection()
    {
        $request = \Craft::$app->getRequest();
        $filter = $request->getParam('filter');
        
        $tags = CraftTag::find();
            
        // Apply any filters
        if ($filter) {
            foreach($filter AS $f) {
                $field = $f['attribute_code'];
                $tags->$field = $f['value'];
            }
        }
        
        $totalTags = $tags->count();
        
        // Process each entry
        $result = [];
        foreach($tags->all() AS $tag) {
            $result[] = Plugin::getInstance()->helper->parseAttributes($tag);
        }
        
        return [
            'collection' => $result,
            'total' => $totalTags
        ];
    }
}