<?php

/*
 * 
 * http://fresh-browsers.com
 *
 * Copyright (c) 2012 by Dmitry Elfimov
 * Released under the MIT License.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: 2012-03-27
 *
 *
 */
 
 
class BrowsersVersions 
{

    public $linksFile = 'links.php';
    
    public $updateTimeOut = 3600; // 
    
    public $banTimeOut = 86400; // ban version for 24h if version was disapproved
    
    public $checkTimeOut = 86400; // send email with approve links once in 24h
    
    public $autoApproveTimeOut = 86400; // approve version from check if it longer then this in check table
    
    public $lockTimeOut = 600; // 600
    public $curl = false;
    public $createShTool = 'curl'; // other option is wget
    
    public $userAgent = 'Mozilla/4.0 (compatible; fresh-browsers.com bot)';
    public $db = null;
    
    public $doNotApprove = true;            // set this to true if you do not want to approve new versions by email
    public $approveLink = null;
    public $approveEmailFrom = '';
    public $approveEmailTo = '';
    
    public $dateFormat = 'Y-m-d';
    public $timeFormat = 'H:i:s';
    
    public $maxVersionsLimit = 10000;

    
    public $versions = null;
    public $wikiLinks = null;
    
    public $errors = array();

	private $_dir = null;
	
    private $_config = null;

    
    public function __construct(PDOWrapper $db)
    {
        $this->_dir = dirname(__FILE__);
        $this->db = $db;
        $this->_config = include $this->_dir.'/config.php';
    }
    
    
    public function getOSes() 
    {
        return $this->_config['os'];
    }
    
    
    public function getBrowsers() 
    {
        return $this->_config['browsers'];
    }
    
    
    public function getBranches() 
    {
        return $this->_config['branches'];
    }
    
    
    public function getWikiLinks() 
    {
        if (!isset($this->wikiLinks)) {
            $this->wikiLinks = include $this->_dir.'/'.$this->linksFile;
        }
        return $this->wikiLinks;
    }
    
    
    
    public function getVersions($conditions = null) 
	{
        $versions = array();
        $result = $this->db->prepare('SELECT * FROM `history`'
                .(empty($conditions) ? '' : ' WHERE '.$conditions)
                .' ORDER BY osId, branchId, browserId, `date` DESC, __modified DESC'
                .' LIMIT '.$this->maxVersionsLimit)
            ->execute();
        while ($browser = $result->fetch()) {
            $versions[$browser['browserId']][$browser['branchId']][$browser['osId']] = array(
                'version'    => $browser['version'],
                'date'       => $browser['date'],
                '__modified' => $browser['__modified'],
                '__id'       => $browser['id'],
            );
        }
        return $versions;
    }
    
    public function getLatestVersions($force = false) 
	{
        if (!isset($this->versions) || $force) {
            $this->versions = array();
            $result = $this->db->prepare('SELECT * FROM `history` GROUP BY osId, branchId, browserId ORDER BY `date` DESC, __modified DESC')
                               ->execute();
            while ($browser = $result->fetch()) {
                $this->versions[$browser['browserId']][$browser['branchId']][$browser['osId']] = array(
                    'version'    => $browser['version'],
                    'date'       => $browser['date'],
                    '__modified' => $browser['__modified'],
                    '__id'       => $browser['id'],
                );
            }
        }
        return $this->versions;
    }
    

    
    public function getExport() 
	{
    
        $versions = $this->getLatestVersions();
        $browsers = $this->getBrowsers();
        $branches = $this->getBranches();
        $oses = $this->getOSes();

        $export = array();

        foreach ($browsers as $browserId => $browser) {              // all browsers
            $browserName = strtolower($browser['shortName']);
            foreach ($branches as $branchId => $branchName) {        // all branches
                $branchName = ucfirst($branchName);
                foreach ($oses as $osId => $osArr) {                 // all oses
                    if (isset($versions[$browserId][$branchId][$osId])) {   // check if we have version for this browser-branch
                        if (!isset($export[$browserName])) {                // create export array if this browser is not in it yet 
                            $export[$browserName] = array(    
                                'name'       => $browser['name'],
                                'link'       => $browser['link'],
                            );
                        }
                        $export[$browserName][$branchName][$osArr[0]] = array(
                            'version' => $versions[$browserId][$branchId][$osId]['version'],
                            'date'    => date($this->dateFormat, $versions[$browserId][$branchId][$osId]['date']),
                        );
                    }
                }
            }
        }
        return $export;
        
    }
    
    
    
