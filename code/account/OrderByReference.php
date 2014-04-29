<?php
/**
 * Provides forms and processing to a controller for viewing order by reference an
 * order that has been previously placed.
 * 
 * @package shop
 * @subpackage forms
 */
class OrderByReference extends OrderManipulation {
	private static $allowed_actions = array(
		'orderref',
		'OrderRefForm',
	);

	protected function order_by_referece(){
		$request = $this->owner->getRequest();
		$reference = $request->param('ID') ?
			$request->param('ID')
			: $request->getVar('ref') ?
				$request->getVar('ref')
				: $request->postVar('Reference');

		$reference = Convert::raw2sql($reference);
		$orders = Order::get()->filter('Reference',$reference);
		if($orders->exists()){
			return $orders->First();
		}
		return false;
	}

	public function OrderRefForm(){
		$fields = FieldList::create(
			TextField::create('Reference','')
				->addPlaceHolder(_t('Cart.ORDERNUMBER','Your order #'))
				->setAttribute('required','required')
				->setAttribute('data-minlength','3')
				->setAttribute('maxlength','30')
				->append(
					FormAction::create('orderref',
						'<span class="icon icon-ok"></span>'._t('Page.CONTACTFORMGO','Send Message')
					)
						->addExtraClass(Page_Controller::getBtnClass())
				)
		);

		$validator = RequiredFields::create('Reference');
		$form = BootstrapForm::create($this->owner,'OrderRefForm',$fields,false,$validator);
		$form->setLayout('inline');
		return $form;
	}

	public function orderref(){
		AccountEditor::require_styled_type('OrderStatus');
		$order = $this->order_by_referece();
		if(!$order) {
			return $this->owner->httpError(404, "Order could not be found");
		}
		return $this->owner->customise(array(
			'Order' => $order
		))->renderWith(array('Page_orderref','BasicPage','Page'));
	}
}