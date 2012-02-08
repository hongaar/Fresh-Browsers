<?php

/*
 * forms PHP library 
 * http://www.elfimov.ru/php/nanobanano
 *
 * Copyright (c) 2012 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2012-02-08
 */
 
class forms {

	public $prefix = '';
	
	public function __construct($prefix = '') {
		$this->prefix = '';
	}
	
	public function getSelect($name, $values, $active) {
		if (!is_array($active)) {
			$active = array($active);
		}
		$out = '';
		$out .= '<select id="'.$this->prefix.$name.'" name="'.$this->prefix.$name.'">';
		foreach ($values as $id => $text) {
			$out .= '<option value="'.$id.'"'.(in_array($id, $active)?' selected="selected"':'').'>'.$text.'</option>';
		}
		$out .= '</select>';
		return $out;
	}
	
}