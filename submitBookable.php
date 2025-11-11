<?php

if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/');
require_once(ABSPATH . 'wp-config.php');

global $wpdb;

$userId = $_POST["userId"];
$proType = $_POST["proType"];
$proName = $_POST["proName"];
$proShortDes = $_POST["proShortDes"];
$proDes = $_POST["proDes"];
$proCats = $_POST["proCats"];
$proTags = $_POST["proTags"];
$proVirtual = $_POST["proVirtual"];
$hasPersons = $_POST["hasPersons"];
$hasResources = $_POST["hasResources"];
$proTaxStatus = $_POST["proTaxStatus"];
$proTaxClass = $_POST["proTaxClass"];
$bookingDuration = $_POST["bookingDuration"];
$bookingDurationDu = $_POST["bookingDurationDu"];
$bookingDurationUnit = $_POST["bookingDurationUnit"];
$minimumDuration = $_POST["minimumDuration"];
$maximumDuration = $_POST["maximumDuration"];
$calendarDisplayMode = $_POST["calendarDisplayMode"];
$requiresConfirmation = $_POST["requiresConfirmation"];
$canbeCancelled = $_POST["canbeCancelled"];
$proWeight = $_POST["proWeight"];
$proLength = $_POST["proLength"];
$proWidth = $_POST["proWidth"];
$proHeight = $_POST["proHeight"];
$proShippingClass = $_POST["proShippingClass"];
$proUpsells = $_POST["proUpsells"];
$proCrosssells = $_POST["proCrosssells"];
$proAttrs = $_POST["proAttrs"];
$numberAttrs = $_POST["numberAttrs"];
$proPurchaseNote = $_POST["proPurchaseNote"];
$proMenuOrder = $_POST["proMenuOrder"];
$proEnableReviews = $_POST["proEnableReviews"];
$maxBookPerBlock = $_POST["maxBookPerBlock"];
$minBlockBookable = $_POST["minBlockBookable"];
$minBlockBookableUnit = $_POST["minBlockBookableUnit"];
$maxBlockBookable = $_POST["maxBlockBookable"];
$maxBlockBookableUnit = $_POST["maxBlockBookableUnit"];
$allDatesAre = $_POST["allDatesAre"];
$checkRulesAgainst = $_POST["checkRulesAgainst"];
$firstBlockStartsAt = $_POST["firstBlockStartsAt"];
$availabilityRange = $_POST["availabilityRange"];
$baseCost = $_POST["baseCost"];
$blockCost = $_POST["blockCost"];
$displayCost = $_POST["displayCost"];
$costRange = $_POST["costRange"];
$minPersons = $_POST["minPersons"];
$maxPersons = $_POST["maxPersons"];
$multiplyAllCostByPersonCount = $_POST["multiplyAllCostByPersonCount"];
$countPersonAsBookings = $_POST["countPersonAsBookings"];
$enablePersonTypes = $_POST["enablePersonTypes"];
$resLabel = $_POST["resLabel"];
$resAre = $_POST["resAre"];
$resRes = $_POST["resRes"];
$proCommission = $_POST["proCommission"];
$proImage = $_POST["proImage"];
$proGallery = $_POST["proGallery"];
$proVisible = $_POST["proVisible"];
$proFeatured = $_POST["proFeatured"];

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
	"menu_order" => $proMenuOrder,
	"post_type" => "product"
));

// insert post meta
$metaKeys = array("_visibility", "_downloadable", "_virtual", "_tax_status", "_tax_class", "_purchase_note", "_featured",
			"_weight", "_length", "_width", "_height", "pv_commission_rate",
			 "_sold_individually", "_manage_stock", "_backorders",
			 "_wc_booking_base_cost", "_wc_booking_cost", "_wc_display_cost", "_wc_booking_min_duration",
			 "_wc_booking_max_duration", "_wc_booking_enable_range_picker", "_wc_booking_calendar_display_mode",
			 "_wc_booking_qty", "_wc_booking_has_persons", "_wc_booking_person_qty_multiplier",
			 "_wc_booking_person_cost_multiplier", "_wc_booking_min_persons_group", "_wc_booking_max_persons_group",
			 "_wc_booking_has_person_types", "_wc_booking_has_resources", "_wc_booking_resources_assignment",
			 "_wc_booking_duration_type", "_wc_booking_duration", "_wc_booking_duration_unit", "_wc_booking_user_can_cancel",
			 "_wc_booking_cancel_limit", "_wc_booking_cancel_limit_unit", "_wc_booking_max_date", "_wc_booking_max_date_unit",
			 "_wc_booking_min_date", "_wc_booking_min_date_unit", "_wc_booking_first_block_time",
			 "_wc_booking_requires_confirmation", "_wc_booking_default_date_availability", "_wc_booking_check_availability_against",
			 "_wc_booking_resouce_label", "_has_additional_costs");
			 
