<?php
/**
 * Improvements to Currency for presenting in templates.
 */
class ShopCurrency extends Currency {

	private static $decimal_delimiter = '.';
	private static $thousand_delimiter = ',';
	private static $negative_value_format = "<span class=\"negative\">(%s)</span>";

	function Nice() {
		$val = $this->config()->currency_symbol .
			number_format(
				abs($this->value), 2, 
				self::config()->decimal_delimiter, 
				self::config()->thousand_delimiter
			);
		if($this->value < 0){
			return sprintf(self::config()->negative_value_format,$val);
		}
		return $val;
	}

	function forTemplate(){
		return $this->Nice();
	}
	
}