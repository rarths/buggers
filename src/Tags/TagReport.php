<?php
namespace Anax\Tags;
 
class TagReport extends \Anax\MVC\CDatabaseModel
{
	public function listForReport($id) {
		// $sql = "
		// 	SELECT prj_tag.id, prj_tag.name
		// 	FROM prj_tagreport
		// 	JOIN prj_tag ON prj_tagreport.tagId = prj_tag.id
		// 	WHERE prj_tagreport.reportid = ?
		// ";
		// $tags = $this->executeRaw($sql, [$id]);

		$tags = $this->query('prj_tag.id, name')
			->join('tag', 'prj_tagreport.tagid = prj_tag.id')
			->where('reportid = ?')
			->execute([$id]);

		return $tags;
	}
}