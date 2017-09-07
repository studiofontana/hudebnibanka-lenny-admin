<?php

namespace Lenny\WebLoader;

use Nette\DI\Container,
	WebLoader,
	Nette\Utils\Finder;

abstract class CssLoader extends WebLoader\Nette\CssLoader
{

	public $addCustomFiles = true;

	public function __construct(Container $context)
	{
		$wwwDir = $context->parameters['wwwDir'];
		$basePath = $context->httpRequest->url->basePath;

		$files = new WebLoader\FileCollection();
		if(is_array($this->getFiles()) && count($this->getFiles()))
			$files->addFiles($this->getFiles());

		if($this->addCustomFiles && isset($context->parameters['custom_theme_path_css'])){
			$files->addFiles($this->getCustomFiles($wwwDir . $context->parameters['custom_theme_path_css'], "css"));
			$files->addFiles($this->getCustomFiles($wwwDir . $context->parameters['custom_theme_path_css'], "less"));
		}


		$compiler = WebLoader\Compiler::createCssCompiler($files, "$wwwDir/etc/_temp");

		// LESS filter
		$compiler->addFileFilter(new WebLoader\Filter\LessFilter);

		// URL replace
		$compiler->addFileFilter(new WebLoader\Filter\CssUrlsFilter($wwwDir, $basePath));

		// Join css files
		$compiler->setJoinFiles($context->parameters['productionMode']);

		// CSS minify
		/*if($context->parameters['productionMode']){
			$compiler->addFilter(function ($code) {
				$minifier = new \CssMinifier($code);
				return $minifier->getMinified();
			});
		}*/

		// Save into the temp folder
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