<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Controller\Component;

use Cake\Auth\Storage\StorageInterface;
use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Core\Exception\Exception;
use Cake\Event\Event;
use Cake\Event\EventDispatcherTrait;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * Authentication control component class.
 *
 * Binds access control with user authentication and session management.
 *
 * @property \Cake\Controller\Component\RequestHandlerComponent $RequestHandler
 * @property \Cake\Controller\Component\FlashComponent $Flash
 * @link https://book.cakephp.org/3/en/controllers/components/authentication.html
 */
class AuthComponent extends Component
{
    use EventDispatcherTrait;

    /**
     * The query string key used for remembering the referrered page when getting
     * redirected to login.
     *
     * @var string
     */
    const QUERY_STRING_REDIRECT = 'redirect';

    /**
     * Constant for 'all'
     *
     * @var string
     */
    const ALL = 'all';

    /**
     * Default config
     *
     * - `authenticate` - An array of authentication objects to use for authenticating users.
     *   You can configure multiple adapters and they will be checked sequentially
     *   when users are identified.
     *
     *   ```
     *   $this->Auth->setConfig('authenticate', [
     *      'Form' => [
     *         'userModel' => 'Users.Users'
     *      ]
     *   ]);
     *   ```
     *
     *   Using the class name without 'Authenticate' as the key, you can pass in an
     *   array of config for each authentication object. Additionally you can define
     *   config that should be set to all authentications objects using the 'all' key:
     *
     *   ```
     *   $this->Auth->setConfig('authenticate', [
     *       AuthComponent::ALL => [
     *          'userModel' => 'Users.Users',
     *          'scope' => ['Users.active' => 1]
     *      ],
     *     'Form',
     *     'Basic'
     *   ]);
     *   ```
     *
     * - `authorize` - An array of authorization objects to use for authorizing users.
     *   You can configure multiple adapters and they will be checked sequentially
     *   when authorization checks are done.
     *
     *   ```
     *   $this->Auth->setConfig('authorize', [
     *      'Crud' => [
     *          'actionPath' => 'controllers/'
     *      ]
     *   ]);
     *   ```
     *
     *   Using the class name without 'Authorize' as the key, you can pass in an array
     *   of config for each authorization object. Additionally you can define config
     *   that should be set to all authorization objects using the AuthComponent::ALL key:
     *
     *   ```
     *   $this->Auth->setConfig('authorize', [
     *      AuthComponent::ALL => [
     *          'actionPath' => 'controllers/'
     *      ],
     *      'Crud',
     *      'CustomAuth'
     *   ]);
     *   ```
     *
     * - ~~`ajaxLogin`~~ - The name of an optional view element to render when an Ajax
     *   request is made with an invalid or expired session.
     *   **This option is deprecated since 3.3.6.** Your client side code should
     *   instead check for 403 status code and show appropriate login form.
     *
     * - `flash` - Settings to use when Auth needs to do a flash message with
     *   FlashComponent::set(). Available keys are:
     *
     *   - `key` - The message domain to use for flashes generated by this component,
     *     defaults to 'auth'.
     *   - `element` - Flash element to use, defaults to 'default'.
     *   - `params` - The array of additional params to use, defaults to ['class' => 'error']
     *
     * - `loginAction` - A URL (defined as a string or array) to the controller action
     *   that handles logins. Defaults to `/users/login`.
     *
     * - `loginRedirect` - Normally, if a user is redirected to the `loginAction` page,
     *   the location they were redirected from will be stored in the session so that
     *   they can be redirected back after a successful login. If this session value
     *   is not set, redirectUrl() method will return the URL specified in `loginRedirect`.
     *
     * - `logoutRedirect` - The default action to redirect to after the user is logged out.
     *   While AuthComponent does not handle post-logout redirection, a redirect URL
     *   will be returned from `AuthComponent::logout()`. Defaults to `loginAction`.
     *
     * - `authError` - Error to display when user attempts to access an object or
     *   action to which they do not have access.
     *
     * - `unauthorizedRedirect` - Controls handling of unauthorized access.
     *
     *   - For default value `true` unauthorized user is redirected to the referrer URL
     *     or `$loginRedirect` or '/'.
     *   - If set to a string or array the value is used as a URL to redirect to.
     *   - If set to false a `ForbiddenException` exception is thrown instead of redirecting.
     *
     * - `storage` - Storage class to use for persisting user record. When using
     *   stateless authenticator you should set this to 'Memory'. Defaults to 'Session'.
     *
     * - `checkAuthIn` - Name of event for which initial auth checks should be done.
     *   Defaults to 'Controller.startup'. You can set it to 'Controller.initialize'
     *   if you want the check to be done before controller's beforeFilter() is run.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'authenticate' => null,
        'authorize' => null,
        'ajaxLogin' => null,
        'flash' => null,
        'loginAction' => null,
        'loginRedirect' => null,
        'logoutRedirect' => null,
        'authError' => null,
        'unauthorizedRedirect' => true,
        'storage' => 'Session',
        'checkAuthIn' => 'Controller.startup',
    ];

    /**
     * Other components utilized by AuthComponent
     *
     * @var array
     */
    public $components = ['RequestHandler', 'Flash'];

