<?php
/**
 * @package     UltimateUser for Joomla!
 * @author      Stéphane Bouey <stephane.bouey@faboba.com> - http://www.faboba.com
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright   Copyright (C) 2012-2013. All rights reserved.
using:
 * if(!class_exists('VirtuemartFxbotHelper')) {
        include_once JPATH_ROOT.'/plugins/system/vmfxbot/helpers/fxbot.php';
    }
 *  */

defined('_JEXEC') or die;

class VirtuemartFxbotHelper
{
public static function getPaypalAmount(){
            $conf = new UuConfig();
            $fee = $conf->get('seller_payment_fee');
            if(preg_match('~/\d{1,10}(?:\.\d{1,10})?/~',$fee)){
                return $fee;
            }elseif(intval($fee)){
                return $fee;
            }
            return 0;
        }
public static function checkIfSignalByVirtId($virtuemart_product_id = 0){
    $db = JFactory::getDbo();
    //select a.id , b.* FROM fxbotmarketx_signals as a INNER JOIN fxbotmarketx_duplicum as b ON a.id_product = b.id_product WHERE a.id_product = 20
    $q = 'SELECT b.id as signal_id, c.* FROM #__fxbotmarketx_files_products as a LEFT JOIN #__fxbotmarketx_signals as b '
    . ' ON a.id = b.id_product LEFT JOIN #__fxbotmarketx_duplicum as c '
    . ' ON a.id=c.id_product WHERE a.product_id = '.$virtuemart_product_id;
    $db->setQuery($q);
    $signal_product = $db->loadObject();
        if(is_object($signal_product) && $signal_product->signal_id > 0 ){
            return true;
        }
        return false;
}
public static function getSignalByVirtId($virtuemart_product_id = 0){
    $db = JFactory::getDbo();
    //select a.id , b.* FROM fxbotmarketx_signals as a INNER JOIN fxbotmarketx_duplicum as b ON a.id_product = b.id_product WHERE a.id_product = 20
    $q = 'SELECT a.id as product_file_id, b.id as signal_id, c.* FROM #__fxbotmarketx_files_products as a LEFT JOIN #__fxbotmarketx_signals as b '
    . ' ON a.id = b.id_product LEFT JOIN #__fxbotmarketx_duplicum as c '
    . ' ON a.id=c.id_product WHERE a.product_id = '.$virtuemart_product_id;
    $db->setQuery($q);
    $signal_product = $db->loadObject();
        if(is_object($signal_product) && $signal_product->signal_id > 0 ){
            return $signal_product;
        }
        return false;
}
public static function getRiskFactors($id = 'mt_risk_factors', $name ='mt_risk_factors',$value = '', $class=''){
        $selected = 'selected="selected"';
        if($value === ''){
            $value = 0;
        }
        $html = '<select id="'.$id.'" name="'.$name.'">';
        $html .= ' <option value="" '.($value===''? $selected:'').'>Select risk factor type</option>';
        $html .= '<option value="0" '.(($value===0 || $value === '0')? $selected:'').'>Auto Risk (Equity) </option>';
        $html .= '<option value="1" '.($value==1? $selected:'').'>Auto Risk (Balance) </option>';
        $html .= '<option value="2" '.($value==2? $selected:'').'>Auto Risk (Free Margin) </option>';
        $html .= '<option value="3" '.($value==3? $selected:'').'>Fixed Multiplier </option>';
        $html .= '<option value="4" '.($value==4? $selected:'').'>Fixed Lot </option>';
        $html .= '<option value="5" '.($value==5? $selected:'').'>Fixed Leverage (Equity) </option>';
        $html .= '<option value="6" '.($value==6? $selected:'').'>Fixed Leverage (Balance) </option>';
        $html .= '<option value="7" '.($value==7? $selected:'').'>Fixed Leverage (Free Margin) </option>';
        $html .= '</select>';
        return $html;
}
    
