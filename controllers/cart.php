<?php

/**
 * Controller for the cart
 *
 * @package	VirtueMart
 * @subpackage Cart
 * @author Max Milbers
 * @link ${PHING.VM.MAINTAINERURL}
 * @copyright Copyright (c) 2004 - 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: cart.php 9466 2017-03-06 11:08:05Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the controller framework
jimport('joomla.application.component.controller');

/**
 * Controller for the cart view
 *
 * @package VirtueMart
 * @subpackage Cart
 */
class VirtueMartControllerCart extends JControllerLegacy {
    protected $ticketurl = '';
    protected $contact_msg_suffix = '';
    protected $conf;
    protected $mailh;
	public function __construct() {
		parent::__construct();
		if (VmConfig::get('use_as_catalog', 0)) {
			$app = JFactory::getApplication();
			$app->redirect('index.php');
		} else {
			if (!class_exists('VirtueMartCart'))
			require(VMPATH_SITE . DS . 'helpers' . DS . 'cart.php');
			if (!class_exists('calculationHelper'))
			require(VMPATH_ADMIN . DS . 'helpers' . DS . 'calculationh.php');
		}
		$this->useSSL = vmURI::useSSL();	//VmConfig::get('useSSL', 0);
		$this->useXHTML = false;
                if(!class_exists('FxbotmarketConfig')) {
                    include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/config.php';
                }
                $this->conf = new FxbotmarketConfig();
                $this->ticketurl = $this->conf->get('ticketurl');
		$this->contact_msg_suffix = ' If you have questions then <a href="'.JRoute::_($this->ticketurl,false).'" target="_blank">contact our support</a>  or live chat.';
        

	}

	public function display($cachable = false, $urlparams = false){

		if(VmConfig::get('use_as_catalog', 0)){
			// Get a continue link
			$virtuemart_category_id = shopFunctionsF::getLastVisitedCategoryId();
			$categoryLink = '';
			if ($virtuemart_category_id) {
				$categoryLink = '&virtuemart_category_id=' . $virtuemart_category_id;
			}
			$ItemId = shopFunctionsF::getLastVisitedItemId();
			$ItemIdLink = '';
			if ($ItemId) {
				$ItemIdLink = '&Itemid=' . $ItemId;
			}

			$continue_link = JRoute::_('index.php?option=com_virtuemart&view=category' . $categoryLink . $ItemIdLink, FALSE);
			$app = JFactory::getApplication();
			$app ->redirect($continue_link,'This is a catalogue, you cannot acccess the cart');
		}

		$document = JFactory::getDocument();
		$viewType = $document->getType();
		$tmpl = vRequest::getCmd('tmpl',false);
		if ($viewType == 'raw' and $tmpl == 'component') {
			$viewType = 'html';
		}

		$viewName = vRequest::getCmd('view', $this->default_view);
		$viewLayout = vRequest::getCmd('layout', 'default');

		$view = $this->getView($viewName, $viewType, '', array('layout' => $viewLayout));

		$view->assignRef('document', $document);

		$cart = VirtueMartCart::getCart();
                $cart_products = $cart->cartProductsData;
                if(count($cart_products) > 1){//delete extra products. we need have only one
                    $lastproductindex = count($cart_products) - 1;
                    $cur_idx = 0;
                    foreach($cart_products as $pr){
                        if($cur_idx >= $lastproductindex){
                            break;
                        }
                        $id_for_delete = $pr['virtuemart_product_id'];
                        $cart->removeProductCart($cur_idx);
                        $cur_idx ++ ;
                    }
                }
		$cart->order_language = vRequest::getString('order_language', $cart->order_language);
		if(!isset($force))$force = VmConfig::get('oncheckout_opc',true);
		$cart->prepareCartData(false);
		$html=true;
		if ($cart->virtuemart_shipmentmethod_id==0 and (($s_id = VmConfig::get('set_automatic_shipment',false)) > 0)){
			vRequest::setVar('virtuemart_shipmentmethod_id', $s_id);
			$cart->setShipmentMethod($force, !$html);
		}
		if ($cart->virtuemart_paymentmethod_id==0 and (($s_id = VmConfig::get('set_automatic_payment',false)) > 0) and $cart->products){
			vRequest::setVar('virtuemart_paymentmethod_id', $s_id);
			$cart->setPaymentMethod($force, !$html);
		}

		$request = vRequest::getRequest();
		$task = vRequest::getCmd('task');
		if(($task == 'confirm' or isset($request['confirm'])) and !$cart->getInCheckOut()){

			$cart->confirmDone();
			$view = $this->getView('cart', 'html');
			$view->setLayout('order_done');
			$cart->_fromCart = false;
			$view->display();
			return true;
		} else {
			//$cart->_inCheckOut = false;
			$redirect = (isset($request['checkout']) or $task=='checkout');
			$cart->_inConfirm = false;
			$cart->checkoutData($redirect);
		}

		$cart->_fromCart = false;

		$view->display();

		return $this;
	}

