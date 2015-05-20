<?php
namespace Anax\Votes;
 
/**
 * A controller for users and admin related events.
 *
 */
class VotesController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable,
    	 \Anax\MVC\TRedirectHelpers;
	

	public function initialize() {
	    $this->vote = new \Anax\Votes\Vote();
	    $this->vote->setDI($this->di);
	    $this->extend = new \Anax\Reports\Extend();
	    $this->extend->setDI($this->di);
	    $this->report = new \Anax\Reports\Report();
	    $this->report->setDI($this->di);
	}




	/**
	* Vote method for report and extend types.
	*
	* The view is avoiding incorrect votes to be made by outputting the correct vote-links. 
	* Still, votes can be violated through link-manipulations to the API.
	* In case of buggers method will take care of it.
	* You can read more about each validation step in the method  
	* 
	* @param $postType 	 string post-type to vote to
	* @param $id 		 int 	post-type ID
	* @param $vote 		 string upvote or downvote (+ or -)
	*
	* @return void
	*/
   	public function voteAction($postType = null, $id = null, $vote = null) {
   		// Verify incoming parameters and if user is logged in
   		//******************************************************

   		// Get logged in user
   		$user = $this->AuthController->getLoggedInUser();
   		isset($postType) && isset($id) && isset($vote) &&
   			($postType == ('report' || 'extend')) && 
   			is_numeric($id) &&
   			($vote == ('+' || '-')) && !empty($user)
   			? null 
   			: die('Stop being such a BUGGER...');

   		$this->initialize();

   		// Check if user is authorised to vote on post-type.
   		// User cant vote on its own post.
   		//******************************************************

   		// Redirect ID will be changed if post-type = extended
   		$redirectId = $id;
   		// Extend's method getPublisher() differs from Reports method. Extend's getPublisher() also
   		// returns the report-ID for the extend-type. The ID is used to redirect user to the correct report.
   		$extend = $this->extend->getPublisher($id);
   		$postType == 'report' && $user['id'] == $this->report->getPublisher($id) ? die('ILLEGAL ACTIONS') : null;
   		$postType == 'extend' ? ($user['id'] == $extend->userid ? die('ILLEGAL ACTIONS') : $redirectId = $extend->reportid) : null;
   		// Get list of users voted on post
		$voted = $this->getByUser($user['id'], $postType, $id);
		// The vote-ID if user already voted
		$votedId = null;
		// User can only vote + or - once
		$voted ? ($voted->vote == $vote ? die('ILLEGAL ACTIONS') : $votedId = $voted->id) : $votedId = null;

		$now = gmdate('Y-m-d H:i:s');
		// Add vote to database or delete if user change their mind
		$delete = $save = false;
	    !empty($voted) 
	    	? ($voted->vote == $vote 
		    	? null
		    	: $delete = $this->vote->delete($voted->id))
	    	: $save = $this->vote->save([
		    	'id'			=> $votedId,
		        'userid' 		=> $user['id'],
		        'posttype' 		=> $postType,
		        'posttypeid' 	=> $id,
		        'vote'			=> $vote,
		        'created'		=> $now
	    	]);

	    // Check if voting succeeded
    	$delete || $save == true 
	    	? null 
	    	: $this->di->sparkles->flash('error', 'Buggers! Something went wrong...');

	    // Redirect to the report
   		$this->redirectTo('reports/id/' . $redirectId);
   	}


	public function get($postType, $id) {
		$this->initialize();

		$sql = "
			SELECT prj_userview.*, prj_votes.vote
			FROM prj_votes
			JOIN prj_userview ON prj_votes.userid = prj_userview.id
			WHERE prj_votes.posttype = ? AND prj_votes.posttypeid = ?
		";
		$votes = $this->vote->executeRaw($sql, [$postType, $id]);

		return $votes;
	}


	public function getByUser($userId, $postType, $id) {
		$this->initialize();

		$sql = "
			SELECT prj_vote.vote, prj_vote.id
			FROM prj_vote
			WHERE prj_vote.userid = ? AND prj_vote.posttype = ? AND prj_vote.posttypeid = ?
		";
		$vote = $this->vote->executeRaw($sql, [$userId, $postType, $id]);

		return $vote ? $vote[0] : false;
	}
}