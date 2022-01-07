<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;

/**
 * Profile controller
 *
 * PHP version 7.0
 */
class Profile extends Authenticated
{

    /**
     * Before filter - called before each action method
     * 
     * @return void
     */
    protected function before()
    {
        parent::before(); //so we will not override

        $this->user = Auth::getUser();
    }

    /**
     * Show the profile
     *
     * @return void
     */
    public function showAction()
    {
        View::renderTemplate('Dashboard/show.html', [
            'user' => $this->user
        ]);
    }

    /**
     * Show the form to edit the profile
     * 
     * @return void
     */
    public function editAction()
    {
        View::renderTemplate('Dashboard/edit.html', [
            'user' => $this->user
        ]);
    }

    /**
     * Update the profile
     * 
     * @return void
     */
    public function updateAction()
    
    {
        if($this->user->updateProfile($_POST)){

            Flash::addMessage('Changes saved');
            $this->redirect('/profile/show');

        }else {

            View::renderTemplate('Dashboard/edit.html', [
                'user' => $this->user
            ]);
        }
    }
}
