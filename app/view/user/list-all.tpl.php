<div class="users-list">
<h3><?= $title ?></h3>

<?php if (!empty($users)) :
foreach ($users as $key => $user) : ?>
	<figure class="user-info">
	<img src="http://www.gravatar.com/avatar/<?=md5(strtolower(trim($user->email)))?>?s=40" />
	<figcaption>
		<a href="<?= $this->url->create('users/id/' . $user->id) ?>"><?= $user->acronym ?></a>
		<a href="<?= $this->url->create('users/id/' . $user->userid) ?>" class="count-reports"><i class="fa fa-book"></i>&nbsp;<?= $user->reports ?></a>
	</figcaption>
	</figure>
<?php endforeach; 
else : ?>
<em>No Users Yet...</em>
<?php endif; ?>
</div>