<?php
class AccountEditor extends AccountPage_Controller {
	
	private static $allowed_actions = array();
	protected $data_less_controller = true;
	private static $menu_title = '';
	
	public function init(){
		parent::init();
		Compressor::compress_file('less_AccountEditor.css',array(
			project().'/less/AccountEditor.less',
		));
		if(!$this->canView()){
			$messages = array(
				'default' =>
					_t(
						'AccountPage.LOGIN',
						'You don\'t have access to this area.'
					),
			);
			return Security::permissionFailure($this,$messages);
		}
	}

	public static function getTitle(){
		$class = get_called_class();
		return _t(
			$class.'.TITLE',
			Config::inst()->get($class,'menu_title')
				?Config::inst()->get($class,'menu_title')
				:preg_replace('!([A-Z])!',' $1',$class)
		);
	}

	public static function getContent(){
		return _t(get_called_class().'.Content');
	}

	public function SubLink(){
		$ar = array($this->Link());
		return call_user_func_array('Controller::join_links',array_merge($ar,func_get_args()));
	}

	public static function AccountMenu() {
		return AccountPage_Controller::AccountMenu();
	}

	public static function canView(){
		return Member::currentUserID()?true:false;
	}
	
	public static function StaticLink() {
		return self::join_links(get_called_class());//self::join_links('profile','Module',get_called_class());
	}

	public function Layout(){
		if(!$this->template){
			$this->template = 'AccountPage_'.get_called_class();
			if(!SSViewer::hasTemplate($this->template)){
				$this->template = 'AccountPage';
			}
			if(
				$this->request->param('Action')
				&& SSViewer::hasTemplate($this->template.'_'.$this->request->param('Action'))
			){
				$this->template = $this->template.'_'.$this->request->param('Action');
			}
		}
		return $this->renderWith($this->template);
	}

	public function MetaTags($includeTitle = true){
		$tags = parent::MetaTags($includeTitle);
		$tags = preg_replace(
			'!<title>([^<]*)</title>!i',
			'<title>'.self::getTitle().' $1</title>',
			$tags
		).'<meta name="robots" content="noindex" />';
		return $tags;
	}
}