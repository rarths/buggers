<div class="report-single item">
	<div class="post-stats">
		<p class="points"><?= $report->votes ?></p>
		<span>

			<!-- Logic for showing vote-links. Link parameters will be validated by vote-method -->
			<?= empty($loggedIn) || $loggedIn['id'] == $report->userid || $report->uservoted == '-' 
				? '<li class="dim fa fa-minus fa-3"></li>' 
				: '<a href="' . $this->url->create('votes/vote/report/' . $report->id . '/-') . '"><li class="fa fa-minus fa-3"></li></a>' ?>
			
			<?= empty($loggedIn) || $loggedIn['id'] == $report->userid || $report->uservoted == '+' 
				? '<li class="dim fa fa-plus fa-3"></li>' 
				: '<a href="' . $this->url->create('votes/vote/report/' . $report->id . '/+') . '"><li class="fa fa-plus fa-3"></li></a>' ?>
		
		</span>
	</div>
	<h1><?= $report->title ?></h1>
	<p><?= $report->content ?></p>
	<p class="created"><?= $report->created ?></p>
</div>