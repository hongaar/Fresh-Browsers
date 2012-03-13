<?php

/*
 * 
 * http:/www.elfimov.ru/browsers
 *
 * Copyright (c) 2012 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2012-03-13
 *
 *
 */
 
 
class browsersVersions {

	public $links = 'links.php';
	
	public $updateTimeOut = 3600; // как часто проверять версию браузера
	
	public $banTimeOut = 86400; // банить версию на сутки
	
	public $checkTimeOut = 86400; // время до повторной отправки письма с проверкой версии
	
	public $autoApproveTimeOut = 172800; // approve version from check if it longer then this in check table
	
	public $lockTimeOut = 600; // 600
	public $curl = false;
	
	public $userAgent = 'Mozilla/4.0 (compatible; fresh-browsers.com bot)';
	public $db = null;
	
	public $approveLink = '';
	public $approveEmailFrom = '';
	public $approveEmailTo = '';
	
	public $branches = array(
		1	=>	'stable',
		2	=>	'lts',
		3	=>	'preview',
		4	=>	'dev',
	);
	
	public $versions = null;
	public $browsers = null;
	public $wikiLinks = null;
	
	public $errors = array();

	public function __construct($db=null) {
		$this->dir = dirname(__FILE__);
		if (isset($db)) {
			$this->db = $db;
		}
	}
	
	public function getBrowsers() {
		if (!isset($this->browsers)) {
			$result = $this->db->prepare('SELECT * FROM `browsers` LIMIT 100')
								->execute();
			$this->browsers = array();
			while ($browser = $result->fetch()) {
				$this->browsers[$browser['id']] = $browser;
			}
		}
		return $this->browsers;
	}
	
	public function getBranches() {
		return $this->branches;
	}
	
	public function getWikiLinks() {
		if (!isset($this->wikiLinks)) {
			$this->wikiLinks = include($this->dir.'/'.$this->links);
		}
		return $this->wikiLinks;
	}
	
	
	public function getVersions($force = false) {
		if (!isset($this->versions) || $force) {
			$this->versions = array();
			$result = $this->db->prepare('SELECT * FROM `history` GROUP BY branchId, browserId ORDER BY releaseDate DESC')
								->execute();
			while ($browser = $result->fetch()) {
				$this->versions[$browser['browserId']][$browser['branchId']] = array(
					'releaseVersion'	=> $browser['releaseVersion'],
					'releaseDate'		=> $browser['releaseDate'],
					'__modified'		=> $browser['__modified'],
					'__id'				=> $browser['id'],
				);
			}
		}
		return $this->versions;
	}
	

	
	