    public static function getBrokers($id = 'mtbroker', $name ='mtbroker',$value = '', $class=''){
        $selected = 'selected="selected"';
        
        $html = '<select id="'.$id.'" name="'.$name.'" onclick="selectBroker();">';
        $html .= ' <option value="" '.($value===''? $selected:'').'>Select broker</option>';
        $html .= '<option value="mt4" '.(($value==='MT4' || $value === 'mt4')? $selected:'').'> MT4 </option>';
        $html .= '<option value="lmax" '.($value=='lmax' ? $selected:'').'> lmax </option>';
        $html .= '<option value="ctrader" '.($value=='ctrader'? $selected:'').'> ctrader </option>';
        $html .= '</select>';
        
        
        
        $html .= "<script type='text/javascript'>\n";
        $html .= "function selectBroker(){\n";
        $html .= "var selectbroker = document.querySelector('#".$id."');\n";
        $html .= "if(selectbroker && (selectbroker.selectedIndex == 2 || selectbroker.selectedIndex == 3)){\n";
        $html .= "jQuery('.mtserverhide').hide();\n";
        $html .= "}else{\n";
        $html .= "jQuery('.mtserverhide').show();}\n";
        //$html .= "}\n";
        
        $html .= "var lmaxmode = document.querySelector('#lmaxmode');\n";
        $html .= "if(selectbroker && selectbroker.selectedIndex == 2){\n";
        $html .= "jQuery('.lmaxmodehide').show();\n";
        $html .= "}else{\n";
        $html .= "jQuery('.lmaxmodehide').hide();}\n";
        
        $html .= "if(selectbroker && selectbroker.selectedIndex == 3){\n";
        $html .= "jQuery('#mtlogin-lbl,#mtlogin-div,#mtpassword-div,#mtpasswd-lbl').hide();\n";
        $html .= "}else{\n";
        $html .= "jQuery('#mtlogin-lbl,#mtlogin-div,#mtpassword-div,#mtpasswd-lbl').show();}\n";
        $html .= "}\n";
        
        $html .= "</script>\n";
        return $html;
    }
    
    public static function getLmaxModeSelect($value = 1,$brokervalue = 'mt4'){
        if($brokervalue != 'lmax'){
             $style = "style='display:none;'";
        }else{
            $style = '';
        }
        $selected = 'selected="selected"';
        $html = '<select id="lmaxmode" name="lmaxmode" class="lmaxmodehide" '.$style.' >';
        $sel1 = $value==1? $selected:'';
        $sel2 = $value==2? $selected:'';
        $html .= ' <option value="1" '.$sel1.'>Live</option>';
        $html .= '<option value="2" '.$sel2.'> Demo </option>';
        $html .= '</select>';
        return $html;
    }
    public static function filterBrokers($bval = ''){
        $brokers = array('mt4','lmax','ctrader');
        $res =  in_array($bval, $brokers);
        return $res;
    }
    public static function filterMtData($mtbroker = '',$mtserver='',$mtlogin = '',$mtpassword =''){
        if($mtbroker == 'ctrader'){
            return true;
        }
        if(strlen($mtlogin) == 0 || strlen($mtpassword) == 0){
            return false;
        }
        if((strtolower($mtbroker == 'mt4')) && (strlen($mtserver) == 0 )){
            return false;
        }
        return true;
    }
    public static function getRiskFactorById($value = -1){
        switch($value){
            case 0:return "Auto Risk (Equity)";
            case 1:return "Auto Risk (Balance)";
            case 2:return "Auto Risk (Free Margin)";
            case 3:return "Fixed Multiplier";
            case 4:return "Fixed Lot";
            case 5:return "Fixed Leverage (Equity)";
            case 6:return "Fixed Leverage (Balance)";
            case 7:return "Fixed Leverage (Free Margin)";
            default: return "";
        }
    }
    public static function getRiskFactorValues($id = 'mt_risk_factors', $name ='mt_risk_factors',$value = '', $class=''){
        if($value === '' ){
            $value = 1;
        }
        $html = '<input type="number" required id="'.$id.'" name="'.$name.'" min="-99.99" max="99.99" value="'.$value.'" step="0.01">';
        /*
        $selected = 'selected="selected"';
        if($value === '' ){
            $value = 1;
        }
        $html = '<select id="'.$id.'" name="'.$name.'">';
        $html .= ' <option value="" '.($value===''? $selected:'').'>Select risk factor value</option>';
        $html .= '<option value="0" '.($value==0? $selected:'').'> 0 </option>';
        $html .= '<option value="1" '.($value==1? $selected:'').'> 1 </option>';
        $html .= '<option value="2" '.($value==2? $selected:'').'> 2 </option>';
        $html .= '</select>';
         */
        return $html;
        
    }
    public static function filterOrderNumber($order_number = ''){
        if(strlen($order_number) > 20){
            return '';
        }
        return $order_number;
        /*$matches = array();
         
        if(preg_match('~[^\a-zA-Z\d]~', $order_number, $matches)){
            return '';
        }
        return $order_number;
        */
    }
    
}
