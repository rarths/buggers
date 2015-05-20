<div class="comment-list form">
<?php if(empty($comments)) : ?>
<em>No comments yet...</em>
<?php else : ?>
<?php foreach ($comments as $key => $comment) : ?>
<figure>
	<img src="http://www.gravatar.com/avatar/<?=md5(strtolower(trim($comment->email)))?>?s=40" />
	<figcaption>
		<a href="<?= $this->url->create('user/id/' . $comment->id) ?>"><?= $comment->acronym ?></a>
		<p class="created"><?= $comment->created ?></p>
		<p class="content"><?= $comment->content ?></p>
	</figcaption>
</figure>
<?php endforeach; ?>
<?php endif; ?>
</div>