    public function newVersion($new, $current, $browserId, $branchId, $osId) 
    {
        $browsers = $this->getBrowsers();
        $branches = $this->getBranches();
        $oses = $this->getOSes();
        
        if ($this->doNotApprove) {
            $this->addVersion(array(
                'browserId' => $browserId,
                'branchId'  => $branchId,
                'osId'      => $osId,
                'version'   => $new['version'],
                'date'      => $new['date']
            ));
            return 'ADDED: '.$browsers[$browserId]['shortName'].' '.$branches[$branchId].' '.$oses[$osId][1].' '.$new['version'].' ('.date('Y-m-d', $new['date']).')';
        }
        
        if ($this->isBanned($new)) {
            return 'BANNED: '.$browsers[$browserId]['shortName'].' '.$branches[$branchId].' '.$oses[$osId][1].' '.$new['version'].' ('.date('Y-m-d', $new['date']).')';
        }
        
        if ($this->isCheck($new)) {
            return 'CHECKING: '.$browsers[$browserId]['shortName'].' '.$branches[$branchId].' '.$oses[$osId][1].' '.$new['version'].' ('.date('Y-m-d', $new['date']).')';
        }
        
        $code = md5(uniqid(rand()));
        
        $result = $this->db->prepare('INSERT INTO `check` (browserId, branchId, osId, version, `date`, code, __modified) VALUES (:browserId, :branchId, :osId, :version, :date, :code, :modified)')
            ->bind(':browserId', $browserId)
            ->bind(':branchId', $branchId)
            ->bind(':osId', $osId)
            ->bind(':version', $new['version'])
            ->bind(':date', $new['date'])
            ->bind(':code', $code)
            ->bind(':modified', time())
            ->execute();
        if ($result === false) {
            $this->errors[] = 'newVersions error: can\'t insert into check table';
            return false;
        }
        
        if ($this->approveEmailTo != '' && $this->approveEmailFrom != '') {
            $result = $this->approveMail($current, $new, $code);
        }
        
        if ($result === false) {
            $this->errors[] = 'newVersions error: can\'t send email';
            return false;
        }
        
        return 'NEW: '.$browsers[$browserId]['shortName'].' '.$branches[$branchId].' '.$oses[$osId][1].' '.$new['version'].' ('.date('Y-m-d', $new['date']).')';
    }
    
    
    private function approveMail($current, $new, $code) 
    {
        if (!isset($this->approveLink)) {
            $this->approveLink = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        }
        $browsers = $this->getBrowsers();
        $branches = $this->getBranches();
        $oses = $this->getOSes();
        $browserId = $new['browserId'];
        $branchId = $new['branchId'];
        $osId = $new['osId'];
        $subject = 'Fresh Browsers - '.$browsers[$browserId]['shortName'].' '.$new['version'].' ('.$branches[$branchId].' '.$oses[$osId].')';
        $message = $browsers[$browserId]['name'].' '.$branches[$branchId] . "\n"
                    . 'New: '.$new['version'] . ' ('.date('Y-m-d', $new['date']).')' . "\n"
                    .(!empty($current) ? 'Old: '.$current['version'].' ('.date('Y-m-d', $current['date']).')' . "\n" : '')
                    . "\n"
                    . 'Approve: '.$this->approveLink.'/yes/'.$code . "\n"
                    . "\n"
                    . 'Delete: '.$this->approveLink.'/no/'.$code . "\n"
                    . "\n\n";
        
        $headers = 'From: Fresh Browsers <' . $this->approveEmailFrom . '>' . "\n" 
                    . 'Reply-To: ' . $this->approveEmailFrom . "\n";
        return mail($this->approveEmailTo, $subject, $message, $headers);
    }
    
    
    public function deleteFromCheck($version) 
    {
        if (is_array($version)) {
            return $this->db->prepare('DELETE FROM `check` WHERE browserId=:browserId AND osId=:osId AND branchId=:branchId AND version=:version AND date=:date')
                            ->bind(':browserId', $version['browserId'])
                            ->bind(':branchId', $version['branchId'])
                            ->bind(':osId', $version['osId'])
                            ->bind(':version', $version['version'])
                            ->bind(':date', $version['date'])
                            ->execute();
        } else {
            return $this->db->prepare('DELETE FROM `check` WHERE id=?')->execute($version);
        }
    }
    
    
    public function approveNewVersion($code) 
    {
        // get version by hash code
        $version = $this->getCheckByCode($code);
        
        if ($version === false) {
            $this->errors[] = 'approveNewVersion error: version is already checked or deleted';
            return false;
        }
        
        // add version to history
        if ($this->addVersion($version) === false) {
            $this->errors[] = 'approveNewVersion error: can\'t add new version to history table';
            return false;
        }
        
        // delete same version from check
        $this->deleteFromCheck($version['id']);
        
        return $version;
    }
    
    
    public function deleteNewVersion($code) 
    {
        $version = $this->getCheckByCode($code);
        
        if ($version === false) {
            $this->errors[] = 'deleteNewVersion error: version is already checked or deleted';
            return false;
        }
        
        // ban this version for some time
        $this->addToBan($version);
        
        // delete same version from check
        $this->deleteFromCheck($version['id']);
        
        return $version;
    }
    
    
    public function addToBan($version) 
    {
        return $result = $this->db->prepare('INSERT INTO `ban` (browserId, branchId, osId, version, `date`, __modified) VALUES (:browserId, :branchId, :osId, :version, :date, :modified)')
                                  ->bind(':browserId', $version['browserId'])
                                  ->bind(':branchId', $version['branchId'])
                                  ->bind(':osId', $version['osId'])
                                  ->bind(':version', $version['version'])
                                  ->bind(':date', $version['date'])
                                  ->bind(':modified', time())
                                  ->execute();
    }
    
