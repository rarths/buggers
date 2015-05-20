<h3><?= isset($title) ? $title : null ?></h3>
<figure class="user-single">
	<img src="http://www.gravatar.com/avatar/<?=md5(strtolower(trim($user->email)))?>?s=40" />
	<figcaption>
		<a href="<?= $this->url->create('users/id/' . $user->id) ?>"><?= $user->acronym ?></a>
		<p><?= $user->name ?></p>
		<p><?= $user->email ?></p>
		<p class="count-reports"><i class="fa fa-book"></i>&nbsp;<?= $user->reports ?></p>
	</figcaption>
</figure>