<div class="comment-add">
	<h2><?= $title ?></h2>
	<?= isset($loggedIn) ? $content : "<a class='message' href=" . $this->url->create('login') . ">Login to extend report</a>" ?>
</div>