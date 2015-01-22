<?php 
class RocketWeb_Search_Block_Entity_Abstract_Renderer extends Mage_Core_Block_Template {
	protected function excerpt($text, $phrase, $radius = 100, $ending = "...") {
		$phraseLen = strlen($phrase);
		if ($radius < $phraseLen) {
			$radius = $phraseLen;
		}
	
		$phrases = explode (' ',$phrase);
	
		foreach ($phrases as $phrase) {
			$pos = strpos(strtolower($text), strtolower($phrase));
			if ($pos > -1) break;
		}
	
		$startPos = 0;
		if ($pos > $radius) {
			$startPos = $pos - $radius;
		}
	
		$textLen = strlen($text);
	
		$endPos = $pos + $phraseLen + $radius;
		if ($endPos >= $textLen) {
			$endPos = $textLen;
		}
	
		$excerpt = substr($text, $startPos, $endPos - $startPos);
		if ($startPos != 0) {
			$excerpt = substr_replace($excerpt, $ending, 0, $phraseLen);
		}
	
		if ($endPos != $textLen) {
			$excerpt = substr_replace($excerpt, $ending, -$phraseLen);
		}
	
		return $excerpt;
	}
	
	protected function highlight($c,$q){
		$q=explode(' ',str_replace(array('','\\','+','*','?','[','^',']','$','(',')','{','}','=','!','<','>','|',':','#','-','_','/'),'',$q));
		for($i=0;$i<sizeOf($q);$i++)
			$c=preg_replace("/($q[$i])(?![^<]*>)/i","<span class=\"highlight\">\${1}</span>",$c);
			return $c;
	}
}