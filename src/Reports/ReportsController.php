<?php
namespace Anax\Reports;
 
/**
 * A controller for users and admin related events.
 *
 */
class ReportsController implements \Anax\DI\IInjectionAware
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
	    $this->report = new \Anax\Reports\Report();
	    $this->report->setDI($this->di);
	    $this->extend = new \Anax\Reports\Extend();
	    $this->extend->setDI($this->di);
	    $this->tagReport = new \Anax\Tags\TagReport();
	    $this->tagReport->setDI($this->di);
	    $this->tag = new \Anax\Tags\Tag();
	    $this->tag->setDI($this->di);
	    $this->userView = new \Anax\Users\UserView();
	    $this->userView->setDI($this->di);
	    $this->vote = new \Anax\Votes\Vote();
	    $this->vote->setDI($this->di);
	}

	public function reportButtonAction() {
        $content = '<a class="message" href="' . $this->url->create('reports/add') . '">Create Report</a>';

	    $this->views->add('buggers/page', [
	    	'class'		=> 'report-add-button',
	        'content' 	=> $content
	    ], 'sidebar');
	} 


	public function solveAction($reportId, $extendId) {
		$this->initialize();

		$loggedIn = $this->di->AuthController->getLoggedInUser();

		// $sql = "
		// 	SELECT prj_extend.userid
		// 	FROM prj_extend
		// 	WHERE prj_extend.id = ?
		// ";
		// $extend = $this->extend->executeRaw($sql, [$extendId]);

		$extend = $this->extend->query('userid')
			->where('id = ?')
			->execute([$extendId]);
		!empty($loggedIn) && $loggedIn['id'] == $extend[0]->userid ? die('ILLEGAL ACTIONS') : null;

		$this->report->save([
			'id'		=> $reportId,
			'solvedby'	=> $extendId
		]);
		$this->redirectTo('reports/id/' . $reportId);
	}


	/**
	* Add tags to each report in reports
	*
	* @param  $reports report-objects
	*
	* @return $reports report-objects
	*/
	private function addTags($reports) {
		foreach ($reports as $key => $report) {
	    	$report->setProperties(['tags' => $this->tagReport->listForReport($report->id)]);
	    }
		return $reports;
	}


	/**
	 * List all reports.
	 *
	 * @param $order  string  SQL ordering
	 * @param $filter array() SQL filtering 
	 *
	 * @return void
	 */
	public function listAction($order = null, $filter = array()) {
	    $this->initialize();

	    $filter = !empty($filter) ? 'WHERE ' . $filter['where'] . '=' . $filter['id']  : null;
	    $order = $order ? 'ORDER BY ' . $order . ' DESC' : 'ORDER BY created DESC';

	    $sql = "
			SELECT prj_report.*, prj_userview.name, prj_userview.acronym, prj_userview.email, 
				IFNULL((SELECT COUNT(prj_extend.id) 
				FROM prj_extend 
				WHERE prj_extend.reportid = prj_report.id), 0) AS extends,
				
				IFNULL((SELECT (SUM(prj_vote.vote = '+') - SUM(prj_vote.vote = '-'))
				FROM prj_vote
				WHERE prj_vote.posttypeid = prj_report.id AND prj_vote.posttype = 'report'), 0) AS votes,
				
				IFNULL((SELECT COUNT(prj_report.id)
				FROM prj_report
				WHERE prj_report.userid = prj_userview.id), 0) AS reports
			FROM prj_report
			JOIN prj_userview ON prj_report.userid = prj_userview.id
			{$filter} 
			GROUP BY prj_report.id
			{$order}
		";
	    $reports = $this->report->executeRaw($sql, []);
	    $reports = $this->addTags($reports);

	    $this->theme->setTitle("ORDER BY");

	    $this->views->add('report/list-all', [
	    	'title'		=> 'All Reports',
	        'reports' 	=> $reports,
	    ]);

	    $this->dispatcher->forward([
	        'controller' => 'reports',
	        'action'     => 'reportButton',
	    ]);
	}


	/**
	* List reports by tag.
	*
	* @param $id int tag-id
	*
	* @return void
	*/
	public function tagAction($id) {
		$this->initialize();

		$sql = "
			SELECT prj_report.*, prj_userview.name, prj_userview.acronym, prj_userview.email,
				IFNULL((SELECT COUNT(prj_extend.id) 
				FROM prj_extend 
				WHERE prj_extend.reportid = prj_report.id), 0) AS extends,
				
				IFNULL((SELECT (SUM(prj_vote.vote = '+') - SUM(prj_vote.vote = '-'))
				FROM prj_vote
				WHERE prj_vote.posttypeid = prj_report.id AND prj_vote.posttype = 'report'), 0) AS votes,
				
				IFNULL((SELECT COUNT(prj_report.id)
				FROM prj_report
				WHERE prj_report.userid = prj_userview.id), 0) AS reports
			FROM prj_tagreport
			JOIN prj_report ON prj_tagreport.reportid = prj_report.id
			JOIN prj_userview ON prj_report.userid = prj_userview.id
			WHERE prj_tagreport.tagid = ?
			GROUP BY prj_report.id
		";
		$reports = $this->report->executeRaw($sql, [$id]);
		// Tag report tags
	    $reports = $this->addTags($reports);

		$this->theme->setTitle("REPORT BY TAG");
	    $this->views->add('report/list-all', [
	    	'title'		=> 'Report By Tag',
	        'reports' 	=> $reports,
	    ]);

	    $this->dispatcher->forward([
	        'controller' => 'tags',
	        'action'     => 'listActive',
	    ]);
	}


	/**
	* List reports by user.
	*
	* @param $id int user-id
	*
	* @return void
	*/
	public function listByUserAction($id) {
		$this->initialize();

	    $sql = "
			SELECT prj_report.*, prj_userview.name, prj_userview.acronym, prj_userview.email, 
				IFNULL((SELECT COUNT(prj_extend.id) 
				FROM prj_extend 
				WHERE prj_extend.reportid = prj_report.id), 0) AS extends,
				
				IFNULL((SELECT (SUM(prj_vote.vote = '+') - SUM(prj_vote.vote = '-'))
				FROM prj_vote
				WHERE prj_vote.posttypeid = prj_report.id AND prj_vote.posttype = 'report'), 0) AS votes,
				
				IFNULL((SELECT COUNT(prj_report.id)
				FROM prj_report
				WHERE prj_report.userid = prj_userview.id), 0) AS reports
			FROM prj_report
			JOIN prj_userview ON prj_report.userid = prj_userview.id
			WHERE prj_userview.id = ?
			GROUP BY prj_report.id
			ORDER BY created DESC LIMIT 5
		";
	    $reports = $this->report->executeRaw($sql, [$id]);
	    $reports = $this->addTags($reports);

	    $this->views->add('report/list-all', [
	    	'title'		=> 'Latest Reports',
	        'reports' 	=> $reports,
	    ]);
	}


	/**
	* Show report by id
	*
	* @param $id int report-id
	*
	* @return void
	*/
	public function idAction($id = null) {
		// Check incoming ID
		if(!isset($id) || (isset($id) && !is_numeric($id))) {
			 $this->sparkles->flash('error', 'No reports found...');
			 $this->redirectTo('reports');
		}

	    $this->initialize();

		$sql = "
			SELECT prj_report.*, prj_userview.name, prj_userview.acronym, prj_userview.email, 
				IFNULL((SELECT COUNT(prj_extend.id) 
				FROM prj_extend 
				WHERE prj_extend.reportid = prj_report.id), 0) AS extends,
				
				IFNULL((SELECT (SUM(prj_vote.vote = '+') - SUM(prj_vote.vote = '-'))
				FROM prj_vote
				WHERE prj_vote.posttypeid = prj_report.id AND prj_vote.posttype = 'report'), 0) AS votes,
				
				IFNULL((SELECT COUNT(prj_report.id)
				FROM prj_report
				WHERE prj_report.userid = prj_userview.id), 0) AS reports
			FROM prj_report
			JOIN prj_userview ON prj_report.userid = prj_userview.id
			WHERE prj_report.id = ?
			GROUP BY prj_report.id
			ORDER BY created DESC
		";
		$report = $this->report->executeRaw($sql, [$id]);
	 
	    $tags 		= $this->tagReport->listForReport($id);
	    $loggedIn 	= $this->di->AuthController->getLoggedInUser();
		$userVote 	= !empty($loggedIn) ? $this->vote->getUserVote('report', $id, $loggedIn['id']) : false;
	 	$report[0]->setProperties(['uservoted' => empty($userVote) ? false : $userVote[0]->vote]);

	    $this->views->add('report/report', [
	    	'title'			=> 'All Reports',
	        'report' 		=> $report[0],
	        'tags'			=> !empty($tags) ? $tags : null,
	        'loggedIn'		=> $loggedIn
	    ]);

        $this->dispatcher->forward([
	        'controller' => 'comments',
	        'action'     => 'add',
	        'params'	 => [
	        	'type' 		=> 'report',
	        	'typeId'	=> 	$id
        	]
   		]);

        $this->dispatcher->forward([
	        'controller' => 'comments',
	        'action'     => 'list',
	        'params'	 => [
	        	'type' 		=> 'report',
	        	'typeId'	=> 	$report[0]->id
    		]
   		]);

        // Add extends to report
		$sql = "
			SELECT prj_extend.*, prj_report.solvedby, prj_userview.name, prj_userview.acronym, prj_userview.email, 				
				IFNULL((SELECT (SUM(prj_vote.vote='+')-SUM(prj_vote.vote='-'))
				FROM prj_vote
				WHERE prj_vote.posttypeid = prj_extend.id AND prj_vote.posttype = 'extend'), 0) AS votes,
				
				IFNULL((SELECT COUNT(prj_report.id)
				FROM prj_report
				WHERE prj_report.userid = prj_userview.id), 0) AS reports				
			FROM prj_extend
			JOIN prj_userview ON prj_extend.userid = prj_userview.id
			JOIN prj_report ON prj_extend.reportid = prj_report.id
			WHERE prj_report.id = ?
			GROUP BY prj_extend.id
			ORDER BY prj_extend.id = prj_report.solvedby DESC, prj_extend.created ASC
		";
		$extends = $this->extend->executeRaw($sql, [$id]);

		foreach ($extends as $key => $extend) {
			$id = $extend->id;
			$userVote = !empty($loggedIn) ? $this->vote->getUserVote('extend', $id, $loggedIn['id']) : false;
			$extend->setProperties(['uservoted' => empty($userVote) ? false : $userVote[0]->vote]);
			$extend->setProperties(['solvesreport' => ($id == $report[0]->solvedby ? true : false)]);
			$this->viewExtend($extend); // Send extend object to be viewed
		}

        $this->addExtend($report[0]->id);

		// Put some information to the sidebar
        $this->dispatcher->forward([
	        'controller'	=> 'users',
	        'action'     	=> 'profile',
	        'params'		=> ['id' => $report[0]->userid]
   		]);

	    $this->di->views->add('tag/list-all', [
	    	'title' => 'Report tags',
	        'tags' 	=> $tags
	    ], 'sidebar');

	    $this->reportButtonAction();
	}


	/**
	* Add report
	*
	* @return void
	*/
	public function addAction() {
        $this->initialize();

        $loggedIn 	= $this->AuthController->getLoggedInUser();
        $form 		= $this->di->form;
        $form->create([], [
            'title' => [
                'type'          => 'text',
                'label'         => 'Title:',
                'required'      => true,
                'break'			=> false,
                'validation'    => ['not_empty'],
            ],
            'content' => [
                'type'          => 'textarea',
                'required'      => true,
                'break'			=> false,
                'description'	=> 'Content is supporting Markdown-language. You can read more about how to use Markdown <a href="https://help.github.com/articles/markdown-basics/">here</a>.',
                'validation'    => ['not_empty'],
            ],
            'tags' => [
                'type'          => 'text',
                'required'      => true,
                'break'			=> false,
                'description'	=> 'Separate multiple tags with "," (tags,like,this,)',
                'validation'    => ['not_empty'],
            ],
            'reset' => [
                'type'          => 'reset',
                'value'         => 'Reset',
                'break'			=> false,
                'class'			=> 'red',
                'callback'      => null, // Reset button, no function needed
            ],
            'submit' => [
                'type'          => 'submit',
                'value'         => 'Create Report',
                'class'			=> 'red',
                'callback'      => function ($form) use ($loggedIn) {
				    
				    empty($loggedIn) ? die('ILLEGAL ACTION') : null;      

		        	$now 		= gmdate('Y-m-d H:i:s');
					$title  	= htmlentities($form->value('title'), null, 'UTF-8');
	    			$content   	= $this->di->textFilter->doFilter(htmlentities($form->value('content'), null, 'UTF-8'), 'markdown');
			    	$form->saveInSession = true;

				    $save = $this->report->save([
				        'title' 	=> $title,
				        'content' 	=> $content,
				        'userid'	=> $loggedIn['id'],
				        'created'	=> $now
			    	]);
				    $lastInsertId = $this->report->lastInsertId();
                    $tags = explode(",", $form->value('tags'));
                	
                	foreach ($tags as $key => $tag) {
						$this->tagReport->save([
							'id'		=> null,
							'tagid' 	=> $this->tag->add($tag),
							'reportid' 	=> $lastInsertId
						]);
                	}

		        	return $save;
                }
            ],
        ]);

        $callbackSuccess = function ($form) {
            $this->sparkles->flash('success', 'Report was created');
            $this->redirectTo('reports');
        };
         
        $callbackFail = function ($form) {
            $this->sparkles->flash('error', 'Buggers! Something went wrong...');
            $this->redirectTo();
        };
         
        // Check the status of the form
        $form->check($callbackSuccess, $callbackFail);

        $this->theme->setTitle("ADD REPORT");
        $this->views->add('buggers/form', [
            'title' 	=> "Create report",
            'class'		=> 'report-add form no-br',
            'message'	=> 'Login to make a report',
            'loggedIn'	=> $loggedIn,
            'content' 	=> $form->getHTML()
        ]);

	    $this->dispatcher->forward([
	        'controller' => 'tags',
	        'action'     => 'listActive',
	    ]);
    }


	/**
	* Show extend
	*
	* @param $extend extend-object
	*
	* @return void
	*/
	public function viewExtend($extend) {
	    $this->views->add('report/extend', [
	        'extend' 	=> $extend,
	        'loggedIn'	=> $this->di->AuthController->getLoggedInUser()
	    ]);

        $this->dispatcher->forward([
	        'controller' => 'comments',
	        'action'     => 'add',
	        'params'	 => [
	        	'type' 		=> 'extend',
	        	'typeId'	=> 	$extend->id
    		]
   		]);

        $this->dispatcher->forward([
	        'controller' => 'comments',
	        'action'     => 'list',
	        'params'	 => [
	        	'type' 		=> 'extend',
	        	'typeId'	=> 	$extend->id
    		]
   		]);
	}


	/**
	* Add extend
	*
	* @param $id int extend-id
	*
	* @return void
	*/
	public function addExtend($id = null) {
        isset($id) && is_numeric($id) ? null : die ('ILLEGAL ACTION');
		$loggedIn = $this->AuthController->getLoggedInUser();
        
        $form = $this->form;
        $form->create([], [
            'title' => [
                'type'          => 'text',
                'label'         => 'Title:',
                'required'      => true,
                'break'			=> false,
                'validation'    => ['not_empty'],
            ],
            'content' => [
                'type'          => 'textarea',
                'required'      => true,
                'break'			=> false,
                'description'   => 'Content is supporting Markdown-language. You can read more about how to use Markdown <a href="https://help.github.com/articles/markdown-basics/">here</a>.',
                'validation'    => ['not_empty'],
            ],
            'reset' => [
                'type'          => 'reset',
                'value'         => 'Reset',
                'class'			=> 'red',
                'callback'      => null, // Reset button, no function needed
            ],
            'submit' => [
                'type'          => 'submit',
                'value'         => 'Extend',
                'class'			=> 'red',
                'name'			=> 'submit-extended-' . $id,
                'callback'      => function ($form) use ($id, $loggedIn) {

                    empty($loggedIn) ? die('ILLEGAL ACTION') : null;
		        	$form->saveInSession = false;          
		        	$now 		= gmdate('Y-m-d H:i:s');
					$title  	= htmlentities($form->value('title'), null, 'UTF-8');
	    			$content   	= $this->di->textFilter->doFilter(htmlentities($form->value('content'), null, 'UTF-8'), 'markdown');

				    $save = $this->extend->save([
				        'title' 	=> $title,
				        'content' 	=> $content,
				        'userId'	=> $loggedIn['id'],
				        'reportId'	=> $id,
				        'created'	=> $now
			    	]);

		    	return $save;
                }
            ],
        ]);

        $callbackSuccess = function ($form) {
            $this->sparkles->flash('success', 'Report Extended');
            $this->redirectTo();
        };
         
        $callbackFail = function ($form) {
            // What to do when form could not be processed?
            $this->sparkles->flash('error', 'Buggers! Something went wrong...');
            $this->redirectTo();
        };
         
        // Check the status of the form
        $form->check($callbackSuccess, $callbackFail);

        $this->theme->setTitle("POST EXTEND");
        $this->views->add('buggers/form', [
            'title' 	=> "Extend Report",
            'class'		=> 'extend-add form no-br',
            'message'	=> 'Login To Extend Report',
            'loggedIn'	=> $loggedIn,
            'content' 	=> $form->getHTML()
        ]);
    }
}