    public function isBanned($version) 
    {
        $count = $this->db->prepare('SELECT * FROM `ban` WHERE browserId=:browserId AND branchId=:branchId AND osId=:osId AND version=:version AND `date`=:date AND __modified > :modified')
                            ->bind(':browserId', $version['browserId'])
                            ->bind(':branchId', $version['branchId'])
                            ->bind(':osId', $version['osId'])
                            ->bind(':version', $version['version'])
                            ->bind(':date', $version['date'])
                            ->bind(':modified', time()-$this->banTimeOut)
                            ->execute()
                            ->fetch();
        return $count !== false && !empty($count);
    }
    
    
    public function isCheck($version) 
    {
        $count = $this->db->prepare('SELECT * FROM `check` WHERE browserId=:browserId AND branchId=:branchId AND osId=:osId AND version=:version AND `date`=:date AND __modified > :modified')
                            ->bind(':browserId', $version['browserId'])
                            ->bind(':branchId', $version['branchId'])
                            ->bind(':osId', $version['osId'])
                            ->bind(':version', $version['version'])
                            ->bind(':date', $version['date'])
                            ->bind(':modified', time()-$this->checkTimeOut)
                            ->execute()
                            ->fetch();
        return $count!==false && !empty($count);
    }
    
    
    public function isNew($current, $new) 
    {
        $newArr = explode('.', $new['version']);
        $curArr = explode('.', $current['version']);
        
        $majorNew = intval($newArr[0]);
        $majorCur = intval($curArr[0]);
        
        // new major version > current version AND < current+2
        // OR major versions are equal but minor are not equal
        return ($majorNew>$majorCur && $majorNew<(2+$majorCur))
                || ($majorNew==$majorCur && $new['version']!=$current['version']);
    }
    
    
    public function autoApproveCheck() 
    {
        $browsers = $this->getBrowsers();
        $branches = $this->getBranches();
        $oses = $this->getOSes();
        
        $result = $this->db->prepare('SELECT * FROM `check` WHERE __modified < ?')
                           ->execute(array(time()-$this->autoApproveTimeOut));
        $info = array();
        while ($new = $result->fetch()) {
            $browserId = $new['browserId'];
            $branchId = $new['branchId'];
            $osId = $new['osId'];
            $current = $this->db->prepare('SELECT * FROM `history` WHERE browserId=:browserId AND branchId=:branchId AND osId=:osId ORDER BY `date` DESC LIMIT 1')
                                ->bind(':browserId', $browserId)
                                ->bind(':branchId', $branchId)
                                ->bind(':osId', $osId)
                                ->execute()
                                ->fetch();

            if ($current!==false && $this->isNew($current, $new)) {
                if ($this->addVersion($new)) {
                    // $this->deleteFromCheck($new['id']);
                    $info[] = 'AUTOUPDATED: '.$browsers[$browserId]['shortName'].' '.$branches[$branchId].' '.$oses[$osId].' '.$new['version'].' ('.date('Y-m-d', $new['date']).')';
                }
            } else {
                $info[] = 'AUTOUPDATE FAILED: '.$browsers[$browserId]['shortName'].' '.$branches[$branchId].' '.$oses[$osId].' '.$new['version'].' ('.date('Y-m-d', $new['date']).')';
                $this->approveMail($current, $new, $new['code']);
                $result = $this->db->prepare('UPDATE `check` SET __modified=?')->execute(time());
            }
        }
        return $info;
    }
    
    
    public function addVersion($version) 
    {
        return $this->db->prepare('INSERT INTO `history` (browserId, branchId, osId, version, `date`, __modified) VALUES (:browserId, :branchId, :osId, :version, :date, :modified)')
            ->bind(':browserId', $version['browserId'])
            ->bind(':branchId', $version['branchId'])
            ->bind(':osId', $version['osId'])
            ->bind(':version', $version['version'])
            ->bind(':date', $version['date'])
            ->bind(':modified', time())
            ->execute();
    }


