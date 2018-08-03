<?php
namespace futureactivities\api\services;

use yii\base\Component;
use craft\elements\Entry AS CraftEntry;

use futureactivities\api\Plugin;

class Entry extends Component
{
    /**
     * Get an entry by ID
     */
    public function id($id)
    {
        $entry = CraftEntry::find()
            ->id($id)
            ->one();
        
        return Plugin::getInstance()->helper->parseAttributes($entry);
    }
    
    /**
     * Get an entry by slug
     */
    public function slug($slug)
    {
        $entry = CraftEntry::find()
            ->slug($slug)
            ->one();
        
        return Plugin::getInstance()->helper->parseAttributes($entry);
    }
    
    /**
     * Get a collection of entries
     */
    public function collection()
    {
        $request = \Craft::$app->getRequest();
        $page = (int)$request->getParam('page');
        $perPage = (int)$request->getParam('perPage') ?: 10;
        $filter = $request->getParam('filter');
        $order = $request->getParam('order');
        
        $entries = CraftEntry::find();
            
        // Apply any filters
        if ($filter) {
            foreach($filter AS $f) {
                $field = $f['field'];
                $entries->$field = $f['value'];
            }
        }
        
        // Set the sort order
        if ($order)
            $entries->orderBy = $order;
        
        // Handle any pagination
        $result = $page ? Plugin::getInstance()->helper->paginate($entries, $page, $perPage) : [];
        
        // Process each entry
        $result['entries'] = [];
        $children = [];
        foreach($entries->all() AS $entry) {
            $result['entries'][] = Plugin::getInstance()->helper->parseAttributes($entry);
            Plugin::getInstance()->helper->getDescendants($entry, $children);
        }
        $result['descendants'] = $children;
        
        return $result;
    }
}