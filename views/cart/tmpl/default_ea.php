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
$doc=JFactory::getDocument();
    $doc->addStyleSheet(JURI::base()."plugins/system/vmfxbot/assets/vmfxbot.css");
    $doc->addStyleSheet(JURI::base()."components/com_fxbotmarket/assets/tooltip.css");
$app = JFactory::getApplication();

if(!class_exists('FxbotmarketProduct')) {
  include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/product.php';
  }
  if(is_object($this->cart) && isset($this->cart->cartPrices) && array_key_exists('salesPriceCoupon', $this->cart->cartPrices)){
    $price_coupon = $this->cart->cartPrices['salesPriceCoupon'];//FxbotmarketProduct::formatMoney($this->cart->cartPrices['salesPriceCoupon'],2); 
  }else{
      $price_coupon = 0;//FxbotmarketProduct::formatMoney(0,2);
  }
    $base_price = $this->cart->cartPrices['basePrice'];//$this->cart->cartPrices['basePrice']
if(is_object($this->cart) && isset($this->cart->cartPrices) && array_key_exists('billTotal', $this->cart->cartPrices)){
    $bill_total = FxbotmarketProduct::formatMoney($this->cart->cartPrices['billTotal'],2); 
  }else{
      $bill_total = FxbotmarketProduct::formatMoney($base_price,2);
  }    
  $session = JFactory::getSession();
  $fxpaymentmethod = $session->get('mtcart.fxpaymentmethod',0);
  $eaaccountnumber = $session->get('mtcart.eaaccountnumber','');
  $eabroker = $session->get('mtcart.eabroker','');
  /*if($eaacc == ''){
                   $app->enqueueMessage('You need to input account number.', 'error');
                    }
                    if($eabroker != 2 || $eabroker != 3){
                        $app->enqueueMessage('You need to select proper MT broker.', 'error');
                        
                    }
$session->set('mtcart.eabroker', $eabroker);
$session->set('mtcart.eaaccountnumber', $eaacc);*/
  /*
   * if(!class_exists('FxbotmarketLogger')) {
                    include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/logger.php';
                  }
                  $logger = new FxbotmarketLogger();
  $logger->logToFile('vmfxbotdefault_downloadable.txt',$fxpaymentmethod);
  */

    ?>

<div class="vm-cart-header-container">
	<div class="width50 floatleft vm-cart-header">
		<h1><?php echo vmText::_ ('COM_VIRTUEMART_CART_TITLE'); ?></h1>
		<div class="payments-signin-button" ></div>
                <div>
            <span style="color:red;">You can only buy one product at a time.</span> 
            </div>
	</div>
	<?php if (VmConfig::get ('oncheckout_show_steps', 1) ){
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
<h3 class="fxbot_cart_product_name_h"> <?php echo $this->selected_rent_msg ?> <span class="fxbot_cart_product_name_s"><?php echo $this->product_name; ?></span></h3>
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
<div class="cart_parent_container">
    <div class="cart_left_side"> 
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
                <p><input name="fxpaymentmethod" value="2" id="bitcoinid" onclick="selectMethod(2);" type="radio" <?php echo $bitcoinchecked; ?>> Bitcoin </p>
                <?php } ?>
                
            <input type="hidden" id="fx_price" value="<?php echo $base_price; ?>"/>
            <input type="hidden" id="fx_quantity" value="1"/>
            <input type="hidden" id="fx_bill_total" value="<?php echo $bill_total; ?>"/>
            <input type="hidden" id="fx_price_coupon" value="<?php echo $price_coupon; ?>"/>
            
            
                        <?php } ?>
            </div>
        <div id="mt_manual_set">
                
                <label for="eabroker" id="mtlogin-lbl">Select broker <span class="question_right" name="You need select one of value">i</span></label>
                <div id="mtlogin-div" class="fxform-control" >
                    <?php 
                    $value = $eabroker;
                    $selected = 'selected="selected"';
                        $html = '<select id="eabroker" name="eabroker" >';
                        $html .= ' <option value="" '.($value===''? $selected:'').'>Select broker</option>';
                        $html .= ' <option value="2" '.(($value==='2' || $value === 2)? $selected:'').'> MT4 </option>';
                        $html .= ' <option value="3" '.(($value=='3' ||  $value === 3)? $selected:'').'> MT5 </option>';
                        $html .= '</select>';
                        echo $html;
                        
                    ?> 

                </div>
                <label for="eaacc" id="eaacc-lbl" <?php ?> >Account number <span class="question_right" name="Enter some of your accounts">i</span></label>
                <div id="mtaccnum-div" class="fxform-control"  >
                    <input type="text" name="eaacc" value="<?php 
                    echo $eaaccountnumber; ?>" <?php 
                            echo 'required="required"';  
                    
                    ?>/>
                </div>
        </div>
    </div> 
    <div class="checkout-button-top"> 
        <?php
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
                echo '<span class="fxbot_top_price_label" >Total:</span><div class="fxbot_inner_price_div">'.$this->currencyDisplay->createPriceDiv ('billTotal', '', $this->cart->cartPrices['billTotal'], FALSE).'</div>'; ?>
                </div>
    </div>
    
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
                function selectMethod(arg){

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
		echo $this->loadTemplate ('pricelist_ea');

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