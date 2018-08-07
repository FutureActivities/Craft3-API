<?php
namespace futureactivities\api\services;

use yii\base\Component;
use craft\elements\Entry AS CraftEntry;

use futureactivities\api\records\UserToken as UserTokenRecord;
use futureactivities\api\Plugin;

class UserAuth extends Component
{
    /**
     * Check if an auth token is valid and return the associated user
     */
    public function auth()
    {
        $request = \Craft::$app->request;
        
        if (!isset($request->headers['authorization']))
            throw new \Exception('Missing authorization header.');
        
        // Find Bearer token
        preg_match('/Bearer\s(\S+)/', $request->headers['authorization'], $matches);
        $token = $matches[1];
        
        // Get customer ID associated with this token
        $tokenRecord = UserTokenRecord::find()->where(['token' => $token])->one();
        if (!$tokenRecord)
            throw new \Exception('Invalid token.');
        
        // Find the customer
        $user = \Craft::$app->users->getUserById($tokenRecord->userId);
        if (!$user)
            throw new \Exception('Invalid token.');
        
        return $user;
    }
    
    /**
     * Generate and save an authorisation token
     */
    public function generateToken($userId)
    {
        $token = bin2hex(random_bytes(16));
        
        $userTokenRecord = UserTokenRecord::find()->where(['userId' => $userId])->one();
        
        if (!$userTokenRecord)
            $userTokenRecord = new UserTokenRecord();
        
        $userTokenRecord->token = $token;
        $userTokenRecord->userId = $userId;
        $userTokenRecord->save();
        
        return $token;
    }
}