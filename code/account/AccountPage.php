<?php
/**
 * Account page shows order history and a form to allow
 * the member to edit his/her details.
 *
 * @package shop
 */
class AccountPage extends BasicPage {

	private static $icon = 'background-image:url(zz-basic/backend/img/treeicons/account-file.png);';
	
	private static $defaults = array(
		'ShowInMenus' => 0,
		'ShowInSearch' => 0,
	);
	
	public function canCreate($member = NULL) {
		if(AccountPage::get()->count()>0){
			return false;
		}
		return true;
	}

	/**
	 * Returns the link or the URLSegment to the account page on this site
	 * @param boolean $urlSegment Return the URLSegment only
	 */
	public static function find_link($urlSegment = false) {
		$page = self::get_if_account_page_exists();
		return ($urlSegment) ? $page->URLSegment : $page->Link();
	}
	/**
	 * Returns the title of the account page on this site
	 * @param boolean $urlSegment Return the URLSegment only
	 */
	public static function find_title() {
		$page = self::get_if_account_page_exists();
		return $page->Title;
	}

	protected static function get_if_account_page_exists() {
		if($page = AccountPage::get()) {
			return $page->First();
		}
		user_error('No AccountPage was found. Please create one in the CMS!', E_USER_ERROR);
	}

	public function requireDefaultRecords() {
		if($this->canCreate()){
			$className = get_class($this);
			$page = new $className();
			$page->setField('Title',_t($className.'DEFAULTTITLE','Profile'));
			$page->setField('Content',_t($className.'DEFAULTCONTENT','<p>Default page content. You can change it in the <a href="/admin/">CMS</a></p>'));
			$page->write();
			$page->publish('Stage', 'Live');
			$page->flushCache();
			DB::alteration_message($className.' page created', 'created');
		}
	}
}

class AccountPage_Controller extends BasicPage_Controller {
	
	private static $url_segment = 'account';
	private static $allowed_actions = array(
		'Module',
	);
	private static $url_handlers = array(
		'Module/$Module!/$Action/$ID/$OtherID' => 'Module'
    );

	protected $member;

	public function init() {
		parent::init();
		if(!Member::currentUserID()) {
			$messages = array(
				'default' => '<p class="message good">'.
					_t(
						'AccountPage.Message',
						'You\'ll need to login before you can access the account page.'
						.'If you are not registered, you won\'t be able to access it until you make your first order,'
						.' otherwise please enter your details below.'
					)
				.'</p>',
				'logInAgain' => 'You have been logged out. If you would like to log in again, please do so below.'
			);
			Security::permissionFailure($this,$messages);
			return false;
		}
		$this->member = Member::currentUser();
	}

	public static function AccountMenu() {
		$classes = self::get_type_classes();
		$menu = array();
		$menu[] = array(
			'Link' => self::join_links(AccountPage::find_link()),
			'Title' => _t('AccountPage.Title','Main Info'),
			'Status' => (
					!isset(Controller::curr()->urlParams['Module'])
					&& get_class(Controller::curr()) == 'AccountPage_Controller'
				)?'active':''
		);
		foreach($classes as $class) {
			if(
				(
					isset(Controller::curr()->urlParams['Module'])
					&& Controller::curr()->urlParams['Module'] == $class
				)
				|| get_class(Controller::curr()) == $class
			){
				$mode = 'active';
			}else{
				$mode = '';
			}
			$menu[] = array(
				'Link' => self::join_links(AccountPage::find_link(),'Module',$class,'/'),
				'Title' => $class::getTitle(),
				'Status' => $mode
			);
		}
		return new ArrayList($menu);
	}

	public function Module(SS_HTTPRequest $args){
		if(!in_array(
			$args->param('Module'),
			$this->get_type_classes()
		)){
			return $this->httpError(404,'Page not found.');
			//return $this->redirect('/');
		}
		$c = singleton($args->param('Module'));
		$c->setURLParams($args);//die('aaa');
		//$c->init();
		return $c->render();
	}

	public function getTitle(){
		if($this->dataRecord && $title = $this->dataRecord->Title){
			return $title;
		}
		return _t('AccountPage.Title','Profile');
	}
	
	public function getMember(){
		return $this->member;
	}

	/* gets account editor classes and removes by hide_ancestor */
	public static function get_type_classes() {
		$classes = ClassInfo::subclassesFor('AccountEditor');
		
		$baseClassIndex = array_search('AccountEditor', $classes);
		if($baseClassIndex !== FALSE) unset($classes[$baseClassIndex]);

		$kill_ancestors = array();
		// figure out if there are any classes we don't want to appear
		foreach($classes as $class) {
			$instance = singleton($class);
			// do any of the progeny want to hide an ancestor?
			if($ancestor_to_hide = $instance->stat('hide_ancestor')) {
				// note for killing later
				$kill_ancestors[] = $ancestor_to_hide;
			}
		}

		// If any of the descendents don't want any of the elders to show up, cruelly render the elders surplus to requirements.
		if($kill_ancestors) {
			$kill_ancestors = array_unique($kill_ancestors);
			foreach($kill_ancestors as $mark) {
				// unset from $classes
				$idx = array_search($mark, $classes);
				unset($classes[$idx]);
			}
		}
		return $classes;
	}
	
	public function Link($action = null,$http = true){
		$link = $this->RelativeLink($action);
		if($this->config()->secure_domain){
			$s = array(
				'http://',
				'http://www.'
			);
			$r = 'https://'.$this->config()->secure_domain;
		}else{
			$s = '';
			$r = '';
		}
		
		return
			str_ireplace(
				$s,
				$r,
				Director::absoluteURL(
					Controller::join_links(
						Director::baseURL(),
						$link
					)
				)
			);
	}
	public function MetaTags($includeTitle = true){
		$tags = parent::MetaTags($includeTitle);
		$tags .= '<meta name="robots" content="noindex" />';
		return $tags;
	}
}