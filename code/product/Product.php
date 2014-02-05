<?php
/**
 * This is a standard Product page-type with fields like
 * Price, Weight, Model and basic management of
 * groups.
 *
 * It also has an associated Product_OrderItem class,
 * an extension of OrderItem, which is the mechanism
 * that links this page type class to the rest of the
 * eCommerce platform. This means you can add an instance
 * of this page type to the shopping cart.
 *
 * @package shop
 */
class Product extends Page implements Buyable{

	private static $db = array(
		'InternalItemID' => 'Varchar(30)', //ie SKU, ProductID etc (internal / existing recognition of product)
		'Model' => 'Varchar(30)',
		
		'CostPrice' => 'Currency', // Wholesale cost of the product to the merchant
		'BasePrice' => 'Currency', // Base retail price the item is marked at.
		
		//physical properties
		'Weight' => 'Decimal(9,2)',
		'Height' => 'Decimal(9,2)',
		'Width' => 'Decimal(9,2)',
		'Depth' => 'Decimal(9,2)',
		
		'Featured' => 'Boolean',
		'AllowPurchase' => 'Boolean',
		
		'Popularity' => 'Float' //storage for ClaculateProductPopularity task
	);
	
	private static $has_one = array(
		'Image' => 'Image'
	);

	private static $many_many = array(
		'ProductCategories' => 'ProductCategory'
	);

	private static $defaults = array(
		'AllowPurchase' => true,
		'ShowInMenus' => false
	);

	private static $casting = array(
		'Price' => 'Currency'
	);

	private static $summary_fields = array(
		'InternalItemID','Title','BasePrice'
	);

	private static $searchable_fields = array(
		'InternalItemID','Title','BasePrice'
	);

	private static $singular_name = 'Product';
	private static $plural_name = 'Products';
	private static $icon = 'shop/img/icons/package';
	private static $default_parent = 'ProductCategory';
	private static $default_sort = '"Title" ASC';
	
	private static $global_allow_purchase = true;
	private static $order_item = 'Product_OrderItem';

	function generateReference(){
		$ref = 'PR'.strtoupper(substr(md5(uniqid(mt_rand(),true)),0,8));
		$this->extend('generateReference',$reference);

		//prevent generating references that are the same
		$refs = Product::get()->filter('InternalItemID',$ref);
		if($refs->exists()){
			$ref .= $refs->count();
		}
		$this->InternalItemID = $ref;
	}

	function onBeforeWrite(){
		parent::onBeforeWrite();
		if(!$this->getField('Reference')){
			$this->generateReference();
		}
	}
	
