<div class="extend-single item">
	<div class="post-stats">
		<p class="points"><?= $extend->votes ?></p>
		<span>

			<!-- Logic for showing vote-links. Link parameters will be validated by vote-method -->
			<?= empty($loggedIn) || $loggedIn['id'] == $extend->userid || $extend->uservoted == '-' 
				? '<li class="dim fa fa-minus fa-3"></li>' 
				: '<a href="' . $this->url->create('votes/vote/extend/' . $extend->id . '/-') . '"><li class="fa fa-minus fa-3"></li></a>' ?>
			
			<?= empty($loggedIn) || $loggedIn['id'] == $extend->userid || $extend->uservoted == '+' 
				? '<li class="dim fa fa-plus fa-3"></li>' 
				: '<a href="' . $this->url->create('votes/vote/extend/' . $extend->id . '/+') . '"><li class="fa fa-plus fa-3"></li></a>' ?>
		
		</span>

		<!-- Logic for showing solve-report-link. Link parameters will be validated by vote-method -->
		<?= $extend->solvesreport == true ? '<i class="fa fa-check fa-3"></i></li>' :
			(!empty($loggedIn) && $loggedIn['id'] != $extend->userid ? 
			'<a href="' . $this->url->create('reports/solve/' . $extend->reportid . '/' . $extend->id) . 
			'"><i class="fa fa-check fa-3"></i></li></a>' : null) ?>

	</div>
	<h2><?= $extend->title ?></h2>
	<figure class="user-info">
		<img src="http://www.gravatar.com/avatar/<?=md5(strtolower(trim($extend->email)))?>?s=40" />
		<figcaption>
			<a href="<?= $this->url->create('users/id/' . $extend->userid) ?>"><?= $extend->acronym ?></a>
			<a href="<?= $this->url->create('users/id/' . $extend->userid) ?>" class="count-reports"><i class="fa fa-book"></i>&nbsp;<?= $extend->reports ?></a>
		</figcaption>
	</figure>
	<p><?= $extend->content ?></p>
	<p class="created"><?= $extend->created ?></p>
</div>
