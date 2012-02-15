<?php

/*
 * 
 * http:/www.elfimov.ru/browsers
 *
 * Copyright (c) 2011 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2012-02-07
 *
 * TODO:
 * предусмотреть возможность сохранения нескольких стабильных и нестабильных версий (м.б. какие-то теги)
 *
 */
 
 
class browsersVersions {

	public $links = 'links.php';
	public $versionsFile = 'versions.json';
	public $updatePeriod = 0; // 86400;
	
	public $lockTimeOut = 600;
	public $curl = false;
	
	public $toBeUpdated = array();
	public $userAgent = 'Mozilla/4.0 (compatible; Fresh Browsers bot)';
	public $excludeLinks = array('regexp');
	public $versions = null;
	
	public $error = false;

	public function __construct() {
		$this->dir = dirname(__FILE__);
		$this->versionsFile = $this->dir.'/'.$this->versionsFile;
		$this->wikiLinks = include($this->dir.'/'.$this->links);
	}
	
	
	// получить список браузеров из конфига
	// если установлен $this->versionsFile, то добавляем версию и дату последнего обновления
	public function getVersions() {
	
		if (file_exists($this->versionsFile)) {
			$browsersValues = json_decode(file_get_contents($this->versionsFile), true);
		} else {
			$browsersValues = array();
		}
		
		$browsersOut = array();
		foreach ($this->wikiLinks as $browser=>$branch) {
			if (!isset($browsersOut[$browser])) {
				$browsersOut[$browser] = array();
			}
			foreach ($branch as $branchName=>$link) {
				if (!in_array($branchName, $this->excludeLinks)) {
					if (isset($browsersValues[$browser][$branchName])) {
						$releaseVersion = $browsersValues[$browser][$branchName]['releaseVersion'];
						$lastUpdate = $browsersValues[$browser][$branchName]['lastUpdate'];
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
		}
		return $browsersOut;
	}

	
	
	public function updateVersions() {
	
		if ($this->isLock()) {
			return false;
		}
		
		$this->setLock();
		
		if (!isset($this->versions)) {
			$this->versions = $this->getVersions();
		}
	
		foreach ($this->versions as $browserName=>$branch) {
			foreach ($branch as $branchName=>$values) {
				if ((time()-$values['lastUpdate']) >= $this->updatePeriod) {
					$this->toBeUpdated[] = array('name'=>$browserName, 'branch'=>$branchName);
				}
			}
		}

		foreach ($this->toBeUpdated as $browser) {
			$release = $this->getVersionFromWikiText($browser['name'], $browser['branch']);
			
			if ($release!==false) {
				$this->versions[$browser['name']][$browser['branch']] = array(
														'releaseVersion'=>	$release[0]['version'],
														'releaseDate'	=>	$release[0]['date'],
														'lastUpdate'	=>	time(),
														);
														
				file_put_contents($this->versionsFile, json_encode($this->versions));
			}
		}
		
		$this->removeLock();
		
	}
	
	
	private function isLock() {
		if (file_exists($this->dir.'/lock')) {
			$lock = (int) file_get_contents($this->dir.'/lock');
			if ((time()-$lock) <= $this->lockTimeOut) { 
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
	
	
	public function getVersionFromWikiText($browserName, $browserBranch) {
	
		$versions = false;
		$text = $this->getWikiText($browserName, $browserBranch);
		if ($text!==false) {
			$regexp = $this->wikiLinks[$browserName]['regexp'];
			preg_match_all($regexp['version'], $text, $ver);
			preg_match_all($regexp['date'], $text, $date);
			if (isset($ver[1]) && !empty($ver[1])) {
				$versions = array();
				$dates = array();
				foreach ($ver[1] as $n=>$version) {
					if (isset($date[2][$n]) && isset($date[3][$n]) && isset($date[4][$n])) {
						$dateTimeStamp = mktime(0, 0, 0, $date[3][$n], $date[4][$n], $date[2][$n]);
						if ($dateTimeStamp===false || $dateTimeStamp<0) {
							$dateTimeStamp = 0;
						}
					} else {
						$dateTimeStamp = 0;
					}
					$versions[] = array('version'=>trim($version), 'date'=>$dateTimeStamp);
				}
			}
			return $versions;
		}		
		return $versions;
		
	}
	
	
	public function getWikiText($browserName, $browserBranch) {
		if ($this->curl) {
			$ch = curl_init();
			$options = array(
				CURLOPT_URL				=>	$this->wikiLinks[$browserName][$browserBranch],
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
		} else {
			$filename = $this->dir.'/'.$browserName.'_'.$browserBranch.'.txt';
			if (file_exists($filename)) {
				$text = file_get_contents($filename);
			} else {
				$text = false;
			}
		}
		return $text;
	}
	
	
	public function createSh() {
		$out = '#!/bin/sh'."\n";
		foreach ($this->wikiLinks as $browser=>$branch) {
			if (!isset($browsersOut[$browser])) {
				$browsersOut[$browser] = array();
			}
			foreach ($branch as $branchName=>$link) {
				if (!in_array($branchName, $this->excludeLinks)) {
					$out .= 'curl "'.$link.'" > '.$this->dir.'/'.$browser.'_'.$branchName.".txt\n";
				}
			}
		}
		$this->error = !file_put_contents($this->dir.'/curl_links_files.sh', $out);		
	}

	 
}