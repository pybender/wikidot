<?php
class LoginModule extends SmartyModule {
	
	public function build($runData){
		// check if not already logged in...
		
		
		$user = $runData->getUser();
		if($user){
			throw new ProcessException(_("You already are logged in."), "already_logged");	
		}	
		
		
		
		// check if reset remebered user
		$pl = $runData->getParameterList();
		if($pl->getParameterValue("reset")){
			setcookie('welcome', 'dummy', time() - 10000000, "/", GlobalProperties::$SESSION_COOKIE_DOMAIN);
		}else{
			// check if a recognized user
			
			$userId = $_COOKIE['welcome'];
			if($userId && is_numeric($userId) && $userId >0){
				$user = DB_OzoneUserPeer::instance()->selectByPrimaryKey($userId);
			}
			if($user == null){
				setcookie('welcome', 'dummy', time() - 10000000, "/", GlobalProperties::$SESSION_COOKIE_DOMAIN);
			}
		}
		
		$originalUrl = $pl->getParameterValue('origUrl');
		if($originalUrl){
			$originalUrlForce = $pl->getParameterValue('origUrlForce');
			if($originalUrlForce){
				$runData->sessionAdd('loginOriginalUrlForce', true);
			}
			$runData->sessionAdd('loginOriginalUrl', $originalUrl);
		}
		
		$runData->contextAdd("user", $user);
		
	}
	
}
?>