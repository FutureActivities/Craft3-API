<?php
namespace futureactivities\api\errors;

use yii\base\Exception;

class ApiException extends Exception implements \JsonSerializable
{
    private $data = [];
    private $status = 400;

    public function __construct($message, $data = [], $status = 400) 
    {
        parent::__construct($message);
        
        $this->data = $data;
        $this->status = $status;
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
     * @return int status code
     */
    public function getStatus()
    {
        return $this->status;
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