	public function newVersion($new, $current, $browserId, $branchId) {
					
		$browsers = $this->getBrowsers();
		$branches = $this->getBranches();
		
		if ($this->isBanned($new)) {
			return 'BANNED: '.$browsers[$browserId]['shortName'].' '.$branches[$branchId].' '.$new['releaseVersion'].' ('.date('Y-m-d', $new['releaseDate']).')';
		}
		
		if ($this->isCheck($new)) {
			return 'CHECKING: '.$browsers[$browserId]['shortName'].' '.$branches[$branchId].' '.$new['releaseVersion'].' ('.date('Y-m-d', $new['releaseDate']).')';
		}
		
		$code = md5(uniqid(rand()));
		
		$result = $this->db->prepare('INSERT INTO `check` (browserId, branchId, releaseVersion, releaseDate, code, __modified) VALUES (:browserId, :branchId, :releaseVersion, :releaseDate, :code, :modified)')
			->bind(':browserId', $browserId)
			->bind(':branchId', $branchId)
			->bind(':releaseVersion', $new['releaseVersion'])
			->bind(':releaseDate', $new['releaseDate'])
			->bind(':code', $code)
			->bind(':modified', time())
			->execute();
		if ($result===false) {
			$this->errors[] = 'newVersions error: can\'t insert into check table';
			return false;
		}
		
		if ($this->approveEmailTo!='' && $this->approveEmailFrom!='') {
			$subject = 'Fresh Browsers - '.$browsers[$browserId]['shortName'].' '.$new['releaseVersion'].' ('.$branches[$branchId].')';
			$message = $browsers[$browserId]['name'].' '.$branches[$branchId] . "\n"
						. 'New: '.$new['releaseVersion'] . ' ('.date('Y-m-d', $new['releaseDate']).')' . "\n"
						. 'Old: '.$current['releaseVersion'].' ('.date('Y-m-d', $current['releaseDate']).')' . "\n"
						. 'Approve: '.$this->approveLink.'/yes/'.$code . "\n"
						. 'Delete: '.$this->approveLink.'/no/'.$code . "\n";
			
			$headers = 'From: Fresh Browsers <' . $this->approveEmailFrom . '>' . "\n" 
						. 'Reply-To: ' . $this->approveEmailFrom . "\n";
			$result = mail($this->approveEmailTo, $subject, $message, $headers);
		}
		
		if ($result===false) {
			$this->errors[] = 'newVersions error: can\'t send email';
			return false;
		}
		
		return 'NEW: '.$browsers[$browserId]['shortName'].' '.$branches[$branchId].' '.$new['releaseVersion'].' ('.date('Y-m-d', $new['releaseDate']).')';
		
	}
	
	
	public function deleteFromCheck($version) {
		if (is_array($version)) {
			return $this->db->prepare('DELETE FROM `check` WHERE browserId=:browserId AND branchId=:branchId AND releaseVersion=:releaseVersion AND releaseDate=:releaseDate')
							->bind(':browserId', $version['browserId'])
							->bind(':branchId', $version['branchId'])
							->bind(':releaseVersion', $version['releaseVersion'])
							->bind(':releaseDate', $version['releaseDate'])
							->execute();
		} else {
			return $this->db->prepare('DELETE FROM `check` WHERE id=:id')
							->bind(':id', $version)
							->execute();
		}
	}
	
	
	public function approveNewVersion($code) {
	
		// get version by hash code
		$version = $this->getCheckByCode($code);
		
		if ($version===false) {
			$this->errors[] = 'approveNewVersion error: version is already checked or deleted';
			return false;
		}
		
		// add version to history
		if ($this->addVersion($version)===false) {
			$this->errors[] = 'approveNewVersion error: can\'t add new version to history table';
			return false;
		}
		
		// delete same version from check
		$this->deleteFromCheck($version['id']);
		
		return $version;
	}
	
	
	public function deleteNewVersion($code) {
		$version = $this->getCheckByCode($code);
		
		if ($version===false) {
			$this->errors[] = 'deleteNewVersion error: version is already checked or deleted';
			return false;
		}
		
		// ban this version for some time
		$this->addToBan($version);
		
		// delete same version from check
		$this->deleteFromCheck($version['id']);
		
		return $version;
	}
	
	
	public function addToBan($version) {
		return $result = $this->db->prepare('INSERT INTO `ban` (browserId, branchId, releaseVersion, releaseDate, __modified) VALUES (:browserId, :branchId, :releaseVersion, :releaseDate, :modified)')
									->bind(':browserId', $version['browserId'])
									->bind(':branchId', $version['branchId'])
									->bind(':releaseVersion', $version['releaseVersion'])
									->bind(':releaseDate', $version['releaseDate'])
									->bind(':modified', time())
									->execute();
	}
	
	public function isBanned($version) {
		$count = $this->db->prepare('SELECT * FROM `ban` WHERE browserId=:browserId AND branchId=:branchId AND releaseVersion=:releaseVersion AND releaseDate=:releaseDate AND __modified>:modified')
							->bind(':browserId', $version['browserId'])
							->bind(':branchId', $version['branchId'])
							->bind(':releaseVersion', $version['releaseVersion'])
							->bind(':releaseDate', $version['releaseDate'])
							->bind(':modified', time()-$this->banTimeOut)
							->execute()
							->fetch();
		return $count!==false && !empty($count);
	}
	
	
	public function isCheck($version) {
		$count = $this->db->prepare('SELECT * FROM `check` WHERE browserId=:browserId AND branchId=:branchId AND releaseVersion=:releaseVersion AND releaseDate=:releaseDate AND __modified>:modified')
							->bind(':browserId', $version['browserId'])
							->bind(':branchId', $version['branchId'])
							->bind(':releaseVersion', $version['releaseVersion'])
							->bind(':releaseDate', $version['releaseDate'])
							->bind(':modified', time()-$this->checkTimeOut)
							->execute()
							->fetch();
		return $count!==false && !empty($count);
	}
	
	
	public function autoApproveCheck() {
		$result = $this->db->prepare('SELECT * FROM `check` WHERE __modified<:modified')
							->bind(':modified', time()-$this->autoApproveTimeOut)
							->execute();
		while ($version = $result->fetch()) {
			if ($this->addVersion($version)) {
				$this->deleteFromCheck($version['id']);
			}
		}
	}
	
	
	public function addVersion($version) {
	
		return $this->db->prepare('INSERT INTO `history` (browserId, branchId, releaseVersion, releaseDate, __modified) VALUES (:browserId, :branchId, :releaseVersion, :releaseDate, :modified)')
			->bind(':browserId', $version['browserId'])
			->bind(':branchId', $version['branchId'])
			->bind(':releaseVersion', $version['releaseVersion'])
			->bind(':releaseDate', $version['releaseDate'])
			->bind(':modified', time())
			->execute();
	
	}


