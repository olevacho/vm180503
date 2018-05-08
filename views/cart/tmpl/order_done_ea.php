<?php
defined('_JEXEC') or die('');

/**
*
* Template for the shopping cart
*
* @package	VirtueMart
* @subpackage Cart
* @author Max Milbers
*
* @link ${PHING.VM.MAINTAINERURL}
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/
if(!class_exists('FxbotmarketProduct')) {
  include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/product.php';
  }
if(FxbotmarketProduct::ifEaTypeOfProduct($this->product_type) ){//checks if product is downloadable
    if($this->fxpaymentmethod == 1){//if payment method is stripe card 
        

                    echo '<h3 class="confirm_order_thanks" style="color:red;">'.vmText::_('COM_VIRTUEMART_CART_ORDERDONE_THANK_YOU').' Please click on button below to continue.</h3>';

                ?>

                <form action="<?php 
                echo JRoute::_ ('index.php?option=com_fxbotmarket&task=paystripe.payea&orderid='.(int)$this->fxbotmarket_id_downloadable_order);?>" method="POST">
                    <input type="hidden" name="idev_custom[ip]" id="idev_custom_x21" />
                    <script type="text/javascript" src="https://fxbot.market/partners/connect/stripe_ip.php"></script>
                    <script
                      src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                      data-key="<?php echo $this->stripe_public_key; ?>"
                      data-image="https://fxbot.market/images/FXbot-3D-robot_1sm.png"
                      data-name="Fxbot Market"
                      data-description="Subscription for 1 montly box"
                      data-amount="<?php echo $this->product_price; ?>"
                      data-label="Pay now!"
                      data-email ="<?php 
                      $user = JFactory::getUser();
                      $email = $user->email;
                      echo $email;
                      ?>">
                    </script>
                                <?php echo JHtml::_('form.token'); ?>
                </form>
        <?php
            
        ?>
            <?php        
            }elseif($this->fxpaymentmethod == 2){//bitcoin
                $document = JFactory::getDocument();
                $document->addStyleSheet('components/com_fxbotmarket/assets/fxbotpaymentform.css');

                ?>
                <div id="bitcoin_address"></div>
        <div id="bitcoin_amnt"></div>
        <div id="error_response"></div>

        <script src="https://js.stripe.com/v3/"></script>

        <script type="text/javascript">
         var stripe = Stripe('<?php echo $this->stripe_public_key; ?>');



          //function filledReceiverHandler(
        function makePayment(){
            var email = document.querySelector("#bitcoinemail");
            var miniloader = document.getElementById('miniloader');
            miniloader.style.display = 'block';
           var paymentbutton = document.querySelector(".paymentbutton");
           if(paymentbutton){
               paymentbutton.style.display = 'none';
           }
          stripe.createSource({
          type: 'bitcoin',
          amount: <?php echo $this->product_price; ?>,
          currency: 'usd',
          owner: {
            email: email.value,
          },
        }).then(function(result) {
            if(result.source){
                // handle result.error or result.source

                document.getElementById("bitcoin_address").innerHTML = 'Bitcoin address: ' + result.source.bitcoin.address;
                document.getElementById("bitcoin_amount").innerHTML = 'Amount in BTC:' + result.source.bitcoin.amount/100000000;//amount
                var bitcoin_source_id = document.querySelector('#bitcoin_source_id'); 
                if(bitcoin_source_id){
                    bitcoin_source_id.value = result.source.id; 
                }
                var bitcoin_amount = document.querySelector('#bitcoin_amount');
                if(bitcoin_amount){
                    bitcoin_amount.value = result.source.bitcoin.amount; 
                }
                var bit_address = document.querySelector('#bit_address');
                if(bit_address){
                    bit_address.value = result.source.bitcoin.address; 
                }
                var form = document.querySelector("#stripeForm");
                        form.submit();
                //console.log(result.source);
                //console.log(result.error);
            }
        });
        }
        </script>
        <form id="stripeForm" name ="stripeForm" action="<?php 
        if(!class_exists('FxbotmarketProduct')) {
          include_once JPATH_ROOT.'/components/com_fxbotmarket/helpers/product.php';
          }
                echo JRoute::_ ('index.php?option=com_fxbotmarket&task=paystripe.paybitcoinea&orderid='.(int)$this->fxbotmarket_id_downloadable_order);?>" method="POST">
            <h4 class="fxbot_h4">You are paying <span class="fxbot_sp1"> <?php echo '$'.FxbotmarketProduct::formatMoney($this->product_price/100, 2); ?> </span> for <span class="fxbot_sp1"> <?php echo $this->product_name; ?> </span></h4>

            <div class="form-field" id="carddiv">

                            <label for="fxcardnumber">Enter your email you are using in Bitcoin</label>
                            <div  class="form-control">
                                <input type="text" id="bitcoinemail" name="bitcointemail" value=""/>
                            </div>
            </div>


            <input type="hidden" id="bit_address" name="bit_address" value=""/>
            <input type="hidden" id="bitcoin_source_id" name="bitcoin_source_id" value=""/>
            <input type="hidden" id="bitcoin_amount" name="bitcoin_amount" value=""/>

            <?php echo JHtml::_('form.token');?>
        </form>
        <button onclick="makePayment();" class="paymentbutton" style="">Pay fee</button>

        <div id="miniloader" style="display: none;">
                    <img src ="<?php echo JURI::base();?>/components/com_uu/assets/img/mini-loader.gif" style="" >   
                    <span style="font-size:9px;color:red;">Wait please</span>
        </div> 

        <?php
        if ($dirh) {
            while (($dirElement = readdir($dirh)) !== false) {

            }
            closedir($dirh);
        }
    }else{//paypal payment method has been selected  by client
            echo "<h1>Hello EA done</h1>";
            echo '<div class="vm-wrap vm-order-done">';
            echo '<h3 class="confirm_order_thanks" style="color:green;">'.vmText::_('COM_VIRTUEMART_CART_ORDERDONE_THANK_YOU').' Please click on button below to continue.</h3>';
            // 
            //vmLoadingDiv
            echo $this->restapilink;
            echo '</div>';
            }
}
?>
<script type="text/javascript">
jQuery(document).ready(function() {	
        setTimeout(function(){
        jQuery(".vmLoadingDiv").css("display","none");
    },500);
});    
</script>


