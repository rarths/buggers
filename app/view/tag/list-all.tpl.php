<div class="tags-list">
<?= isset($title) ? '<h3>' . $title . '</h3>' : null ?>

<?php  if (!empty($tags)) :
foreach ($tags as $key => $tag) : ?>
	<a class="tag" href="<?= $this->url->create('reports/tag/' . $tag->id) ?>"><i class="fa fa-tag"></i>&nbsp;<?= $tag->name ?></a>
<?php endforeach;
else: echo "<em>No Tags Here...</em>"; 
endif; ?>
</div>
