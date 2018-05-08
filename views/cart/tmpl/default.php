<?php
/**
 *
 * Layout for the shopping cart
 *
 * @package    VirtueMart
 * @subpackage Cart
 * @author Max Milbers
 *
 * @link ${PHING.VM.MAINTAINERURL}
 * @copyright Copyright (c) 2004 - 2016 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: cart.php 2551 2010-09-30 18:52:40Z milbo $
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');

vmJsApi::vmValidator();
if(!class_exists('FxbotmarketConfig')) {
  include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/config.php';
  }
  $config_p = new FxbotmarketConfig();
  $bitcoin_annual_percent_amount = $config_p->get('bitcoin_annual_percent_amount');
 if(!class_exists('FxbotmarketProduct')) {
  include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/product.php';
  }
  if(is_object($this->cart) && isset($this->cart->cartPrices) && array_key_exists('salesPriceCoupon', $this->cart->cartPrices)){
    $price_coupon = $this->cart->cartPrices['salesPriceCoupon'];//FxbotmarketProduct::formatMoney($this->cart->cartPrices['salesPriceCoupon'],2); 
  }else{
      $price_coupon = 0;//FxbotmarketProduct::formatMoney(0,2);
  }
  $feepertransaction = FxbotmarketProduct::getFeePerTransaction();
  
  $base_price = $this->cart->cartPrices['basePrice'];//$this->cart->cartPrices['basePrice']
  $bitcoin_price = FxbotmarketProduct::formatMoney($base_price + ($base_price )/100*$bitcoin_annual_percent_amount,2);
  $bitcoin_annual_total = FxbotmarketProduct::formatMoney((($base_price + $feepertransaction + $base_price /100*$bitcoin_annual_percent_amount ) * 12 + $price_coupon),2);
  $bitcoin_annual_total_without_flat = FxbotmarketProduct::formatMoney(($base_price + ($base_price )/100*$bitcoin_annual_percent_amount ) * 12,2);
  $bitcoin_flat_annual = FxbotmarketProduct::formatMoney($feepertransaction * 12, 2) ;
  $base_and_flat = FxbotmarketProduct::formatMoney($base_price + $feepertransaction,2);
  $base_price = FxbotmarketProduct::formatMoney($base_price,2);
  $feepertransaction = FxbotmarketProduct::formatMoney($feepertransaction,2);
  if(is_object($this->cart) && isset($this->cart->cartPrices) && array_key_exists('billTotal', $this->cart->cartPrices)){
    $bill_total = FxbotmarketProduct::formatMoney($this->cart->cartPrices['billTotal'],2); 
  }else{
      $bill_total = FxbotmarketProduct::formatMoney($base_price,2);
  }
  
  //[salesPriceCoupon]	float	-31	
  
?>
 <?php
    $doc=JFactory::getDocument();
    $doc->addStyleSheet(JURI::base()."plugins/system/vmfxbot/assets/vmfxbot.css");
    $doc->addStyleSheet(JURI::base()."components/com_fxbotmarket/assets/tooltip.css");
    $doc->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css');
    $doc->addScript('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js');
    ?>

<div class="vm-cart-header-container">
	<div class="width50 floatleft vm-cart-header">
		<h1><?php echo vmText::_ ('COM_VIRTUEMART_CART_TITLE'); ?></h1>
		<div class="payments-signin-button" ></div>
                <div>
            <span style="color:red;">You can only buy one product at a time.</span> 
            </div>
	</div>
	<?php 
        if (VmConfig::get ('oncheckout_show_steps', 1) ){
		if($this->checkout_task == 'checkout') {
			echo '<div class="checkoutStep" id="checkoutStep1">' . vmText::_ ('COM_VIRTUEMART_USER_FORM_CART_STEP1') . '</div>';
		} else { //if($this->checkout_task == 'confirm') {
			echo '<div class="checkoutStep" id="checkoutStep4">' . vmText::_ ('COM_VIRTUEMART_USER_FORM_CART_STEP4') . '</div>';
		}
	}  ?>
	<div class="width50 floatleft right vm-continue-shopping">
		<?php // Continue Shopping Button
		if (!empty($this->continue_link_html)) {
			echo $this->continue_link_html;
		} ?>
            
	</div>
	<div class="clear"></div>
</div>

<div id="cart-view" class="cart-view">


	<?php
	$uri = vmUri::getCurrentUrlBy('get');
	$uri = str_replace(array('?tmpl=component','&tmpl=component'),'',$uri);
	echo shopFunctionsF::getLoginForm ($this->cart, FALSE,$uri);

	// This displays the form to change the current shopper
	if ($this->allowChangeShopper and !$this->isPdf){
		echo $this->loadTemplate ('shopperform');
	}


	$taskRoute = '';
	?><form method="post" id="checkoutForm" name="checkoutForm" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=cart' . $taskRoute, $this->useXHTML, $this->useSSL); ?>">
            
             <?php 
               $user = JFactory::getUser() ;
               if(is_object($user)){
                   $id_user = $user->id;
               }else{
                   $id_user = 0;
               }
               /*if(isset($this->cart) && isset($this->cart->products) && count($this->cart->products[0]) > 0 ){
                    $virtuemart_product_id = $this->cart->products[0]->virtuemart_product_id;
               }else{
                   $virtuemart_product_id = 0;
               }*/
               $virtuemart_product_id = 0;
               
               if(isset($this->cart) && isset($this->cart->products) ){
                   $cnt = count($this->cart->products);
                   if($cnt > 1){
                       $i_ = 1;
                       foreach($this->cart->products as $index=>$prod){
                            //$virtuemart_product_id = $prod->virtuemart_product_id;
                            if($i_ < $cnt){
                                $this->cart->removeProductCart($index);
                            }
                            $i_++;
                        }
                   }
                   foreach($this->cart->products as $prod){
                       $virtuemart_product_id = $prod->virtuemart_product_id;
                       break;
                   }
               }
               //i need to check if product is signal
               //move to helper file
    $db = JFactory::getDbo();
    //select a.id , b.* FROM fxbotmarketx_signals as a INNER JOIN fxbotmarketx_duplicum as b ON a.id_product = b.id_product WHERE a.id_product = 20
    /*$q = 'SELECT b.id as signal_id, c.* FROM #__fxbotmarketx_files_products as a LEFT JOIN #__fxbotmarketx_signals as b '
    . ' ON a.id = b.id_product LEFT JOIN #__fxbotmarketx_duplicum as c '
    . ' ON a.id=c.id_product WHERE a.product_id = '.$virtuemart_product_id;
    $db->setQuery($q);
    $signal_product = $db->loadObject();
        if(is_object($signal_product) && $signal_product->signal_id > 0 && $id_user > 0){
            */
            //get stored mt account of this user
    //check how many products already bought by this customer
    $q = 'SELECT count(*) FROM #__fxbotmarketx_files_products as a INNER JOIN #__fxbotmarketx_signal_orders b '
    . ' ON a.id = b.id_product WHERE a.product_id = '.$virtuemart_product_id.' AND b.paid_status = 1 and b.id_customer = '.(int)$id_user;
    $db->setQuery($q);
    $cnt = $db->loadResult();
        ?>
            <?php if($cnt > 0){ ?>   
        <div class="alert alert-error payment-notice">
                    <a class="close" data-dismiss="alert">×</a>
			<h4 class="alert-heading" >WARNING</h4>
			<div>															
                            <div class="alert-message">
                                You already <a href="<?php echo JRoute::_('index.php?option=com_fxbotmarket&view=customersignals'); ?>">BOUGHT</a> this product before. Are you sure that you want to buy this product TWICE?    
                            </div>
                        </div>
	</div> 	
        <?php } ?> 
           
            
    <?php
    if(!class_exists('VirtuemartFxbotHelper')) include_once JPATH_ROOT.'/plugins/system/vmfxbot/helpers/fxbot.php';
    if(VirtuemartFxbotHelper::checkIfSignalByVirtId($virtuemart_product_id) && $id_user > 0){
            $q = 'SELECT * FROM #__fxbotmarketx_mt WHERE id_user ='.$id_user.' AND deleted = 0';
            //var_dump($q);
            $db->setQuery($q);
            $stored_mt_accounts = $db->loadObjectList();
            $count_stored_mt_accounts = count($stored_mt_accounts);
            /*$cart->mtbroker = $mtbroker;
                $cart->mtserver = $mtserver;
                $cart->mtlogin = $mtlogin;
                $cart->mtpassword = $mtpassword;*/
            $mtbroker = 'mt4';
            $session = JFactory::getSession();
            $broker = $session->get('mtcart.mtbroker','');
            $lmaxmode = $session->get('mtcart.lmaxmode','');
            $session->set('mtcart.mtbroker', '');
            $mtserver = $session->get('mtcart.mtserver','');
            $session->set('mtcart.mtserver', '');
            $mtlogin = $session->get('mtcart.mtlogin','');
            $session->set('mtcart.mtlogin', '');
            $mtpassword = $session->get('mtcart.mtpassword','');
            $session->set('mtcart.mtpassword', '');
            $mt_account_type = $session->get('mtcart.mt_account_type',-1);
            $session->set('mtcart.mt_account_type', -1);
            $id_mt_account = $session->get('mtcart.id_mt_account',0);//id of mt account in database
            $session->set('mtcart.id_mt_account', 0);
            $risk_factor_type = $session->get('mtcart.mt_risk_factors','');//id of mt account in database
            $session->set('mtcart.mt_risk_factors', '');
            $mt_risk_value = $session->get('mtcart.mt_risk_value','');//id of mt account in database
            $session->set('mtcart.mt_risk_value', '');
            $fxpaymentmethod = $session->get('mtcart.fxpaymentmethod',0);
            /*$session->set('mtcart.mtbroker', $mtbroker);
                $session->set('mtcart.mtserver', $mtserver);
                $session->set('mtcart.mtlogin', $mtlogin);
                $session->set('mtcart.mtpassword', $mtpassword);
                there can be 3 cases :mt_account_type == 2
             * mt_account_type == 1
             * mt_account_type == 0
             * if mt_account_type == 2 then  i need to select manual radibutton, display manual div and set values of fields
             * if mt_account_type == 1 then  i need to select Stored account radibutton, display Select stored account div and set value of selectbox
             * if mt_account_type == 0 then  i need to select manual radibutton, display manual div and set values of fields
             * but actually rules are more complicated : when user does not have any acccounts 
             * then i need to hide mt_stored_set
             *  mt_account_type == 1 stored in database	            
             */
            ?>
            
        <?php if($mt_account_type == 0 && $count_stored_mt_accounts > 0 && false){ ?>   
        <div class="alert alert-warning payment-notice">
                    <a class="close" data-dismiss="alert">×</a>
			<h4 class="alert-heading">Notice</h4>
			<div>															
                            <div class="alert-message">
                                You need to point out Your broker account before you buy signal product!!! <br/>
                                Please select one of two options below.    
                            </div>
                        </div>
	</div> 	
        <?php } ?> 
            
<div class="cart_parent_container">
    <div class="cart_left_side">
        <?php if($count_stored_mt_accounts > 0){ ?>
            <div style="width:50%;margin:auto;">
                              <p ><input type="radio" name="mt_account_type" id="mt_stored" value="1" <?php if ($mt_account_type == 1 || $mt_account_type == -1){ echo 'checked="checked"'; } else{ echo '';} ?> onclick="mtaccountswitch();"/><label for="mt_stored">Stored account</label></p>
                              <p ><input type="radio" name="mt_account_type" id="mt_manual" value="2" <?php if ($mt_account_type != 1 && $mt_account_type != -1){ echo 'checked="checked"'; } else{ echo '';} ?>   onclick="mtaccountswitch();"/><label for="mt_manual">Enter manual</label></p>
            </div>
        <?php   }else{ ?>
           <input type="hidden" name="mt_account_type" value="3" />
      <?php  } ?>         
           <div id="mt_manual_set"  <?php if ($mt_account_type == 1 || ($mt_account_type == -1 && $count_stored_mt_accounts > 0)){ echo 'style="display:none"'; }  ?>  >
                <label for="mtbroker">Broker <span class="question_right" name="Enter your account details here.  This is the account that the robot will be copied to.">i</span></label>
                <div id="mtbroker-div" class="fxform-control">
                   <?php 
                    
                    echo    VirtuemartFxbotHelper::getBrokers('mtbroker', 'mtbroker',$broker , '');
                    
                        ?>
                </div>
                <label for="lmaxmode" style="<?php echo $broker != 'lmax'? 'display:none;':'';  ?>" class="lmaxmodehide">Lmax mode <span class="question_right lmaxmodehide" name="Select demo or live.">i</span></label>
                <div id="lmaxmode-div" class="fxform-control">
                    <?php 
                    
                    echo    VirtuemartFxbotHelper::getLmaxModeSelect($lmaxmode,$broker);
                    
                        ?>
                </div>
                
                <label for="mtserver" class="mtserverhide" style="<?php 
                
                echo $broker != 'mt4'? 'display:none;':'';  
                
                ?>">Server</label>
                <div id="mtserver-div" class="fxform-control mtserverhide"  style="<?php 
                echo $broker != 'mt4'? 'display:none;':'';  
                ?>">
                    
                    <select class="js-example-basic-single" name="mtserver">
                    <?php
                        if(strlen($mtserver) > 0){
                            echo "<option selected='selected' value='".$mtserver."'>".$mtserver."</option>";
                        }
                        //echo $this->getServersOptions($mtserver);
                    ?>
                    </select>
                </div>
                <label for="mtlogin" <?php 
                
                echo $broker == 'ctrader'? 'style="display:none;"':'';  
                
                ?> id="mtlogin-lbl">Login</label>
                <div id="mtlogin-div" class="fxform-control" <?php 
                
                echo $broker == 'ctrader'? 'style="display:none;"':'';  
                
                ?>>
                    <input type="text" name="mtlogin" value="<?php 
                    echo $mtlogin; ?>" 
                    <?php 
                    if($broker != 'ctrader'){
                            echo 'required="required"';  
                    }
                    ?>
                    />
                </div>
                <label for="mtpassword" id="mtpasswd-lbl" <?php 
                
                echo $broker == 'ctrader'? 'style="display:none;"':'';  
                
                ?> >Password</label>
                <div id="mtpassword-div" class="fxform-control" <?php 
                
                echo $broker == 'ctrader'? 'style="display:none;"':'';  
                
                ?> >
                    <input type="text" name="mtpassword" value="<?php 
                    echo $mtpassword; ?>" <?php 
                    if($broker != 'ctrader'){
                            echo 'required="required"';  
                    }
                    ?>/>
                </div>
            </div>
            <?php if($count_stored_mt_accounts > 0){?>
                <div id="mt_stored_set"   class="fxform-control" <?php if ($mt_account_type <> 1 && $mt_account_type != -1){ echo 'style="display:none"'; }  ?> >
                    <select id="mt_stored_accounts" name="mt_stored_accounts">
                        <option value="" selected="selected">Select stored account</option>
                        <?php foreach($stored_mt_accounts as $account){ ?>
                        <option value="<?php echo $account->id; ?>" <?php if($id_mt_account == $account->id) {echo 'selected="selected"';} ?>><?php echo ($account->mttype==2? "MT5":"MT4")." ".$account->serverip." ".$account->username." "; ?></option>
                        <?php } ?>
                    </select>
                </div> 
            <?php } ?>
           <label for="mt_risk_value">Trade Copy Allocation Method <span class="question_right" name="Trade Copy Allocation Method">i</span></label>
           
            <div id="mt_risktype_div" class="fxform-control">
                <?php 
                   echo VirtuemartFxbotHelper::getRiskFactors('mt_risk_factors', 'mt_risk_factors',$risk_factor_type , '');
                ?>
            </div>
           <label for="mt_risk_value">Risk Level <span class="question_right" name="It can be within range -99.99 to 99.99 with step 0.01.">i</span></label>
            <div id="mt_riskval_div" class="fxform-control">
                <?php 
                   echo VirtuemartFxbotHelper::getRiskFactorValues('mt_risk_value', 'mt_risk_value',$mt_risk_value , '');
                ?>
            </div>
            <?php 
        }//if(is_object($signal_product) && $signal_product->signal_id > 0){
            ?>
    </div>
    <div class="checkout-button-top"> <?php
           $hiddenFields = '';

                if(!empty($this->userFieldsCart['fields']) ) {

                        // Output: Userfields
                        foreach($this->userFieldsCart['fields'] as $field) {
                            if($field['name'] != 'tos'){
                                continue;
                            }
                        ?>
                        <fieldset class="vm-fieldset-<?php echo str_replace('_','-',$field['name']) ?>">
                                <div  class="cart <?php echo str_replace('_','-',$field['name']) ?>" title="<?php echo strip_tags($field['description']) ?>">
                                <span class="cart <?php echo str_replace('_','-',$field['name']) ?>" ><?php echo $field['title'] ?></span>

                                <?php
                                if ($field['hidden'] == true) {
                                        // We collect all hidden fields
                                        // and output them at the end
                                        $hiddenFields .= $field['formcode'] . "\n";
                                } else { ?>
                                                <?php echo $field['formcode'] ?>
                                        </div>
                        <?php } ?>

                        </fieldset>

                        <?php
                        }
                        // Output: Hidden Fields
                        echo $hiddenFields;
                }
           
           
           
			echo $this->checkout_link_html;
			?>
               <div class="fxbot_top_price_div" >
                <?php 
                echo '<span class="fxbot_top_price_label" >Total (monthly):</span><div class="fxbot_inner_price_div">'.$this->currencyDisplay->createPriceDiv ('billTotal', '', $this->cart->cartPrices['billTotal'], FALSE).'</div>'; ?>
                </div>
    </div>
            <div class="fxbot_payment_sw">
                <?php 
                /*    
                $paypalchecked = 'checked';
                    $cardchecked = '';

                        if ($fxpaymentmethod == 1 ){
                            $paypalchecked = '';
                            $cardchecked = 'checked';
                        }
                        */
                        switch($fxpaymentmethod){
                            case 1:$paypalchecked = '';
                                    $cardchecked = 'checked';
                                    $bitcoinchecked = '';
                                    $methodval = 1;
                                    break;
                            case 2:$paypalchecked = '';
                                    $cardchecked = '';
                                    $bitcoinchecked = 'checked';
                                    $methodval = 2;
                                    break;
                            default:
                                $paypalchecked = 'checked';
                                    $cardchecked = '';
                                    $bitcoinchecked = '';
                                    $methodval = 0;
                                    break;
                        }
                        if(true){
                ?>
                <p><input name="fxpaymentmethod" value="1" id="creditid"  onclick="selectMethod(1);" type="radio" <?php echo $cardchecked; ?>> Credit Card</p>
                <p><input name="fxpaymentmethod" value="0" id="paypalid" onclick="selectMethod(0);" type="radio" <?php echo $paypalchecked; ?>> Paypal</p>
                <?php if(false){//BITCOIN OPTION IS NOT SUPPORTED BY STRIPE ?>
                <p><input name="fxpaymentmethod" value="2" id="bitcoinid" onclick="selectMethod(2);" type="radio" <?php echo $bitcoinchecked; ?>> Bitcoin (Annual payment)<span style="color:red;"> Note: You can not get refund when you will want to cancel this subscription.</span></p>
                <?php } ?>
            <input type="hidden" id="fx_price" value="<?php echo $base_price; ?>"/>
            <input type="hidden" id="fx_quantity" value="1"/>
            <input type="hidden" id="fx_bitcoin_discount" value="<?php echo $bitcoin_annual_percent_amount; ?>"/>
            <input type="hidden" id="fx_flatrate" value="<?php echo $feepertransaction; ?>"/>
            <input type="hidden" id="fx_bitcoin_annual_total" value="<?php echo $bitcoin_annual_total; ?>"/>
            <input type="hidden" id="fx_bitcoin_annual_minus_flat" value="<?php echo $bitcoin_annual_total_without_flat; ?>"/>
            <input type="hidden" id="fx_bitcoin_flat_annual" value="<?php echo $bitcoin_flat_annual; ?>"/>
            <input type="hidden" id="fx_bitcoin_price" value="<?php echo $bitcoin_price; ?>"/>
            <input type="hidden" id="fx_base_and_flat" value="<?php echo $base_and_flat; ?>"/>
            <input type="hidden" id="fx_bill_total" value="<?php echo $bill_total; ?>"/>
            <input type="hidden" id="fx_price_coupon" value="<?php echo $price_coupon; ?>"/>
            
            
                        <?php } ?>
            </div>
            <script type="text/javascript">
                jQuery( document ).ready(function() {
                    selectMethod(<?php echo $methodval; ?>);
                });
                function selectMethod(arg){
                        var quantity = document.querySelector('td.vm-cart-item-quantity');
                        var fx_price = document.querySelector('#fx_price');
                        var fx_bill_total = document.querySelector('#fx_bill_total');
                        if(fx_bill_total){
                            var fx_bill_total_v = fx_bill_total.value;
                        }else{
                            fx_bill_total_v = fx_price.value;
                        }//alert(fx_bill_total_v);
                        var fxprice = fx_price.value;                
                        var fx_flatrate = document.querySelector('#fx_flatrate');
                        var fxflat = fx_flatrate.value;
                        var PricesalesPrice = document.querySelectorAll('span.PricesalesPrice');
                        var PricebillTotal = document.querySelectorAll('span.PricebillTotal');
                        //////////
                        var fx_bitcoin_annual_total = document.querySelector('#fx_bitcoin_annual_total').value;
                        var fx_bitcoin_annual_total_without_flat = document.querySelector('#fx_bitcoin_annual_minus_flat').value;
                        var fx_price_coupon = document.querySelector('#fx_price_coupon').value;
                        var h4 = document.querySelectorAll('h4');
                        
                        var PricesalesPricePayment = document.querySelector('span.PricesalesPricePayment');
                        var PricediscountedPriceWithoutTax = document.querySelector('span.PricediscountedPriceWithoutTax');
                        
                        var fx_bitcoin_flat_annual = document.querySelector('#fx_bitcoin_flat_annual').value;
                        var fx_bitcoin_price = document.querySelector('#fx_bitcoin_price').value;
                        var fx_base_and_flat = document.querySelector('#fx_base_and_flat').value;
                    if(arg == 2){
                        if(quantity){
                            quantity.innerHTML = '<input class="quantity-input js-recalculate" size="3" maxlength="4" name="quantity[0]" value="1" type="hidden"> 12   ';
                            PricebillTotal.innerHTML = '$' + fx_bitcoin_annual_total;
                            PricesalesPricePayment.innerHTML = '$' + fx_bitcoin_flat_annual;
                            PricediscountedPriceWithoutTax.innerHTML = '$' + fx_bitcoin_price;
                            for(var ii = 0; ii < PricebillTotal.length; ii++){
                                PricebillTotal[ii].innerHTML = '$' + fx_bitcoin_annual_total;
                            }
                            for(var ii = 0; ii < PricesalesPrice.length; ii++){
                                PricesalesPrice[ii].innerHTML = '$' + fx_bitcoin_annual_total_without_flat;
                            }
                            for(var ii = 0; ii < h4.length; ii++){
                                var txt = h4[ii].innerHTML;// = '$' + fx_bitcoin_annual_total_without_flat;
                                if(txt == 'FLAT FEE (Each month)'){
                                    h4[ii].innerHTML = 'FLAT FEE (Annual)';
                                }
                            }
                        }
                    }else{
                        if(quantity){
                            quantity.innerHTML = '<input class="quantity-input js-recalculate" size="3" maxlength="4" name="quantity[0]" value="1" type="hidden"> 1   ';
                            PricebillTotal.innerHTML = '$' + fx_bill_total_v;//fxprice;
                            PricesalesPricePayment.innerHTML = '$' + fxflat;
                            PricediscountedPriceWithoutTax.innerHTML = '$' + fxprice;
                            for(var ii = 0; ii < PricesalesPrice.length; ii++){
                                PricesalesPrice[ii].innerHTML = '$' + fxprice ;
                            }
                            for(var ii = 0; ii < PricebillTotal.length; ii++){
                                PricebillTotal[ii].innerHTML = '$' + fx_bill_total_v;//fx_base_and_flat;
                            }
                            for(var ii = 0; ii < h4.length; ii++){
                                var txt = h4[ii].innerHTML;// = '$' + fx_bitcoin_annual_total_without_flat;
                                if(txt == 'FLAT FEE (Annual)'){
                                    h4[ii].innerHTML = 'FLAT FEE (Each month)';
                                }
                            }
                        }
                    }
                }
            </script>
