<?php
namespace futureactivities\api\services;

use yii\base\Component;
use craft\elements\Category AS CraftCategory;

use futureactivities\api\Plugin;

class Category extends Component
{
    /**
     * Get an entry by ID
     */
    public function id($id)
    {
        $entry = CraftCategory::find()
            ->id($id)
            ->one();
        
        return Plugin::getInstance()->helper->parseAttributes($entry);
    }
    
    /**
     * Get an entry by slug
     */
    public function slug($slug)
    {
        $entry = CraftCategory::find()
            ->slug($slug)
            ->one();
        
        return Plugin::getInstance()->helper->parseAttributes($entry);
    }
    
    /**
     * Get categories by group
     */
    public function group($name)
    {
        $result = [
            'entries' => [],
            'descendants' => []
        ];
        
        $categories = CraftCategory::find()
            ->group($name)
            ->level(1)
            ->all();

        // Process each entry
        $children = [];
        foreach($categories AS $category) {
            $result['entries'][] = Plugin::getInstance()->helper->parseAttributes($category);
            Plugin::getInstance()->helper->getDescendants($category, $children);
        }
        $result['descendants'] = $children;
        
        return $result;
    }
}