    /**
     * Objects that will be used for authentication checks.
     *
     * @var \Cake\Auth\BaseAuthenticate[]
     */
    protected $_authenticateObjects = [];

    /**
     * Objects that will be used for authorization checks.
     *
     * @var \Cake\Auth\BaseAuthorize[]
     */
    protected $_authorizeObjects = [];

    /**
     * Storage object.
     *
     * @var \Cake\Auth\Storage\StorageInterface|null
     */
    protected $_storage;

    /**
     * Controller actions for which user validation is not required.
     *
     * @var string[]
     * @see \Cake\Controller\Component\AuthComponent::allow()
     */
    public $allowedActions = [];

    /**
     * Request object
     *
     * @var \Cake\Http\ServerRequest
     */
    public $request;

    /**
     * Response object
     *
     * @var \Cake\Http\Response
     */
    public $response;

    /**
     * Instance of the Session object
     *
     * @var \Cake\Http\Session
     * @deprecated 3.1.0 Will be removed in 4.0
     */
    public $session;

    /**
     * The instance of the Authenticate provider that was used for
     * successfully logging in the current user after calling `login()`
     * in the same request
     *
     * @var \Cake\Auth\BaseAuthenticate|null
     */
    protected $_authenticationProvider;

    /**
     * The instance of the Authorize provider that was used to grant
     * access to the current user to the URL they are requesting.
     *
     * @var \Cake\Auth\BaseAuthorize|null
     */
    protected $_authorizationProvider;

    /**
     * Initialize properties.
     *
     * @param array $config The config data.
     * @return void
     */
    public function initialize(array $config)
    {
        $controller = $this->_registry->getController();
        $this->setEventManager($controller->getEventManager());
        $this->response =& $controller->response;
        $this->session = $controller->getRequest()->getSession();

        if ($this->getConfig('ajaxLogin')) {
            deprecationWarning(
                'The `ajaxLogin` option is deprecated. Your client-side ' .
                'code should instead check for 403 status code and show ' .
                'appropriate login form.'
            );
        }
    }

    /**
     * Callback for Controller.startup event.
     *
     * @param \Cake\Event\Event $event Event instance.
     * @return \Cake\Http\Response|null
     */
    public function startup(Event $event)
    {
        return $this->authCheck($event);
    }