	public function getCheckByCode($code) {
		return $this->db->prepare('SELECT * FROM `check` WHERE code=:code')
					->bind(':code', $code)
					->execute()
					->fetch();
	}


	public function updateVersions() {
	
		if ($this->isLock()) {
			return false;
		}
		
		$updateBrowsers = array();
		$updated = array();
		
		$this->setLock();
		
		$this->autoApproveCheck();
		
		$versions = $this->getVersions();

		foreach ($versions as $browserId=>$branch) {
			foreach ($branch as $branchId=>$values) {
				if ((time()-$values['__modified']) >= $this->updateTimeOut) {
					$updateBrowsers[] = array('browserId'=>$browserId, 'branchId'=>$branchId);
				}
			}
		}
		

		foreach ($updateBrowsers as $browser) {
			$new = $this->getVersionFromWikiText($browser['browserId'], $browser['branchId']);

			if ($new!==false) {
				$current = $versions[$browser['browserId']][$browser['branchId']];
				if (isset($new['releaseDate'])  && isset($new['releaseVersion'])
					&& trim($new['releaseVersion']) != ''
					&& $new['releaseDate'] > $current['releaseDate']
					) {
					$info = $this->newVersion($new, $current, $browser['browserId'], $browser['branchId']);
					if ($info!==false) {
						$updated[] = $info;
					}
				}
			}
		}
		
		// forced versions update
		$this->getVersions(true);
		
		$this->removeLock();
		
		return $updated;
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
	
	
	public function getVersionFromWikiText($browserId, $branchId) {
	
		$browsers = $this->getBrowsers();
		$branches = $this->getBranches();
		$wikiLinks = $this->getWikiLinks();
	
		$browserName = strtolower($browsers[$browserId]['shortName']);
		$browserBranch = $branches[$branchId];
	
		$versions = false;
		$text = $this->getWikiText($browserName, $browserBranch);
		if ($text!==false) {
			$regexp = $wikiLinks[$browserName]['regexp'];
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
					$versions = array('browserId'=>$browserId, 'branchId'=>$branchId, 'releaseVersion'=>trim($version), 'releaseDate'=>$dateTimeStamp);
					break;
				}
			}
		}		
		return $versions;
		
	}
	
	
	public function getWikiText($browserName, $branchName) {
		if ($this->curl) {
			$wikiLinks = $this->getWikiLinks();
			$ch = curl_init();
			$options = array(
				CURLOPT_URL				=>	$wikiLinks[$browserName][$branchName],
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
			$filename = $this->dir.'/'.$browserName.'_'.$branchName.'.txt';
			if (file_exists($filename)) {
				$text = file_get_contents($filename);
			} else {
				$text = false;
			}
		}
		return $text;
	}
	
	
	public function createSh() {
		$branches = $this->getBranches();
		$wikiLinks = $this->getWikiLinks();
		$out = '#!/bin/sh'."\n";
		foreach ($wikiLinks as $browserName=>$branch) {
			foreach ($branch as $branchName=>$link) {
				if (in_array($branchName, $branches)) {
					$out .= 'curl "'.$link.'" > '.$this->dir.'/'.$browserName.'_'.$branchName.".txt\n";
				}
			}
		}
		return file_put_contents($this->dir.'/curl_links_files.sh', $out);		
	}
	
	 
}