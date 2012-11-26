<pre>
<?php


$this->mainTemplate = 'empty.tpl.php';

$browsers = $this->lib->browsersVersions->getBrowsers();

$updated = $this->lib->browsersVersions->updateVersions();

echo '<hr>';
print_r($updated);

?>
</pre>