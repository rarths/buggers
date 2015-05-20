<?php
namespace Anax\Users;
 
/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware
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
	    $this->userView = new \Anax\Users\UserView();
	    $this->userView->setDI($this->di);
	}


	public function idAction($id) {
		$this->initialize();
		$user = $this->userView->id($id);

		$this->theme->setTitle("USER INFORMATION" );
	    $this->views->add('user/user', [
	        'title'		=> "User Information",
	        'loggedIn' 	=> $this->AuthController->getLoggedInUser(),
	        'user'  	=> $user
	    ], 'sidebar');

	    $this->dispatcher->forward([
	        'controller' => 'reports',
	        'action'     => 'listByUser',
	        'params'     => ['id' => $id]
	    ]);
	}


	public function profileAction($id) {
		$this->initialize();
		$user = $this->userView->id($id);

	    $this->views->add('user/user', [
	        'title'		=> "User Information",
	        'loggedIn' 	=> $this->AuthController->getLoggedInUser(),
	        'user'  	=> $user
	    ], 'sidebar');		
	}


	public function listAction() {
		$this->initialize();

		$sql = "
			SELECT *, IFNULL(COUNT(prj_report.id),0) AS reports
			FROM prj_userview
			LEFT JOIN prj_report ON prj_userview.id = prj_report.userid
			GROUP BY prj_userview.id
		";
		$users = $this->user->executeRaw($sql, []);

	    $this->views->add('user/list-all', [
	        'title'	=> "All Users",
	        'users'  => $users
	    ]);
	}


	public function listActiveAction() {
		$this->initialize();

		$sql = "
			SELECT *, COUNT(prj_report.id) AS reports
			FROM prj_userview
			JOIN prj_report ON prj_userview.id = prj_report.userid
			GROUP BY prj_userview.id
			ORDER BY reports DESC
		";
		$users = $this->user->executeRaw($sql, []);

	    $this->views->add('user/list-all', [
	        'title'	=> "Active Users",
	        'users'  => $users
	    ], 'sidebar');
	}


	/**
	 * Add new user. Session started in config_with_app
	 *
	 * @return void
	 */
	public function addAction()
	{
		// Get CForm from $di service
		$form = $this->di->form;

		$form->create([], [
	        'acronym' => [
	            'type'        	=> 'text',
	            'label'       	=> 'Username:',
	            'required'    	=> true,
	            'break'			=> false,
	            'validation'  	=> ['not_empty'],
	        ],
	        'name' => [
	            'type'        	=> 'text',
	            'label'       	=> 'Name:',
	            'required'    	=> true,
	            'break'			=> false,
	            'validation'  	=> ['not_empty'],
	        ],
	        'email' => [
	            'type'        	=> 'text',
	            'required'    	=> true,
	            'break'			=> false,
	            'validation'  	=> ['not_empty', 'email_adress'],
	        ],
	        'text' => [
				'type'			=> 'password',
				'label'			=> 'New Password',
				'required'		=> true,
				'break'			=> false,
				'validation'	=> ['not_empty'],
	        ],
	        'submit' => [
	            'type'      => 'submit',
	            'class'		=> 'red',
	            'callback'  => function ($form) {
					// Save input data to session 
	                $form->saveInSession = false;

    			    $now = gmdate('Y-m-d H:i:s');
    			    $acronym = $form->value('acronym');
    			    $password = $form->value('password');
    			    
				    // Save user input to database
				    $save = $this->user->save([
				        'acronym' 	=> $acronym,
				        'name' 		=> $form->value('name'),
				        'email' 	=> $form->value('email'),
				        'password' 	=> password_hash($password, PASSWORD_DEFAULT),
				        'created' 	=> $now,
				        'active' 	=> $now,
				    ]);
				    //$lastInsertId = $this->user->lastInsertId();
	 				
	                if($save) {
	                	// Login created user
	                	$this->AuthController->authorize($acronym, $password);
	                	return true;
	                } else { return false; }
	            }
	        ],
	    ]);

		$callbackSuccess = function ($form) {
		    $this->di->sparkles->flash('success', 'User created');
		    // Redirect to the created user-ID
		    $this->redirectTo('reports');
		};
		 
		$callbackFail = function ($form) {
	        // What to do when form could not be processed?
	        $this->di->sparkles->flash('error', 'Ohno, something went wrong!');
	        $this->redirectTo();
		};
		 
		// Check the status of the form
		$form->check($callbackSuccess, $callbackFail);

	    $this->views->add('buggers/page', [
	        'title' 	=> "REGISTER USER",
	        'class'		=> 'add-user form no-br',
	        'content' 	=> $form->getHTML()
	    ]);

	    $this->dispatcher->forward([
	        'controller' => 'reports',
	        'action'     => 'reportButton',
	    ]);
	}


	/**
	* Edit a user. Session started in config_with_app
	*
	* @param int user $id to update.
	*
	* @return void
	*/
	public function editAction($id = null)
	{
		$this->initialize();
		$loggedIn = $this->AuthController->getLoggedInUser();

		// Validate parameters and user
		isset($id) && is_numeric($id) && $loggedIn['id'] == $id ? null : die('ILLEGAL ACTION');
		
		// Get CForm from $di service
		$form = $this->di->form;
		$form->create([], [
	        'acronym' => [
	            'type'        	=> 'text',
	            'label'       	=> 'Username:',
	            'required'    	=> true,
	            'break'			=> false,
	            'validation'  	=> ['not_empty'],
	            'value'			=> isset($loggedIn['acronym']) ? $loggedIn['acronym'] : null,
	        ],
	        'name' => [
	            'type'        	=> 'text',
	            'label'       	=> 'Name:',
	            'required'    	=> true,
	            'break'			=> false,
	            'validation'  	=> ['not_empty'],
	            'value'			=> isset($loggedIn['name']) ? $loggedIn['name'] : null,
	        ],
	        'email' => [
	            'type'        	=> 'text',
	            'required'    	=> true,
	            'break'			=> false,
	            'validation'  	=> ['not_empty', 'email_adress'],
	            'value'			=> isset($loggedIn['email']) ? $loggedIn['email'] : null,
	        ],
	        'password' => [
	            'type'        	=> 'password',
	            'required'    	=> true,
	            'break'			=> false,
	            'validation'  	=> ['not_empty'],
	        ],
            'reset' => [
                'type'          => 'reset',
                'value'         => 'Reset',
                'class'			=> 'red',
                'callback'      => null, // Reset button, no method needed
            ],
	        'submit' => [
	            'type'      	=> 'submit',
	            'class'			=> 'red',
      			'value'			=> 'Update',
	            'callback'  	=> function ($form) use ($id) {
    			    
    			    $now = gmdate('Y-m-d H:i:s');
					// Save input data to session 
	                $form->saveInSession = false;
    			    
				    // Save user input to database
				    $edit = $this->user->save([
				    	'id'		=> $id,
				        'acronym' 	=> $form->value('acronym'),
				        'name' 		=> $form->value('name'),
				        'email' 	=> $form->value('email'),
				        'password' 	=> password_hash($form->value('password'), PASSWORD_DEFAULT),
				        'updated'	=> $now
				    ]);

	                return $edit;
	            }
	        ]
	    ]);

		$callbackSuccess = function ($form) use ($id) {
			$this->di->sparkles->flash('success', 'Details Updated!');
		    $this->redirectTo('users/id/' . $id);
		};
		 
		$callbackFail = function ($form) use ($id) {
			$this->di->sparkles->flash('error', 'Updating details failed...');
	        $this->redirectTo('users/edit/' . $id);
		};
		 
		// Check the status of the form
		$form->check($callbackSuccess, $callbackFail);

		$this->theme->setTitle("EDIT USER");
        $this->views->add('buggers/form', [
            'title' 	=> "Edit User",
            'class'		=> 'user-edit form',
            'message'	=> 'Only registerd users can be edited',
            'loggedIn'	=> $loggedIn,
            'content' 	=> $form->getHTML()
        ]);

	    // Show user information in the sidebar
	    //$this->profileAction($user->id);
	}
}