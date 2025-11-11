<?php

if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/');
require_once(ABSPATH . 'wp-config.php');

global $wpdb;

$userId = $_POST["userId"];
$proType = $_POST["proType"];
$siteUrl = $_POST["siteUrl"];
$proName = $_POST["proName"];
$proShortDes = $_POST["proShortDes"];
$proDes = $_POST["proDes"];
$proCats = $_POST["proCats"];
$proTags = $_POST["proTags"];
$proVisible = $_POST["proVisible"];
$proFeatured = $_POST["proFeatured"];
$proSku = $_POST["proSku"];
$proStockStatus = $_POST["proStockStatus"];
$proUpsells = $_POST["proUpsells"];
$proCrosssells = $_POST["proCrosssells"];
$proGrouped = $_POST["proGrouped"];
$proAttrs = $_POST["proAttrs"];
$numberAttrs = $_POST["numberAttrs"];
$proPurchaseNote = $_POST["proPurchaseNote"];
$proMenuOrder = $_POST["proMenuOrder"];
$proEnableReviews = $_POST["proEnableReviews"];
$proCommission = $_POST["proCommission"];
$proImage = $_POST["proImage"];
$proGallery = $_POST["proGallery"];

$checkId = $wpdb->get_var("SELECT max(ID) FROM $wpdb->posts");
$postId = $checkId + 1;

$date = date("Y-m-d h:i:s");
$postids = array();

// insert post
$wpdb->insert( $wpdb->prefix. 'posts', 
array( 
	"ID" => $postId, 
	"post_author" => $userId,
	"post_date" => $date,
	"post_date_gmt" => $date,
	"post_content" => $proDes,
	"post_title" => $proName,
	"post_excerpt" => $proShortDes,
	"post_status" => "pending",
	"comment_status" => "open",
	"ping_status" => "closed",
	"post_name" => $proName,
	"post_modified" => $date,
	"post_modified_gmt" => $date,
	"guid" => $siteUrl.'/?post_type=product&#038;p='.$postId,
	"post_type" => "product",
	"menu_order" => $proMenuOrder
));

// insert post meta
$metaKeys = array("_visibility", "_stock_status", "_downloadable", "_virtual", "_purchase_note", "_featured",
	"_weight", "_length", "_width", "_height", "_sku", "_sale_price_dates_from", "pv_commission_rate",
	"_sale_price_dates_to", "_sold_individually", "_manage_stock", "_backorders");
	
foreach($metaKeys as $metakey){
	if($metakey == "_visibility")
		$value = $proVisible;
	elseif($metakey == "_stock_status")
		$value = $proStockStatus;
	elseif($metakey == "_downloadable")
		$value = "no";
	elseif($metakey == "_virtual")
		$value = "no";
	elseif($metakey == "_purchase_note")
		$value = $proPurchaseNote;
	elseif($metakey == "_featured")
		$value = $proFeatured;
	elseif($metakey == "_sku")
		$value = $proSku;
	elseif($metakey == "_sale_price_dates_from")
		$value = strtotime($proSalePriceDateFrom);
	elseif($metakey == "_sale_price_dates_to")
		$value = strtotime($proSalePriceDateTo);
	elseif($metakey == "_sold_individually")
		$value = "no";
	elseif($metakey == "_manage_stock")
		$value = "no";
	elseif($metakey == "_backorders")
		$value = "no";
	elseif($metakey == "pv_commission_rate")
		$value = $proCommission;
		
	$wpdb->insert( $wpdb->prefix. 'postmeta', 
	array( 
		'post_id' => $postId, 
		'meta_key' => $metakey,
		'meta_value' => $value
	));
}