    /**
     * Main execution method, handles initial authentication check and redirection
     * of invalid users.
     *
     * The auth check is done when event name is same as the one configured in
     * `checkAuthIn` config.
     *
     * @param \Cake\Event\Event $event Event instance.
     * @return \Cake\Http\Response|null
     * @throws \ReflectionException
     */
    public function authCheck(Event $event)
    {
        if ($this->_config['checkAuthIn'] !== $event->getName()) {
            return null;
        }

        /** @var \Cake\Controller\Controller $controller */
        $controller = $event->getSubject();

        $action = strtolower($controller->getRequest()->getParam('action'));
        if (!$controller->isAction($action)) {
            return null;
        }

        $this->_setDefaults();

        if ($this->_isAllowed($controller)) {
            return null;
        }

        $isLoginAction = $this->_isLoginAction($controller);

        if (!$this->_getUser()) {
            if ($isLoginAction) {
                return null;
            }
            $result = $this->_unauthenticated($controller);
            if ($result instanceof Response) {
                $event->stopPropagation();
            }

            return $result;
        }

        if (
            $isLoginAction ||
            empty($this->_config['authorize']) ||
            $this->isAuthorized($this->user())
        ) {
            return null;
        }

        $event->stopPropagation();

        return $this->_unauthorized($controller);
    }

    /**
     * Events supported by this component.
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Controller.initialize' => 'authCheck',
            'Controller.startup' => 'startup',
        ];
    }

    /**
     * Checks whether current action is accessible without authentication.
     *
     * @param \Cake\Controller\Controller $controller A reference to the instantiating
     *   controller object
     * @return bool True if action is accessible without authentication else false
     */
    protected function _isAllowed(Controller $controller)
    {
        $action = strtolower($controller->getRequest()->getParam('action'));

        return in_array($action, array_map('strtolower', $this->allowedActions));
    }

    /**
     * Handles unauthenticated access attempt. First the `unauthenticated()` method
     * of the last authenticator in the chain will be called. The authenticator can
     * handle sending response or redirection as appropriate and return `true` to
     * indicate no further action is necessary. If authenticator returns null this
     * method redirects user to login action. If it's an AJAX request and config
     * `ajaxLogin` is specified that element is rendered else a 403 HTTP status code
     * is returned.
     *
     * @param \Cake\Controller\Controller $controller A reference to the controller object.
     * @return \Cake\Http\Response|null Null if current action is login action
     *   else response object returned by authenticate object or Controller::redirect().
     * @throws \Cake\Core\Exception\Exception
     */
    protected function _unauthenticated(Controller $controller)
    {
        if (empty($this->_authenticateObjects)) {
            $this->constructAuthenticate();
        }
        $response = $this->response;
        $auth = end($this->_authenticateObjects);
        if ($auth === false) {
            throw new Exception('At least one authenticate object must be available.');
        }
        $result = $auth->unauthenticated($controller->getRequest(), $response);
        if ($result !== null) {
            return $result;
        }

        if (!$controller->getRequest()->is('ajax')) {
            $this->flash($this->_config['authError']);

            // return $controller->redirect($this->_loginActionRedirectUrl());
            return $controller->redirect($this->_config['loginAction']);

        }

        if (!empty($this->_config['ajaxLogin'])) {
            $controller->viewBuilder()->setTemplatePath('Element');
            $response = $controller->render(
                $this->_config['ajaxLogin'],
                $this->RequestHandler->ajaxLayout
            );
        }

        return $response->withStatus(403);
    }

    /**
     * Returns the URL of the login action to redirect to.
     *
     * This includes the redirect query string if applicable.
     *
     * @return array|string
     */
    protected function _loginActionRedirectUrl()
    {
        $urlToRedirectBackTo = $this->_getUrlToRedirectBackTo();

        $loginAction = $this->_config['loginAction'];
        if ($urlToRedirectBackTo === '/') {
            return $loginAction;
        }

        if (is_array($loginAction)) {
            $loginAction['?'][static::QUERY_STRING_REDIRECT] = $urlToRedirectBackTo;
        } else {
            $char = strpos($loginAction, '?') === false ? '?' : '&';
            $loginAction .= $char . static::QUERY_STRING_REDIRECT . '=' . urlencode($urlToRedirectBackTo);
        }

        return $loginAction;
    }

    /**
     * Normalizes config `loginAction` and checks if current request URL is same as login action.
     *
     * @param \Cake\Controller\Controller $controller A reference to the controller object.
     * @return bool True if current action is login action else false.
     */
    protected function _isLoginAction(Controller $controller)
    {
        $uri = $controller->request->getUri();
        $url = Router::normalize($uri->getPath());
        $loginAction = Router::normalize($this->_config['loginAction']);

        return $loginAction === $url;
    }