    public function getCheckByCode($code) 
    {
        return $this->db->prepare('SELECT * FROM `check` WHERE code=?')
                    ->execute($code)
                    ->fetch();
    }


    public function updateVersions() 
    {
        if ($this->_isLock()) {
            return false;
        }

        $updated = array();
        
        $this->_setLock();
        
        $updated += $this->autoApproveCheck();
        
        $versions = $this->getLatestVersions();
        $browsers = $this->getBrowsers();
		$wikiLinks = $this->getWikiLinks();
        $branches = $this->getBranches();
        $oses = $this->getOSes();
        
		$newVersions = array();
		
		foreach ($browsers as $browser) {
			$browserName = strtolower($browser['shortName']);
			if (!empty($browser['import'])) {
				$newVersions[$browserName] = include $this->_dir . '/'. $browser['import'];
			} else if (!empty($wikiLinks[$browserName])) {
				$newVersions[$browserName] = $this->getVersionFromWiki($browser);
			} 
		}
		
		print_r($newVersions);
		print_r($versions);	
			
		foreach ($versions as $browserId => $browser) {
			$browserName = strtolower($browsers[$browserId]['shortName']);
			if (!empty($newVersions[$browserName])) {
				foreach ($browser as $branchId => $branch) {
					$branchName = $branches[$branchId];
					if (!empty($newVersions[$browserName][$branchName])) {
						foreach ($branch as $osId => $current) {
							$osName = $oses[$osId][0];
							if (!empty($newVersions[$browserName][$branchName][$osName])) {
								$new = $newVersions[$browserName][$branchName][$osName];
								if ((time()-$current['__modified']) >= $this->updateTimeOut 	// data updated long enough 
									&& (!empty($new['date'])  && !empty($new['version'])		// not empty info
										&& $new['date'] > $current['date'])					    // new browser release date > current
								) {
									$updated[] = $this->newVersion($new, $current, $browserId, $branchId, $osId);
								}
							}
						}
					}
				}
			}
		} 

        $this->_removeLock();
		
		$this->getLatestVersions(true);
        
        return $updated;
		
    }
	

	public function getVersionFromWiki($browser) 
	{
		$newVersions = false;
		$links = $this->getWikiLinks();
		$browserName = strtolower($browser['shortName']);
		if (empty($links[$browserName])) return false;
		foreach ($links[$browserName]['releases'] as $osName => $osBranches) {
			foreach ($osBranches as $branchName => $link) {
				$filename = $this->_dir.'/'.$browserName.'_'.$branchName.'_'.$osName.'.txt';
				if (file_exists($filename)) {
					$text = file_get_contents($filename);
				} else {
					// $text = $this->_curlGet($link);
					$text = false;
				}
				if (!empty($text)) {
					if (empty($newVersions)) {
						$newVersions = array();
					}
					$newVersions[$branchName][$osName] = $this->_parseWikiText($text, $links[$browserName]['regexp'], $branchName, $osName);
				}
			}
		}
		return $newVersions;
	}
	
	
	private function _parseWikiText($text, $regexps, $branchName, $osName) 
	{
		$versionRegexp = $this->_getRegexp($regexps, $branchName, $osName, 'version');
		$dateRegexp = $this->_getRegexp($regexps, $branchName, $osName, 'date');

		$info = array();
		
		preg_match_all($versionRegexp, $text, $data);
		if (empty($data[1][0])) {
			$info['version'] = false;
		} else {
			$info['version'] = trim($data[1][0]);
		}
		
		preg_match_all($dateRegexp, $text, $data);
		if (empty($data[1][0])) {
			$info['date'] = false;
		} else {
			$info['date'] = strtotime(str_replace(array('|', '-'), '/', trim($data[1][0])));
		}
		
		return $info;
	}
	
