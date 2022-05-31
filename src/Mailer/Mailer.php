<?php
/**
 * Project: SmartDating.cz.
 *
 * Author: Tomas Nikl <tomasnikl.cz@gmail.com>
 */

namespace Lenny\Mailer;


use Latte\Engine;
use Latte\Macros\BlockMacros;
use Latte\Macros\CoreMacros;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Nette\DI\Container;
use Nette\Environment;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Utils\DateTime;

class Mailer {

    use Nette\SmartObject;

	private $params = array();

	/**
	 * @var Container
	 */
	private $container;

	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	public function setTemplate($template)
	{
		$this->params['part'] = $template;
	}

	public function setParams($params)
	{
		foreach($params as $key => $value) {
			$this->params[$key] = $value;
		}
	}

	public function getTemplateInstring()
	{
		$latte = new Engine();
		$this->params['css'] = file_get_contents(WWW_DIR . '/www/resources/css/email.css');
		$this->params['presenter'] = Environment::getApplication()->getPresenter();
		$this->params['_control'] = Environment::getApplication()->getPresenter();
		$this->params['parameters'] = $this->container->parameters;

		$latte->onCompile[] = function(Engine $latte) {
			CoreMacros::install($latte->getCompiler());
			BlockMacros::install($latte->getCompiler());
			UIMacros::install($latte->getCompiler());
		};

		$latte->addFilter('czechDayName', function (DateTime $dateTime) {
			$dayNames = array('neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota');
			return $dayNames[date("w", $dateTime->getTimestamp())];
		});

		return $latte->renderToString(EMAIL_TEMPLATE, $this->params);
	}

	public function send($mail)
	{
		$latte = new Engine();
		$this->params['css'] = file_get_contents(WWW_DIR . '/www/resources/css/email.css');
		$this->params['presenter'] = Environment::getApplication()->getPresenter();
		$this->params['_control'] = Environment::getApplication()->getPresenter();
		$this->params['parameters'] = $this->container->parameters;

		$latte->onCompile[] = function(Engine $latte) {
			CoreMacros::install($latte->getCompiler());
			BlockMacros::install($latte->getCompiler());
			UIMacros::install($latte->getCompiler());
		};

		$latte->addFilter('czechDayName', function (DateTime $dateTime) {
			$dayNames = array('neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota');
			return $dayNames[date("w", $dateTime->getTimestamp())];
		});

		$mail->setHtmlBody($latte->renderToString(EMAIL_TEMPLATE, $this->params));

		$mailer = new SendmailMailer;
		$mailer->send($mail);
	}
} 