if($numberAttrs){
	$attributes = "a:".$numberAttrs.":{";
	
	$checkAttrs = explode("@", $proAttrs);
	foreach($checkAttrs as $attr){
		$checkAttr = explode("#", $attr);
		if($checkAttr[0] == "select"){
			if($checkAttr[1] && $checkAttr[1] != "null"){
				$attrIds = explode(",", $checkAttr[1]);
				foreach($attrIds as $key=>$attrId){
					if($key == 0){
						$sql = "SELECT taxonomy FROM ".$wpdb->prefix ."term_taxonomy where term_taxonomy_id = ".$attrId;
						$result = $wpdb->get_results($sql, OBJECT);
						$taxonomy = $result[0]->taxonomy;
						
						$attributes .= 's:'.strlen($taxonomy).':"'.$taxonomy.'";';
						$attributes .= 'a:6:{';
						$attributes .= 's:4:"name";';
						$attributes .= 's:'.strlen($taxonomy).':"'.$taxonomy.'";';
						$attributes .= 's:5:"value";';
						$attributes .= 's:0:"";';
						$attributes .= 's:8:"position";';
						$attributes .= 'i:0;';
						$attributes .= 's:10:"is_visible";';
						$attributes .= 'i:1;';
						$attributes .= 's:12:"is_variation";';
						$attributes .= 'i:1;';
						$attributes .= 's:11:"is_taxonomy";';
						$attributes .= 'i:1;';
						$attributes .= '}';
					}
					$wpdb->insert( $wpdb->prefix. 'term_relationships', 
					array( 
						'object_id' => $postId, 
						'term_taxonomy_id' => $attrId,
						'term_order' => "0"
					));
				}
			}
		}else{
			$checkText = explode("-", $checkAttr[1]);
			if($checkText[1]){
				$texts = explode("|", $checkText[1]);
				
				$sql = "SELECT attribute_name FROM ".$wpdb->prefix ."woocommerce_attribute_taxonomies where attribute_id = ".$checkText[0];
				$result = $wpdb->get_results($sql, OBJECT);
				$attrName = "pa_".$result[0]->attribute_name;
				
				$termId = $wpdb->get_var("SELECT max(term_id) FROM $wpdb->terms");
				$termTaxonomyId = $wpdb->get_var("SELECT max(term_taxonomy_id) FROM $wpdb->term_taxonomy");
				
				foreach($texts as $text){
					$termId++;
					$termTaxonomyId++;
					
					$wpdb->insert( $wpdb->prefix. 'terms', 
					array( 
						'term_id' => $termId, 
						'name' => $text,
						'slug' => str_replace(" ", "-", $text),
						'term_group' => "0"
					));
					
					$wpdb->insert( $wpdb->prefix. 'term_taxonomy', 
					array( 
						'term_taxonomy_id' => $termTaxonomyId,
						'term_id' => $termId,
						'taxonomy' => $attrName
					));
					
					$wpdb->insert( $wpdb->prefix. 'term_relationships', 
					array( 
						'object_id' => $postId, 
						'term_taxonomy_id' => $termTaxonomyId,
						'term_order' => "0"
					));
				}
				
				$attributes .= 's:'.strlen($attrName).':"'.$attrName.'";';
				$attributes .= 'a:6:{';
				$attributes .= 's:4:"name";';
				$attributes .= 's:'.strlen($attrName).':"'.$attrName.'";';
				$attributes .= 's:5:"value";';
				$attributes .= 's:0:"";';
				$attributes .= 's:8:"position";';
				$attributes .= 'i:0;';
				$attributes .= 's:10:"is_visible";';
				$attributes .= 'i:1;';
				$attributes .= 's:12:"is_variation";';
				$attributes .= 'i:1;';
				$attributes .= 's:11:"is_taxonomy";';
				$attributes .= 'i:1;';
				$attributes .= '}';
				
			}
		}
	}
	
	$attributes .= '}';
	$wpdb->insert( $wpdb->prefix. 'postmeta', 
	array( 
		'post_id' => $postId, 
		'meta_key' => "_product_attributes",
		'meta_value' => $attributes
	));
}

if($proUpsells){
	$proUpsells = explode(",", $proUpsells);
	$upsellString = "a:".count($proUpsells).":{";
	
	foreach($proUpsells as $key=>$id){
		$upsellString .= "i:".$key.";";
		$upsellString .= "i:".$id.";";
	}
	
	$upsellString .= "}";
	$wpdb->insert( $wpdb->prefix. 'postmeta', 
	array( 
		'post_id' => $postId, 
		'meta_key' => "_upsell_ids",
		'meta_value' => $upsellString
	));
}

