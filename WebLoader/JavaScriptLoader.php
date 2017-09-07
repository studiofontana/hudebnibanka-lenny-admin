<?php

namespace Lenny\WebLoader;

use Nette\DI\Container,
	WebLoader,
	Nette\Utils\Finder;

abstract class JavaScriptLoader extends WebLoader\Nette\JavaScriptLoader
{

	public function __construct(Container $context)
	{
		$wwwDir = $context->parameters['wwwDir'];
		$basePath = $context->httpRequest->url->basePath;

		$files = new WebLoader\FileCollection(WWW_DIR);

		if(is_array($this->getFiles()))
			$files->addFiles($this->getFiles());
		if(isset($context->parameters['custom_theme_path_js'])){
			$files->addFiles($this->getCustomFiles($wwwDir . $context->parameters['custom_theme_path_js'], "js"));
			$files->addFiles($this->getCustomFiles($wwwDir . $context->parameters['custom_theme_path_js'], "coffee"));
		}


		$compiler = WebLoader\Compiler::createJsCompiler($files, "$wwwDir/etc/_temp");

		// Coffeescript filter
		$compiler->addFileFilter(new WebLoader\Filter\CoffeescriptFilter);		

		if($context->parameters['productionMode']) {
			
			/*$compiler->addFilter(function ($code) {
			    $packer = new Webloader\JavaScriptPacker($code, "None");
			    return $packer->pack();
			});*/

			/*$compiler->addFilter(function ($code) {
				$packer = new WebLoader\Filter\JSMin($code, "None");
				return $packer->minify($code);
			});*/

			/*$compiler->addFilter(function ($code) {
				$packer = new \JavaScriptPacker($code, "None", false, true);
				return $packer->pack();
			});*/
		}

		// Join js files
		$compiler->setJoinFiles($context->parameters['productionMode']);

		parent::__construct($compiler, $basePath . "etc/_temp");
	}

	public function getCustomFiles($dir, $extension) {
		$files = array();
		foreach (Finder::findFiles("*.$extension")->from($dir) as $key => $file) {
			$files[] = str_replace("\\", "/", $key);
		}
		return $files;
	}

	abstract protected function getFiles();
}