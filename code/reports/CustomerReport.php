<?php
/**
 * List top customers, especially those who spend alot, and those who buy alot.
 * @todo customer making the most purchases
 * @todo customer who has spent the most money
 * @todo new registrations graph
 * @todo demographics
 */
class CustomerReport extends ShopPeriodReport{
	
	protected $dataClass = "Member";
	protected $periodfield = "Order.Paid";

	function columns(){
		return array(
			'FirstName' => _t('CustomerReport.FIRSTNAME','First Name'),
			'Surname' => _t('CustomerReport.SURNAME','Surname'),
			'Email' => _t('CustomerReport.EMAIL','Email'),
			'Created' => _t('CustomerReport.JOINED','Joined'),
			'Spent' => _t('CustomerReport.SPENT','Spent'),
			'Orders' => _t('CustomerReport.ORDERS','Orders'),
			'NumVisit' => _t('CustomerReport.VISITS','Visits'),
			'edit'=>	array(
				'title' => _t('CustomerReport.EDIT','Edit'),
				'formatting' =>
					'<a href=\"admin/security/EditForm/field/Members/item/$ID/edit\" target=\"_new\">'
						._t('CustomerReport.EDIT','Edit')
					.'</a>'
			),
			
		);
	}
	
	function getReportField(){
		$field = parent::getReportField();
		return $field;
	}
	
	function query($params){
		$query = parent::query($params);
		$query->selectField($this->periodfield, "FilterPeriod")
			->addSelect(array("Member.ID", "Member.FirstName", "Member.Surname", "Member.Email", "NumVisit", "Member.Created"))
			->selectField("Count(Order.ID)", "Orders")
			->selectField("Sum(Order.Total)", "Spent");
		$query->addInnerJoin("Order", "Member.ID = Order.MemberID");
		$query->addGroupBy("Member.ID");
		if(!$query->getOrderBy()){
			$query->setOrderBy("Spent DESC,Orders DESC");
		}
		$query->setLimit("50");
		return $query;
	}

}