foreach($metaKeys as $metakey){
	if($metakey == "_visibility")
		$value = $proVisible;
	elseif($metakey == "_downloadable")
		$value = "no";
	elseif($metakey == "_virtual")
		$value = $proVirtual;
	elseif($metakey == "_tax_status")
		$value = $proTaxStatus;
	elseif($metakey == "_tax_class")
		$value = $proTaxClass;
	elseif($metakey == "_purchase_note")
		$value = $proPurchaseNote;
	elseif($metakey == "_featured")
		$value = $proFeatured;
	elseif($metakey == "_weight")
		$value = $proWeight;
	elseif($metakey == "_length")
		$value = $proLength;
	elseif($metakey == "_width")
		$value = $proWidth;
	elseif($metakey == "_height")
		$value = $proHeight;
	elseif($metakey == "_sold_individually")
		$value = "no";
	elseif($metakey == "_manage_stock")
		$value = "no";
	elseif($metakey == "_backorders")
		$value = "no";
	elseif($metakey == "_wc_booking_base_cost")
		$value = $baseCost;
	elseif($metakey == "_wc_booking_cost")
		$value = $blockCost;
	elseif($metakey == "_wc_display_cost")
		$value = $displayCost;
	elseif($metakey == "_wc_booking_min_duration")
		$value = $minimumDuration;
	elseif($metakey == "_wc_booking_max_duration")
		$value = $maximumDuration;
	elseif($metakey == "_wc_booking_enable_range_picker")
		$value = "no";
	elseif($metakey == "_wc_booking_calendar_display_mode")
		$value = $calendarDisplayMode;
	elseif($metakey == "_wc_booking_qty")
		$value = $maxBookPerBlock;
	elseif($metakey == "_wc_booking_has_persons")
		$value = $hasPersons;
	elseif($metakey == "_wc_booking_person_qty_multiplier")
		$value = $countPersonAsBookings;
	elseif($metakey == "_wc_booking_person_cost_multiplier")
		$value = $multiplyAllCostByPersonCount;
	elseif($metakey == "_wc_booking_min_persons_group")
		$value = $minPersons;
	elseif($metakey == "_wc_booking_max_persons_group")
		$value = $maxPersons;
	elseif($metakey == "_wc_booking_has_person_types")
		$value = "no";
	elseif($metakey == "_wc_booking_has_resources")
		$value = $hasResources;
	elseif($metakey == "_wc_booking_resources_assignment")
		$value = $resAre;
	elseif($metakey == "_wc_booking_duration_type")
		$value = $bookingDuration;
	elseif($metakey == "_wc_booking_duration")
		$value = $bookingDurationDu;
	elseif($metakey == "_wc_booking_duration_unit")
		$value = $bookingDurationUnit;
	elseif($metakey == "_wc_booking_user_can_cancel")
		$value = $canbeCancelled;
	elseif($metakey == "_wc_booking_cancel_limit")
		$value = "1";
	elseif($metakey == "_wc_booking_cancel_limit_unit")
		$value = "month";
	elseif($metakey == "_wc_booking_max_date")
		$value = $maxBlockBookable;
	elseif($metakey == "_wc_booking_max_date_unit")
		$value = $maxBlockBookableUnit;
	elseif($metakey == "_wc_booking_min_date")
		$value = $minBlockBookable;
	elseif($metakey == "_wc_booking_min_date_unit")
		$value = $minBlockBookableUnit;
	elseif($metakey == "_wc_booking_first_block_time")
		$value = $firstBlockStartsAt;
	elseif($metakey == "_wc_booking_requires_confirmation")
		$value = $requiresConfirmation;
	elseif($metakey == "_wc_booking_default_date_availability")
		$value = $allDatesAre;
	elseif($metakey == "_wc_booking_check_availability_against")
		$value = $checkRulesAgainst;
	elseif($metakey == "_wc_booking_resouce_label")
		$value = $resLabel;
		
	$wpdb->insert( $wpdb->prefix. 'postmeta', 
	array( 
		'post_id' => $postId, 
		'meta_key' => $metakey,
		'meta_value' => $value
	));
}

