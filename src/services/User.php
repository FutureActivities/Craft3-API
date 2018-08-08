<?php
namespace futureactivities\api\services;

use yii\base\Component;
use craft\elements\User AS UserElement;

use futureactivities\api\errors\ApiException;
use futureactivities\api\Plugin;

class User extends Component
{
    /**
     * Verify user credentials and generate an access token
     */
    public function token()
    {
        $request = \Craft::$app->request;
        
        if (!$request->isPost)
            throw new ApiException('Invalid Request.');
        
        if (!$request->getParam('username') || !$request->getParam('password')) 
            throw new ApiException('Missing required parameter');
        
        $loginName = $request->getParam('username');
        $password = $request->getParam('password');
        
        $user = \Craft::$app->getUsers()->getUserByUsernameOrEmail($loginName);
        
        if (!$user || $user->password === null)
            throw new ApiException('Invalid user and/or password.');
            
        if (!$user->authenticate($password)) {
            \Craft::$app->users->handleInvalidLogin($user);
            throw new ApiException('Invalid user and/or password.');
        }
        
        \Craft::$app->users->handleValidLogin($user);
        
        $token = Plugin::getInstance()->userAuth->generateToken($user->id);
        
        return [
            'token' => $token 
        ];
    }
    
    /**
     * Get a users account details
     */
    public function account()
    {
        $user = Plugin::getInstance()->userAuth->auth();
        
        return Plugin::getInstance()->helper->parseAttributes($user);
    }
    
    /**
     * Register a new user
     */
    public function register() 
    {
        $request = \Craft::$app->request;
        
        if (!$request->isPost)
            throw new ApiException('Invalid request.');
            
        $customerData = $request->getBodyParam('customer');
        
        $user = new UserElement();
        $user->username = isset($customerData['username']) ? $customerData['username'] : $customerData['email'];
        
        foreach ($customerData AS $key => $value)
            $user->$key = $value;
        
        $user->newPassword = $request->getBodyParam('password');
        
        \Craft::$app->elements->saveElement($user);
    }
    
    /**
     * Send password reset link
     */
    public function sendPasswordReset()
    {
        if (!\Craft::$app->request->isPost)
            throw new ApiException('Invalid request.');
            
        $loginName = \Craft::$app->getRequest()->getBodyParam('username');
        $user = \Craft::$app->getUsers()->getUserByUsernameOrEmail($loginName);
        if (!$user)
            throw new ApiException('Invalid user.');
        
        return \Craft::$app->getUsers()->sendPasswordResetEmail($user);
    }
    
    /**
     * Reset a users password
     */
    public function doPasswordReset()
    {
        if (!\Craft::$app->request->isPost)
            throw new ApiException('Invalid request.');
        
        $code = \Craft::$app->getRequest()->getRequiredBodyParam('code');
        $uid = \Craft::$app->getRequest()->getRequiredParam('id');
        $userToProcess = \Craft::$app->getUsers()->getUserByUid($uid);
        $isCodeValid = \Craft::$app->getUsers()->isVerificationCodeValidForUser($userToProcess, $code);

        if (!$userToProcess || !$isCodeValid)
            throw new ApiException('Invalid password reset token.');
            
        $userToProcess->newPassword = \Craft::$app->getRequest()->getRequiredBodyParam('newPassword');
        $userToProcess->setScenario(UserElement::SCENARIO_PASSWORD);
        
        return \Craft::$app->getElements()->saveElement($userToProcess);
    }
}