<footer><span class='sitefooter'>Copyright (c) Robin Hansson | <a href='https://github.com/mosbth/Anax-MVC'>Buggers på GitHub</a> | <a href='http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance'>Unicorn</a></span>
</footer>

<?php
// Outputs messages stored with sparkles
$messages = $this->sparkles->output();
if (!empty($messages)) {
    echo '<div class="top-flash">';
    foreach ($messages as $key => $message) {
        echo $message;
    }
    echo '</div>';
}
?>