if($proCrosssells){
	$proCrosssells = explode(",", $proCrosssells);
	$crosssellString = "a:".count($proCrosssells).":{";
	
	foreach($proCrosssells as $key=>$id){
		$crosssellString .= "i:".$key.";";
		$crosssellString .= "i:".$id.";";
	}
	
	$crosssellString .= "}";
	$wpdb->insert( $wpdb->prefix. 'postmeta', 
	array( 
		'post_id' => $postId, 
		'meta_key' => "_crosssell_ids",
		'meta_value' => $crosssellString
	));
}

if($proGrouped){
	$proGrouped = explode(",", $proGrouped);
	$groupedString = "a:".count($proGrouped).":{";
	
	foreach($proGrouped as $key=>$id){
		$groupedString .= "i:".$key.";";
		$groupedString .= "i:".$id.";";
	}
	
	$groupedString .= "}";
	$wpdb->insert( $wpdb->prefix. 'postmeta', 
	array( 
		'post_id' => $postId, 
		'meta_key' => "_children",
		'meta_value' => $groupedString
	));
}

if($proGallery){
	$galleryId = $wpdb->get_var("SELECT max(ID) FROM $wpdb->posts");
	$galleryIds = array();
	
	$proGallery = explode("@", $proGallery);
	foreach($proGallery as $gallery){
		$tach = explode("/", $gallery);
		$imgName = $tach[count($tach) - 1];
		$imgName2 = explode(".", $imgName);
		$imgName3 = $imgName2[0];
		
		$ext = $imgName2[1];
		if($ext = "jpg" || $ext == "JPG")
			$mime_type = "image/jpeg";
		elseif($ext == "png" || $ext == "PNG")
			$mime_type = "image/png";
		else
			$mime_type = "image/gif";
		
		$galleryId++;
		
		$wpdb->insert( $wpdb->prefix. 'posts', 
		array( 
			'ID' => $galleryId,
			'post_author' => $userId,
			'post_date' => $date,
			'post_date_gmt' => $date,
			'post_title' => $imgName3,
			'post_status' => 'inherit',
			'comment_status' => 'open',
			'ping_status' => 'closed',
			'post_name' => $imgName3,
			'post_modified' => $date,
			'post_modified_gmt' => $date,
			'post_parent' => $postId,
			'guid' => $gallery,
			'menu_order' => 0,
			'post_type' => 'attachment',
			'post_mime_type' => $mime_type,
			'comment_count' => 0
		));
		
		$wpdb->insert( $wpdb->prefix. 'postmeta', 
		array( 
			'post_id' => $galleryId, 
			'meta_key' => '_wp_attached_file',
			'meta_value' => $tach[count($tach) - 3]."/".$tach[count($tach) - 2]."/".$imgName
		));
		array_push($galleryIds, $galleryId);
	}
	
	$productGallery = implode(",", $galleryIds);
	$wpdb->insert( $wpdb->prefix. 'postmeta', 
	array( 
		'post_id' => $postId, 
		'meta_key' => "_product_image_gallery",
		'meta_value' => $productGallery
	));
}

