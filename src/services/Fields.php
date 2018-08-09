<?php
namespace futureactivities\api\services;

use yii\base\Component;
use craft\elements\Entry AS CraftEntry;

use futureactivities\api\Plugin;

class Fields extends Component
{
    const MAX_LEVELS = 3;
    
    /**
     * Processes a craft field and returns a usable value
     */
    public function process($field, $level = 1)
    {
        if (is_null($field) || is_string($field) || is_bool($field) || is_int($field) || is_array($field))
            return $field;
        
        if (is_a($field, 'DateTime'))
            return $field->format('Y-m-d H:i:s');
        
        if (get_parent_class($field) === 'craft\elements\db\ElementQuery')
            return $this->elementQuery($field, $level);
        
        if (is_a($field, 'craft\fields\data\SingleOptionFieldData'))
            return $this->singleOption($field);
        
        if (is_a($field, 'craft\fields\data\MultiOptionsFieldData'))
            return $this->multiOption($field);
        
        if (is_a($field, 'craft\fields\data\ColorData'))
            return $field->getHex();
        
        if (is_a($field, 'craft\redactor\FieldData'))
            return $field->getParsedContent();
        
        return 'Field not yet supported.';
    }
    
    /**
     * Loops through an element query results parsing the attributes for each result
     */
    protected function elementQuery($query, $level = 1)
    {
        if ($level > self::MAX_LEVELS)
            return 'ERROR: NESTING TOO DEEP';
            
        $result = [];
        foreach($query->all() AS $element) {
            $parsed = [];
            foreach($element->getAttributes() AS $key => $attribute)
                $parsed[$key] = $this->process($attribute, $level + 1);
            
            if (isset($element->type) && isset($element->type->handle))
                $parsed['handle'] = $element->type->handle;
            
            $result[] = $parsed;
        }
        
        return $result;
    }
    
    /**
     * SingleOptionFieldData
     */
    protected function singleOption($field)
    {
        $options = $field->getOptions();
        foreach ($options AS $option) {
            if ($option->selected)
                return $option;
        }
    }
    
    /**
     * MultiOptionsFieldData
     */
    protected function multiOption($field)
    {
        $options = $field->getOptions();
        $selected = [];
        foreach ($options AS $option) {
            if (!$option->selected) continue;
            $selected[] = $option;
        }
        
        return $selected;
    }
}