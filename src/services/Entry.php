<?php
namespace futureactivities\api\services;

use yii\base\Component;
use craft\elements\Entry AS CraftEntry;

use futureactivities\api\errors\ApiException;
use futureactivities\api\Plugin;

class Entry extends Component
{
    /**
     * Get an entry by ID
     * 
     * @cache
     */
    public function id($id)
    {
        $entry = CraftEntry::find()
            ->id($id)
            ->one();
            
        if (!$entry)
            throw new ApiException('Entry not found');
            
        $parsed = Plugin::getInstance()->helper->parseAttributes($entry);
        Plugin::getInstance()->helper->getDescendants($entry, $parsed);
        
        return $parsed;
    }
    
    /**
     * Get an entry by slug
     * 
     * @cache
     */
    public function slug($slug)
    {
        $entry = CraftEntry::find()
            ->slug($slug)
            ->one();
            
        if (!$entry)
            throw new ApiException('Entry not found');
        
        $parsed = Plugin::getInstance()->helper->parseAttributes($entry);
        Plugin::getInstance()->helper->getDescendants($entry, $parsed);
        
        return $parsed;
    }
    
    /**
     * Get a collection of entries
     * 
     * @cache
     */
    public function collection()
    {
        $request = \Craft::$app->getRequest();
        $page = (int)$request->getParam('page');
        $perPage = (int)$request->getParam('perPage') ?: 10;
        $filter = $request->getParam('filter');
        
        $entries = CraftEntry::find();
            
        // Apply any filters
        if ($filter) {
            foreach($filter AS $f) {
                $field = $f['attribute_code'];
                $entries->$field = $f['value'];
            }
        }
        
        // Handle any pagination
        $result = $page ? Plugin::getInstance()->helper->paginate($entries, $page, $perPage) : [];
        
        // Process each entry
        $result = [];
        foreach($entries->all() AS $entry) {
            $parsed = Plugin::getInstance()->helper->parseAttributes($entry);
            Plugin::getInstance()->helper->getDescendants($entry, $parsed);
            $result[] = $parsed;
        }
        
        return $result;
    }
    
    /**
     * Get all the entries the next level down
     */
    protected function getDescendants($parent)
    {
        $level = $parent->level + 1;
        
        $entries = CraftEntry::find()
            ->descendantOf($parent->id)
            ->level($level);
            
        $result = [];
        foreach($entries->all() AS $entry) {
            $result[] = Plugin::getInstance()->helper->parseAttributes($entry);
        }
        
        return $result;
    }
}