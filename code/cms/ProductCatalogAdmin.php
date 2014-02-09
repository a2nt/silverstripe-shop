<?php

/**
 * Product Catalog Admin
 * @package shop
 * @subpackage cms
 **/
class ProductCatalogAdmin extends ModelAdmin {

	private static $url_segment = 'catalog';
	private static $menu_title = 'Catalog';
	private static $menu_priority = 2;
	private static $menu_icon = 'shop/img/icons/catalog-admin.png';
<<<<<<< HEAD

=======
	private static $managed_models = array(
		"Product","ProductCategory","ProductAttributeType"
	);
>>>>>>> 75f5fd7fd4c0ddcb5320b96839df4d3fd54d2cef
	private static $model_importers = array(
		'Product' => 'ProductBulkLoader'	
	);
<<<<<<< HEAD
	
	public function getManagedModels() {
		$models = array();
		
		if(class_exists('ProductAttributeType')){
			$models[] = 'ProductAttributeType';
		}
		
		$product_class = Config::inst()->get('ShopConfig','product_class')
			?Config::inst()->get('ShopConfig','product_class')
			:'Product';
		$models[] = $product_class;

		$category_class = Config::inst()->get('ShopConfig','product_category_class')
			?Config::inst()->get('ShopConfig','product_category_class')
			:'ProductCategory';

		$models[] = $category_class;

		// Normalize models to have their model class in array key
		foreach($models as $k => $v) {
			if(is_numeric($k)) {
				$models[$v] = array('title' => singleton($v)->i18n_singular_name());
				unset($models[$k]);
			}
		}
		
		return $models;
=======
	public function alternateAccessCheck(){
		return false;
>>>>>>> 75f5fd7fd4c0ddcb5320b96839df4d3fd54d2cef
	}
}