    /**
     * Handle unauthorized access attempt
     *
     * @param \Cake\Controller\Controller $controller A reference to the controller object
     * @return \Cake\Http\Response
     * @throws \Cake\Http\Exception\ForbiddenException
     */
    protected function _unauthorized(Controller $controller)
    {
        if ($this->_config['unauthorizedRedirect'] === false) {
            throw new ForbiddenException($this->_config['authError']);
        }

        $this->flash($this->_config['authError']);
        if ($this->_config['unauthorizedRedirect'] === true) {
            $default = '/';
            if (!empty($this->_config['loginRedirect'])) {
                $default = $this->_config['loginRedirect'];
            }
            if (is_array($default)) {
                $default['_base'] = false;
            }
            $url = $controller->referer($default, true);
        } else {
            $url = $this->_config['unauthorizedRedirect'];
        }

        return $controller->redirect($url);
    }

    /**
     * Sets defaults for configs.
     *
     * @return void
     */
    protected function _setDefaults()
    {
        $defaults = [
            'authenticate' => ['Form'],
            'flash' => [
                'element' => 'error',
                'key' => 'flash',
                'params' => ['class' => 'error'],
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login',
                'plugin' => null,
            ],
            'logoutRedirect' => $this->_config['loginAction'],
            'authError' => __d('cake', 'You are not authorized to access that location.'),
        ];

        $config = $this->getConfig();
        foreach ($config as $key => $value) {
            if ($value !== null) {
                unset($defaults[$key]);
            }
        }
        $this->setConfig($defaults);
    }

    /**
     * Check if the provided user is authorized for the request.
     *
     * Uses the configured Authorization adapters to check whether or not a user is authorized.
     * Each adapter will be checked in sequence, if any of them return true, then the user will
     * be authorized for the request.
     *
     * @param array|\ArrayAccess|null $user The user to check the authorization of.
     *   If empty the user fetched from storage will be used.
     * @param \Cake\Http\ServerRequest|null $request The request to authenticate for.
     *   If empty, the current request will be used.
     * @return bool True if $user is authorized, otherwise false
     */
    public function isAuthorized($user = null, ServerRequest $request = null)
    {
        if (empty($user) && !$this->user()) {
            return false;
        }
        if (empty($user)) {
            $user = $this->user();
        }
        if (empty($request)) {
            $request = $this->getController()->getRequest();
        }
        if (empty($this->_authorizeObjects)) {
            $this->constructAuthorize();
        }
        foreach ($this->_authorizeObjects as $authorizer) {
            if ($authorizer->authorize($user, $request) === true) {
                $this->_authorizationProvider = $authorizer;

                return true;
            }
        }

        return false;
    }

    /**
     * Loads the authorization objects configured.
     *
     * @return array|null The loaded authorization objects, or null when authorize is empty.
     * @throws \Cake\Core\Exception\Exception
     */
    public function constructAuthorize()
    {
        if (empty($this->_config['authorize'])) {
            return null;
        }
        $this->_authorizeObjects = [];
        $authorize = Hash::normalize((array)$this->_config['authorize']);
        $global = [];
        if (isset($authorize[AuthComponent::ALL])) {
            $global = $authorize[AuthComponent::ALL];
            unset($authorize[AuthComponent::ALL]);
        }
        foreach ($authorize as $alias => $config) {
            if (!empty($config['className'])) {
                $class = $config['className'];
                unset($config['className']);
            } else {
                $class = $alias;
            }
            $className = App::className($class, 'Auth', 'Authorize');
            if (!class_exists($className)) {
                throw new Exception(sprintf('Authorization adapter "%s" was not found.', $class));
            }
            if (!method_exists($className, 'authorize')) {
                throw new Exception('Authorization objects must implement an authorize() method.');
            }
            $config = (array)$config + $global;
            $this->_authorizeObjects[$alias] = new $className($this->_registry, $config);
        }

        return $this->_authorizeObjects;
    }

