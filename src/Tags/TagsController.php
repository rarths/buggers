<?php

namespace Anax\Tags;

class TagsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable,
        \Anax\MVC\TRedirectHelpers;


    public function initialize() 
    {
	    $this->tag = new \Anax\Tags\Tag();
	    $this->tag->setDI($this->di);
	    $this->tagReport = new \Anax\Tags\TagReport();
	    $this->tagReport->setDI($this->di);
    }


    public function listAction() {
    	$this->initialize();
    	$tags = $this->tag->query()
    		->execute();

	    $this->views->add('tag/list-all', [
	        'title' => "All Tags",
	        'tags' 	=> $tags,
	    ]);
    }


	public function listActiveAction() {
		$this->initialize();

		$sql = "
			SELECT prj_tag.id, prj_tag.name, COUNT(prj_report.id) AS reports
			FROM prj_tagreport
			JOIN prj_tag ON prj_tagreport.tagid = prj_tag.id
			JOIN prj_report ON prj_tagreport.reportid = prj_report.id
			GROUP BY prj_tag.id
		";
		$tags = $this->tag->executeRaw($sql, []);

// 		$tags = $this->tagReport->query('prj_tag.id, prj_tag.name, COUNT(prj_report.id) AS reports')
// 			->join('tag', 'prj_tagreport.tagid = prj_tag.id')
// 			->join('report', 'prj_tagreport.reportid = prj_report.id')
// 			->groupBy('prj_tag.id')
// 			->execute();
// dump($tags);

	    $this->views->add('tag/list-all', [
	        'title' => "Active Report Tags",
	        'tags' 	=> $tags,
	    ], 'sidebar');
	}


	// public function listTagsAction() 
	// {
	// 	$this->initialize();

	//  //    $sql = "
	// 	// 	SELECT prj_tag.name, prj_tag.id, COUNT(prj_reportTags.tagId) AS count
	// 	// 	FROM prj_reporttags
	// 	// 	JOIN prj_tag ON prj_reporttags.tagId = prj_tag.id
	// 	// 	GROUP BY prj_reporttags.tagId
	// 	// 	ORDER BY count DESC
	// 	// ";
	 
	//  //    $tags = $this->tags->executeRaw($sql, []);
	// 	$tags = $this->tagReport->query('prj_tag.name, prj_tag.id, COUNT(prj_reportTags.tagId) AS count')

	//     $this->views->add('report/list-tags', [
	//         'tags' 		=> $tags,
	//         'title' 	=> "Active Tags",
	//     ], 'sidebar');
	// }

	// private function getTagsByReportId($id = null) 
	// {
	// 	$this->initialize();

	// 	$sql = "
	// 		SELECT prj_tag.id, prj_tag.name 
	// 		FROM prj_tag
	// 		JOIN prj_reportTags ON prj_tag.id = prj_reportTags.tagId
	// 		WHERE reportId = ?
	// 	";
	// 	$tags = $this->tags->executeRaw($sql, [$id]);

	// 	return $tags;
	// }
}