	private function _getRegexp($regexps, $branchName, $osName, $type)
	{
		if (isset($regexps[$type.'_'.$branchName.'_'.$osName])) {
			$regexp = $regexps[$type.'_'.$branchName.'_'.$osName];
		} else if (isset($regexps[$type.'_'.$branchName])) {
			$regexp = $regexps[$type.'_'.$branchName];
		} else if (isset($regexps[$type.'__'.$osName])) {
			$regexp = $regexps[$type.'__'.$osName];
		} else {
			$regexp = $regexps[$type];
		}
		return $regexp;
	}
	
	
	private function _curlGet($link) 
	{
		$ch = curl_init();
		$options = array(
			CURLOPT_URL            => $link,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_HEADER         => false, 
			CURLOPT_TIMEOUT        => 4, 
			CURLOPT_USERAGENT      => $this->userAgent
		);
		curl_setopt_array($ch, $options);
		$text = curl_exec($ch);
		curl_close($ch);
        return $text;
    }
    
    
    private function _isLock() {
        if (file_exists($this->_dir.'/lock')) {
            $lock = (int) file_get_contents($this->_dir.'/lock');
            if ((time()-$lock) <= $this->lockTimeOut) { 
                return true;
            }
        }
        return false;
    }    
    
    private function _setLock() {
        file_put_contents($this->_dir.'/lock', time());
    }    
    
    private function _removeLock() {
        unlink($this->_dir.'/lock');
    }
    
    
    public function getVersionFromWikiText($browserId, $branchId) {
    
        $browsers = $this->getBrowsers();
        $branches = $this->getBranches();
        $wikiLinks = $this->getWikiLinks();
    
        $browserName = strtolower($browsers[$browserId]['shortName']);
        $browserBranch = $branches[$branchId];
    
        $versions = false;
        $text = $this->getWikiText($browserName, $browserBranch);
        
        // print_r($text );
        if ($text!==false) {
            if (isset($wikiLinks[$browserName]['regexp']['version_'.$browserBranch])) {
                $regexpVersion = $wikiLinks[$browserName]['regexp']['version_'.$browserBranch];
            } else {
                $regexpVersion = $wikiLinks[$browserName]['regexp']['version'];
            }
            if (isset($wikiLinks[$browserName]['regexp']['date_'.$browserBranch])) {
                $regexpDate = $wikiLinks[$browserName]['regexp']['date_'.$browserBranch];
            } else {
                $regexpDate = $wikiLinks[$browserName]['regexp']['date'];
            }
            preg_match_all($regexpVersion, $text, $ver);
            preg_match_all($regexpDate, $text, $date);
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
                    $versions = array('browserId'=>$browserId, 'branchId'=>$branchId, 'version'=>trim($version), 'date'=>$dateTimeStamp);
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
                CURLOPT_URL            => $wikiLinks[$browserName][$branchName],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_HEADER         => false, 
                CURLOPT_TIMEOUT        => 4, 
                CURLOPT_USERAGENT      => $this->userAgent
            );
            curl_setopt_array($ch, $options);
            $text = curl_exec($ch);
            curl_close($ch);
        } else {
            $filename = $this->_dir.'/'.$browserName.'_'.$branchName.'.txt';
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
        $out = '#!/bin/sh' . PHP_EOL;
        foreach ($wikiLinks as $browserName => $branch) {
            foreach ($branch as $branchName => $link) {
                if (in_array($branchName, $branches)) {
                    if ($this->createShTool=='curl') {
                        $out .= 'curl "'.$link.'" > '.$this->_dir.'/'.$browserName.'_'.$branchName.'.txt' . PHP_EOL;
                    } else {
                        $out .= 'wget "'.$link.'" -O '.$this->_dir.'/'.$browserName.'_'.$branchName.'.txt '. PHP_EOL;
                    }
                }
            }
        }
        return file_put_contents($this->_dir.'/curl_links_files.sh', $out);        
    }
    
     
}