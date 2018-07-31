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
    public function parseAttributes($entry, $parent = null)
    {
        $result = [];
        
        foreach($entry->getAttributes() AS $key => $attribute) {
            $value = null;
            
            if (is_null($attribute) || is_string($attribute) || is_bool($attribute) || is_int($attribute) || is_array($attribute)) {
                $value = $attribute;
            } else if (is_a($attribute, 'DateTime')) {
                $value = $attribute->format('Y-m-d H:i:s');
            } else if (get_parent_class($attribute) === 'craft\elements\db\ElementQuery') {
                $value = $this->elementQuery($attribute, $entry, $parent);
            } else if (is_a($attribute, 'craft\fields\data\SingleOptionFieldData') || is_a($attribute, 'craft\fields\data\MultiOptionsFieldData')) {
                $value = $attribute->getOptions();
            } else if (is_a($attribute, 'craft\fields\data\ColorData')) {
                $value = $attribute->getHex();
            } else if (is_a($attribute, 'craft\redactor\FieldData')) {
                $value = $attribute->getParsedContent();
            } else {
                $value = 'Field not yet supported.';
            }
            
            $result[$key] = $value;
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
            $result[$entry->id][] = $this->parseAttributes($child);
            $this->getDescendants($child, $result);
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
    
    /**
     * Loops through an element query results parsing the attributes for each result
     */
    protected function elementQuery($query, $entry = null, $parent = null)
    {
        $result = [];
        foreach($query->all() AS $element) {
            if ($parent && $element->id == $parent->id) {
                $result[] = [
                    'error' => true,
                    'msg' => 'Recursive loop blocked.',
                    'parent' => $parent->id
                ];
            } else {
                $result[] = Plugin::getInstance()->helper->parseAttributes($element, $entry);
            }
        }
        
        return $result;
    }
}