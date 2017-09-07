<?php

namespace Lenny\Grid;

use Nette\Application\UI\PresenterComponent;
use Nette\Utils\Html;

class Button extends PresenterComponent
{

	private $class;
	private $label;
	private $link;
	private $confirm;
	
	public function setConfirm($confirm)
	{
		$this->confirm = $confirm;
		return $this;
	}
	
	private function getConfirm($item)
	{
		if (is_callable($this->confirm)) {
			return call_user_func($this->confirm, $item);
		} else {
			return $this->confirm;
		}
	}

	public function setClass($class)
	{
		$this->class = $class;
		return $this;
	}

	private function getClass($item)
	{
		if (is_callable($this->class)) {
			return call_user_func($this->class, $item);
		} else {
			return $this->class;
		}
	}

	public function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}

	private function getLabel($item)
	{
		if (is_callable($this->label)) {
			return call_user_func($this->label, $item);
		} else {
			return $this->label;
		}
	}

	public function setLink($link)
	{
		$this->link = $link;
		return $this;
	}

	private function getLink($item)
	{
		if (is_callable($this->link)) {
			return call_user_func($this->link, $item);
		} else {
			return $this->link;
		}
	}

	public function render($item = NULL)
	{		
		$el = Html::el("a")->href($this->getLink($item))
			->class($this->getClass($item))
			->setText($this->getLabel($item));
		if(!empty($this->confirm)){
			$el->setOnClick("return confirm('$this->confirm')");
		}
		echo $el;
	}

}