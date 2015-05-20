<div class="reports-list">
<?= '<h2>' . $title . '</h2>' ?>
<div class="order-by">
	<a class="button red" href="<?= $this->url->create('reports/list/extends') ?>">Order by Extends</a>
	<a class="button red" href="<?= $this->url->create('reports/list/votes') ?>">Order by Votes</a>
</div>

<?php if(!empty($reports)) :
foreach ($reports as $key => $report) : ?>
	
<div class="report list-item">
	<div class="report-stats">
		<div>
			<strong><?= $report->extends ?></strong>
			<p>extends</p>
		</div>
		<div>
			<?= $report->solvedby ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>'; ?>
			<p>solved</p>
		</div>
		<div>
			<strong><?= $report->votes ?></strong>
			<p>Votes</p>
		</div>
	</div>
	<figure class="user-info">
		<img src="http://www.gravatar.com/avatar/<?=md5(strtolower(trim($report->email)))?>?s=40" />
		<figcaption>
			<a href="<?= $this->url->create('users/id/' . $report->userid) ?>"><?= $report->acronym ?></a>
			<a href="<?= $this->url->create('users/id/' . $report->userid) ?>" class="count-reports"><i class="fa fa-book"></i>&nbsp;<?= $report->reports ?></a>
		</figcaption>
	</figure>
	<p class="created"><?= $report->created ?></p>
	<div class="report-content">
		<h2><a href="<?= $this->url->create('reports/id/' . $report->id) ?>"><?= $report->title ?></a></h2>

		<div class="tags">
		<?php foreach ($report->tags as $key => $tag) : ?>
			<a class="tag" href="<?= $this->url->create('reports/tag/' . $tag->id) ?>"><i class="fa fa-tag"></i>&nbsp;<?= $tag->name ?></a>
		<?php endforeach ?>
		</div>
	</div>
</div>

<?php endforeach;
else : ?>
	<em>No Reports Yet...</em>
<?php endif; ?>
</div>