<?php
namespace futureactivities\api\services;

use yii\base\Component;
use craft\elements\User AS UserElement;

use futureactivities\api\Plugin;

class User extends Component
{
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
            throw new \Exception('Invalid request.');
            
        $customerData = $request->getBodyParam('customer');
        
        $user = new UserElement();
        $user->username = isset($customerData['username']) ? $customerData['username'] : $customerData['email'];
        $user->firstName = $customerData['firstname'];
        $user->lastName = $customerData['lastname'];
        $user->email = $customerData['email'];
        $user->newPassword = $request->getBodyParam('password');
        
        \Craft::$app->elements->saveElement($user);
    }
    
    /**
     * Send password reset link
     */
    public function sendPasswordReset()
    {
        if (!\Craft::$app->request->isPost)
            throw new \Exception('Invalid request.');
            
        $loginName = \Craft::$app->getRequest()->getBodyParam('username');
        $user = \Craft::$app->getUsers()->getUserByUsernameOrEmail($loginName);
        if (!$user)
            throw new \Exception('Invalid user.');
        
        return \Craft::$app->getUsers()->sendPasswordResetEmail($user);
    }
    
    /**
     * Reset a users password
     */
    public function doPasswordReset()
    {
        if (!\Craft::$app->request->isPost)
            throw new \Exception('Invalid request.');
        
        $code = \Craft::$app->getRequest()->getRequiredBodyParam('code');
        $uid = \Craft::$app->getRequest()->getRequiredParam('id');
        $userToProcess = \Craft::$app->getUsers()->getUserByUid($uid);
        $isCodeValid = \Craft::$app->getUsers()->isVerificationCodeValidForUser($userToProcess, $code);

        if (!$userToProcess || !$isCodeValid)
            throw new \Exception('Invalid password reset token.');
            
        $userToProcess->newPassword = \Craft::$app->getRequest()->getRequiredBodyParam('newPassword');
        $userToProcess->setScenario(UserElement::SCENARIO_PASSWORD);
        
        return \Craft::$app->getElements()->saveElement($userToProcess);
    }
}