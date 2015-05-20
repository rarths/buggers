<div <?= isset($class) ? "class='{$class}'" : null  ?>>
	<?= isset($title) ? '<h2>' . $title . '</h2>' : null ?>
	<?= isset($loggedIn) ? $content : "<a class='message' href=" . $this->url->create('login') . ">{$message}</a>" ?>
</div>