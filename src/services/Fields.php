<?php
namespace futureactivities\api\services;

use yii\base\Component;
use craft\elements\Entry AS CraftEntry;

use futureactivities\api\Plugin;

class Fields extends Component
{
    /**
     * Processes a craft field and returns a usable value
     */
    public function process($field)
    {
        if (is_null($field) || is_string($field) || is_bool($field) || is_int($field) || is_array($field))
            return $field;
        
        if (is_a($field, 'DateTime'))
            return $field->format('Y-m-d H:i:s');
        
        if (get_class($field) === 'craft\elements\db\MatrixBlockQuery' || get_class($field) === 'craft\elements\db\TagQuery')
            return $this->elementQuery($field);
        
        if (get_parent_class($field) === 'craft\elements\db\ElementQuery')
            return $field->ids();
        
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
    protected function elementQuery($query)
    {
        $result = [];
        foreach($query->all() AS $element) {
            $parsed = Plugin::getInstance()->helper->parseAttributes($element);
            
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