    /**
     * Getter for authorize objects. Will return a particular authorize object.
     *
     * @param string $alias Alias for the authorize object
     * @return \Cake\Auth\BaseAuthorize|null
     */
    public function getAuthorize($alias)
    {
        if (empty($this->_authorizeObjects)) {
            $this->constructAuthorize();
        }

        return isset($this->_authorizeObjects[$alias]) ? $this->_authorizeObjects[$alias] : null;
    }

    /**
     * Takes a list of actions in the current controller for which authentication is not required, or
     * no parameters to allow all actions.
     *
     * You can use allow with either an array or a simple string.
     *
     * ```
     * $this->Auth->allow('view');
     * $this->Auth->allow(['edit', 'add']);
     * ```
     * or to allow all actions
     * ```
     * $this->Auth->allow();
     * ```
     *
     * @param string|string[]|null $actions Controller action name or array of actions
     * @return void
     * @link https://book.cakephp.org/3/en/controllers/components/authentication.html#making-actions-public
     */
    public function allow($actions = null)
    {
        if ($actions === null) {
            $controller = $this->_registry->getController();
            $this->allowedActions = get_class_methods($controller);

            return;
        }
        $this->allowedActions = array_merge($this->allowedActions, (array)$actions);
    }

    /**
     * Removes items from the list of allowed/no authentication required actions.
     *
     * You can use deny with either an array or a simple string.
     *
     * ```
     * $this->Auth->deny('view');
     * $this->Auth->deny(['edit', 'add']);
     * ```
     * or
     * ```
     * $this->Auth->deny();
     * ```
     * to remove all items from the allowed list
     *
     * @param string|string[]|null $actions Controller action name or array of actions
     * @return void
     * @see \Cake\Controller\Component\AuthComponent::allow()
     * @link https://book.cakephp.org/3/en/controllers/components/authentication.html#making-actions-require-authorization
     */
    public function deny($actions = null)
    {
        if ($actions === null) {
            $this->allowedActions = [];

            return;
        }
        foreach ((array)$actions as $action) {
            $i = array_search($action, $this->allowedActions, true);
            if (is_int($i)) {
                unset($this->allowedActions[$i]);
            }
        }
        $this->allowedActions = array_values($this->allowedActions);
    }

    /**
     * Set provided user info to storage as logged in user.
     *
     * The storage class is configured using `storage` config key or passing
     * instance to AuthComponent::storage().
     *
     * @param array|\ArrayAccess $user User data.
     * @return void
     * @link https://book.cakephp.org/3/en/controllers/components/authentication.html#identifying-users-and-logging-them-in
     */
    public function setUser($user)
    {
        $this->storage()->write($user);
    }

    /**
     * Log a user out.
     *
     * Returns the logout action to redirect to. Triggers the `Auth.logout` event
     * which the authenticate classes can listen for and perform custom logout logic.
     *
     * @return string Normalized config `logoutRedirect`
     * @link https://book.cakephp.org/3/en/controllers/components/authentication.html#logging-users-out
     */
    public function logout()
    {
        $this->_setDefaults();
        if (empty($this->_authenticateObjects)) {
            $this->constructAuthenticate();
        }
        $user = (array)$this->user();
        $this->dispatchEvent('Auth.logout', [$user]);
        $this->storage()->delete();

        return Router::normalize($this->_config['logoutRedirect']);
    }

    /**
     * Get the current user from storage.
     *
     * @param string|null $key Field to retrieve. Leave null to get entire User record.
     * @return mixed|null Either User record or null if no user is logged in, or retrieved field if key is specified.
     * @link https://book.cakephp.org/3/en/controllers/components/authentication.html#accessing-the-logged-in-user
     */
    public function user($key = null)
    {
        $user = $this->storage()->read();
        if (!$user) {
            return null;
        }

        if ($key === null) {
            return $user;
        }

        return Hash::get($user, $key);
    }

