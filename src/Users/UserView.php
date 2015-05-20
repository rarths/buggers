<?php
namespace Anax\Users;
 
/**
 * Model for Users based on database view.
 *
 */
class UserView extends \Anax\MVC\CDatabaseModel
{
	public function id($id) {
		$sql = "
			SELECT prj_userview.*, COUNT(prj_report.id) AS reports
			FROM prj_userview
			JOIN prj_report ON prj_userview.id = prj_report.userid
			WHERE prj_userview.id = ?
		";
		$user = $this->executeRaw($sql, [$id]);

		return $user[0];
	}
}