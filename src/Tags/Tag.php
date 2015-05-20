<?php
namespace Anax\Tags;
 
/**
 * Model for Tags.
 *
 */
class Tag extends \Anax\MVC\CDatabaseModel
{
	public function add($tag) {
		// Check if tag exist
		$query = $this->query()
			->where('name = ?')
			->execute([$tag]);
		
		if (empty($query)) {
			$this->save(['id' => null, 'name' => $tag]);
			return $this->lastInsertId();
		} else {
			return $query[0]->id;
		}
	}
}