    /**
     * Similar to AuthComponent::user() except if user is not found in
     * configured storage, connected authentication objects will have their
     * getUser() methods called.
     *
     * This lets stateless authentication methods function correctly.
     *
     * @return bool true If a user can be found, false if one cannot.
     */
    protected function _getUser()
    {
        $user = $this->user();
        if ($user) {
            return true;
        }

        if (empty($this->_authenticateObjects)) {
            $this->constructAuthenticate();
        }
        foreach ($this->_authenticateObjects as $auth) {
            $result = $auth->getUser($this->getController()->getRequest());
            if (!empty($result) && is_array($result)) {
                $this->_authenticationProvider = $auth;
                $event = $this->dispatchEvent('Auth.afterIdentify', [$result, $auth]);
                if ($event->getResult() !== null) {
                    $result = $event->getResult();
                }
                $this->storage()->write($result);

                return true;
            }
        }

        return false;
    }

    /**
     * Get the URL a user should be redirected to upon login.
     *
     * Pass a URL in to set the destination a user should be redirected to upon
     * logging in.
     *
     * If no parameter is passed, gets the authentication redirect URL. The URL
     * returned is as per following rules:
     *
     *  - Returns the normalized redirect URL from storage if it is
     *    present and for the same domain the current app is running on.
     *  - If there is no URL returned from storage and there is a config
     *    `loginRedirect`, the `loginRedirect` value is returned.
     *  - If there is no session and no `loginRedirect`, / is returned.
     *
     * @param string|array|null $url Optional URL to write as the login redirect URL.
     * @return string Redirect URL
     */
    public function redirectUrl($url = null)
    {
        $redirectUrl = $this->getController()->getRequest()->getQuery(static::QUERY_STRING_REDIRECT);
        if ($redirectUrl && (substr($redirectUrl, 0, 1) !== '/' || substr($redirectUrl, 0, 2) === '//')) {
            $redirectUrl = null;
        }

        if ($url !== null) {
            $redirectUrl = $url;
        } elseif ($redirectUrl) {
            if (Router::normalize($redirectUrl) === Router::normalize($this->_config['loginAction'])) {
                $redirectUrl = $this->_config['loginRedirect'];
            }
        } elseif ($this->_config['loginRedirect']) {
            $redirectUrl = $this->_config['loginRedirect'];
        } else {
            $redirectUrl = '/';
        }
        if (is_array($redirectUrl)) {
            return Router::url($redirectUrl + ['_base' => false]);
        }

        return $redirectUrl;
    }

    /**
     * Use the configured authentication adapters, and attempt to identify the user
     * by credentials contained in $request.
     *
     * Triggers `Auth.afterIdentify` event which the authenticate classes can listen
     * to.
     *
     * @return array|false User record data, or false, if the user could not be identified.
     */
    public function identify()
    {
        $this->_setDefaults();

        if (empty($this->_authenticateObjects)) {
            $this->constructAuthenticate();
        }
        // dd($this->_authenticateObjects);
        foreach ($this->_authenticateObjects as $auth) {
            $result = $auth->authenticate($this->getController()->getRequest(), $this->response);
            if (!empty($result)) {
                $this->_authenticationProvider = $auth;
                $event = $this->dispatchEvent('Auth.afterIdentify', [$result, $auth]);
                if ($event->getResult() !== null) {
                    return $event->getResult();
                }

                return $result;
            }
        }

        return false;
    }

    /**
     * Loads the configured authentication objects.
     *
     * @return array|null The loaded authorization objects, or null on empty authenticate value.
     * @throws \Cake\Core\Exception\Exception
     */
    public function constructAuthenticate()
    {
        if (empty($this->_config['authenticate'])) {
            return null;
        }
        $this->_authenticateObjects = [];
        $authenticate = Hash::normalize((array)$this->_config['authenticate']);
        $global = [];
        if (isset($authenticate[AuthComponent::ALL])) {
            $global = $authenticate[AuthComponent::ALL];
            unset($authenticate[AuthComponent::ALL]);
        }
        foreach ($authenticate as $alias => $config) {
            if (!empty($config['className'])) {
                $class = $config['className'];
                unset($config['className']);
            } else {
                $class = $alias;
            }
            $className = App::className($class, 'Auth', 'Authenticate');
            if (!class_exists($className)) {
                throw new Exception(sprintf('Authentication adapter "%s" was not found.', $class));
            }
            if (!method_exists($className, 'authenticate')) {
                throw new Exception('Authentication objects must implement an authenticate() method.');
            }
            $config = array_merge($global, (array)$config);
            $this->_authenticateObjects[$alias] = new $className($this->_registry, $config);
            $this->getEventManager()->on($this->_authenticateObjects[$alias]);
        }

        return $this->_authenticateObjects;
    }