</div>
		<?php
		if(!$this->isPdf and VmConfig::get('multixcart')=='byselection'){
			if (!class_exists('ShopFunctions')) require(VMPATH_ADMIN . DS . 'helpers' . DS . 'shopfunctions.php');
			echo shopFunctions::renderVendorFullVendorList($this->cart->vendorId);
			?><input type="submit" name="updatecart" title="<?php echo vmText::_('COM_VIRTUEMART_SAVE'); ?>" value="<?php echo vmText::_('COM_VIRTUEMART_SAVE'); ?>" class="button"  style="margin-left: 10px;"/><?php
		}
		echo $this->loadTemplate ('address');
		// This displays the pricelist MUST be done with tables, because it is also used for the emails
		echo $this->loadTemplate ('pricelist');

		if (!empty($this->checkoutAdvertise)) {
			?> <div id="checkout-advertise-box"> <?php
			foreach ($this->checkoutAdvertise as $checkoutAdvertise) {
				?>
				<div class="checkout-advertise">
					<?php echo $checkoutAdvertise; ?>
				</div>
			<?php
			}
			?></div><?php
		}

		echo $this->loadTemplate ('cartfields');

		?> 
                        
		<?php // Continue and Checkout Button END ?>
		<input type='hidden' name='order_language' value='<?php echo $this->order_language; ?>'/>
		<input type='hidden' name='task' value='updatecart'/>
		<input type='hidden' name='option' value='com_virtuemart'/>
		<input type='hidden' name='view' value='cart'/>
	</form>


