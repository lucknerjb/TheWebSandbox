<?php
	/**
	* Stringify
	* 
	* General String functions used in day-to-day programming
	* @author: Luckner Jr. Jean-Baptiste
	* @date: 01 June 2011 
	*/
	class Stringify {

		/**
		* Shorten a string by returning $length words if the length of the string is greater than $length
		* 
		* @param mixed $string
		* @param mixed $length
		*/
		public static function shorten($string, $length){
			//In case something goes wrong
			$retVal = $string;

			//Count to see if there are more than $length words. If not, return the whole string.
			if (substr_count($string, ' ') > 0){
				$array = explode(" ", $string);
				if (count($array) <= $length){
					$retVal = $string;
				}else{
					array_splice($array, $length);
					$retVal = implode( " ", $array )." ...";
				}
			}

			return $retVal;
		}
	}
?>
