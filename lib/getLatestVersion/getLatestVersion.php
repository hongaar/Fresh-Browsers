<?php

/*
 * 
 * http:/www.elfimov.ru/browsers
 *
 * Copyright (c) 2011 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2012-02-06
 *
 * TODO:
 * предусмотреть возможность сохранения нескольких стабильных и нестабильных версий (м.б. какие-то теги)
 *
 */
 
 
class PDOWrapper {

	public $links = 'links.php';
	public $versionsFile = 'versions.json';
	public $updatePeriod = 86400;
	public $toBeUpdated = array();
	public $userAgent = 'Mozilla/4.0 (compatible; Fresh Browsers bot)';

	public function __construct() {
		
		$this->dir = __DIR__;
		
		if ($this->isLock()) {
			return false;
		}
		
		$this->setLock()
		
		$this->versionsFile = $this->dir.'/'.$this->versionsFile;
		
		$this->wikiLinks = include($this->dir.'/'.$this->links);
		
		 = $this->setVersions();
	
		$this->updateVersions();
		
		$this->removeLock();
		
	}
	
	
	private function isLock() {
		if (file_exists($this->dir.'/lock')) {
			$lock = (int) file_get_contents($this->dir.'/lock');
			if ((time()-$lock)<600) { // 10 минут
				return true;
			}
		}
		return false;
	}
	
	
	private function setLock() {
		file_put_contents($this->dir.'/lock', time());
	}
	
	
	private function removeLock() {
		unlink($this->dir.'/lock');
	}
	
	
	// получить список браузеров из конфига
	// если на вход передан массив, то добавляем версию и дату последнего обновления
	public function getVersions($browsersValues = array()) {
		$browsersOut = array();
		foreach ($this->wikiLinks as $browser=>$branch) {
			if (!isset($browsersOut[$browser])) {
				$browsersOut[$browser] = array();
			}
			foreach ($branch as $branchName=>$link) {
				if (isset($browsersValues[$browser][$branchName])) {
					$releaseVersion = $browsersValues[$browser][$branchName]['releaseVersion'];
					$lastUpdate = $browsersValues[$browser][$branchName]['releaseDate'];
					$releaseDate = $browsersValues[$browser][$branchName]['releaseDate'];
				} else {
					$releaseVersion = 0;
					$lastUpdate	= 0;
					$releaseDate = 0;
				}
				$browsersOut[$browser][$branchName] = array(
													'releaseVersion'=>	$releaseVersion,
													'releaseDate'	=>	$releaseDate,
													'lastUpdate'	=>	$lastUpdate,
													);
			}
		}
		return $browsersOut;
	}
	
	
	public function setVersions() {
		$versions = NULL;
		if (file_exists($this->versionsFile)) {
			$versions = json_decode(file_get_contents($this->versionsFile));
		}
		$this->versions = $this->getVersions($versions);
	}
	
	
	
	public function updateVersions() {
		foreach ($this->versions as $browser=>$branch) {
			foreach ($branch as $branchName=>$values) {
				if ((time()-$lastUpdate)<=$this->updatePeriod) {
					$this->toBeUpdated[] = array('name'=>$browser, 'branch'=>$branchName);
				}
			}
		}
		
		foreach ($this->toBeUpdated as $browser) {
			$release = $this->getVersionFromWiki($this->wikiLinks[$browser['name'][$browser['branch']]);
			
			$this->versions[$browser['name']][$browser['branch']] = array(
													'releaseVersion'=>	$release[0]['version'],
													'releaseDate'	=>	$release[0]['date'],
													'lastUpdate'	=>	time(),
													);
													
			file_put_contents($this->versionsFile, json_encode($this->versions));
		}
		
	}
	
	public function getVersionFromWiki($link) {
		$file_get_contents
		$ch = curl_init();
		$options = array(
			CURLOPT_URL				=>	$link,
			CURLOPT_RETURNTRANSFER	=>	true,
			CURLOPT_FOLLOWLOCATION	=>	true,
			CURLOPT_MAXREDIRS		=>	10,
			CURLOPT_HEADER			=>	false, 
			CURLOPT_TIMEOUT			=>	4, 
			CURLOPT_USERAGENT		=>	$this->userAgent
		);
		curl_setopt_array($ch, $options);
		$text = curl_exec($ch);
		curl_close($ch);
		
		preg_match_all('/{{(.*)}}/sU', $text, $matches);
		if (isset($matches[1]) && !empty($matches[1])) {
			$versions = array();
			foreach ($matches[1] as $match) {
				preg_match_all('/^|latest_release_version = (.*)/i', $match, $ver);
				if (!isset($ver[1][1])) return false;
				$versions[] = $ver[1][1];
			}
			return $versions;
		}
		return false;
		
	}

	 
}