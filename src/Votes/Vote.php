<?php
namespace Anax\Votes;
 
/**
 * Model for Votes.
 *
 */
class Vote extends \Anax\MVC\CDatabaseModel
{
   	public function getUserVote($postType, $id, $userId) {
   		$sql = "
			SELECT prj_vote.vote
			FROM prj_vote
			JOIN prj_userview ON prj_vote.userid = prj_userview.id
			WHERE prj_vote.posttype = ? AND prj_vote.posttypeid = ? AND prj_userview.id = ?
   		";
		$vote = $this->executeRaw($sql, [$postType, $id, $userId]);

		return $vote;
	}
}