    /**
     * Get/set user record storage object.
     *
     * @param \Cake\Auth\Storage\StorageInterface|null $storage Sets provided
     *   object as storage or if null returns configured storage object.
     * @return \Cake\Auth\Storage\StorageInterface|null
     */
    public function storage(StorageInterface $storage = null)
    {
        if ($storage !== null) {
            $this->_storage = $storage;

            return null;
        }

        if ($this->_storage) {
            return $this->_storage;
        }

        $config = $this->_config['storage'];
        if (is_string($config)) {
            $class = $config;
            $config = [];
        } else {
            $class = $config['className'];
            unset($config['className']);
        }
        $className = App::className($class, 'Auth/Storage', 'Storage');
        if (!class_exists($className)) {
            throw new Exception(sprintf('Auth storage adapter "%s" was not found.', $class));
        }
        $request = $this->getController()->getRequest();
        $response = $this->getController()->getResponse();
        $this->_storage = new $className($request, $response, $config);

        return $this->_storage;
    }

    /**
     * Magic accessor for backward compatibility for property `$sessionKey`.
     *
     * @param string $name Property name
     * @return mixed
     */
    public function __get($name)
    {
        if ($name === 'sessionKey') {
            return $this->storage()->getConfig('key');
        }

        return parent::__get($name);
    }

    /**
     * Magic setter for backward compatibility for property `$sessionKey`.
     *
     * @param string $name Property name.
     * @param mixed $value Value to set.
     * @return void
     */
    public function __set($name, $value)
    {
        if ($name === 'sessionKey') {
            $this->_storage = null;

            if ($value === false) {
                $this->setConfig('storage', 'Memory');

                return;
            }

            $this->setConfig('storage', 'Session');
            $this->storage()->setConfig('key', $value);

            return;
        }

        $this->{$name} = $value;
    }

    /**
     * Getter for authenticate objects. Will return a particular authenticate object.
     *
     * @param string $alias Alias for the authenticate object
     * @return \Cake\Auth\BaseAuthenticate|null
     */
    public function getAuthenticate($alias)
    {
        if (empty($this->_authenticateObjects)) {
            $this->constructAuthenticate();
        }

        return isset($this->_authenticateObjects[$alias]) ? $this->_authenticateObjects[$alias] : null;
    }

    /**
     * Set a flash message. Uses the Flash component with values from `flash` config.
     *
     * @param string|false $message The message to set. False to skip.
     * @return void
     */
    public function flash($message)
    {
        if ($message === false) {
            return;
        }

        $this->Flash->set($message, $this->_config['flash']);
    }

    /**
     * If login was called during this request and the user was successfully
     * authenticated, this function will return the instance of the authentication
     * object that was used for logging the user in.
     *
     * @return \Cake\Auth\BaseAuthenticate|null
     */
    public function authenticationProvider()
    {
        return $this->_authenticationProvider;
    }

    /**
     * If there was any authorization processing for the current request, this function
     * will return the instance of the Authorization object that granted access to the
     * user to the current address.
     *
     * @return \Cake\Auth\BaseAuthorize|null
     */
    public function authorizationProvider()
    {
        return $this->_authorizationProvider;
    }

    /**
     * Returns the URL to redirect back to or / if not possible.
     *
     * This method takes the referrer into account if the
     * request is not of type GET.
     *
     * @return string
     */
    protected function _getUrlToRedirectBackTo()
    {
        $urlToRedirectBackTo = $this->request->getRequestTarget();
        if (!$this->request->is('get')) {
            $urlToRedirectBackTo = $this->request->referer(true);
        }

        return $urlToRedirectBackTo;
    }
}