	public function updatecart($html=true,$force = null){
		if(!class_exists('VirtuemartFxbotHelper')) {
                    include_once JPATH_ROOT.'/plugins/system/vmfxbot/helpers/fxbot.php';
                }
		$cart = VirtueMartCart::getCart();
		$cart->_fromCart = true;
		$cart->_redirected = false;
		if(vRequest::get('cancel',0)){
			$cart->_inConfirm = false;
		}
		if($cart->getInCheckOut()){
			vRequest::setVar('checkout',true);
		}

		$cart->saveCartFieldsInCart();

		if($cart->updateProductCart()){
			vmInfo('COM_VIRTUEMART_PRODUCT_UPDATED_SUCCESSFULLY');
		}

		//Maybe better in line 133
		$STsameAsBT = vRequest::getInt('STsameAsBT', null);
		if(isset($STsameAsBT)){
			$cart->STsameAsBT = $STsameAsBT;
		}

		$currentUser = JFactory::getUser();
		if(!$currentUser->guest){
			$cart->selected_shipto = vRequest::getVar('shipto', $cart->selected_shipto);
			if(!empty($cart->selected_shipto)){
				$userModel = VmModel::getModel('user');
				$stData = $userModel->getUserAddressList($currentUser->id, 'ST', $cart->selected_shipto);

				if(isset($stData[0]) and is_object($stData[0])){
					$stData = get_object_vars($stData[0]);
					$cart->ST = $stData;
					$cart->STsameAsBT = 0;
				} else {
					$cart->selected_shipto = 0;
				}
			}
			if(empty($cart->selected_shipto)){
				$cart->STsameAsBT = 1;
				$cart->selected_shipto = 0;
				//$cart->ST = $cart->BT;
			}
		} else {
			$cart->selected_shipto = 0;
			if(!empty($cart->STsameAsBT)){
				//$cart->ST = $cart->BT;
			}
		}

		if(!isset($force))$force = VmConfig::get('oncheckout_opc',true);

		$cart->setShipmentMethod($force, !$html);
		$cart->setPaymentMethod($force, !$html);

		$cart->prepareCartData();

		$coupon_code = trim(vRequest::getString('coupon_code', ''));
		if(!empty($coupon_code)){
			$msg = $cart->setCouponCode($coupon_code);
			if($msg) vmInfo($msg);
			$cart->setOutOfCheckout();
		}
                $app = JFactory::getApplication();
                $input = $app->input;
                $session = JFactory::getSession();
                $type_of_product = 7;//by default pdf
                if(isset($cart->products) && (count($cart->products) > 0) && isset($cart->products[min(array_keys($cart->products))]->virtuemart_product_id)){
                    $virtuemart_product_id = $cart->products[min(array_keys($cart->products))]->virtuemart_product_id;	
                    if(!class_exists('FxbotmarketProduct')) {
                                include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/product.php';
                          }
                        //  var_dump($virtuemart_product_id);
                        $fxbot_product =   FxbotmarketProduct::getFxbotProductByVid($virtuemart_product_id);
                        //var_dump($fxbot_product);
                        //exit;
                        //die();
			if(($fxbot_product !== false) && is_object($fxbot_product) && isset($fxbot_product->id) && ($fxbot_product->id > 0)){
                            $type_of_product = $fxbot_product->typeofproduct > 0? $fxbot_product->typeofproduct: 1;
                            //Signal = 1,  Robot (MT4 EA) =2,Robot (MT5 EA) = 3, Indicator = 4,Script = 5,Software = 6,eBook (PDF) = 7, TAP = 1000
                                        
                        }else{
                            $app->enqueueMessage('Can not find product.'.$this->contact_msg_suffix,'error');
                            $app->redirect('index.php?option=com_fxbotmarket&view=customersignals');
                        }
                    
                }
                /*if(!class_exists('FxbotmarketLogger')) {
                    include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/logger.php';
                  }
                  $logger = new FxbotmarketLogger();
                  */
                if($type_of_product == 1){
                ///signal product code start
                $mt_account_type =  $input->post->get('mt_account_type',0,'int');//$input->post->get('fxbotform', array(), 'array')
                $id_mt_account = 0;
                if($mt_account_type == 2 || $mt_account_type == 3){
                    //$mtbroker = 'mt4';
                    $mtbroker = $input->post->get('mtbroker','','string');//if broker == lmax or ctrader then we do  not need 
                    if(!VirtuemartFxbotHelper::filterBrokers($mtbroker)){
                        $app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), 'You need to select broker. Error code #14'.$this->contact_msg_suffix,'error');
                    }
                    $mtserver = $input->post->get('mtserver','','string');
                    $mtlogin = $input->post->get('mtlogin','','string');
                    $mtpassword = $input->post->get('mtpassword','','string');
                    $lmaxmode = $input->post->get('lmaxmode','',1);
                    //if(strlen($mtserver) == 0 || strlen($mtlogin) == 0 || strlen($mtpassword) == 0){
                    if(!VirtuemartFxbotHelper::filterMtData($mtbroker,$mtserver,$mtlogin,$mtpassword)){
                        $app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), 'You need to fill account data before copying. Error code #12'.$this->contact_msg_suffix,'error');
                    }
                }elseif($mt_account_type == 1){//if we get server from stored records
                    $mt_account_type = 1;
                    $db = JFactory::getDbo();
                    //get id from 
                    $user = JFactory::getUser();
                    if(is_object($user) && $user->id > 0){
                        $id_user= $user->id;
                        $id_mt_account =$input->post->get('mt_stored_accounts',0,'int');
                        $q = "SELECT * FROM #__fxbotmarketx_mt WHERE id = ".(int)$id_mt_account." AND id_user = ".$id_user;
                        $db->setQuery($q);
                        $mt_account = $db->loadObject();
                        if(is_object($mt_account)){
                            $mtbroker = 'MT4';
                            $mtserver = $mt_account->serverip;
                            $mtlogin = $mt_account->username;
                            $mtpassword = $mt_account->password;
                            if(strlen($mtserver) == 0 || strlen($mtlogin) == 0 || strlen($mtpassword) == 0){
                                $app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), 'You need to fill account data before copying. Error code #10'.$this->contact_msg_suffix,'error');
                            }
                            
                        }else{
                            $app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), 'You need to fill account data before copying. Error code #11'.$this->contact_msg_suffix,'error');
                        }
                    }else{
                        $app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), 'You do not have acceess'.$this->contact_msg_suffix,'error');
                    }
                }else{
                        $mtbroker = '';
                        $mtserver = '';
                        $mtlogin = '';
                        $mtpassword = '';
                }
                
                $fxpaymentmethod = $input->get('fxpaymentmethod',0,'int');
                $session->set('mtcart.fxpaymentmethod', $fxpaymentmethod);
                $session->set('mtcart.mt_account_type', $mt_account_type);
                $session->set('mtcart.mtbroker', $mtbroker);
                $session->set('mtcart.mtserver', $mtserver);
                $session->set('mtcart.mtlogin', $mtlogin);
                $session->set('mtcart.mtpassword', $mtpassword);
                $session->set('mtcart.id_mt_account', (int)$id_mt_account);
				$session->set('mtcart.lmaxmode', $lmaxmode);
                //get risk factors
                $mt_risk_factors =  $input->post->get('mt_risk_factors',-1,'int');//$input->post->get('fxbotform', array(), 'array')
                $mt_risk_value =  $input->post->get('mt_risk_value','','string');//$input->post->get('fxbotform', array(), 'array')
                ///^-?(?:\d+|\d*\.\d+)$/
                //preg_match('~^[-+]?[0-9]{0,2}\.?[0-9]{1,2}$~',$mt_risk_factors);
                if($mt_risk_factors == -1){
                    $app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), 'You need to fill account data before copying. Error code #13'.$this->contact_msg_suffix,'error');
                }else{
                    $session->set('mtcart.mt_risk_factors', (int)$mt_risk_factors);
                }
                if(!preg_match('~^[-+]?[0-9]{0,2}\.?[0-9]{1,2}$~',$mt_risk_value)){
                    $app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), 'You need to enter correct risk factor value. It can be within range -99.99 to 99.99 with step 0.01.'.$this->contact_msg_suffix,'error');
                }else{
                    $session->set('mtcart.mt_risk_value', $mt_risk_value);
                }
                $fv = floatval($mt_risk_value);
                if($fv > 99.99 || $fv <= -99.99){
                    $app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), 'You need to enter correct risk factor value. It can be within range -99.99 to 99.99 with step 0.01.'.$this->contact_msg_suffix,'error');
                }
                }elseif(FxbotmarketProduct::ifDownloadableTypeOfProduct($type_of_product)){
                    //if downloadable product
                    
                    $fxpaymentmethod = $input->post->get('fxpaymentmethod',0,'int');
                    //$logger->logToFile('vmfxbotcart.txt',$fxpaymentmethod);
                    $session->set('mtcart.fxpaymentmethod', $fxpaymentmethod);
                }elseif(FxbotmarketProduct::ifEaTypeOfProduct($type_of_product) ){
                    //if ea product
                    
                    $fxpaymentmethod = $input->post->get('fxpaymentmethod',0,'int');
                    $eaacc = $input->post->get('eaacc','','string');
                    $eabroker = $input->post->get('eabroker',0,'int');
                    if($eaacc == ''){
                        $app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), 'You need to enter account number.'.$this->contact_msg_suffix,'error');
                    }
                    if($eabroker < 2 || $eabroker > 3){
                        $app->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart'), 'You need to select proper MT broker.'.$this->contact_msg_suffix,'error');
                    }
                    
                    $session->set('mtcart.fxpaymentmethod', $fxpaymentmethod);
                    $session->set('mtcart.eabroker', $eabroker);
                    $session->set('mtcart.eaaccountnumber', $eaacc);
                }elseif($type_of_product == 1001){ //blockpad_register
                    $blockpad_register = $input->post->get('blockpad_register',0,'int');
                    if($blockpad_register == 1){//blockpad_source
                        $fxbregisterselected = $input->post->get('fxbregisterselected',1,'int');                        
                        //fxbregisterselected
                        if($fxbregisterselected == 1){
                            $this->registerlogin();
                        }elseif($fxbregisterselected == 2){
                            $this->login();
                        }
                        
                    }
                    
                }
                /////signal product code end
		if ($html) {
			$this->display();
		} else {
			$json = new stdClass();
			ob_start();
			$this->display ();
			$json->msg = ob_get_clean();
			echo json_encode($json);
			jExit();
		}

	}
        public function registerlogin2(){

            $app = JFactory::getApplication();
            $input = $app->input;
            $data = array();
            $data['username'] = $input->post->get('fxbusername','','string');
            $data['email1'] = $input->post->get('fxbemail1','','string');
            if(!filter_var($data['email1'],FILTER_VALIDATE_EMAIL)){
                $app->enqueueMessage('Email has wrong format;');
                return false;
            }
            $data['password1'] = $input->post->get('fxbpassword1','','string');
            $data['name'] = $input->post->get('fxbname','','string');
            $data['cf_phone'] = $input->post->get('fxbcf_phone','','string');
            $data['email']		= $data['email1'];
            $data['password']	= $data['password1'];
            // Initialise the table with JUser.
            if(!class_exists('FxbotmarketUser')) {
                include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/fxbotmarketuser.php';
                }
            $data['groups'] = array(FxbotmarketUser::getRegisteredGroupId());
            $data['accepted_terms'] = 1;
            $data['cf_additiona_phones'] = "";
            $data['cf_address'] = "";
            $data['cf_city'] = "";
            $data['cf_state_province'] = "";
            $data['cf_zip'] = "";
            $data['ip_address'] = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
            $code_of_country = $input->post->get('fxbcountry_id',0,'int');//[fxbotmarket_country_id]	string	"16"
            $mt_accounts = array();//[fxbcountry_id]	string	"13"	
            $seller_info = new stdClass();
            $braintreeinfo = new stdClass();
            $braintreeinfo->braincustomerid = "";
            $braintreeinfo->paymentmethod = "";
            $braintreeinfo->paymentverified = "";
            $braintreeinfo->firstname = $data['name'];
            $braintreeinfo->lastname = "";
            if(!class_exists('UuModelRegistration')) {
                include_once JPATH_ROOT.'/components/com_uu/models/registration.php';
            }
            //UStringHelper
            if(!class_exists('UStringHelper')) {
                include_once JPATH_ROOT.'/components/com_uu/helpers/ustring.php';
            }
            //uufieldinterface
            include_once JPATH_ROOT.'/components/com_uu/libraries/uufieldinterface.php';
            $uumodel = new UuModelRegistration();
            $result = $uumodel->quickregister($data,$code_of_country,$mt_accounts,$seller_info ,$braintreeinfo );
            if($result > 0){
                $logindata = array();
                $logindata['username'] = $data['username'];//$input->$method->get('username', '', 'USERNAME');
                $logindata['password'] = $data['password1'];//$input->$method->get('password', '', 'RAW');
                $credentials = array();
                $credentials['username'] = $logindata['username'];
                $credentials['password'] = $logindata['password'];
                $options = array();
                $options['remember'] = false;
                //$options['return'] = 'index.php?option=com_virtuemart&view=cart&Itemid=370';
                if (true === $app->login($credentials, $options)) {
                    $user = JFactory::getUser();
                    $id_user = $user->id;
                    return true;
                              
                    } else {
                        return false;
                                        

                }
            }else{
                return false;
            }
        }

        public function registerlogin(){
            $params = JComponentHelper::getParams('com_users');
            $app = JFactory::getApplication();
            $input = $app->input;
            $data = array();
            $data['username'] = $input->post->get('fxbusername','','string');
            $data['email1'] = $input->post->get('fxbemail1','','string');
            if(!filter_var($data['email1'],FILTER_VALIDATE_EMAIL)){
                $app->enqueueMessage('Email has wrong format;');
                return false;
            }
            $data['password1'] = $input->post->get('fxbpassword1','','string');
            $data['name'] = $input->post->get('fxbname','','string');
            $data['cf_phone'] = $input->post->get('fxbcf_phone','','string');
            //$data['name'] = $input->post->get('fxbname','','string');
            //$data['name'] = $input->post->get('fxbname','','string');
            $data['email']		= $data['email1'];
            $data['password']	= $data['password1'];
            // Initialise the table with JUser.
            if(!class_exists('FxbotmarketUser')) {
                include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/fxbotmarketuser.php';
                }
            $data['groups'] = array(FxbotmarketUser::getRegisteredGroupId());
            $usern = new JUser;
            //$data = (array)$this->getData();



            // Prepare the data for the user object.
            
            $useractivation = $params->get('useractivation');
            $sendpassword = $params->get('sendpassword', 1);
            $logvar = "2.enter\n" ;
                    //error_log(print_r($logvar,true),3,$fl2);
            // Check if the user needs to activate their account.
            if (($useractivation == 1) || ($useractivation == 2)) {
                //$data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
                //$data['block'] = 1;
            }
            // Bind the data.
            if (!$usern->bind($data)) {
                $this->setError(JText::sprintf('COM_UU_REGISTRATION_BIND_FAILED', $usern->getError()));
                return false;
            }
            $logvar = "3.enter\n" ;
                    //error_log(print_r($logvar,true),3,$fl2);
            // Load the users plugin group.
            JPluginHelper::importPlugin('user');

            //used to save a user
            JTable::addIncludePath(JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table');

            // Store the joomla user data.
            if (!$usern->save()) {
                $logvar = $usern->getError() ;
                    //error_log(print_r($logvar,true),3,$fl2);

                            //$logvar = JText::sprintf('COM_UU_REGISTRATION_SAVE_FAILED', $usern->getError()) ;
                    //error_log(print_r($logvar,true),3,$fl2);
                $app->enqueueMessage(JText::sprintf('Wrong registration data. Please correct them.', $usern->getError()),'error');
                return false;
            }

            /*
            $user = JFactory::getUser();
                $id_user = $user->id;
                $inst = $user->getInstance(408);*/
            $logindata = array();
            $logindata['username'] = $data['username'];//$input->$method->get('username', '', 'USERNAME');
            $logindata['password'] = $data['password1'];//$input->$method->get('password', '', 'RAW');
            $credentials = array();
            $credentials['username'] = $logindata['username'];
            $credentials['password'] = $logindata['password'];
            $options = array();
            $options['remember'] = false;
            //$options['return'] = 'index.php?option=com_virtuemart&view=cart&Itemid=370';
            if (true === $app->login($credentials, $options)) {
                $user = JFactory::getUser();
                $id_user = $user->id;
                return true;
                                    // Success
                        //$url = UuSiteHelper::getRedirectUrl($conf->get('red_login_success'),$conf->get('red_login_success_custom'));
                        //$app->setUserState('uu.login.form.return', $url);
                                    //$app->redirect(JRoute::_($app->getUserState('uu.login.form.return'), false));
                } else {
                    return false;
                                    // Login failed !
                                    //$logindata['remember'] = (int)$options['remember'];
                                    //$app->setUserState('uu.login.form.data', $logindata);
                                    //$app->redirect(JRoute::_('index.php?option=com_uu&view=login', false));

            }

        }
        
        public function login(){
            $app = JFactory::getApplication();
            $input = $app->input;
            $credentials = array();
            $credentials['username'] = $input->post->get('fxblogin','','string');
            $credentials['password'] = $input->post->get('fxbloginpassword1','','string');
            $options = array();
            $options['remember'] = false;
            //$options['return'] = 'index.php?option=com_virtuemart&view=cart&Itemid=370';
            if (true === $app->login($credentials, $options)) {
                $user = JFactory::getUser();
                $id_user = $user->id;
                return true;
                                    // Success
                        //$url = UuSiteHelper::getRedirectUrl($conf->get('red_login_success'),$conf->get('red_login_success_custom'));
                        //$app->setUserState('uu.login.form.return', $url);
                                    //$app->redirect(JRoute::_($app->getUserState('uu.login.form.return'), false));
                } else {
                    return false;
                                    // Login failed !
                                    //$logindata['remember'] = (int)$options['remember'];
                                    //$app->setUserState('uu.login.form.data', $logindata);
                                    //$app->redirect(JRoute::_('index.php?option=com_uu&view=login', false));

            }
        }
        
	public function updatecartJS(){
		$this->updatecart(false);
	}


	/**
	 * legacy
	 * @deprecated
	 */
	public function confirm(){
		$this->updatecart();
	}

	public function setshipment(){
		$this->updatecart(true,true);
	}

	public function setpayment(){
		$this->updatecart(true,true);
	}

	/**
	 * Add the product to the cart
	 * @access public
	 */
	public function add() {
		$mainframe = JFactory::getApplication();
		if (VmConfig::get('use_as_catalog', 0)) {
			$msg = vmText::_('COM_VIRTUEMART_PRODUCT_NOT_ADDED_SUCCESSFULLY');
			$type = 'error';
			$mainframe->redirect('index.php', $msg, $type);
		}
                $session = JFactory::getSession();
                $session->set('mtcart.fx_product_rent', 0);
                $input = $mainframe->input;
                $fx_product_rent = $input->post->get('fx_product_rent', 0, 'integer');//('fxbotmarket_payreceive_id', 0, 'integer');
		$fx_product_demo = $input->post->get('fx_product_demo', 0, 'integer');//('fxbotmarket_payreceive_id', 0, 'integer');
                $fx_product_trial = $input->post->get('fx_product_trial', 0, 'integer');//('fxbotmarket_payreceive_id', 0, 'integer');
		if($fx_product_rent == 0 && $fx_product_demo > 0){//if customer wants download demo
                    $this->downloadDemoFile($fx_product_demo);
                }elseif($fx_product_trial > 0){
                    $virt_product_id = $input->post->get('pid', 0, 'integer');//('fxbotmarket_payreceive_id', 0, 'integer');
                    if(!class_exists('FxbotmarketProductcustomer')) {
                        include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/productcustomer.php';
                    }
                    $productcustomer = new FxbotmarketProductcustomer();
                    // $productcustomer->downloadTrialFile($fx_product_trial);
                    $id_rent_log = $productcustomer->finishDownloadTrialFile($fx_product_trial);
                    $app = JFactory::getApplication();
                    if($id_rent_log > 0){
                        $app->enqueueMessage('Fill fields below to finish download trial procedure. After you fill all fields downloadable link will get available.', 'success');
                        $app->redirect(JRoute::_('index.php?option=com_fxbotmarket&view=customerea&id='.$fx_product_trial.'&type_of_record=1'), FALSE);
                    }else{
                        $app->enqueueMessage('You are not allowed to download trial. Perhaps you already used this feature'.$this->contact_msg_suffix, 'error');
                        $app->redirect(JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$virt_product_id), FALSE);
                    }
                }
                elseif($fx_product_rent > 0){
                    switch($fx_product_rent){
                        case 1:
                            $session->set('mtcart.fx_product_rent', 1);
                            break;
                        case 3:
                            $session->set('mtcart.fx_product_rent', 3);
                            break;
                        case 6:
                            $session->set('mtcart.fx_product_rent', 6);
                            break;
                        case 12:
                            $session->set('mtcart.fx_product_rent', 12);
                            break;
                    }
                }
                $cart = VirtueMartCart::getCart();
		if ($cart) {
			$virtuemart_product_ids = vRequest::getInt('virtuemart_product_id');
                        $cart->emptyCart();//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			$error = false;
                        $input = $mainframe->input;
                        $blockpad_register = $input->post->get('blockpad_register',0,'int');
                        if($blockpad_register == 1){//blockpad_source
                        $fxbregisterselected = $input->post->get('fxbregisterselected',1,'int');                        
                        //fxbregisterselected
                        if($fxbregisterselected == 1){
                           $result_log = $this->registerlogin2();
                        }elseif($fxbregisterselected == 2){
                           $result_log = $this->login();
                        }
                        if($result_log == false){
                            $mainframe->enqueueMessage('Wrong login or register?'.$this->contact_msg_suffix, 'error');
                            $mainframe->redirect('index.php?'.$_SERVER['QUERY_STRING']);
                            return;
                        }
                    }
                        
                        
			$cart->add($virtuemart_product_ids,$error);
			if (!$error) {
				$msg = vmText::_('COM_VIRTUEMART_PRODUCT_ADDED_SUCCESSFULLY');
				$type = '';
                                //here i need to add mtaccount to session or coockie
                                //another way is to add into database
			} else {
				$msg = vmText::_('COM_VIRTUEMART_PRODUCT_NOT_ADDED_SUCCESSFULLY');
				$type = 'error';
			}
                        
                        $session->set('mtcart.mt_account_type', -1);
			$mainframe->enqueueMessage($msg, $type);
                        
                        
                    
                        
                        
			$mainframe->redirect(JRoute::_('index.php?option=com_virtuemart&view=cart', FALSE));

		} else {
			$mainframe->enqueueMessage('Cart does not exist?'.$this->contact_msg_suffix, 'error');
		}
	}

        public function downloadDemoFile($id_product = 0){
            $user = JFactory::getUser();
            $user_can_download = false;
            if(is_object($user) && isset($user->id) && $user->id > 0){
                $user_can_download = true;
            }
            $app = JFactory::getApplication();
            if(!$user_can_download){
                $app->enqueueMessage('You need to login before download Demo files ', 'error');
                $app->redirect('index.php?option=com_users&view=login');
                return;
            }
            $db = JFactory::getDbo();
            $query = 'SELECT * FROM #__fxbotmarketx_demo_files WHERE id_product = '.(int)$id_product;
            $db->setQuery($query);
            $demo_file = $db->loadObject();
            if(is_object($demo_file) && isset($demo_file->id) && $demo_file->id > 0){
                //load config helper
                if(!class_exists('FxbotmarketConfig')) {
                    include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/config.php';
                }
                $fxconf = new FxbotmarketConfig();
                $demo_path = $fxconf->get('demo_files_directory');
                $filename = $demo_file->filename;
                //$full_path = 
                $file = rtrim($demo_path, '/').'/'.$filename;
                $header_filename = rawurlencode(basename($file));
                $fileExists = file_exists($file);
                if($fileExists){
                header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.$header_filename);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
                        $size = @filesize($file);
			header('Content-Length: ' . $size );
			ob_clean();
			flush();
                        $this->readfile_chunked($file);
                }
                        exit;
            }else{
                return false;
            }
        }
        
        public function downloadTrialFile($id_product = 0){
            if(!class_exists('FxbotmarketUser')) {
              include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/fxbotmarketuser.php';
            }
            if(!class_exists('FxbotmarketProductcustomer')) {
                include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/productcustomer.php';
            }
            $productcustomer = new FxbotmarketProductcustomer();
            $user_can_download = FxbotmarketUser::checkUserCanDownloadTrialEa($id_product);
            $app = JFactory::getApplication();
            
            if($user_can_download->code < 1){
                $app->enqueueMessage($user_can_download->msg.$this->contact_msg_suffix, 'error');
                $app->redirect('index.php?option=com_users&view=login');
                return;
            }
            $db = JFactory::getDbo();
            $user = JFactory::getUser();
            $query = 'SELECT a.filename, a.file_id FROM #__quicksell_files as a INNER JOIN '
                    . ' #__fxbotmarketx_files_products as b ON a.file_id = b.id_file WHERE b.id = '.(int)$id_product;
            $db->setQuery($query);
            $trial_file = $db->loadObject();
            if(is_object($trial_file) && isset($trial_file->file_id ) && $trial_file->file_id  > 0){
                //load config helper
                if(!class_exists('FxbotmarketConfig')) {
                    include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/config.php';
                }
                $fxconf = new FxbotmarketConfig();
                $demo_path = $fxconf->get('demo_files_directory');
                $filename = $trial_file->filename;
                //$full_path = 
                $file = rtrim($demo_path, '/').'/'.$filename;
                $header_filename = rawurlencode(basename($file));
                $fileExists = file_exists($file);
                if($fileExists){
                header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.$header_filename);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
                        $size = @filesize($file);
			header('Content-Length: ' . $size );
			ob_clean();
			flush();
                        $this->readfile_chunked($file);
                        $id_rent = $productcustomer->storeRentLogRecord($id_product,$user->id,2);
                        $productcustomer->activateRentRecordById($id_rent);
                        FxbotmarketUser::storeUserDownloadedTrial($id_product, $user->id);
                }
                        exit;
            }else{
                return false;
            }
        }
        function readfile_chunked( $filename, $retbytes = true ) {
		@error_reporting(0);
		@ini_set('display_errors', '0');
		$chunksize = 1 * (1024 * 1024); // how many bytes per chunk
		$buffer = '';
		$cnt = 0;
		$handle = fopen( $filename, 'rb' );
		if ( $handle === false ) {
			return false;
		}
		ob_end_clean(); //added to fix ZIP file corruption
		ob_start(); //added to fix ZIP file corruption
		@header( 'Content-Type:' ); //added to fix ZIP file corruption
		while ( !feof( $handle ) ) {
			$buffer = fread( $handle, $chunksize );
			//$buffer = str_replace("ï»¿","",$buffer);
			echo $buffer;
			ob_flush();
			flush();
			if ( $retbytes ) {
				$cnt += strlen( $buffer );
			}
		}
		$status = fclose( $handle );
		if ( $retbytes && $status ) {
			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;
	}
    
	/**
	 * Add the product to the cart, with JS
	 * @access public
	 */
	public function addJS() {
		if(VmConfig::showDebug()) {
			VmConfig::$echoDebug = 1;
			ob_start();
		}
		$this->json = new stdClass();
		$cart = VirtueMartCart::getCart();
		if ($cart) {
			$view = $this->getView ('cart', 'json');
			$virtuemart_category_id = shopFunctionsF::getLastVisitedCategoryId();

			$virtuemart_product_ids = vRequest::getInt('virtuemart_product_id');

			$view = $this->getView ('cart', 'json');
			$errorMsg = 0;

			$products = $cart->add($virtuemart_product_ids, $errorMsg );


			$view->setLayout('padded');
			$this->json->stat = '1';

			if(!$products or count($products) == 0){
				$product_name = vRequest::get('pname');
				if(is_array($virtuemart_product_ids)){
					$pId = $virtuemart_product_ids[0];
				} else {
					$pId = $virtuemart_product_ids;
				}
				if($product_name && $pId) {
					$view->product_name = $product_name;
					$view->virtuemart_product_id = $pId;
				} else {
					$this->json->stat = '2';
				}
				$view->setLayout('perror');
			}

			$view->assignRef('products',$products);
			$view->assignRef('errorMsg',$errorMsg);

			if(!VmConfig::showDebug()) {
				ob_start();
			}
			$view->display ();
			$this->json->msg = ob_get_clean();
			if(VmConfig::showDebug()) {
				VmConfig::$echoDebug = 0;
			}
		} else {
			$this->json->msg = '<a href="' . JRoute::_('index.php?option=com_virtuemart', FALSE) . '" >' . vmText::_('COM_VIRTUEMART_CONTINUE_SHOPPING') . '</a>';
			$this->json->msg .= '<p>' . vmText::_('COM_VIRTUEMART_MINICART_ERROR') . '</p>';
			$this->json->stat = '0';
		}
		echo json_encode($this->json);
		jExit();
	}

	/**
	 * Add the product to the cart, with JS
	 *
	 * @access public
	 */
	public function viewJS() {

		if (!class_exists('VirtueMartCart'))
		require(VMPATH_SITE . DS . 'helpers' . DS . 'cart.php');
		$cart = VirtueMartCart::getCart(false);
		$cart -> prepareCartData();
		$data = $cart -> prepareAjaxData(true);

		echo json_encode($data);
		Jexit();
	}

	/**
	 * For selecting couponcode to use, opens a new layout
	 */
	public function edit_coupon() {

		$view = $this->getView('cart', 'html');
		$view->setLayout('edit_coupon');

		// Display it all
		$view->display();
	}

	/**
	 * Store the coupon code in the cart
	 * @author Max Milbers
	 */
	public function setcoupon() {

		$this->updatecart();
	}


	/**
	 * For selecting shipment, opens a new layout
	 */
	public function edit_shipment() {


		$view = $this->getView('cart', 'html');
		$view->setLayout('select_shipment');

		// Display it all
		$view->display();
	}

	/**
	 * To select a payment method
	 */
	public function editpayment() {

		$view = $this->getView('cart', 'html');
		$view->setLayout('select_payment');

		// Display it all
		$view->display();
	}

	/**
	 * Delete a product from the cart
	 * @access public
	 */
	public function delete() {
		$mainframe = JFactory::getApplication();
		/* Load the cart helper */
		$cart = VirtueMartCart::getCart();
		if ($cart->removeProductCart())
		$mainframe->enqueueMessage(vmText::_('COM_VIRTUEMART_PRODUCT_REMOVED_SUCCESSFULLY'));
		else
		$mainframe->enqueueMessage(vmText::_('COM_VIRTUEMART_PRODUCT_NOT_REMOVED_SUCCESSFULLY'), 'error');

		$this->display();
	}

	public function getManager(){
		$id = vmAccess::getBgManagerId();
		return JFactory::getUser( $id );
	}

	/**
	 * Change the shopper
	 *
	 * @author Maik Künnemann
	 */
	public function changeShopper() {
		vRequest::vmCheckToken() or jexit ('Invalid Token');
		$app = JFactory::getApplication();

		$redirect = vRequest::getString('redirect',false);
		if($redirect){
			$red = $redirect;
		} else {
			$red = JRoute::_('index.php?option=com_virtuemart&view=cart');
		}

		$id = vmAccess::getBgManagerId();
		$current = JFactory::getUser( );;
		$manager = vmAccess::manager('user');
		if(!$manager){
			vmdebug('Not manager ',$id,$current);
			$app->enqueueMessage(vmText::sprintf('COM_VIRTUEMART_CART_CHANGE_SHOPPER_NO_PERMISSIONS', $current->name .' ('.$current->username.')'), 'error');
			$app->redirect($red);
			return false;
		}

		$userID = vRequest::getCmd('userID');
		if($manager and !empty($userID) and $userID!=$current->id ){
			if($userID == $id){

			} else if(vmAccess::manager('core',$userID)){
				vmdebug('Manager want to change to  '.$userID,$id,$current);
			//if($newUser->authorise('core.admin', 'com_virtuemart') or $newUser->authorise('vm.user', 'com_virtuemart')){
				$app->enqueueMessage(vmText::sprintf('COM_VIRTUEMART_CART_CHANGE_SHOPPER_NO_PERMISSIONS', $current->name .' ('.$current->username.')'), 'error');
				$app->redirect($red);
			}
		}

		$searchShopper = vRequest::getString('searchShopper');

		if(!empty($searchShopper)){
			$this->display();
			return false;
		}

		//update session
		$session = JFactory::getSession();
		$adminID = $session->get('vmAdminID');
		if(!isset($adminID)) {
			if(!class_exists('vmCrypt'))
				require(VMPATH_ADMIN.DS.'helpers'.DS.'vmcrypt.php');
			$session->set('vmAdminID', vmCrypt::encrypt($current->id));
		}

		if(!empty($userID)){
			$newUser = JFactory::getUser($userID);
			$session->set('user', $newUser);
		} else {
			$newUser = new stdClass();
			$newUser->email = '';
		}


		//update cart data
		$cart = VirtueMartCart::getCart();
		$usermodel = VmModel::getModel('user');
		$data = $usermodel->getUserAddressList($userID, 'BT');

		if(isset($data[0])){
			foreach($data[0] as $k => $v) {
				$data[$k] = $v;
			}
		}

		$cart->BT['email'] = $newUser->email;

		$cart->ST = 0;
		$cart->STsameAsBT = 1;
		$cart->selected_shipto = 0;
		$cart->virtuemart_shipmentmethod_id = 0;
		$cart->saveAddressInCart($data, 'BT');

		$msg = vmText::sprintf('COM_VIRTUEMART_CART_CHANGED_SHOPPER_SUCCESSFULLY', $newUser->name .' ('.$newUser->username.')');

		if(empty($userID)){
			$red = JRoute::_('index.php?option=com_virtuemart&view=user&task=editaddresscart&addrtype=BT&new=1');
			$msg = vmText::sprintf('COM_VIRTUEMART_CART_CHANGED_SHOPPER_SUCCESSFULLY','');
		}

		$app->enqueueMessage($msg, 'info');
		$app->redirect($red);
	}


	function cancel() {

		$cart = VirtueMartCart::getCart();
		if ($cart) {
			$cart->setOutOfCheckout();
		}
		$this->display();
	}

}

//pure php no Tag