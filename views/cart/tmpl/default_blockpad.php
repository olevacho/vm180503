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
$app = JFactory::getApplication();
$user = JFactory::getUser();
$user_id = $user->id;
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
<h3 class="fxbot_cart_product_name_h"> You are ordering: <span class="fxbot_cart_product_name_s"><?php echo $this->product_name; ?></span></h3>
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
            <div class="fxbot_payment_sw" style="width:100%;">
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
                        
                        $input = $app->input;
                        $blockpad_source = $input->get('blockpad_source',0,'int');    
                        $blockpad_register = $input->post->get('blockpad_register',0,'int');  
                        if(($blockpad_source == 1 || $blockpad_register == 1) && $user_id <= 0)  {
                                ?>
                <p><input name="fxbregisterselected" value="1" id="creditid2"  onclick="displayPanel(1);" type="radio" checked="checked"> I want Register</p>
                <h4 style="color:red;">OR</h4>
                <p><input name="fxbregisterselected" value="2" id="paypalid2" onclick="displayPanel(0);" type="radio" > I want Login</p>
                 <input type="hidden" name="blockpad_register" value="1">
                <div id="fxblogpanel" style="display: none;">
                    <div class="form-group">
                        
                        <input title="" placeholder="Username..." value="" data-validation="required server"
                               data-validation-url="/accounts/register-new-user?task=registration.ajaxCheckUserName&amp;format=json"
                               data-validation-error-msg="Your username is required" id="jform_username"
                               name="fxblogin" maxlength="200" size="40" class="" type="text" style="float:none;">
		</div>                                                                 
                

                
                <div class="form-group has-error">
                    <input title="" placeholder="Password..." value="" name="fxbloginpassword1" 
                           data-validation="required strength" data-validation-strength="1" 
                           id="jform_password1" maxlength="200" size="40" class="error"
                           autocomplete="off" style="float:none;width:100%;height:45px;" 
                           data-validation-has-keyup-event="true" type="password">
                    
                </div>
                </div>
                
                <div id="fxbregpanel" >
                <div class="form-group has-error">
                       
                        <input title="" placeholder="Name..." value="" data-validation="length" 
                               data-validation-length="min3" 
                               data-validation-error-msg="Your name is too short!" 
                               id="jform_name" name="fxbname" maxlength="200" size="40" 
                               class="error" style="float:none;" data-validation-has-keyup-event="true" type="text">
			
                </div>
                <div class="form-group">
                        
                        <input title="" placeholder="Username..." value="" data-validation="required server"
                               data-validation-url="/accounts/register-new-user?task=registration.ajaxCheckUserName&amp;format=json"
                               data-validation-error-msg="Your username is required" id="jform_username"
                               name="fxbusername" maxlength="200" size="40" class="" type="text" style="float:none;">
		</div>                                                                 
                
                <div class="form-group">
                   
                    <input title="" placeholder="Email..." value="" 
                           data-validation="email server" 
                           data-validation-url="/accounts/register-new-user?task=registration.ajaxCheckEmail&amp;format=json" 
                           id="jform_email1" name="fxbemail1" maxlength="200" size="40"  style="float:none;"
                           class="required"
                           type="text">
		</div>
                
                <div class="form-group has-error">
                    <input title="" placeholder="Password..." value="" name="fxbpassword1" 
                           data-validation="required strength" data-validation-strength="1" 
                           id="jform_password1" maxlength="200" size="40" class="error"
                           autocomplete="off" style="float:none;width:100%;height:45px;" 
                           data-validation-has-keyup-event="true" type="password">
                    
                </div>
                
                <div class="form-group has-error">
                  
                    <input title="" placeholder="Mobile Phone..." value="" data-validation="required" 
                           data-validation-error-msg="is required. Make sure it contains a valid value!" 
                           id="jform_cf_phone" name="fxbcf_phone" maxlength="200" size="40" class="error" 
                           style="float:none;" data-validation-has-keyup-event="true"
                           type="text">

                </div>
               
                <div class="form-group">

										<label id="lblfield-1" for="jform_cf_country" class="sr-only">*Country</label>

                <select id="fxbotmarket_country_id_field" name="fxbcountry_id" class="fxbotmarket-select required fxbotmarket-select form-control" style="width: 100%;height:45px;"  aria-required="true" required="required">
                        <option value="" selected="selected">-- Select country --</option>
                        <option value="1">Afghanistan</option>
                        <option value="2">Albania</option>
                        <option value="3">Algeria</option>
                        <option value="4">American Samoa</option>
                        <option value="5">Andorra</option>
                        <option value="6">Angola</option>
                        <option value="7">Anguilla</option>
                        <option value="8">Antarctica</option>
                        <option value="9">Antigua and Barbuda</option>
                        <option value="10">Argentina</option>
                        <option value="11">Armenia</option>
                        <option value="12">Aruba</option>
                        <option value="13">Australia</option>
                        <option value="14">Austria</option>
                        <option value="15">Azerbaijan</option>
                        <option value="16">Bahamas</option>
                        <option value="17">Bahrain</option>
                        <option value="18">Bangladesh</option>
                        <option value="19">Barbados</option>
                        <option value="20">Belarus</option>
                        <option value="21">Belgium</option>
                        <option value="22">Belize</option>
                        <option value="23">Benin</option>
                        <option value="24">Bermuda</option>
                        <option value="25">Bhutan</option>
                        <option value="26">Bolivia</option>
                        <option value="243">Bonaire, Sint Eustatius and Saba</option>
                        <option value="27">Bosnia and Herzegovina</option>
                        <option value="28">Botswana</option>
                        <option value="29">Bouvet Island</option>
                        <option value="30">Brazil</option>
                        <option value="31">British Indian Ocean Territory</option>
                        <option value="32">Brunei Darussalam</option>
                        <option value="33">Bulgaria</option>
                        <option value="34">Burkina Faso</option>
                        <option value="35">Burundi</option>
                        <option value="36">Cambodia</option>
                        <option value="37">Cameroon</option>
                        <option value="38">Canada</option>
                        <option value="244">Canary Islands</option>
                        <option value="39">Cape Verde</option>
                        <option value="40">Cayman Islands</option>
                        <option value="41">Central African Republic</option>
                        <option value="42">Chad</option>
                        <option value="43">Chile</option>
                        <option value="44">China</option>
                        <option value="45">Christmas Island</option>
                        <option value="46">Cocos (Keeling) Islands</option>
                        <option value="47">Colombia</option>
                        <option value="48">Comoros</option>
                        <option value="49">Congo, Republic of the</option>
                        <option value="50">Cook Islands</option>
                        <option value="51">Costa Rica</option>
                        <option value="53">Croatia</option>
                        <option value="54">Cuba</option>
                        <option value="55">Cyprus</option>
                        <option value="56">Czech Republic</option>
                        <option value="52">Côte d'Ivoire</option>
                        <option value="57">Denmark</option>
                        <option value="58">Djibouti</option>
                        <option value="59">Dominica</option>
                        <option value="60">Dominican Republic</option>
                        <option value="62">Ecuador</option>
                        <option value="63">Egypt</option>
                        <option value="64">El Salvador</option>
                        <option value="65">Equatorial Guinea</option>
                        <option value="66">Eritrea</option>
                        <option value="67">Estonia</option>
                        <option value="68">Ethiopia</option>
                        <option value="69">Falkland Islands (Malvinas)</option>
                        <option value="70">Faroe Islands</option>
                        <option value="71">Fiji</option>
                        <option value="72">Finland</option>
                        <option value="73">France</option>
                        <option value="75">French Guiana</option>
                        <option value="76">French Polynesia</option>
                        <option value="77">French Southern Territories</option>
                        <option value="78">Gabon</option>
                        <option value="79">Gambia</option>
                        <option value="80">Georgia</option>
                        <option value="81">Germany</option>
                        <option value="82">Ghana</option>
                        <option value="83">Gibraltar</option>
                        <option value="84">Greece</option>
                        <option value="85">Greenland</option>
                        <option value="86">Grenada</option>
                        <option value="87">Guadeloupe</option>
                        <option value="88">Guam</option>
                        <option value="89">Guatemala</option>
                        <option value="90">Guinea</option>
                        <option value="91">Guinea-Bissau</option>
                        <option value="92">Guyana</option>
                        <option value="93">Haiti</option>
                        <option value="94">Heard and McDonald Islands</option>
                        <option value="95">Honduras</option>
                        <option value="96">Hong Kong</option>
                        <option value="97">Hungary</option>
                        <option value="98">Iceland</option>
                        <option value="99">India</option>
                        <option value="100">Indonesia</option>
                        <option value="101">Iran, Islamic Republic of</option>
                        <option value="102">Iraq</option>
                        <option value="103">Ireland</option>
                        <option value="104">Israel</option>
                        <option value="105">Italy</option>
                        <option value="106">Jamaica</option>
                        <option value="107">Japan</option>
                        <option value="241">Jersey</option>
                        <option value="108">Jordan</option>
                        <option value="109">Kazakhstan</option>
                        <option value="110">Kenya</option>
                        <option value="111">Kiribati</option>
                        <option value="112">Korea, Democratic People's Republic of</option>
                        <option value="113">Korea, Republic of</option>
                        <option value="114">Kuwait</option>
                        <option value="115">Kyrgyzstan</option>
                        <option value="116">Lao People's Democratic Republic</option>
                        <option value="117">Latvia</option>
                        <option value="118">Lebanon</option>
                        <option value="119">Lesotho</option>
                        <option value="120">Liberia</option>
                        <option value="121">Libya</option>
                        <option value="122">Liechtenstein</option>
                        <option value="123">Lithuania</option>
                        <option value="124">Luxembourg</option>
                        <option value="125">Macau</option>
                        <option value="126">Macedonia, the former Yugoslav Republic of</option>
                        <option value="127">Madagascar</option>
                        <option value="128">Malawi</option>
                        <option value="129">Malaysia</option>
                        <option value="130">Maldives</option>
                        <option value="131">Mali</option>
                        <option value="132">Malta</option>
                        <option value="133">Marshall Islands</option>
                        <option value="134">Martinique</option>
                        <option value="135">Mauritania</option>
                        <option value="136">Mauritius</option>
                        <option value="137">Mayotte</option>
                        <option value="138">Mexico</option>
                        <option value="139">Micronesia, Federated States of</option>
                        <option value="140">Moldova, Republic of</option>
                        <option value="141">Monaco</option>
                        <option value="142">Mongolia</option>
                        <option value="143">Montserrat</option>
                        <option value="144">Morocco</option>
                        <option value="145">Mozambique</option>
                        <option value="146">Myanmar</option>
                        <option value="147">Namibia</option>
                        <option value="148">Nauru</option>
                        <option value="149">Nepal</option>
                        <option value="150">Netherlands</option>
                        <option value="151">Netherlands Antilles</option>
                        <option value="152">New Caledonia</option>
                        <option value="153">New Zealand</option>
                        <option value="154">Nicaragua</option>
                        <option value="155">Niger</option>
                        <option value="156">Nigeria</option>
                        <option value="157">Niue</option>
                        <option value="158">Norfolk Island</option>
                        <option value="159">Northern Mariana Islands</option>
                        <option value="160">Norway</option>
                        <option value="161">Oman</option>
                        <option value="162">Pakistan</option>
                        <option value="163">Palau</option>
                        <option value="248">Palestinian Territory, Occupied</option>
                        <option value="164">Panama</option>
                        <option value="165">Papua New Guinea</option>
                        <option value="166">Paraguay</option>
                        <option value="167">Peru</option>
                        <option value="168">Philippines</option>
                        <option value="169">Pitcairn</option>
                        <option value="170">Poland</option>
                        <option value="171">Portugal</option>
                        <option value="172">Puerto Rico</option>
                        <option value="173">Qatar</option>
                        <option value="175">Romania</option>
                        <option value="176">Russian Federation</option>
                        <option value="177">Rwanda</option>
                        <option value="174">Réunion</option>
                        <option value="242">Saint Barthélemy</option>
                        <option value="197">Saint Helena</option>
                        <option value="178">Saint Kitts and Nevis</option>
                        <option value="179">Saint Lucia</option>
                        <option value="246">Saint Martin (French part)</option>
                        <option value="198">Saint Pierre and Miquelon</option>
                        <option value="180">Saint Vincent and the Grenadines</option>
                        <option value="181">Samoa</option>
                        <option value="182">San Marino</option>
                        <option value="183">Sao Tome And Principe</option>
                        <option value="184">Saudi Arabia</option>
                        <option value="185">Senegal</option>
                        <option value="245">Serbia</option>
                        <option value="186">Seychelles</option>
                        <option value="187">Sierra Leone</option>
                        <option value="188">Singapore</option>
                        <option value="247">Sint Maarten (Dutch part)</option>
                        <option value="189">Slovakia</option>
                        <option value="190">Slovenia</option>
                        <option value="191">Solomon Islands</option>
                        <option value="192">Somalia</option>
                        <option value="193">South Africa</option>
                        <option value="194">South Georgia and the South Sandwich Islands</option>
                        <option value="195">Spain</option>
                        <option value="196">Sri Lanka</option>
                        <option value="199">Sudan</option>
                        <option value="200">Suriname</option>
                        <option value="201">Svalbard and Jan Mayen</option>
                        <option value="202">Swaziland</option>
                        <option value="203">Sweden</option>
                        <option value="204">Switzerland</option>
                        <option value="205">Syrian Arab Republic</option>
                        <option value="206">Taiwan</option>
                        <option value="207">Tajikistan</option>
                        <option value="208">Tanzania, United Republic of</option>
                        <option value="209">Thailand</option>
                        <option value="237">The Democratic Republic of Congo</option>
                        <option value="61">Timor-Leste</option>
                        <option value="210">Togo</option>
                        <option value="211">Tokelau</option>
                        <option value="212">Tonga</option>
                        <option value="213">Trinidad and Tobago</option>
                        <option value="214">Tunisia</option>
                        <option value="215">Turkey</option>
                        <option value="216">Turkmenistan</option>
                        <option value="217">Turks and Caicos Islands</option>
                        <option value="218">Tuvalu</option>
                        <option value="219">Uganda</option>
                        <option value="220">Ukraine</option>
                        <option value="221">United Arab Emirates</option>
                        <option value="222">United Kingdom</option>
                        <option value="223">United States</option>
                        <option value="224">United States Minor Outlying Islands</option>
                        <option value="225">Uruguay</option>
                        <option value="226">Uzbekistan</option>
                        <option value="227">Vanuatu</option>
                        <option value="228">Vatican City State (Holy See)</option>
                        <option value="229">Venezuela</option>
                        <option value="230">Viet Nam</option>
                        <option value="231">Virgin Islands, British</option>
                        <option value="232">Virgin Islands, U.S.</option>
                        <option value="233">Wallis and Futuna</option>
                        <option value="234">Western Sahara</option>
                        <option value="235">Yemen</option>
                        <option value="238">Zambia</option>
                        <option value="239">Zimbabwe</option>
                </select>
										
</div>
                </div>                
                
                
                                                                                
                <?php
                        }          
                            
                ?>
                
                
                <?php if(false){?>
                <p><input name="fxpaymentmethod" value="1" id="creditid"  onclick="selectMethod(1);" type="radio" <?php echo $cardchecked; ?>> Credit Card</p>
                        <?php  } ?>
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
                function displayPanel(arg){
                    if(arg == 1){
                      jQuery('#fxbregpanel').show();
                      jQuery('#fxblogpanel').hide();
                    }else{
                      jQuery('#fxblogpanel').show();
                      jQuery('#fxbregpanel').hide();
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