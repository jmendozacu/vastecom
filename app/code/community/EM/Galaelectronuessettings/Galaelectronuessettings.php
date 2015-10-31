<?php
/**
 * @deprecated use Mage::helper('galaelectronuessettings') instead
 * @methods:
 * - get[Section]_[ConfigName]($defaultValue = '')
 */
class EM_Galaelectronuessettings_Galaelectronuessettings
{
	static function __callStatic($name, $args) {
		if (method_exists(self, $name))
			call_user_func_array(array(self, $name), $args);
			
		elseif (preg_match('/^get([^_][a-zA-Z0-9_]+)$/', $name, $m)) {
			$segs = explode('_', $m[1]);
			foreach ($segs as $i => $seg)
				$segs[$i] = strtolower(preg_replace('/([^A-Z])([A-Z])/', '$1_$2', $seg));

			$value = Mage::getStoreConfig('galaelectronues/'.implode('/', $segs));
			if (!$value) $value = @$args[0];
			return $value;
		}
		
		else 
			call_user_func_array(array(self, $name), $args);
	}

	
	/**
	 * @return array
	 */
	public static function getAllCssConfig() {
		return array(
			'general_color' => Mage::getStoreConfig('galaelectronues/typography/general_color'),
			'primary_color' => Mage::getStoreConfig('galaelectronues/typography/primary_color'),
			'secondary_color' => Mage::getStoreConfig('galaelectronues/typography/secondary_color'),
			'secondary2_color' => Mage::getStoreConfig('galaelectronues/typography/secondary2_color'),
			'negative_color' => Mage::getStoreConfig('galaelectronues/typography/negative_color'),
			'negative2_color' => Mage::getStoreConfig('galaelectronues/typography/negative2_color'),
			'line_color' => Mage::getStoreConfig('galaelectronues/typography/line_color'),
			'secondary_line_color' => Mage::getStoreConfig('galaelectronues/typography/secondary_line_color'),
			'primary_bgcolor' => Mage::getStoreConfig('galaelectronues/typography/primary_bgcolor'),
			'secondary_bgcolor' => Mage::getStoreConfig('galaelectronues/typography/secondary_bgcolor'),
			'box_shadow' => Mage::getStoreConfig('galaelectronues/typography/box_shadow'),
			'rounded_corner' => Mage::getStoreConfig('galaelectronues/typography/rounded_corner'),
			'button1' => Mage::getStoreConfig('galaelectronues/typography/button1'),
			'button2' => Mage::getStoreConfig('galaelectronues/typography/button2'),
			'button3' => Mage::getStoreConfig('galaelectronues/typography/button3'),
			'button4' => Mage::getStoreConfig('galaelectronues/typography/button4'),
			'page_bg_image' => Mage::getStoreConfig('galaelectronues/typography/page_bg_image'),
			'general_font' => Mage::getStoreConfig('galaelectronues/typography/general_font'),
			'h1_font' => Mage::getStoreConfig('galaelectronues/typography/h1_font'),
			'h2_font' => Mage::getStoreConfig('galaelectronues/typography/h2_font'),
			'h3_font' => Mage::getStoreConfig('galaelectronues/typography/h3_font'),
			'h4_font' => Mage::getStoreConfig('galaelectronues/typography/h4_font'),
			'h5_font' => Mage::getStoreConfig('galaelectronues/typography/h5_font'),
			'h6_font' => Mage::getStoreConfig('galaelectronues/typography/h6_font'),
			
		);
	}   
}