	/**
	 * Add product fields to CMS
	 * @return FieldList updated field list
	 */
	function getCMSFields() {
		self::disableCMSFieldsExtensions();
		$fields = parent::getCMSFields();
		$fields->fieldByName('Root.Main.Title')->setTitle(_t('Product.db_Title','Product Title'));
		
		// general fields
		$fields->addFieldsToTab('Root.Main',array(
			TextField::create(
				'InternalItemID',
				_t('Product.db_InternalItemID', 'Product Code/SKU'),
				'', 30
			),
			DropdownField::create(
				'ParentID',
				_t('Product.db_ParentID','Category'),
				$this->categoryoptions()
			)
				->setDescription(_t('Product.CATEGORYDESCRIPTION','This is the parent page or default category.')),
			ListBoxField::create(
				'ProductCategories',
				_t('Product.many_many_ProductCategories','Additional Categories'),
				ProductCategory::get()->map('ID','NestedTitle')->toArray()
			)
				->setMultiple(true),
			TextField::create(
				'Model',
				_t('Product.MODEL', 'Model'),
				'', 30
			),
			CheckboxField::create(
				'FeaturedProduct',
				_t('Product.db_FeaturedProduct', 'Featured Product')
			),
			CheckboxField::create(
				'AllowPurchase',
				_t('Product.db_AllowPurchase', 'Allow product to be purchased'),
				1
			)
		),'Content');
		//

		$tabset = $fields->findOrMakeTab('Root');
		
		// pricing fields
		$tabset->push(
			Tab::create(
				'PricingTab',
				_t('Product.PRICINGTAB','Pricing')
			)
				->push(
					CurrencyField::create('BasePrice', _t('Product.PRICE', 'Price'))
						->setDescription(_t('Product.PRICEDESC','Base price to sell this product at.'))
						->setMaxLength(12)
				)
				->push(
					CurrencyField::create('CostPrice', _t('Product.COSTPRICE', 'Cost Price'))
						->setDescription(_t('Product.COSTPRICEDESC','Wholesale price before markup.'))
						->setMaxLength(12)
				)
		);
		//
		
		// shipping fields
		if(Config::inst()->get('ShopConfig','shipping') === true){
			// physical measurements
			$weightunit = 'kg'; //TODO: globalise / make custom
			$lengthunit = 'cm';  //TODO: globalise / make custom
			$tabset->push(
				Tab::create(
					'ShippingTab',
					_t('Product.SHIPPINGTAB','Shipping')
				)
					->push(
						TextField::create(
							'Weight',
							_t('Product.WEIGHT','Weight ({unit})',array('unit' => $weightunit)),
							'',12
						)
					)
					->push(
						TextField::create(
							'Height',
							_t('Product.HEIGHT', 'Height ({unit})',array('unit' => $lengthunit)),
							'',12
						)
					)
					->push(
						TextField::create(
							'Depth',
							_t('Product.DEPTH', 'Depth ({unit})',array('unit' => $lengthunit)),
							'',12
						)
					)
			);
		}
		//

		if(!$fields->dataFieldByName('Image')) {
			$tabset->push(
				Tab::create(
					'ImageTab',
					_t('Product.IMAGETAB','Images')
				)
					->push(
						UploadField::create('Image', _t('Product.has_one_Image','Product Image'))
					)
			);
		}
		self::enableCMSFieldsExtensions();
		$this->extend('updateCMSFields', $fields);

		return $fields;
	}

	/**
	 * Helper function for generating list of categories to select from.
	 * @return array categories
	 */
	private function categoryoptions(){
		$categories = ProductCategory::get()->map('ID','NestedTitle')->toArray();
		$categories = array(
			0 => _t('SiteTree.PARENTTYPE_ROOT', 'Top-level page')
		) + $categories;
		
		if($this->ParentID && !($this->Parent() instanceof ProductCategory)){
			$categories = array(
				$this->ParentID => $this->Parent()->Title.' ('.$this->Parent()->i18n_singular_name().')'
			) + $categories;
		}

		return $categories;
	}

	/**
	 * Returns the shopping cart.
	 * @todo Does HTTP::set_cache_age() still need to be set here?
	 *
	 * @return Order
	 */
	function getCart() {
		if(!self::$global_allow_purchase){
			return false;
		} 
		HTTP::set_cache_age(0);

		return ShoppingCart::curr();
	}

	/**
	 * Conditions for whether a product can be purchased:
	 *  - global allow purchase is enabled
	 *  - product AllowPurchase field is true
	 *  - product page is published
	 *  - if variations, then one of them needs to be purchasable
	 *  - if not variations, selling price must be above 0
	 *
	 * Other conditions may be added by decorating with the canPurcahse function
	 * @return boolean
	 */
	function canPurchase($member = null) {
		if(!self::config()->global_allow_purchase ||
			!$this->AllowPurchase ||
			!$this->isPublished()
		){
			return false;
		} 
		$allowpurchase = false;
		if(
			self::has_extension('ProductVariationsExtension') &&
			ProductVariation::get()->filter('ProductID',$this->ID)->first()
		){ 
			foreach($this->Variations() as $variation){
				if($variation->canPurchase()){
					$allowpurchase = true;
					break;
				}
			}
		}elseif($this->sellingPrice() > 0){
			$allowpurchase = true;
		}
		// Standard mechanism for accepting permission changes from decorators
		$extended = $this->extendedCan('canPurchase', $member);
		if($allowpurchase && $extended !== null){
			$allowpurchase = $extended;
		}

		return $allowpurchase;
	}

	/**
	 * Returns if the product is already in the shopping cart.
	 * @return boolean
	 */
	function IsInCart() {
		return $this->Item() && $this->Item()->Quantity > 0;
	}

