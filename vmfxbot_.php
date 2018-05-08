<?php


// no direct access
defined('_JEXEC') or die ;
//productdetails
$mainframe = JFactory::getApplication();
if ($mainframe->isSite())
	{
            if((key_exists('option', $_GET) && ($_GET['option'] == 'com_virtuemart')) || (key_exists('option', $_POST) && ($_POST['option'] == 'com_virtuemart'))){
                $viewcart = false ;
                $taskadd = false;
                $productdetailsview = false;
                $productcartview = false;
                $taskupdate = false;
		foreach($_GET as $key => $value){
                    if((strpos( $key , 'view') !== FALSE)  && ((strpos($value,'productdetails') !== FALSE) )){// || (strpos($value,'saveorder') !== FALSE) ))){//saveorder
                        $productdetailsview = true;
                        include_once JPATH_ROOT."/plugins/system/vmfxbot/views/productdetails/view.html.php";
                    }
                    if((strpos( $key , 'view') !== FALSE)  && ((strpos($value,'category') !== FALSE) )){// || (strpos($value,'saveorder') !== FALSE) ))){//saveorder
                        include_once JPATH_ROOT."/plugins/system/vmfxbot/views/category/view.html.php";
                    }
                    if((strpos( $key , 'view') !== FALSE)  && ((strpos($value,'cart') !== FALSE) )){// || (strpos($value,'saveorder') !== FALSE) ))){//saveorder
                        $productcartview = true;
                        include_once JPATH_ROOT."/plugins/system/vmfxbot/views/cart/view.html.php";
                    }
                }
                foreach($_POST as $key => $value){
                    if($productdetailsview){
                        if((strpos( $key , 'view') !== FALSE)  && ((strpos($value,'cart') !== FALSE) )){// || (strpos($value,'saveorder') !== FALSE) ))){//saveorder
                            $viewcart = true;
                        }
                        if((strpos( $key , 'task') !== FALSE)  && ((strpos($value,'add') !== FALSE) )){// || (strpos($value,'saveorder') !== FALSE) ))){//saveorder
                            $taskadd = true;
                        }
                        
                    }
                    if($productcartview){
                        if((strpos( $key , 'view') !== FALSE)  && ((strpos($value,'cart') !== FALSE) )){// || (strpos($value,'saveorder') !== FALSE) ))){//saveorder
                            $viewcart = true;
                        }
                        if((strpos( $key , 'task') !== FALSE)  && ((strpos($value,'updatecart') !== FALSE) )){// || (strpos($value,'saveorder') !== FALSE) ))){//saveorder
                            $taskupdate = true;
                        }
                    }
                    
                }
                if($viewcart && $taskadd){
                    include_once JPATH_ROOT."/plugins/system/vmfxbot/controllers/cart.php";
                }
                if($viewcart && $taskupdate){
                    include_once JPATH_ROOT."/plugins/system/vmfxbot/controllers/cart.php";
                }
            }
    }
				

jimport( 'joomla.plugin.plugin' );
class plgvmfxbot extends JPlugin
{

public function __construct( &$subject, $config )
{
parent::__construct( $subject, $config );
 
// Do some extra initialisation in this constructor if required
}
 

function onAfterInitialise()
{
	//die('after initialise');
	//$option = JRequest::getVar("option", "");
}
 

function onAfterRoute()
{

}
 

function onAfterDispatch()
{
	//die('after dispatch');
	$option = JRequest::getVar("option", "");
}
 

function onAfterRender()
{
	$option = JRequest::getVar("option", "");
}
}
