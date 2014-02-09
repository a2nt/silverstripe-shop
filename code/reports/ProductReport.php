<?php

class ProductReport extends ShopPeriodReport{
	
	protected $description = "Understand which products are performing, and which aren't.";
	//protected $periodfield = "SiteTree.Created";

	public function dataClass() {
		return Config::inst()->get('ShopConfig','product_class')?Config::inst()->get('ShopConfig','product_class'):'Product';
	}

	function getReportField(){
		$reportfield = parent::getReportField();
		$reportfield->getConfig()->removeComponentsByType('GridFieldPaginator');
		return $reportfield;
	}
	
	function columns(){
		return array(
			'Title' => array(
				'title' => _t('ProductReport.PRODUCTTITLE','Title'),
				'formatting' => '<a href=\"admin/products/'.$this->dataClass().'/$ID/edit\" target=\"_new\">$Title</a>'
			),
			'BasePrice' => _t('ProductReport.PRICE','Price'),
			'Created' => 'Created',
			'Quantity' => 'Quantity',
			'Sales' => 'Sales'
		);
	}
	
	function query($params){
		$class = $this->dataClass();
		$sng = singleton($class);
		$baseClass = ClassInfo::baseDataClass($class);

		$query = parent::query($params);
		$query->selectField($baseClass.'.Created','FilterPeriod')
			->addSelect(array(
				$class.'.ID',
				$baseClass.'.Created',
				$baseClass.'.ClassName',
				//$baseClass.'.Title'
			))
			->selectField($baseClass.'.Title','Title')
			->selectField('Count(OrderItem.Quantity)','Quantity')
			->selectField('Sum(OrderAttribute.CalculatedTotal)','Sales');
		if($baseClass == 'SiteTree'){
			$query->addInnerJoin('SiteTree',$class.'.ID = SiteTree.ID');
		}

		if(array_key_exists('BasePrice',$sng->stat('db'))){
			$query->selectField($class.'.BasePrice','BasePrice');
		}

		if(array_key_exists('Price',$sng->stat('db'))){
			$query->selectField($class.'.Price','BasePrice');
		}
		$query->setFrom($this->dataClass());
		$query->addLeftJoin($sng->stat('order_item'),$class.'.ID = '.$sng->stat('order_item').'.'.$class.'ID');
		$query->addLeftJoin('OrderItem',$sng->stat('order_item').'.ID = OrderItem.ID');
		$query->addLeftJoin('OrderAttribute',$sng->stat('order_item').'.ID = OrderAttribute.ID');
		$query->addLeftJoin('Order','OrderAttribute.OrderID = Order.ID');
		$query->addGroupby($this->dataClass().'.ID');
		$query->addWhere('"Order"."Paid" IS NOT NULL OR "'.$sng->stat('order_item').'"."ID" IS NULL');
		if(!$query->getOrderBy()){
			$query->setOrderBy('Sales DESC');
		}
		return $query;
	}
	
}