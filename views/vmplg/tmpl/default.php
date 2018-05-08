<?php

/**
 *
 * Show Confirmation message from Offlien Payment
 *
 * @package	VirtueMart
 * @subpackage
 * @author Valerie Isaksen
 * @link ${PHING.VM.MAINTAINERURL}
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 3217 2011-05-12 15:51:19Z alatak $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
if(!class_exists('FxbotmarketProduct')) {
  include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/product.php';
  }
  
if(array_key_exists('fxbotmarket_type_of_product', $GLOBALS)){
    $type_of_product = $GLOBALS['fxbotmarket_type_of_product'];
    if(FxbotmarketProduct::ifDownloadableTypeOfProduct($type_of_product)){//if product is downloadable
        if(array_key_exists('fxbotmarket_d_id', $GLOBALS)){
            $downloadable_id = (int)$GLOBALS['fxbotmarket_d_id'];
            if($downloadable_id > 0){
                $app = JFactory::getApplication();
                $app->redirect(JRoute::_('index.php?option=com_fxbotmarket&view=customerdownloads' , false),'Order successful');
            }
        }
    }elseif($type_of_product == 1001){
        $app = JFactory::getApplication();
        $app->redirect('index.php?option=com_fxbotmarket&view=customerblockpads');
    }
}
echo "<h3>" . $this->paymentResponse . "</h3>";
if ($this->paymentResponseHtml) {
    echo "<fieldset>";
    $this->paymentResponseHtml = str_replace('Sandbox (1)','',$this->paymentResponseHtml);
    echo $this->paymentResponseHtml;
    echo "</fieldset>";
}
if($this->signal_order){
    $s_o = $this->signal_order;
    if($s_o->sell_status == 2){
        echo "<h2><span style='color:green'>Robot successfully copied. You can see your product in <a href='".JRoute::_('index.php?option=com_fxbotmarket&view=customersignals')."' >Dashboard</a></span></h2>";
    }elseif($s_o->sell_status == 1){
        echo "<h2><span style='color:yellow'>Robot  copied. But not activated. ".$this->err_msg."  </span></h2>";
    }else{
        echo "<h2><span style='color:red'>Robot  has not been copied. ".$this->err_msg."  </span></h2>";
    }
    //var_dump($this->signal_order);
}

// add something???

