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
     * Get collection of categories
     */
    public function collection()
    {
        $request = \Craft::$app->getRequest();
        $filter = $request->getParam('filter');
        $order = $request->getParam('order');
        
        $result = [
            'categories' => [],
            'descendants' => []
        ];
        
        $categories = CraftCategory::find();
            
        // Apply any filters
        if ($filter) {
            foreach($filter AS $f) {
                $field = $f['field'];
                $categories->$field = $f['value'];
            }
        }
        
        // Set the sort order
        if ($order)
            $categories->orderBy = $order;

        // Process each entry
        $children = [];
        foreach($categories->all() AS $category) {
            $result['categories'][] = Plugin::getInstance()->helper->parseAttributes($category);
            Plugin::getInstance()->helper->getDescendants($category, $children);
        }
        $result['descendants'] = $children;
        
        return $result;
    }
}