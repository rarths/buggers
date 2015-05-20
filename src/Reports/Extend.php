<?php
namespace Anax\Reports;
 
class Extend extends \Anax\MVC\CDatabaseModel 
{
    public function getPublisher($id) {
    	$extend = $this->query('reportid, userid')
    		->where('id = ?')
    		->execute([$id]);

    	return $extend ? $extend[0] : false;
    }
}