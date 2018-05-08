<?php
/**
*
* View for the PluginResponse
*
* @package	VirtueMart
* @subpackage
* @author Valérie Isaksen
* @link ${PHING.VM.MAINTAINERURL}
* @copyright Copyright (c) 2004 - 2014 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: view.html.php 3386 2011-05-27 12:34:11Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the view framework
if(!class_exists('VmView'))
        require(JPATH_ROOT . '/components/com_virtuemart/helpers/vmview.php');


class VirtueMartViewVmplg extends VmView {
public $signal_order;
public $err_msg = '';
	public function display($tpl = null) {
		$mainframe = JFactory::getApplication();
		$pathway = $mainframe->getPathway();
		$document = JFactory::getDocument();
		$layoutName = $this->getLayout();
		$document->setMetaData('robots','NOINDEX, NOFOLLOW, NOARCHIVE, NOSNIPPET');
                $this->signal_order = $this->getSignalOrder();
                if(is_object($this->signal_order) && isset($this->signal_order->sell_status)){
                if($this->signal_order->sell_status < 2){
                    if(!class_exists('FxbotmarketProductcustomer')) {
                        include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/productcustomer.php';
                      }
                    $productcustomer = new FxbotmarketProductcustomer();
                    $err_code = $productcustomer->getErrorCode();
                    switch($err_code){
                        case 4001:
                            $this->err_msg = 'You have entered account data which already in use. Please correct server/login/password or contact administrator. ';
                            break;
                        case 4006:
                            $this->err_msg = '';
                            break;
                        default:
                            $this->err_msg = ' Please contact administrator. ';
                            break;
                    }
                }
                }
                $this->_path['template'][1]=JPATH_ROOT."/plugins/system/vmfxbot/views/vmplg/tmpl/";//bpm
		parent::display($tpl);
	}
        public function getSignalOrder(){//TODO MOVE THIS TO APPROPRIATE CONTROLLER
            $app = JFactory::getApplication();
            $input = $app->input;
            if(!class_exists('VirtuemartFxbotHelper')) include_once JPATH_ROOT.'/plugins/system/vmfxbot//helpers/fxbot.php';
            $order_number = VirtuemartFxbotHelper::filterOrderNumber($input->get('on',''));
            $db = JFactory::getDbo();
            $q = 'SELECT * FROM #__fxbotmarketx_signal_orders WHERE virtuemart_order_number LIKE '.$db->quote($order_number);
            $db->setQuery($q);
            $signal_order = $db->loadObject();
            if(is_object($signal_order) && $signal_order->id > 0){
                return $signal_order;
            }else{
                return false;
            }
        }
}

//no closing tag