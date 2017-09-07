<?php

namespace Lenny\Application\UI;

use Nette\Application\ForbiddenRequestException;
use Nette\Security\IAuthorizator;
use Nette\Security\User;

class Presenter extends \Nette\Application\UI\Presenter
{
	
	public function startup()
	{
		parent::startup();
		$this->setEnviroment();
	}

	public function setEnviroment() {

		foreach ($this->context->parameters['folders'] as $folder) {
			$folders[] = $folder;
		}

		$this->createFolderStructure($folders);
	}

	/**
	* Create folder structure by path
	*/
	private function createFolderStructure($paths = array(), $start = WWW_DIR) {
		if(empty($paths)){
			return false;
		}

		foreach($paths as $path){
			$folders = explode("/", $path);
			$folder_path = "";
			foreach ($folders as $folder) {
				if($folder && !empty($folder)){
					$folder_path = $folder_path . "/" . $folder;
					if(!is_dir($start . "/" . $folder_path)){
						mkdir($start . "/" . $folder_path);
					}
				}
			}
		}
	}
	
	public function checkRequirements($element)
	{
		$user = $this->user;

		if ($element->hasAnnotation("Secured")) {
			if (!$user->isLoggedIn()) {
				if ($user->getLogoutReason() === User::INACTIVITY) {
					$this->flashMessage("You have been logged out.");
				}

				$this->flashMessage("You must first login.");
				$this->redirect(":Auth:Page:login", array("backlink" => $this->storeRequest()));
			}

			$secured = (array) $element->getAnnotation("Secured");

			if(isset($secured["role"]) && !$user->isInRole($secured["role"]) && $user->getIdentity()->data['role'] == "administrator")
				$this->redirect(':Admin:Page:default');

			if(isset($secured["role"]) && !$user->isInRole($secured["role"]) && $user->getIdentity()->data['role'] == "customer")
				$this->redirect(':Customer:Page:default');
			
			
			if (isset($secured["role"]) && !$user->isInRole($secured["role"])) {
				$this->user->logout();
				$this->flashMessage('You do not have permission to access this section.');
				$this->redirect('default');
				throw new ForbiddenRequestException();
			}
		}

		// @todo dodelat autorizaci pomoci Resouce a Privilege

		if ($element->hasAnnotation("Resource")) {
			$privilege = $element->hasAnnotation("Privilege") ? $element->getAnnotation("Privilege") : IAuthorizator::ALL;
			if (!$user->isAllowed($element->getAnnotation("Resource"), $privilege)) {
				throw new ForbiddenRequestException();
			}
		}
	}
}