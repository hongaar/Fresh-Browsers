<?php

$this->mainTemplate = 'empty.tpl';

if ($this->variables[0]=='yes') {
	$result = $this->lib->browsersVersions->approveNewVersion();
	if ($result===false) {
		// ошибка
	}
} else 
if ($this->variables[0]=='no') {
	$result = $this->lib->browsersVersions->deleteNewVersion();
	if ($result===false) {
		// ошибка
	}
} else {
	// неправильная ссылка
}

