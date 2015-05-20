<?php
namespace Anax\Reports;
 
class Report extends \Anax\MVC\CDatabaseModel
{
	// public function getSolution($id) {
	// 	$this->initialize();

	// 	$sql = "
	// 		SELECT extendId
	// 		FROM prj_reportSolution
	// 		WHERE prj_reportSolution.reportId = ?
	// 	";
	// 	$extendId = $this->executeRaw($sql, [$id]);

	// 	return $extendId ? $extendId[0]->extendId : null;
	// }

    public function getPublisher($id) {
    	$userId = $this->query('userid')
    		->where('id = ?')
    		->execute([$id]);

    	return $userId ? $userId[0]->userid : false;
    }
}