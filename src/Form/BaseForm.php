<?php

namespace Lenny\Form;

use Nette,
	Nette\Utils\Html,
	Nette\Latte\Engine,
	Nette\Templating\FileTemplate;

class BaseForm extends \Nette\Application\UI\Form
{
	
	public $entity;
	public $file;

	/** @var Nette\DI\Container */
	public $container;

	public function __construct(Nette\DI\Container $container)
	{
		$this->container = $container;
	}

	// public function __construct($parent = NULL, $name = NULL)
	// {
	// 	parent::__construct($parent, $name);
		
	// 	if (method_exists($this, 'process'))
	// 		$this->onSuccess[] = callback($this, 'process');

	// 	$renderer = $this->getRenderer();
	// 	$renderer->wrappers['controls']['container'] = NULL;
	// 	$renderer->wrappers['label']['container'] = NULL;
	// 	$renderer->wrappers['pair']['container'] = 'div class="control-group"';
	// 	$renderer->wrappers['control']['container'] = 'div class="controls"';
	// }
	
	public function bind($entity)
	{
		$this->entity = $entity;

		if(!is_array($this->entity)) {
		    foreach($this->entity as $k => $v) {
		        if($this->entity->$k instanceof \MongoDB\Model\BSONArray) {
		            $this->entity->$k = (array) $v;
                }
            }
        }

		foreach ($this->components as $input) {
			$inputName = $input->name;
			if($input instanceof \Kdyby\Replicator\Container && is_array($this->entity) && isset($entity[$inputName])) {
				try {
					$input->setDefaults($entity[$inputName]);
				} catch(\Exception $e) {}

			}else if($input instanceof \Kdyby\Replicator\Container && isset($entity->$inputName)) {
				try {
					$input->setDefaults($entity->$inputName);
				} catch(\Exception $e) {}

			}else if(is_array($this->entity) && isset($entity[$inputName])) {
				try {
					$input->setDefaultValue($entity[$inputName]);
				} catch(\Exception $e) {}

			}else if(isset($entity->$inputName)){
				try {
					$input->setDefaultValue($entity->$inputName);
				} catch(\Exception $e) {}
			}
		}

        if (method_exists($this, 'init')) {
            $this->init();
        }
	}

	public function bindDynamic($entity)
	{
		if(!$entity->count())
			return false;

		foreach ($this->components as $input) {
			$inputName = $input->name;
			if(is_array($this->entity) && isset($entity[$inputName])) {
				$input->setDefaultValue($entity[$inputName]);
			}else if(isset($entity->$inputName)){
				$input->setDefaultValue($entity->$inputName);
			}
		}
	}

	private function getPathToForm()
	{
		$object = new \ReflectionObject($this);
		return dirname($object->getFileName());
	}


	public function render(...$args): void {
		$template = new FileTemplate($this->getPathToForm() . '/form.latte');
		$template->registerFilter(new Engine());
		$template->form = $this->name;
		$template->_control = $this->presenter;
		$template->_presenter = $this->presenter;
		$template->action = $this->presenter->action;
		$template->baseUrl = $this->presenter->context->httpRequest->url->baseUrl;
		$template->render();
	}
}