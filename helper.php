<?php

if ( !defined('ABSPATH') )
    define('ABSPATH', dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/');
require_once(ABSPATH . 'wp-config.php');

global $wpdb;

$task = $_POST["task"];

if($task == "getproducts"){
	$keyword = $_POST["keyword"];
	$products = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' and post_type = 'product' and post_title like '%".$keyword."%'");
	echo json_encode($products, 1);
}elseif($task == "delete_product"){
	$proId = $_POST["proId"];
	
	echo $wpdb->delete( "$wpdb->posts", array( 'ID' => $proId ) );
	$wpdb->delete( "$wpdb->postmeta", array( 'post_id' => $proId ) );
	$wpdb->delete( "$wpdb->term_relationships", array( 'object_id' => $proId ) );
}