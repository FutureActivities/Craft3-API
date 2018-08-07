<?php
namespace futureactivities\api\services;

use yii\base\Component;
use craft\elements\Entry AS CraftEntry;

use futureactivities\api\Plugin;

class Helper extends Component
{
    /**
     * Parse an entries attributes converting them into a more usable format
     */
    public function parseAttributes($entry)
    {
        $result = [];
        
        foreach($entry->getAttributes() AS $key => $attribute) {
            $result[$key] = Plugin::getInstance()->fields->process($attribute);
        }
        
        return $result;
    }
    
    /**
     * Recursive function to parse all the descendants
     */
    public function getDescendants($entry, &$result = [])
    {
        if ($entry->children->count() == 0)
            return [];
            
        foreach ($entry->children->all() AS $child) {
            $parsed = $this->parseAttributes($child);
            $this->getDescendants($child, $parsed);
            $result['descendants'][] = $parsed;
        }
        
        return $result;
    }
    
    /**
     * Paginate a query object
     */
    public function paginate(&$query, $page = 1, $perPage = 20)
    {
        $offset = $perPage * ($page-1);
        $query->offset($offset)->limit($perPage);
        
        return [
            'total' => (int)$query->count(),
            'page' => $page ?: 1,
            'perPage' => $page ? $perPage : (int)$query->count(),
            'pages' => $page ? (int)$query->count() / $perPage : 1
        ];
    }
}