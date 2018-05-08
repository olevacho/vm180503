<?php
/**
 * @version		$Id: featured.php 22338 2011-11-04 17:24:53Z github_bot $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 */
class TableQuickorders extends JTable
{
var $order_id = null;
var $user_id = null;
var $mc_gross = null;
var $protection_eligibility = null;
var $payer_id = null;
var $tax = null;
var $payment_date = null;
var $payment_status = null;
var $charset = null;
var $first_name = null;
var $mc_fee = null;
var $notify_version = null;
var $custom = null;
var $payer_status = null;
var $business = null;
var $num_cart_items = null;
var $verify_sign = null;
var $payer_email = null;
var $txn_id = null;
var $item_name = null;
var $payer_business_name = null;
var $last_name = null;
var $receiver_email = null;
var $payment_fee = null;
var $receiver_id = null;
var $mc_currency = null;
var $residence_country = null;
var $transaction_subject = null;
var $published = null;
var $filename = null;
var $downloads = null;
var $key = null;
var $notification_email = null;
var $by_deposit = null;
var $cart_id = null;
/**
	 * @param	JDatabase	A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__quicksell_orders', 'order_id', $db);
	}
	function loadByKey($key) {
		if ($key == '') {
			return false;
		}
		$db = JFactory::getDBO();
		$db->setQuery("select order_id from #__quicksell_orders where `key` = ".$db->Quote($key) . " limit 1");
		$this->load($db->loadResult());
	}
        
        function loadById($id) {
		if ($id == 0) {
			return false;
		}
		$db = JFactory::getDBO();
		$db->setQuery("select order_id from #__quicksell_orders where `order_id` = ".(int)$id . " limit 1");
		$this->load($db->loadResult());
	}
}