if($proImage){
	$thumbnailId = $wpdb->get_var("SELECT max(ID) FROM $wpdb->posts");
	
	$tach = explode("/", $proImage);
	$imgName = $tach[count($tach) - 1];
	$imgName2 = explode(".", $imgName);
	$imgName3 = $imgName2[0];
	
	$ext = $imgName2[1];
	if($ext = "jpg" || $ext == "JPG")
		$mime_type = "image/jpeg";
	elseif($ext == "png" || $ext == "PNG")
		$mime_type = "image/png";
	else
		$mime_type = "image/gif";
	
	$wpdb->insert( $wpdb->prefix. 'posts', 
	array( 
		'ID' => $thumbnailId + 1,
		'post_author' => $userId,
		'post_date' => $date,
		'post_date_gmt' => $date,
		'post_title' => $imgName3,
		'post_status' => 'inherit',
		'comment_status' => 'open',
		'ping_status' => 'closed',
		'post_name' => $imgName3,
		'post_modified' => $date,
		'post_modified_gmt' => $date,
		'post_parent' => $postId,
		'guid' => $gallery,
		'menu_order' => 0,
		'post_type' => 'attachment',
		'post_mime_type' => $mime_type,
		'comment_count' => 0
	));
	
	$wpdb->insert( $wpdb->prefix. 'postmeta', 
	array( 
		'post_id' => $thumbnailId + 1, 
		'meta_key' => '_wp_attached_file',
		'meta_value' => $tach[count($tach) - 3]."/".$tach[count($tach) - 2]."/".$imgName
	));
	
	$wpdb->insert( $wpdb->prefix. 'postmeta', 
	array( 
		'post_id' => $postId, 
		'meta_key' => "_thumbnail_id",
		'meta_value' => $thumbnailId + 1
	));
}

// insert tags
if($proTags){
	
	$proTags = explode(",", $proTags);
	foreach($proTags as $tag){
		
		$checkTerm = $wpdb->get_var("SELECT term_id FROM $wpdb->terms where name='".$tag."'");
		if($checkTerm){
			$termTaxonomyId = $wpdb->get_var("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy where term_id = ".$checkTerm);
			
			$wpdb->insert( $wpdb->prefix. 'term_relationships', 
			array( 
				'object_id' => $postId, 
				'term_taxonomy_id' => $termTaxonomyId,
				'term_order' => "0"
			));
		}else{
			$termId = $wpdb->get_var("SELECT max(term_id) FROM $wpdb->terms");
			$termTaxonomyId = $wpdb->get_var("SELECT max(term_taxonomy_id) FROM $wpdb->term_taxonomy");
			
			$termId++;
			$termTaxonomyId++;
			
			$wpdb->insert( $wpdb->prefix. 'terms', 
			array( 
				'term_id' => $termId, 
				'name' => $tag,
				'slug' => str_replace(" ", "-", $tag)
			));
			
			$wpdb->insert( $wpdb->prefix. 'term_taxonomy', 
			array( 
				'term_taxonomy_id' => $termTaxonomyId, 
				'term_id' => $termId,
				'taxonomy' => "product_tag"
			));
			
			$wpdb->insert( $wpdb->prefix. 'term_relationships', 
			array( 
				'object_id' => $postId, 
				'term_taxonomy_id' => $termTaxonomyId,
				'term_order' => "0"
			));
		}
	}
}

// product type
if($proType == "Simple product")
	$termIdType = $wpdb->get_var("SELECT term_id FROM $wpdb->terms where name = 'simple'");
elseif($proType == "Grouped product")
	$termIdType = $wpdb->get_var("SELECT term_id FROM $wpdb->terms where name = 'grouped'");
elseif($proType == "External/Affiliate product")
	$termIdType = $wpdb->get_var("SELECT term_id FROM $wpdb->terms where name = 'external'");
elseif($proType == "Variable product")
	$termIdType = $wpdb->get_var("SELECT term_id FROM $wpdb->terms where name = 'variable'");
elseif($proType == "Bookable product")
	$termIdType = $wpdb->get_var("SELECT term_id FROM $wpdb->terms where name = 'booking'");

$taxonomyId = $wpdb->get_var("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy where term_id = ".$termIdType);

$wpdb->insert( $wpdb->prefix. 'term_relationships', 
array( 
	'object_id' => $postId, 
	'term_taxonomy_id' => $taxonomyId,
	'term_order' => "0"
));

// cates
if($proCats){
	$proCats = explode(",", $proCats);
	foreach($proCats as $cat){
		$taxonomyId = $wpdb->get_var("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy where term_id = ".$cat);
		
		$wpdb->insert( $wpdb->prefix. 'term_relationships', 
		array( 
			'object_id' => $postId, 
			'term_taxonomy_id' => $taxonomyId,
			'term_order' => "0"
		));
	}
}

echo 1;