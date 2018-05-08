<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Valerie Isaksen

 * @link ${PHING.VM.MAINTAINERURL}
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_images.php 9413 2017-01-04 17:20:58Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
/*
vmJsApi::loadPopUpLib();
if(VmConfig::get('usefancy',1)){
$imageJS = '
jQuery(document).ready(function() {
	Virtuemart.updateImageEventListeners()
});
Virtuemart.updateImageEventListeners = function() {
	jQuery("a[rel=vm-additional-images]").fancybox({
		"titlePosition" 	: "inside",
		"transitionIn"	:	"elastic",
		"transitionOut"	:	"elastic"
	});
	jQuery(".additional-images a.product-image.image-0").removeAttr("rel");
	jQuery(".additional-images img.product-image").click(function() {
		jQuery(".additional-images a.product-image").attr("rel","vm-additional-images" );
		jQuery(this).parent().children("a.product-image").removeAttr("rel");
		var src = jQuery(this).parent().children("a.product-image").attr("href");
		jQuery(".main-image img").attr("src",src);
		jQuery(".main-image img").attr("alt",this.alt );
		jQuery(".main-image a").attr("href",src );
		jQuery(".main-image a").attr("title",this.alt );
		jQuery(".main-image .vm-img-desc").html(this.alt);
		}); 
	}
	';
} else {
	$imageJS = '
	jQuery(document).ready(function() {
		Virtuemart.updateImageEventListeners()
	});
	Virtuemart.updateImageEventListeners = function() {
		jQuery("a[rel=vm-additional-images]").facebox();
		var imgtitle = jQuery("span.vm-img-desc").text();
		jQuery("#facebox span").html(imgtitle);
	}
	';
}

vmJsApi::addJScript('imagepopup',$imageJS);

if (!empty($this->product->images)) {
	$image = $this->product->images[0];
	?>
	<div class="main-image">
		<?php echo $image->displayMediaFull("",true,"rel='vm-additional-images'"); ?>
		<div class="clear"></div>
	</div>
	<?php
}
*/
if (!empty($this->product->images)) {
	$image = $this->product->images[0];
	
}
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base()."plugins/system/vmfxbot/assets/carousel.css");
?>
<div style="text-align:center">
    <?php
    $imgs = $this->product->images;
    $imgcount = count($imgs);
        $i = 1;
        foreach($imgs as $image){
            echo '<span class="dot" onclick="currentSlide('.$i.')"></span>';
            $i++;
        }
    ?>

</div>
<br/>
<div class="fxcarousel-container"> 
 <div class="slideshow-container">
     <?php 
     $i = 1;
     foreach($imgs as $img){ 
      ?>
        <div class="mySlides fxfade tt">
          <div class="numbertext"><?php echo $i.'/'.$imgcount; ?></div>
          <?php
          if ($img->file_url == "images/virtuemart/product/") { ?>
            <img src="<?php echo JURI::root().'/components/com_virtuemart/assets/images/vmgeneral/noimage.gif'; ?>" style="">
          <?php }else{ ?>
            <img src="<?php echo $img->file_url; ?>" style="">
          <?php } ?>
          <div class="text"></div>
        </div>
     <?php
     $i++;
     }
     ?>

  <a class="prev" onclick="plusSlides(-1)"></a>
  <a class="next" onclick="plusSlides(1)"></a>
</div>
</div>



<script type="text/javascript">
var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
} 
</script>