	/**
	 * Returns the order item which contains the product
	 * @return  OrderItem
	 */
	function Item() {
		$filter = array();
		$this->extend('updateItemFilter',$filter);
		$item = ShoppingCart::singleton()->get($this,$filter);
		if(!$item)
			$item = $this->createItem(0); //return dummy item so that we can still make use of Item
		$this->extend('updateDummyItem',$item);
		return $item;
	}
	
	/**
	 * @see Buyable::createItem()
	 */
	function createItem($quantity = 1, $filter = null){
		$orderitem = self::config()->order_item;
		$item = new $orderitem();
		$item->ProductID = $this->ID;
		if($filter){
			$item->update($filter); //TODO: make this a bit safer, perhaps intersect with allowed fields
		}
		$item->Quantity = $quantity;
		return $item;
	}

	/**
	 * The raw retail price the visitor will get when they
	 * add to cart. Can include discounts or markups on the base price.
	 */
	function sellingPrice(){
		$price = $this->BasePrice;
		$this->extend('updateSellingPrice',$price); //TODO: this is not ideal, because prices manipulations will not happen in a known order
		if($price < 0){
			$price = 0; //prevent negative values
		}
		return $price;
	}
	
	/**
	 * This value is cased to Currency in temlates.
	 */
	function getPrice(){
		return $this->sellingPrice();
	}
	
	function setPrice($val){
		$this->setField('BasePrice', $val);
	}

	function Link(){
		$link = parent::Link();
		$this->extend('updateLink',$link);
		return $link;
	}
	
	//passing on shopping cart links ...is this necessary?? ...why not just pass the cart?
	function addLink() {
		return ShoppingCart_Controller::add_item_link($this);
	}

	function removeLink() {
		return ShoppingCart_Controller::remove_item_link($this);
	}

	function removeallLink() {
		return ShoppingCart_Controller::remove_all_item_link($this);
	}

	public function MetaTags($includeTitle = true){
		$tags = parent::MetaTags($includeTitle);
		$tags .= '<meta property="og:type" content="website" />';
		$tags .= '<meta property="og:title" content="'.Convert::raw2att($this->meta_title()).'"/>';
		$tags .= '<meta property="og:url" content="'.$this->AbsoluteLink().'"/>';
		$tags .= '<meta property="og:image" content="'.Director::absoluteURL($this->Image()->SetWidth(200)->Link()).'"/>';
		$tags .= '<meta property="og:description" content="'.$this->getField('MetaDescription').'"/>';
		return $tags;
	}
}

class Product_Controller extends Page_Controller {

	private static $allowed_actions = array(
		'Form',
		'AddProductForm'
	);
	
	public $formclass = 'AddProductForm'; //allow overriding the type of form used
	
	function Form(){
		$formclass = $this->formclass;
		$form = new $formclass($this,'Form');
		$this->extend('updateForm',$form);
		return $form;
	}

}

class Product_OrderItem extends OrderItem {

	private static $db = array(
		'ProductVersion' => 'Int'
	);

	private static $has_one = array(
		'Product' => 'Product'
	);
	
	/**
	 * the has_one join field to identify the buyable
	 */
	private static $buyable_relationship = 'Product';

	/**
	 * Get related product
	 *  - live version if in cart, or
	 *  - historical version if order is placed 
	 *
	 * @param boolean $forcecurrent - force getting latest version of the product.
	 * @return Product
	 */
	public function Product($forcecurrent = false) {
		//TODO: this might need some unit testing to make sure it compliles with comment description
			//ie use live if in cart (however I see no logic for checking cart status)
		if($this->ProductID && $this->ProductVersion && !$forcecurrent){
			return Versioned::get_version('Product', $this->ProductID, $this->ProductVersion);
		}elseif($this->ProductID && $product = Versioned::get_one_by_stage('Product','Live', '"Product"."ID"  = '.$this->ProductID)){
			return $product;
		}
		return false;		
	}

	function onPlacement(){
		parent::onPlacement();
		if($product = $this->Product(true)){
			$this->ProductVersion = $product->Version;
		}
	}
	
	function TableTitle() {
		$product = $this->Product();
		$tabletitle = ($product) ? $product->Title : $this->i18n_singular_name();
		$this->extend('updateTableTitle',$tabletitle);
		return $tabletitle;
	}

	function Link() {
		if($product = $this->Product()){
			return $product->Link();
		}
	}
}