if($availabilityRange){
	$availabilityRange = explode("@", $availabilityRange);
	$availabilityString = "a:".count($availabilityRange).":{";
	
	foreach($availabilityRange as $key=>$rg){
		$range = explode("#", $rg);
		
		$availabilityString .= "i:".$key.";";
		$availabilityString .= "a:5:{";
		$availabilityString .= 's:4:"type";';
		$availabilityString .= 's:'.strlen($range[0]).':"'.$range[0].'";';
		$availabilityString .= 's:8:"bookable";';
		$availabilityString .= 's:'.strlen($range[3]).':"'.$range[3].'";';
		$availabilityString .= 's:8:"priority";';
		$availabilityString .= 'i:'.$range[4].';';
		$availabilityString .= 's:4:"from";';
		$availabilityString .= 's:'.strlen($range[1]).':"'.$range[1].'";';
		$availabilityString .= 's:2:"to";';
		$availabilityString .= 's:'.strlen($range[2]).':"'.$range[2].'";';
		$availabilityString .= "}";
	}
	$availabilityString .= "}";
	
	$wpdb->insert( $wpdb->prefix. 'postmeta', 
	array( 
		'post_id' => $postId, 
		'meta_key' => '_wc_booking_availability',
		'meta_value' => $availabilityString
	));
}

if($costRange){
	$costRange = explode("@", $costRange);
	$costRangeString = "a:".count($costRange).":{";
		foreach($costRange as $key=>$rg){
			$range = explode("#", $rg);
			
			$costRangeString .= "i:".$key.";";
			$costRangeString .= "a:7:{";
				$costRangeString .= 's:4:"type";';
				$costRangeString .= 's:'.strlen($range[0]).':"'.$range[0].'";';
				$costRangeString .= 's:4:"cost";';
				$costRangeString .= 's:'.strlen($range[6]).':"'.$range[6].'";';
				$costRangeString .= 's:8:"modifier";';
				$costRangeString .= 's:'.strlen($range[5]).':"'.$range[5].'";';
				$costRangeString .= 's:9:"base_cost";';
				$costRangeString .= 's:'.strlen($range[4]).':"'.$range[4].'";';
				$costRangeString .= 's:13:"base_modifier";';
				$costRangeString .= 's:'.strlen($range[3]).':"'.$range[3].'";';
				$costRangeString .= 's:4:"from";';
				$costRangeString .= 's:'.strlen($range[1]).':"'.$range[1].'";';
				$costRangeString .= 's:2:"to";';
				$costRangeString .= 's:'.strlen($range[2]).':"'.$range[2].'";';
			$costRangeString .= "}";
		}
	$costRangeString .= "}";
	
	$wpdb->insert( $wpdb->prefix. 'postmeta', 
	array( 
		'post_id' => $postId, 
		'meta_key' => '_wc_booking_pricing',
		'meta_value' => $costRangeString
	));
	
	$wpdb->insert( $wpdb->prefix. 'postmeta', 
	array( 
		'post_id' => $postId, 
		'meta_key' => '_has_additional_costs',
		'meta_value' => "yes"
	));
}

if($resRes){
	$resouces = explode("@", $resRes);
	
	$resBase = 'a:'.count($resouces).':{';
	$resBlock = 'a:'.count($resouces).':{';
	
	foreach($resouces as $res){
		$r = explode("#", $res);
		
		$resBase .= 'i:'.$r[0].';';
		$resBlock .= 'i:'.$r[0].';';
		
		$resBase .= 's:'.strlen($r[1]).':"'.$r[1].'";';
		$resBlock .= 's:'.strlen($r[2]).':"'.$r[2].'";';
		
		$wpdb->insert( $wpdb->prefix. 'wc_booking_relationships', 
		array( 
			'product_id' => $postId, 
			'resource_id' => $r[0]
		));
	}
	$resBase .= '}';
	$resBlock .= '}';
	
	$wpdb->insert( $wpdb->prefix. 'postmeta', 
	array( 
		'post_id' => $postId, 
		'meta_key' => '_resource_base_costs',
		'meta_value' => $resBase
	));
	
	$wpdb->insert( $wpdb->prefix. 'postmeta', 
	array( 
		'post_id' => $postId, 
		'meta_key' => '_resource_block_costs',
		'meta_value' => $resBlock
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
// end meta post

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