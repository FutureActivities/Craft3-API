<?php
namespace futureactivities\api\errors;

use yii\base\Exception;

class ApiException extends Exception implements \JsonSerializable
{
    private $data = [];

    public function __construct($message, $data = []) 
    {
        parent::__construct($message);
        
        $this->data = $data;
    }    
    
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'API Error';
    }

    /**
     * @return array Additional data
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * Format exception for JSON
     */
    public function jsonSerialize() 
    {
        return [
            'message' => $this->getMessage(),
            'data' => $this->getData()
        ];
    }
}
