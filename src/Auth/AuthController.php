<?php

namespace Anax\Auth;

class AuthController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable,
    	 \Anax\MVC\TRedirectHelpers;


	/**
	 * Initialize the controller.
	 *
	 * @return void
	 */
	public function initialize()
	{
	    $this->user = new \Anax\Users\User();
	    $this->user->setDI($this->di);
	}


	private function saveToSession($user) {
		if (!$this->session->has('loggedIn')) {
			$this->session->name('loggedIn');
		}
		$this->session->set('loggedIn', $user);
	}


	public function logoutAction() {
		if ($this->session->has('loggedIn')) {
			$this->session->set('loggedIn', []);
		}
		$this->di->sparkles->flash('success', 'Bye!');
		$this->redirectTo('index.php');
	}


	public function getLoggedInUser() {
		$user = null;
		if($this->session->has('loggedIn')) {
			$user = $this->session->get('loggedIn');
		}
		
		return $user;
	}


	public function authorize($acronym, $password) {
		$this->initialize();

	    $user = $this->user->query()
	    	->where('acronym = ?')
	    	->execute([$acronym]);

		if (!empty($user) && password_verify($password, $user[0]->password)) {
			$_user = $user[0]->getProperties($user[0]);
			$this->saveToSession($_user);
			return true;
		} else { return false; }
	}

	/**
	 * Add new user. Session started in config_with_app
	 *
	 * @return void
	 */
	public function loginFormAction()
	{
		// Get CForm from $di service
		$form = $this->di->form;
		$form->create([], [
	        'acronym' => [
	            'type'        	=> 'text',
	            'label'       	=> 'Username:',
	            'required'    	=> true,
	            'validation'  	=> ['not_empty'],
	        ],
	        'text' => [
	            'type'        	=> 'password',
	            'label'			=> 'Password',
	            'required'    	=> true,
	            'validation'  	=> ['not_empty'],
	        ],
	        'submit' => [
	            'type'      	=> 'submit',
	            'class'			=> 'red',
	            'callback'  	=> function ($form) {

	 				// Save input data to session 
	                $form->saveInSession = false;
				    // Using login() method to login user
	                return $this->authorize($form->value('acronym'), $form->value('text'));
	            }
	        ]
	    ]);

		$callbackSuccess = function ($form) {
		    $this->di->sparkles->flash('success', 'Welcome!');
		    $this->redirectTo('users/id/' . $this->AuthController->getLoggedInUser()['id']);
		};
		 
		$callbackFail = function ($form) {
	        // What to do when form could not be processed?
	        $this->di->sparkles->flash('error', 'Login failed');
	        $this->redirectTo();
		};
		 
		// Check the status of the form
		$form->check($callbackSuccess, $callbackFail);

	    $this->views->add('buggers/page', [
	        'title' 	=> "LOGIN",
	        'class'		=> 'login form',
	        'content' 	=> $form->getHTML()
	    ], 'sidebar');
	}
}