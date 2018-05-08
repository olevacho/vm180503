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

if($this->ifsignal){
    if($this->fxpaymentmethod == 1){
        if($this->credit_card_mode == 1){
        ?>

<div class="vm-wrap vm-order-done">
<form id="uuForm" name="uuForm" action="<?php echo JRoute::_('index.php?option=com_fxbotmarket&task=cardpayment.paycard'); ?>" method="post" class="uu-form-validate" enctype="multipart/form-data" role="form">

        <div class="form-field" id="carddiv">
                    <label for="fxcardnumber">Card Type</label>
                    <div  class="form-control">
                        <select id="fxcardtype" name="fxcardtype" required="required">
                            <option value="">Select Card Type</option>
                            <option value="1">Visa</option>
                            <option value="2">Mastercard</option>
                            <option value="3">Amex</option>
                            <option value="4">Discover</option>
                            <option value="5">Maestro</option>
                        </select>
                    </div> 
                    <label for="fxcardnumber">First name</label>
                    <div  class="form-control">
                        <input type="text" name="fxfirstname" id="fxfirstname" class="fxinput"/>
                    </div>
                    <label for="fxcardnumber">Last name</label>
                    <div  class="form-control">
                        <input type="text" name="fxlastname" id="fxlastname" class="fxinput"/>
                    </div>
                    <label for="fxcardnumber">Card Number</label>
                    <div  class="form-control">
                        <input type="text" name="fxcardnumber" id="fxcardnumber" class="fxinput"/>
                    </div>
                        <label for="fxcvv">CVV</label>
                        <div  class="form-control">
                            <input type="text" name="fxcvv" id="fxcvv" class="fxinput"/>
                        </div>
                            <!--<label for="cvv">Postal Code</label>
                        <div class="form-control" id="postal-code"></div> -->
                          <label for="fxexpirationmonth">Expiration Month</label>
                          <div  class="form-control">
                              <input type="text" name="fxexpirationmonth" id="fxexpirationmonth" class="fxinput"/>
                          </div>
                          <label for="fxexpirationyear">Expiration Year</label>
                          <div  class="form-control">
                              <input type="text" name="fxexpirationyear" id="fxexpirationyear" class="fxinput"/>
                          </div>
                              
        </div>
        <ul class="cFormList cFormHorizontal cResetList">

            <li style="margin-top:20px;">
                <div class="form-field">
                    <div id="cwin-wait" style="display:none;"></div>
                    <div id="cwin-btn">
                        <input class="cButton cButton-Blue validateSubmit" type="submit" id="btnSubmit" value="<?php echo JText::_('Pay fee'); ?>" name="submit2">
                        <?php echo JText::_('COM_UU_OR');?>
                        <a href="<?php echo JRoute::_('index.php?option=com_fxbotmarket&view=customersignals');?>" title="<?php echo JText::_('JCANCEL');?>"><?php echo JText::_('JCANCEL');?></a>
                    </div>
                </div>
            </li>
        </ul>
           
            
        <input type="hidden" name="option" value="com_fxbotmarket" />
        <input type="hidden" name="usertype" value="<?php echo $this->usertype;?>" />
        <input type="hidden" name="task" value="cardpayment.paycard"/>
        <?php echo JHtml::_('form.token'); ?>
        
    </form>
</div>
        <?php }
        else{
        ?>
        <form action="<?php 
        echo JRoute::_ ('index.php?option=com_fxbotmarket&task=paystripe.paycard&orderid='.(int)$this->signal_orderid);?>" method="POST">
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
        }
?>
    <?php        
    }elseif($this->fxpaymentmethod == 2){
        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_fxbotmarket/assets/fxbotpaymentform.css');

        ?>
        <div id="bitcoin_address"></div>
<div id="error_response"></div>

<script src="https://js.stripe.com/v2/stripe.js"></script>

<script type="text/javascript">
  Stripe.setPublishableKey('<?php echo $this->stripe_public_key; ?>');

  function populateBitcoinCheckout(status, response) {
    if (status === 200) {
      document.getElementById("bit_address").value = response.inbound_address;
    } else {
      document.getElementById("error_response").innerHTML = JSON.stringify(response);
    }

    Stripe.bitcoinReceiver.pollReceiver(response.id, function() {
      // post response.id to your server to create a charge
        var response_id = document.querySelector('#response_id');
        if(response_id){
            response_id.value = response.id; 
        }
        var bitcoin_amount = document.querySelector('#bitcoin_amount');
        if(bitcoin_amount){
            bitcoin_amount.value = response.bitcoin_amount; 
        }
        var bitcoin_uri = document.querySelector('#bitcoin_uri');
        if(bitcoin_uri){
            bitcoin_uri.value = response.bitcoin_uri; 
        }
    var form = document.querySelector("#stripeForm");
            form.submit();
    });
  }

  //function filledReceiverHandler(
function makePayment(){
    var email = document.querySelector("#bitcoinemail");
    var miniloader = document.getElementById('miniloader');
    miniloader.style.display = 'block';
  Stripe.bitcoinReceiver.createReceiver({
    amount: <?php echo $this->product_price; ?>,
    currency: 'usd',
    description: 'Seller Fee Payment',
    email: email.value
  }, populateBitcoinCheckout);
}
</script>
<form id="stripeForm" name ="stripeForm" action="<?php 
        echo JRoute::_ ('index.php?option=com_fxbotmarket&task=paystripe.paybitcoin&orderid='.(int)$this->signal_orderid);?>" method="POST">
    <div class="form-field" id="carddiv">

                    <label for="fxcardnumber">Enter Your email you are using in Bitcoin</label>
                    <div  class="form-control">
                        <input type="text" id="bitcoinemail" name="bitcointemail" value=""/>
                    </div>
    </div>
    
    
    <input type="hidden" id="bitcoin_amount" name="bitcoin_amount" value=""/>
    <input type="hidden" id="bitcoin_uri" name="bitcoin_uri" value=""/>
    <input type="hidden" id="bit_address" name="bit_address" value=""/>
    <input type="hidden" id="response_id" name="response_id" value=""/>
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
    }else{
    echo '<div class="vm-wrap vm-order-done">';
    echo '<h3>'.vmText::_('COM_VIRTUEMART_CART_ORDERDONE_THANK_YOU').' Please click on button below to continue.</h3>';
    // 
    //vmLoadingDiv
    echo $this->restapilink;
    echo '</div>';
    }
}else{
    echo '<div class="vm-wrap vm-order-done">';

if (vRequest::getBool('display_title',true)) {
	echo '<h3>'.vmText::_('COM_VIRTUEMART_CART_ORDERDONE_THANK_YOU').'</h3>';
}
        $this->html = vRequest::get('html', vmText::_('COM_VIRTUEMART_ORDER_PROCESSED') );
        echo $this->html;

        if (vRequest::getBool('display_loginform',true)) {
                $cuser = JFactory::getUser();
                if (!$cuser->guest) echo shopFunctionsF::getLoginForm();
        }
        echo '</div>';
}

?>
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
        setTimeout(function(){
        //jQuery("vmLoadingDiv").css("height","50px");
        //jQuery("vmLoadingDiv").height("50px");
        jQuery(".vmLoadingDiv").css("display","none");
    },500);
});    
</script>

