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
        $category = CraftCategory::find()
            ->id($id)
            ->one();
        
        $parsed = Plugin::getInstance()->helper->parseAttributes($category);
        Plugin::getInstance()->helper->getDescendants($category, $parsed);
        
        return $parsed;
    }
    
    /**
     * Get an entry by slug
     */
    public function slug($slug)
    {
        $category = CraftCategory::find()
            ->slug($slug)
            ->one();
            
        $parsed = Plugin::getInstance()->helper->parseAttributes($category);
        Plugin::getInstance()->helper->getDescendants($category, $parsed);
        
        return $parsed;
    }
    
    /**
     * Get collection of categories
     */
    public function collection()
    {
        $request = \Craft::$app->getRequest();
        $filter = $request->getParam('filter');
        
        $result = [];
        
        $categories = CraftCategory::find();
            
        // Apply any filters
        if ($filter) {
            foreach($filter AS $f) {
                $field = $f['attribute_code'];
                $categories->$field = $f['value'];
            }
        }

        // Process each entry
        foreach($categories->all() AS $category) {
            $parsed = Plugin::getInstance()->helper->parseAttributes($category);
            Plugin::getInstance()->helper->getDescendants($category, $parsed);
            $result[] = $parsed;
        }
        
        return $result;
    }
    
    /**
     * Get all the categories the next level down
     */
    protected function getDescendants($parent)
    {
        $level = $parent->level + 1;
        
        $categories = CraftCategory::find()
            ->descendantOf($parent->id)
            ->level($level);
            
        $result = [];
        foreach($categories->all() AS $category) {
            $result[] = Plugin::getInstance()->helper->parseAttributes($category);
        }
        
        return $result;
    }
}