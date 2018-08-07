<?php 
namespace futureactivities\api\controllers;

use Craft;
use craft\web\Controller;

use futureactivities\api\records\UserToken as UserTokenRecord;
use futureactivities\api\Plugin;

class UserController extends Controller
{
    protected $allowAnonymous = true;
    
    /**
     * Generate an access token for a user
     */
    public function actionToken()
    {
        $request = \Craft::$app->request;
        
        if (!$request->isPost)
            throw new \Exception('Invalid Request.');
        
        if (!$request->getParam('username') || !$request->getParam('password')) 
            throw new \Exception('Missing required parameter');
        
        $loginName = $request->getParam('username');
        $password = $request->getParam('password');
        
        $user = Craft::$app->getUsers()->getUserByUsernameOrEmail($loginName);
        
        if (!$user || $user->password === null)
            throw new \Exception('Invalid user and/or password.');
            
        if (!$user->authenticate($password)) {
            Craft::$app->users->handleInvalidLogin($user);
            throw new \Exception('Invalid user and/or password.');
        }
        
        Craft::$app->users->handleValidLogin($user);
        
        $token = Plugin::getInstance()->userAuth->generateToken($user->id);
        
        return $this->asJson([
            'token' => $token 
        ]);
    }
    
    /**
     * Call methods in the user service class
     */
    public function actionRequest($method, $params = null)
    {
        // Check authorisation
        $user = Plugin::getInstance()->userAuth->auth();
        
        $params = explode('/', $params);
        $result = Plugin::getInstance()->user->$method($user, ...$params);
        
        return $this->asJson($result);
    }
}