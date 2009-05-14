<?php
/**
 * Wikidot - free wiki collaboration software
 * http://www.wikidot.org
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @category Wikidot
 * @package Wikidot
 * @version $Id$
 * @copyright Copyright (c) 2008, Wikidot Inc.
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License
 */

class ManageUsersAction extends SmartyAction {
	
	public function isAllowed($runData){
		if ($runData->getTemp("site")->getSiteId() != 1) {
			throw new WDPermissionException("No permission");
		}
		WDPermissionManager::instance()->hasPermission('manage_site', $runData->getUser(), $runData->getTemp("site"));	
		return true;
	}
	
	public function perform($r){}
	
	public function saveEvent($runData){
		$params = $runData->getParameterList()->asArray();
		
		$ids = array();
		foreach ($params as $param_key => $param_val) {
			$m = array();
			if (preg_match(';^nick_name_([new0-9]+)$;', $param_key, $m)) {
				$ids[] = $m[1];
			}
		}
		
		foreach ($ids as $id) {
			$nick_name = $params["nick_name_$id"];
			$email = $params["email_$id"];
			$password = $params["password_$id"];
			
			if ($nick_name) {
				if ($id = 1 * $id) {
					$u = DB_OzoneUserPeer::instance()->selectByPrimaryKey($id);
				} else {
					$u = null;
				}
				
				$next = false;
				
				if (! $u) {
					$u = new DB_OzoneUser();
					if (! $password) {
						$next = true;
					}
				}
				
				if (! $next) {
					
					$u->setName($email);
					$u->setEmail($email);
					$u->setNickName($nick_name);
					$u->setUnixName(WDStringUtils::toUnixName($nick_name));
					
					if ($password) {
						$u->setPassword(md5($password));
					}
					
					$u->save();
				}
			}
		}
	}
	
}