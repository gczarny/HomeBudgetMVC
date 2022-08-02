<?php

namespace Core;

use \App\Auth;
Use \App\Flash;

/**
 * Base controller
 *
 * PHP version 7.0
 */
abstract class Controller
{

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];

    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }

    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name  Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
        
    }

    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after()
    {
    }

    /**
     * Redirect to a different page
     * 
     * @param string $url The relative URL
     * 
     * @return void
     */
    public function redirect($url)
    {
        
        //header('Location: https://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $protocol = 'https';
        } else {
            $protocol = 'http';
        }
     
        header("Location: $protocol://" . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit;
    }

    /**
     * Require the user to be logged in before giving acces to the 
     * requested page. Remember the requested page for later, then 
     * redirect to the login page.
     * 
     * @return void
     */
    public function requireLogin()
    {
        if(! Auth::getUser()){

            Flash::addMessage('Please, login to access that page', Flash::INFO);

            Auth::rememberRequestPage();
            $this->redirect('/login');
        }
    }

    /**
     * Require no user to be logged in.
     *
     *
     * @return void
     */
    public function requireGuest()
    {
        if (Auth::getUser()) {
            
            $this->redirect('/dashboard');
        }
    }
}
