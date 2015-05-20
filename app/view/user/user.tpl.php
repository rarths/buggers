<div>
<figure class="user-single">
	<?= isset($title) ? '<h3>' . $title . '</h3>' : null ?>
	<img src="http://www.gravatar.com/avatar/<?=md5(strtolower(trim($user->email)))?>?s=40" />
	<figcaption>
		<a href="<?= $this->url->create('users/id/' . $user->id) ?>"><?= $user->acronym ?></a>
		<p><?= $user->name ?></p>
		<p><?= $user->email ?></p>
		<a href="<?= $this->url->create('users/id/' . $user->id) ?>" class="count-reports"><i class="fa fa-book"></i>&nbsp;<?= $user->reports ?></a>
	</figcaption>
		<?= !empty($loggedIn) && $user->id == $loggedIn['id'] 
			? '<a class="button red" href="' . $this->url->create('users/edit/' . $loggedIn['id']) . '">Edit Profile</a>' 
			: null ?>
</figure>
</div>