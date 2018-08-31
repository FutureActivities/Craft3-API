<?php
namespace futureactivities\api\services;

use yii\base\Component;
use craft\elements\Category AS CraftCategory;

use futureactivities\api\errors\ApiException;
use futureactivities\api\Plugin;

class Category extends Component
{
    /**
     * Get an entry by ID
     * 
     * @cache
     */
    public function id($id)
    {
        $category = CraftCategory::find()
            ->id($id)
            ->one();
            
        if (!$category)
            throw new ApiException('Category not found');
        
        return $this->processCategory($category);
    }
    
    /**
     * Get an entry by slug
     * 
     * @cache
     */
    public function slug($slug)
    {
        $category = CraftCategory::find()
            ->slug($slug)
            ->one();
        
        if (!$category)
            throw new ApiException('Category not found');
            
        return $this->processCategory($category);
    }
    
    /**
     * Get collection of categories
     * 
     * @cache
     */
    public function collection()
    {
        $request = \Craft::$app->getRequest();
        $filter = $request->getParam('filter');
        $response = $request->getParam('response');
        
        $result = [];
        
        $categories = CraftCategory::find();
            
        // Apply any filters
        if ($filter) {
            foreach($filter AS $f) {
                $field = $f['attribute_code'];
                $categories->$field = $f['value'];
            }
        }
        
        // Custom output? E.g. ids, count, etc.
        if ($response)
            return $categories->$response();
        
        // Process each entry
        foreach($categories->all() AS $category) {
            $result[] = $this->processCategory($category);
        }
        
        return $result;
    }
    
    protected function processCategory($category)
    {
        // Parse the category attributes
        $parsed = Plugin::getInstance()->helper->parseAttributes($category);
        
        // Add parent ID to result if applicable
        if ($category->parent)
            $parsed['parentId'] = $category->parent->id;
            
        // Add the descendants if applicable
        Plugin::getInstance()->helper->getDescendants($category, $parsed);
        
        return $parsed;
    }
}