<?php

if(VmConfig::get('oncheckout_ajax',false)){
	vmJsApi::addJScript('updDynamicListeners',"
if (typeof Virtuemart.containerSelector === 'undefined') Virtuemart.containerSelector = '#cart-view';
if (typeof Virtuemart.container === 'undefined') Virtuemart.container = jQuery(Virtuemart.containerSelector);

jQuery(document).ready(function() {
	if (Virtuemart.container)
		Virtuemart.updDynFormListeners();
}); ");
}


vmJsApi::addJScript('vm.checkoutFormSubmit',"
Virtuemart.bCheckoutButton = function(e) {
	e.preventDefault();
	jQuery(this).vm2front('startVmLoading');
	jQuery(this).attr('disabled', 'true');
	jQuery(this).removeClass( 'vm-button-correct' );
	jQuery(this).addClass( 'vm-button' );
	jQuery(this).fadeIn( 400 );
	var name = jQuery(this).attr('name');
	var div = '<input name=\"'+name+'\" value=\"1\" type=\"hidden\">';

	jQuery('#checkoutForm').append(div);
	//Virtuemart.updForm();
	jQuery('#checkoutForm').submit();
}
jQuery(document).ready(function($) {
	jQuery(this).vm2front('stopVmLoading');
	var el = jQuery('#checkoutFormSubmit');
	el.unbind('click dblclick');
	el.on('click dblclick',Virtuemart.bCheckoutButton);
});
	");

if( !VmConfig::get('oncheckout_ajax',false)) {
	vmJsApi::addJScript('vm.STisBT',"
		jQuery(document).ready(function($) {

			if ( $('#STsameAsBTjs').is(':checked') ) {
				$('#output-shipto-display').hide();
			} else {
				$('#output-shipto-display').show();
			}
			$('#STsameAsBTjs').click(function(event) {
				if($(this).is(':checked')){
					$('#STsameAsBT').val('1') ;
					$('#output-shipto-display').hide();
				} else {
					$('#STsameAsBT').val('0') ;
					$('#output-shipto-display').show();
				}
				var form = jQuery('#checkoutFormSubmit');
				form.submit();
			});
		});
	");
}

$this->addCheckRequiredJs();
?><div style="display:none;" id="cart-js">
<?php echo vmJsApi::writeJS(); ?>
</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function() {	
	//checkoutFormSubmit
        /*jQuery("#checkoutFormSubmit").click(function(event){
          var result = confirm("Are You sure?");
          if(result){
              return true;
          }else{
              event.preventDefault();
              return false;
          }
      }); */
});    
function mtaccountswitch(){
        if(document.getElementById('mt_manual').checked){
            //carddiv
            jQuery('#mt_manual_set').show();
            jQuery('#mt_stored_set').hide();
        }else{
            //paypaldiv
            jQuery('#mt_stored_set').show();
            jQuery('#mt_manual_set').hide();
        }
    }
    function InitToolTips(){
	jQuery("span.question_left").hover(function () {
		jQuery(this).append('<div class="mytooltip_left"><p>'+jQuery(this).attr("name")+'</p></div>');
		  }, function () {
			jQuery("div.mytooltip_left").remove();
	  });
	jQuery("span.question_right").hover(function () {
		jQuery(this).append('<div class="mytooltip_right"><p>'+jQuery(this).attr("name")+'</p></div>');
		  }, function () {
			jQuery("div.mytooltip_right").remove();
	  });
}
    jQuery(document).ready(function($){
        InitToolTips();
        //jQuery('.js-example-basic-single').select2({tags:true});
        $(".js-example-basic-single").select2({
  ajax: {
    url: "<?php echo JURI::base();?>index.php?option=com_fxbotmarket&task=customersignal.getserverlist",
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return {
        q: params.term, // search term
        page: params.page
      };
    },
    results:function (data, params) {
    //processResults: function (data, params) {
      // parse the results into the format expected by Select2
      // since we are using custom formatting functions we do not need to
      // alter the remote JSON data, except to indicate that infinite
      // scrolling can be used
      /*params.page = params.page || 1;

      return {
        results: data.items,
        pagination: {
          more: (params.page * 30) < data.total_count
        }
      };
      */
     console.log(data);
     return {
        results: data.items,
      };
    },
    cache: true
  },
  placeholder: 'Search for a server',
  minimumInputLength: 1,tags:true
});
        
	jQuery('b[role="presentation"]').hide();
        jQuery("span:contains('Sandbox (1)')").hide();
        //jQuery('.select2-search__field').css('height: 30px;');
    }
    );
</script>

 <script type="text/javascript">
                jQuery( document ).ready(function() {
                    //selectMethod(<?php echo $methodval; ?>);
                    
                    jQuery('.continue_link').click(function(event){
                        if(confirm('Please note: You can only buy one product at a time! You will discard current purchase when you click YES. Are you sure? ')){
                            return true;
                        }else{
                            event.preventDefault();
                        }
                       //alert(this.innerHTML); 
                       return true;
                    });
                });

                
</script>  