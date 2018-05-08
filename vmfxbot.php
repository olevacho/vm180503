<?php


// no direct access
defined('_JEXEC') or die ;
$fl2=__DIR__."/vmfxbot.txt";
//productdetails
$mainframe = JFactory::getApplication();
if ($mainframe->isSite())
	{
    if((key_exists('option', $_GET) && ($_GET['option'] == 'com_fxbotmarket')) || (key_exists('option', $_POST) && ($_POST['option'] == 'com_fxbotmarket'))){
        if( (key_exists('task', $_POST) && ($_POST['task'] == 'productfile.signal') || (key_exists('task', $_POST)) && ($_POST['task'] == 'productfile.product'))){//productfile.product
                defined ('VMPATH_ROOT') or define ('VMPATH_ROOT', JPATH_ROOT);
                defined ('VMPATH_ADMINISTRATOR') or define ('VMPATH_ADMINISTRATOR',	VMPATH_ROOT .'/administrator');
                defined ('VMPATH_ADMIN') or define ('VMPATH_ADMIN', VMPATH_ADMINISTRATOR .'/components/com_virtuemart' );
                defined('JVM_VERSION') or define ('JVM_VERSION', 3);
		if(!class_exists('vmUploader')) {
                    require(JPATH_ROOT."/plugins/system/vmfxbot/helpers/vmuploader.php");
			}
        }
    }		
			
			
            if((key_exists('option', $_GET) && ($_GET['option'] == 'com_virtuemart')) || (key_exists('option', $_POST) && ($_POST['option'] == 'com_virtuemart'))){
				
                $logvar = 'virtuemart'."\n";//'date='.$date." alpha_refer_id=".$alpha_refer_id."\n";
                //error_log(print_r($logvar,true),3,$fl2);
                $viewcart = false ;
                $taskadd = false;
                $productdetailsview = false;
                $productcartview = false;
                $taskupdate = false;
				
				
		foreach($_GET as $key => $value){
				//$logvar = $key;//'date='.$date." alpha_refer_id=".$alpha_refer_id."\n";
				//error_log(print_r($logvar,true),3,$fl2);
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
                        include_once JPATH_ROOT."/plugins/system/vmfxbot/controllers/cart.php";
                    }
                }
				//$logvar = 'POST foreach';//'date='.$date." alpha_refer_id=".$alpha_refer_id."\n";
				//error_log(print_r($logvar,true),3,$fl2);
                foreach($_POST as $key => $value){
					$logvar = $key;//'date='.$date." alpha_refer_id=".$alpha_refer_id."\n";
					//error_log(print_r($logvar,true),3,$fl2);
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
            }else{
				$fl2=__DIR__."/vmfxbot.txt";
                $logvar = $_GET;
                //error_log(print_r($logvar,true),3,$fl2);
			}
                  
    }

jimport( 'joomla.plugin.plugin' );
class plgSystemVmfxbot extends JPlugin
{

public function __construct( &$subject, $config )
{
parent::__construct( $subject, $config );
 
// Do some extra initialisation in this constructor if required
}
 

function onAfterInitialise()
{

}
 

function onAfterRoute()
{
    
	$fl2=__DIR__."/vmfxbot2.txt";
	$app = JFactory::getApplication();
        if ($app->isSite())
	{
            $viewcart = false;
            $productdetailsview = false;
            $taskadd = false;
            $taskupdate = false;
            $option = JRequest::getVar("option", "");
            $view = JRequest::getVar("view", "");
            $task = JRequest::getVar("task", "");
            $manage = JRequest::getVar("manage","");
            if($option == 'com_virtuemart'){
                if($view == 'productdetails' ){// || (strpos($value,'saveorder') !== FALSE) ))){//saveorder
                        $productdetailsview = true;
                        include_once JPATH_ROOT."/plugins/system/vmfxbot/views/productdetails/view.html.php";
                    }
                    if($view == 'category'){// || (strpos($value,'saveorder') !== FALSE) ))){//saveorder
                        include_once JPATH_ROOT."/plugins/system/vmfxbot/views/category/view.html.php";
                    }
                    if($view == 'cart'){// || (strpos($value,'saveorder') !== FALSE) ))){//saveorder
                         $viewcart = true;
                        include_once JPATH_ROOT."/plugins/system/vmfxbot/views/cart/view.html.php";
                        include_once JPATH_ROOT."/plugins/system/vmfxbot/controllers/cart.php";
                         if($task == 'add'){
                            $taskadd = true;
                        }
                        if($task == 'updatecart'){// || (strpos($value,'saveorder') !== FALSE) ))){//saveorder
                            $taskupdate = true;
                        }
                    }
                    if($viewcart && $taskadd){
                        include_once JPATH_ROOT."/plugins/system/vmfxbot/controllers/cart.php";
                    }
                    if($viewcart && $taskupdate){
                        include_once JPATH_ROOT."/plugins/system/vmfxbot/controllers/cart.php";
                    }
                    if($view == 'vmplg' && ($task == 'pluginresponsereceived'|| $task == 'pluginUserPaymentCancel')){// || (strpos($value,'saveorder') !== FALSE) ))){//saveorder
                        include_once JPATH_ROOT."/plugins/system/vmfxbot/views/vmplg/view.html.php";
                    }
                    //option=com_virtuemart&tmpl=component&manage=1&view=product&task=edit&virtuemart_product_id=30
                    if(isset($manage) && $manage == 1 ){// || (strpos($value,'saveorder') !== FALSE) ))){//saveorder
                        $app->redirect(JRoute::_('index.php?option=com_user&view=login'));
                    }
            }
        }
	/*$input = $app->input;
	$logvar = "onAfterRoute"."\n";
	error_log(print_r($logvar,true),3,$fl2);
	$option = $input->get('option');
	$logvar = "option=".$option."\n";
	error_log(print_r($logvar,true),3,$fl2);
	$task = $input->get('task');
	$logvar = "task=".$task."\n";
	error_log(print_r($logvar,true),3,$fl2);
	$view = $input->get('view');
	$logvar = "view=".$view."\n";
	error_log(print_r($logvar,true),3,$fl2);
	$poption = $input->post->get('option');
	$logvar = "poption=".$poption."\n";
	error_log(print_r($logvar,true),3,$fl2);
	$ptask = $input->post->get('task');
	$logvar = "ptask=".$ptask."\n";
	error_log(print_r($logvar,true),3,$fl2);
	$pview = $input->post->get('view');
	$logvar = "pview=".$pview."\n";
	error_log(print_r($logvar,true),3,$fl2);
	$option = JRequest::getVar("option", "");
	$logvar = "option=".$option."\n";
	error_log(print_r($logvar,true),3,$fl2);
	$view = JRequest::getVar("view", "");
	$logvar = "view=".$view."\n";
	error_log(print_r($logvar,true),3,$fl2);
	$task = JRequest::getVar("task", "");
	$logvar = "task=".$task."\n";
	error_log(print_r($logvar,true),3,$fl2);
	*/
	
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
