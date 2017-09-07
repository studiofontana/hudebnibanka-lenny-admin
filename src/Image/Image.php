<?php

namespace Lenny;

class Image extends \Nette\Image {
	
	public function addText($text, $fontSize = 20, $angle = 0, $x = 0, $y = 0, $colorsString, $fontPath = "arial.ttf") {

		if(count($colorsString)){
			$colors = explode('-', $colorsString);
			$obrazek = imagecreate(1000, 1000);
			$color = imagecolorallocate($this->getImageResource(), $colors[0],$colors[1],$colors[2]);
		}else{
			$color = 1;
		}

		imagettftext($this->getImageResource(), $fontSize, $angle, $x, $y, $color, $fontPath, $text);
		return $this;
	}

}