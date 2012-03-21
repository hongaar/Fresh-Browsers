<?php

$this->edit = true;

if (!$this->edit) {
	return false;
}

if (isset($this->variables[0])) {
	$id = (int) $this->variables[0];
	$result = $this->lib->db->prepare('DELETE FROM history WHERE id=:id')
			->bind(':id', $id)
			->execute();
	echo '<br><br><div class="alert alert-success">Deleted</div>';
}

