<?php
/**
 * Plugin Name: WooCommerce Add Product from FrontEnd
 * Plugin URI: http://smartcmsmarket.net/
 * Description: Allowing add product from frontend for WooCommerce
 * Version: 2.8
 * Author: SmartCms Team
 * Author URI: http://smartcmsmarket.net/
 * License: GPLv2 or later
*/
 
define ( 'SMARTCMS_SCWAFF_URL', plugin_dir_url(__FILE__));
 
add_action( 'widgets_init', 'smartcms_scwaff_widgets' );
add_action( 'plugins_loaded', 'smartcms_scwaff_load' );

function smartcms_scwaff_widgets() {
	register_widget('SCWAFF_Add_Product_FrontEnd');
}

function smartcms_scwaff_load() {
    global $mfpd;
    $mfpd = new SCWAFF_Add_Product_FrontEnd();
}

class SCWAFF_Add_Product_FrontEnd extends WP_Widget {
	function __construct() {
		parent::__construct (
			  'smartcms_scwaff_id',
			  esc_html__('SmartCms Add Product FrontEnd', 'scwaff-translate'),
			  array(
				  'description' => esc_html__('Allowing add product from frontend for WooCommerce', 'scwaff-translate')
			  )
		);
		add_shortcode( 'smartcms_scwaff_shortcode_addproduct' , array(&$this, 'smartcms_scwaff_shortcode_addproduct_func') );
		add_shortcode( 'smartcms_scwaff_shortcode_manage' , array(&$this, 'smartcms_scwaff_shortcode_manage_func') );
	}
	
	function smartcms_scwaff_shortcode_addproduct_func($atts = array(), $content = null){
		global $wpdb;
		$current_user = wp_get_current_user();
		$userId = $current_user->ID;
		if($userId){
			$product_categories = $wpdb->get_results("SELECT a.term_id, a.name FROM $wpdb->terms a, $wpdb->term_taxonomy b WHERE a.term_id = b.term_id and b.taxonomy = 'product_cat'");
			
			$attributes =  wc_get_attribute_taxonomies();
			$taxonomy_terms = array();
			foreach($attributes as $attr){
				if (taxonomy_exists(wc_attribute_taxonomy_name($attr->attribute_name))){
					$taxonomy_terms[$attr->attribute_label] = get_terms( wc_attribute_taxonomy_name($attr->attribute_name), 'orderby=name&hide_empty=0' );
				}
			}
			
			$resources = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'publish' and post_type = 'bookable_resource'");
			
			wp_register_style( 'smartcms-scwaff-style', SMARTCMS_SCWAFF_URL .'css/style.css?v=1.1' );
			wp_enqueue_style( 'smartcms-scwaff-style' );
			wp_register_style( 'smartcms-scwaff-font', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' );
			wp_enqueue_style( 'smartcms-scwaff-font' );
			
			wp_register_script('smartcms-scwaff-editor', SMARTCMS_SCWAFF_URL .'js/ckeditor.js');
			wp_enqueue_script('smartcms-scwaff-editor');
			wp_register_script('smartcms-scwaff-mixitup', SMARTCMS_SCWAFF_URL .'js/jquery.mixitup.min.js');
			wp_enqueue_script('smartcms-scwaff-mixitup');
			wp_register_script('smartcms-scwaff-editor1', SMARTCMS_SCWAFF_URL .'js/sample.js');
			wp_enqueue_script('smartcms-scwaff-editor1');
			wp_register_script('smartcms-scwaff-script', SMARTCMS_SCWAFF_URL .'js/script.js');
			wp_enqueue_script('smartcms-scwaff-script');
			
			wp_enqueue_style('thickbox'); // call to media files in wp
			wp_enqueue_script('thickbox');
			wp_enqueue_script( 'media-upload'); 
			wp_enqueue_media();
			
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_register_style( 'jquery-ui', '//code.jquery.com/ui/1.13.0-rc.2/themes/smoothness/jquery-ui.css' );
			wp_enqueue_style( 'jquery-ui' );
			
			wp_register_script('smartcms-scwaff-colorboxjs', 'https://cdn.jsdelivr.net/npm/jquery-colorbox@1.6.4/jquery.colorbox.min.js');
			wp_enqueue_script('smartcms-scwaff-colorboxjs');
			wp_register_style( 'smartcms-scwaff-colorboxcss', '//cdn.jsdelivr.net/npm/jquery-colorbox@1.6.4/example1/colorbox.css' );
			wp_enqueue_style( 'smartcms-scwaff-colorboxcss' );
			
			wp_register_script('smartcms-scwaff-submitsimple', SMARTCMS_SCWAFF_URL .'js/submitSimple.js');
			wp_enqueue_script('smartcms-scwaff-submitsimple');
			wp_register_script('smartcms-scwaff-submitgrouped', SMARTCMS_SCWAFF_URL .'js/submitGrouped.js');
			wp_enqueue_script('smartcms-scwaff-submitgrouped');
			wp_register_script('smartcms-scwaff-submitexternal', SMARTCMS_SCWAFF_URL .'js/submitExternal.js');
			wp_enqueue_script('smartcms-scwaff-submitexternal');
			wp_register_script('smartcms-scwaff-submitvariable', SMARTCMS_SCWAFF_URL .'js/submitVariable.js');
			wp_enqueue_script('smartcms-scwaff-submitvariable');
			wp_register_script('smartcms-scwaff-submitbookable', SMARTCMS_SCWAFF_URL .'js/submitBookable.js');
			wp_enqueue_script('smartcms-scwaff-submitbookable');
			?>
			<input class="smartcms_plugin_url" type="hidden" value="<?php echo SMARTCMS_SCWAFF_URL ?>">
			<input class="smartcms_user_id" type="hidden" value="<?php echo esc_attr($userId) ?>">
			<input class="smartcms_site_url" type="hidden" value="<?php echo site_url(); ?>">
			<div class="smartcms_scwaff">
				<input class="scwaff_input_header" id="scwaff_infor" type="radio" name="tabs" checked>
				<label class="scwaff_label_header" for="scwaff_infor"><i class="fas fa-info"></i> <?php echo esc_html__("Product Information", "scwaff-translate") ?></label>
				<input class="scwaff_input_header" id="scwaff_data" type="radio" name="tabs">
				<label class="scwaff_label_header" for="scwaff_data"><i class="fas fa-database"></i> <?php echo esc_html__("Product Data", "scwaff-translate") ?></label>
				<input class="scwaff_input_header" id="scwaff_images" type="radio" name="tabs">
				<label class="scwaff_label_header" for="scwaff_images"><i class="fas fa-image"></i> <?php echo esc_html__("Product Images", "scwaff-translate") ?></label>
				
				<section id="scwaff_infor_content">
					<div class="scwaff_proname">
						<label for="scwaff_proname"><?php echo esc_html__("Product Name", "scwaff-translate") ?></label>
						<input id="scwaff_proname">
					</div>
					<div class="scwaff_proshortdes">
						<label for="scwaff_proshortdes"><?php echo esc_html__("Product Short Description", "scwaff-translate") ?></label>
						<textarea id="scwaff_proshortdes"></textarea>
					</div>
					<div class="scwaff_prodes">
						<label for="scwaff_prodes"><?php echo esc_html__("Product Description", "scwaff-translate") ?></label>
						<textarea id="scwaff_prodes"></textarea>
					</div>
					<div class="scwaff_procat">
						<label><?php echo esc_html__("Product Categories", "scwaff-translate") ?></label>
						<?php
							foreach($product_categories as $cat){
								?>
								<div class="product_category">
									<label style="width: auto; font-weight: normal;" for="product_categories<?php echo esc_attr($cat->term_id) ?>"><?php echo esc_attr($cat->name) ?></label>
									<input style="width: auto; margin-left: 10px; margin-top: 1px;" value="<?php echo esc_attr($cat->term_id) ?>" type="checkbox" id="product_categories<?php echo esc_attr($cat->term_id) ?>" name="product_categories[]">
								</div>
								<?php
							}
						?>
					</div>
					<div class="scwaff_protags">
						<label for="scwaff_protags"><?php echo esc_html__("Product Tags", "scwaff-translate") ?></label>
						<input id="scwaff_protags" placeholder="<?php echo esc_html__("Separate tags with commas", "scwaff-translate") ?>">
					</div>
					<div class="scwaff_provisible">
						<label for="scwaff_provisible"><?php echo esc_html__("Catalog visibility", "scwaff-translate") ?></label>
						<div class="scwaff_provisible_content">
							<div class="scwaff_provisible_item">
								<label for="scwaff_provisible_item_visible"><?php echo esc_html__("Visible", "scwaff-translate") ?></label>
								<input checked="checked" id="scwaff_provisible_item_visible" value="visible" type="radio" name="scwaff_provisible">
							</div>
							<div class="scwaff_provisible_item">
								<label for="scwaff_provisible_item_catalog"><?php echo esc_html__("Catalog", "scwaff-translate") ?></label>
								<input id="scwaff_provisible_item_catalog" value="catalog" type="radio" name="scwaff_provisible">
							</div>
							<div class="scwaff_provisible_item">
								<label for="scwaff_provisible_item_search"><?php echo esc_html__("Search", "scwaff-translate") ?></label>
								<input id="scwaff_provisible_item_search" value="search" type="radio" name="scwaff_provisible">
							</div>
							<div class="scwaff_provisible_item">
								<label for="scwaff_provisible_item_hidden"><?php echo esc_html__("Hidden", "scwaff-translate") ?></label>
								<input id="scwaff_provisible_item_hidden" value="hidden" type="radio" name="scwaff_provisible">
							</div>
						</div>
					</div>
					<div class="scwaff_profeatured">
						<label for="scwaff_profeatured"><?php echo esc_html__("Featured Product", "scwaff-translate") ?></label>
						<input id="scwaff_profeatured" type="checkbox">
						<span><?php echo esc_html__("Enable this option to feature this product.", "scwaff-translate") ?></span>
					</div>
				</section>
				<section id="scwaff_data_content">
					<div class="scwaff_protype">
						<label for="scwaff_protype"><?php echo esc_html__("Product Type", "scwaff-translate") ?></label>
						<ul id="scwaff_protype" name="clearfix">
							<li><span class="filter active" data-filter=".scwaff_protype_simple"><?php echo esc_html__("Simple product", "scwaff-translate") ?></span></li>
							<li><span class="filter" data-filter=".scwaff_protype_grouped"><?php echo esc_html__("Grouped product", "scwaff-translate") ?></span></li>
							<li><span class="filter" data-filter=".scwaff_protype_external"><?php echo esc_html__("External/Affiliate product", "scwaff-translate") ?></span></li>
							<li><span class="filter" data-filter=".scwaff_protype_variable"><?php echo esc_html__("Variable product", "scwaff-translate") ?></span></li>
							<?php
							$checkType = $wpdb->get_var("SELECT term_id FROM $wpdb->terms where name = 'booking'");
							if($checkType){
								?>
								<li><span class="filter" data-filter=".scwaff_protype_bookable"><?php echo esc_html__("Bookable product", "scwaff-translate") ?></span></li>
								<?php
							}
							?>
						</ul>
					</div>
					<div class="scwaff_prodata">
						<div class="scwaff_prodata_header">
							<span class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_bookable">
								<label for="scwaff_virtual"><?php echo esc_html__("Virtual", "scwaff-translate") ?></label>
								<input id="scwaff_virtual" type="checkbox">
							</span>
							<span class="scwaff_prodata_item scwaff_protype_simple">
								<label for="scwaff_downloadable"><?php echo esc_html__("Downloadable", "scwaff-translate") ?></label>
								<input id="scwaff_downloadable" type="checkbox">
							</span>
							<span class="scwaff_prodata_item scwaff_protype_bookable">
								<label for="scwaff_haspersons"><?php echo esc_html__("Has persons", "scwaff-translate") ?></label>
								<input id="scwaff_haspersons" type="checkbox">
							</span>
							<span class="scwaff_prodata_item scwaff_protype_bookable">
								<label for="scwaff_resources"><?php echo esc_html__("Has resources", "scwaff-translate") ?></label>
								<input id="scwaff_resources" type="checkbox">
							</span>
						</div>
						<div class="scwaff_prodata_content">
							<div class="scwaff_prodata_content_left">
								<div scwaff-data="general" class="active scwaff_prodata_item scwaff_protype_simple scwaff_protype_external scwaff_protype_variable scwaff_protype_bookable">
									<i class="fas fa-wrench"></i>
									<span><?php echo esc_html__("General", "scwaff-translate") ?></span>
								</div>
								<div scwaff-data="inventory" class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_grouped scwaff_protype_external scwaff_protype_variable">
									<i class="fas fa-archive"></i>
									<span><?php echo esc_html__("Inventory", "scwaff-translate") ?></span>
								</div>
								<div scwaff-data="shipping" class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_variable scwaff_protype_bookable">
									<i class="fas fa-truck"></i>
									<span><?php echo esc_html__("Shipping", "scwaff-translate") ?></span>
								</div>
								<div scwaff-data="linked" class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_grouped scwaff_protype_external scwaff_protype_variable scwaff_protype_bookable">
									<i class="fas fa-link"></i>
									<span><?php echo esc_html__("Linked Products", "scwaff-translate") ?></span>
								</div>
								<div scwaff-data="attributes" class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_grouped scwaff_protype_external scwaff_protype_variable scwaff_protype_bookable">
									<i class="fas fa-ticket-alt"></i>
									<span><?php echo esc_html__("Attributes", "scwaff-translate") ?></span>
								</div>
								<!--<div scwaff-data="variations" class="scwaff_prodata_item scwaff_protype_variable">
									<i class="fa fa-arrows" aria-hidden="true"></i>
									<span>Variations</span>
								</div>-->
								<div scwaff-data="advanced" class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_grouped scwaff_protype_external scwaff_protype_variable scwaff_protype_bookable">
									<i class="fas fa-cog"></i>
									<span><?php echo esc_html__("Advanced", "scwaff-translate") ?></span>
								</div>
								<div scwaff-data="availability" class="scwaff_prodata_item scwaff_protype_bookable">
									<i class="fas fa-bookmark"></i>
									<span><?php echo esc_html__("Availability", "scwaff-translate") ?></span>
								</div>
								<div scwaff-data="costs" class="scwaff_prodata_item scwaff_protype_bookable">
									<i class="fas fa-money-bill-alt"></i>
									<span><?php echo esc_html__("Costs", "scwaff-translate") ?></span>
								</div>
								<div scwaff-data="persons" class="scwaff_prodata_persons" style="display:none">
									<i class="fas fa-users"></i>
									<span><?php echo esc_html__("Persons", "scwaff-translate") ?></span>
								</div>
								<div scwaff-data="scresources" class="scwaff_prodata_resources" style="display:none">
									<i class="fas fa-book"></i>
									<span><?php echo esc_html__("Resources", "scwaff-translate") ?></span>
								</div>
								<div scwaff-data="commission" class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_grouped scwaff_protype_external scwaff_protype_variable scwaff_protype_bookable">
									<i class="fas fa-gift"></i>
									<span><?php echo esc_html__("Commission", "scwaff-translate") ?></span>
								</div>
							</div>
							<div class="scwaff_prodata_content_right">
								<div class="scwaff_prodata_content_right_item" id="scwaff_general">
									<div class="scwaff_prodata_item scwaff_protype_external">
										<label for="scwaff_producturl"><?php echo esc_html__("Product URL", "scwaff-translate") ?></label>
										<input id="scwaff_producturl">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_external">
										<label for="scwaff_buttontext"><?php echo esc_html__("Button Text", "scwaff-translate") ?></label>
										<input id="scwaff_buttontext">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_external">
										<label for="scwaff_regularprice"><?php echo esc_html__("Regular Price", "scwaff-translate") ?></label>
										<input id="scwaff_regularprice">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_external">
										<label for="scwaff_saleprice"><?php echo esc_html__("Sale Price", "scwaff-translate") ?></label>
										<input id="scwaff_saleprice">
									</div>
									<div class="scwaff_prodata_item_downloadablefiles">
										<label for="scwaff_downloadablefiles"><?php echo esc_html__("Downloadable files", "scwaff-translate") ?></label>
										<div class="scwaff_downloadablefiles">
											<div class="scwaff_downloadablefiles_header">
												<span class="scwaff_downloadablefiles_header_name"><?php echo esc_html__("Name", "scwaff-translate") ?></span>
												<span class="scwaff_downloadablefiles_header_url"><?php echo esc_html__("File URL", "scwaff-translate") ?></span>
											</div>
											<div class="scwaff_downloadablefiles_content">
												<div class="scwaff_downloadablefiles_content_item">
													<input class="scwaff_downloadablefiles_content_name">
													<input class="scwaff_downloadablefiles_content_url">
													<span class="scwaff_downloadablefiles_content_button"><img src="<?php echo SMARTCMS_SCWAFF_URL ?>images/browser_icon.png"></span>
													<span class="scwaff_downloadablefiles_content_remove"><i class="fa fa-trash" aria-hidden="true"></i></span>
												</div>
											</div>
											<span class="scwaff_downloadablefiles_add"><?php echo esc_html__("Add File", "scwaff-translate") ?></span>
										</div>
									</div>
									<div class="scwaff_prodata_item_downloadlimit">
										<label for="scwaff_downloadlimit"><?php echo esc_html__("Download Limit", "scwaff-translate") ?></label>
										<input id="scwaff_downloadlimit">
									</div>
									<div class="scwaff_prodata_item_downloadexpiry">
										<label for="scwaff_downloadexpiry"><?php echo esc_html__("Download Expiry", "scwaff-translate") ?></label>
										<input id="scwaff_downloadexpiry">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_external">
										<label for="scwaff_salepricedate"><?php echo esc_html__("Sale Price Dates", "scwaff-translate") ?></label>
										<input id="scwaff_salepricedatefrom" placeholder="<?php echo esc_html__("From… YYYY-MM-DD", "scwaff-translate") ?>"><br>
										<input id="scwaff_salepricedateto" placeholder="<?php echo esc_html__("To… YYYY-MM-DD", "scwaff-translate") ?>">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_external scwaff_protype_variable scwaff_protype_bookable">
										<label for="scwaff_taxstatus"><?php echo esc_html__("Tax Status", "scwaff-translate") ?></label>
										<select id="scwaff_taxstatus" name="scwaff_taxstatus">
											<option value="taxable" selected="selected"><?php echo esc_html__("Taxable", "scwaff-translate") ?></option>
											<option value="shipping"><?php echo esc_html__("Shipping only", "scwaff-translate") ?></option>
											<option value="none"><?php echo esc_html__("None", "scwaff-translate") ?></option>
										</select>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_external scwaff_protype_variable scwaff_protype_bookable">
										<label for="scwaff_taxclass"><?php echo esc_html__("Tax Class", "scwaff-translate") ?></label>
										<select id="scwaff_taxclass" name="scwaff_taxclass">
											<option value="" selected="selected"><?php echo esc_html__("Standard", "scwaff-translate") ?></option>
											<option value="reduced-rate"><?php echo esc_html__("Reduced Rate", "scwaff-translate") ?></option>
											<option value="zero-rate"><?php echo esc_html__("Zero Rate", "scwaff-translate") ?></option>
										</select>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_bookingduration"><?php echo esc_html__("Booking Duration", "scwaff-translate") ?></label>
										<select name="scwaff_bookingduration" id="scwaff_bookingduration">
											<option value="fixed" selected="selected"><?php echo esc_html__("Fixed blocks of", "scwaff-translate") ?></option>
											<option value="customer"><?php echo esc_html__("Customer defined blocks of", "scwaff-translate") ?></option>
										</select>
										<input id="scwaff_bookingduration_duration" step="1" type="number">
										<select name="scwaff_bookingduration_unit" id="scwaff_bookingduration_unit">
											<option value="month" selected="selected"><?php echo esc_html__("Month(s)", "scwaff-translate") ?></option>
											<option value="day"><?php echo esc_html__("Day(s)", "scwaff-translate") ?></option>
											<option value="hour"><?php echo esc_html__("Hour(s)", "scwaff-translate") ?></option>
											<option value="minute"><?php echo esc_html__("Minutes(s)", "scwaff-translate") ?></option>
										</select>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_minimum_duration"><?php echo esc_html__("Minimum Duration", "scwaff-translate") ?></label>
										<input id="scwaff_minimum_duration" step="1" type="number">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_maximum_duration"><?php echo esc_html__("Maximum Duration", "scwaff-translate") ?></label>
										<input id="scwaff_maximum_duration" step="1" type="number">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_calendardisplaymode"><?php echo esc_html__("Calendar Display Mode", "scwaff-translate") ?></label>
										<select id="scwaff_calendardisplaymode" name="scwaff_calendardisplaymode">
											<option value="" selected="selected"><?php echo esc_html__("Display calendar on click", "scwaff-translate") ?></option>
											<option value="always_visible"><?php echo esc_html__("Calendar always visible", "scwaff-translate") ?></option>
										</select>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_requiresconfirmation"><?php echo esc_html__("Requires Confirmation", "scwaff-translate") ?></label>
										<div>
											<input id="scwaff_requiresconfirmation" type="checkbox">
											<span><?php echo esc_html__("Check this box if the booking requires admin approval/confirmation. Payment will not be taken during checkout.", "scwaff-translate") ?></span>
										</div>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_canbecancelled"><?php echo esc_html__("Can be cancelled", "scwaff-translate") ?></label>
										<div>
											<input id="scwaff_canbecancelled" type="checkbox">
											<span><?php echo esc_html__("Check this box if the booking can be cancelled by the customer after it has been purchased. A refund will not be sent automatically.", "scwaff-translate") ?></span>
										</div>
									</div>
								</div>
								<div class="scwaff_prodata_content_right_item" id="scwaff_inventory" style="display:none">
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_grouped scwaff_protype_external scwaff_protype_variable">
										<label for="scwaff_prosku"><?php echo esc_html__("SKU", "scwaff-translate") ?></label>
										<input id="scwaff_prosku">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_variable">
										<label for="scwaff_managestock"><?php echo esc_html__("Manage Stock?", "scwaff-translate") ?></label>
										<div>
											<input id="scwaff_managestock" type="checkbox">
											<span><?php echo esc_html__("Enable stock management at product level", "scwaff-translate") ?></span>
										</div>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_variable">
										<label for="scwaff_stockquantity"><?php echo esc_html__("Stock Quantity", "scwaff-translate") ?></label>
										<input id="scwaff_stockquantity" disabled="disabled">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_variable">
										<label for="scwaff_allowbackorders"><?php echo esc_html__("Allow Backorders?", "scwaff-translate") ?></label>
										<select id="scwaff_allowbackorders" name="scwaff_allowbackorders" disabled="disabled">
											<option value="no" selected="selected"><?php echo esc_html__("Do not allow", "scwaff-translate") ?></option>
											<option value="notify"><?php echo esc_html__("Allow, but notify customer", "scwaff-translate") ?></option>
											<option value="yes"><?php echo esc_html__("Allow", "scwaff-translate") ?></option>
										</select>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_grouped">
										<label for="scwaff_stockstatus"><?php echo esc_html__("Stock Status", "scwaff-translate") ?></label>
										<select id="scwaff_stockstatus" name="scwaff_stockstatus">
											<option value="instock" selected="selected"><?php echo esc_html__("In stock", "scwaff-translate") ?></option>
											<option value="outofstock"><?php echo esc_html__("Out of stock", "scwaff-translate") ?></option>
										</select>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_variable">
										<label for="scwaff_soldindividually"><?php echo esc_html__("Sold Individually", "scwaff-translate") ?></label>
										<div>
											<input id="scwaff_soldindividually" type="checkbox">
											<span><?php echo esc_html__("Enable this to only allow one of this item to be bought in a single order", "scwaff-translate") ?></span>
										</div>
									</div>
								</div>
								<div class="scwaff_prodata_content_right_item" id="scwaff_shipping" style="display:none">
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_bookable scwaff_protype_variable">
										<label for="scwaff_proweight"><?php echo esc_html__("Weight(lbs)", "scwaff-translate") ?></label>
										<input id="scwaff_proweight">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_bookable scwaff_protype_variable">
										<label for="scwaff_prodimensions"><?php echo esc_html__("Dimensions(in)", "scwaff-translate") ?></label>
										<div>
											<input id="scwaff_prodimensions_length" placeholder="<?php echo esc_html__("Length", "scwaff-translate") ?>">
											<input id="scwaff_prodimensions_width" placeholder="<?php echo esc_html__("Width", "scwaff-translate") ?>">
											<input id="scwaff_prodimensions_height" placeholder="<?php echo esc_html__("Height", "scwaff-translate") ?>">
										</div>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_bookable scwaff_protype_variable">
										<label for="scwaff_shippingclass"><?php echo esc_html__("Shipping Class", "scwaff-translate") ?></label>
										<select id="scwaff_shippingclass" name="scwaff_shippingclass">
											<option value="-1" selected="selected"><?php echo esc_html__("No shipping class", "scwaff-translate") ?></option>
										</select>
									</div>
								</div>
								<div class="scwaff_prodata_content_right_item" id="scwaff_linked" style="display:none">
									<div class="scwaff_prodata_item scwaff_protype_grouped scwaff_protype_simple scwaff_protype_external scwaff_protype_variable scwaff_protype_bookable">
										<label for="scwaff_upsells"><?php echo esc_html__("Up-sells", "scwaff-translate") ?></label>
										<div class="scwaff_chooseproducts">
											<input id="scwaff_upsells">
											<span class="scwaff_upsells_span"><a href="#scwaff_chooose_products"><?php echo esc_html__("Choose Products", "scwaff-translate") ?></a></span>
										</div>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_grouped scwaff_protype_simple scwaff_protype_external scwaff_protype_variable scwaff_protype_bookable">
										<label for="scwaff_crosssells"><?php echo esc_html__("Cross-sells", "scwaff-translate") ?></label>
										<div class="scwaff_chooseproducts">
											<input id="scwaff_crosssells">
											<span class="scwaff_crosssells_span"><a href="#scwaff_chooose_products"><?php echo esc_html__("Choose Products", "scwaff-translate") ?></a></span>
										</div>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_grouped">
										<label for="scwaff_groupedproducts"><?php echo esc_html__("Grouped Products", "scwaff-translate") ?></label>
										<div class="scwaff_chooseproducts">
											<input id="scwaff_groupedproducts">
											<span class="scwaff_groupedproducts_span"><a href="#scwaff_chooose_products"><?php echo esc_html__("Choose Products", "scwaff-translate") ?></a></span>
										</div>
									</div>
									<div style='display:none'>
										<div id="scwaff_chooose_products" style='padding:10px; background:#fff;'>
											<div class="scwaff_chooose_products_header">
												<label for="scwaff_chooose_products_header"><?php echo esc_html__("Search Products", "scwaff-translate") ?></label>
												<input id="scwaff_chooose_products_header">
												<span class="scwaff_chooose_products_add"><?php echo esc_html__("Add Products", "scwaff-translate") ?></span>
											</div>
											<div class="scwaff_chooose_products_content">
												
											</div>
										</div>
									</div>
								</div>
								<div class="scwaff_prodata_content_right_item" id="scwaff_attributes" style="display:none">
									<?php
									$checkKey = 0;
									foreach($taxonomy_terms as $key=>$attr){
										?>
										<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_grouped scwaff_protype_external scwaff_protype_variable scwaff_protype_bookable">
											<label><?php echo esc_attr($key) ?></label>
											<?php
											if($attributes[$checkKey]->attribute_type == "select"){
												?>
												<select class="scwaff_attributes_select" multiple="multiple">
													<option value="">-- <?php echo esc_html__("Select Option", "scwaff-translate") ?> --</option>
													<?php
													foreach($attr as $att){
														?>
														<option value="<?php echo esc_attr($att->term_taxonomy_id) ?>"><?php echo esc_attr($att->name) ?></option>
														<?php
													}
													?>
												</select>
												<?php
											}else{
												?>
												<input data-attrid="<?php echo esc_attr($attributes[$checkKey]->attribute_id) ?>" class="scwaff_attributes_text" placeholder='"|" <?php echo esc_html__("separate terms", "scwaff-translate") ?>'>
												<?php
											}
											?>
										</div>
										<?php
										$checkKey++;
									}
									?>
								</div>
								<!--<div class="scwaff_prodata_content_right_item" id="scwaff_variations" style="display:none">
									wa
								</div>-->
								<div class="scwaff_prodata_content_right_item" id="scwaff_advanced" style="display:none">
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_grouped scwaff_protype_external scwaff_protype_variable scwaff_protype_bookable">
										<label for="scwaff_pronote"><?php echo esc_html__("Purchase Note", "scwaff-translate") ?></label>
										<textarea id="scwaff_pronote"></textarea>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_grouped scwaff_protype_external scwaff_protype_variable scwaff_protype_bookable">
										<label for="scwaff_promenuorder"><?php echo esc_html__("Menu Order", "scwaff-translate") ?></label>
										<input id="scwaff_promenuorder" step="1" type="number">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_grouped scwaff_protype_external scwaff_protype_variable scwaff_protype_bookable">
										<label for="scwaff_proenablereview"><?php echo esc_html__("Enable Reviews", "scwaff-translate") ?></label>
										<input id="scwaff_proenablereview" type="checkbox">
									</div>
								</div>
								<div class="scwaff_prodata_content_right_item" id="scwaff_availability" style="display:none">
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_promaxbookingperblock"><?php echo esc_html__("Max Bookings Per Block", "scwaff-translate") ?></label>
										<input id="scwaff_promaxbookingperblock" step="1" type="number">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_prominimumblockbookable"><?php echo esc_html__("Minimum Block Bookable", "scwaff-translate") ?></label>
										<div>
											<input id="scwaff_prominimumblockbookable_input" step="1" type="number" min="0">
											<select name="scwaff_prominimumblockbookable_select" id="scwaff_prominimumblockbookable_select">
												<option value="month" selected="selected"><?php echo esc_html__("Month(s)", "scwaff-translate") ?></option>
												<option value="week"><?php echo esc_html__("Week(s)", "scwaff-translate") ?></option>
												<option value="day"><?php echo esc_html__("Day(s)", "scwaff-translate") ?></option>
												<option value="hour"><?php echo esc_html__("Hour(s)", "scwaff-translate") ?></option>
											</select>
										</div>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_promaximumblockbookable"><?php echo esc_html__("Maximum Block Bookable", "scwaff-translate") ?></label>
										<div>
											<input id="scwaff_promaximumblockbookable_input" step="1" type="number" min="1">
											<select name="scwaff_promaximumblockbookable_select" id="scwaff_promaximumblockbookable_select">
												<option value="month" selected="selected"><?php echo esc_html__("Month(s)", "scwaff-translate") ?></option>
												<option value="week"><?php echo esc_html__("Week(s)", "scwaff-translate") ?></option>
												<option value="day"><?php echo esc_html__("Day(s)", "scwaff-translate") ?></option>
												<option value="hour"><?php echo esc_html__("Hour(s)", "scwaff-translate") ?></option>
											</select>
										</div>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_proalldatesare"><?php echo esc_html__("All Dates Are", "scwaff-translate") ?>...</label>
										<select id="scwaff_proalldatesare" name="scwaff_proalldatesare">
											<option value="available" selected="selected"><?php echo esc_html__("available by default", "scwaff-translate") ?></option>
											<option value="non-available"><?php echo esc_html__("not-available by default", "scwaff-translate") ?></option>
										</select>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_procheckrulesagainst"><?php echo esc_html__("Check Rules Against", "scwaff-translate") ?>...</label>
										<select id="scwaff_procheckrulesagainst" name="scwaff_procheckrulesagainst">
											<option value="" selected="selected"><?php echo esc_html__("All blocks being booked", "scwaff-translate") ?></option>
											<option value="start"><?php echo esc_html__("The starting block only", "scwaff-translate") ?></option>
										</select>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_profirstblockstartsat"><?php echo esc_html__("First Block Starts At", "scwaff-translate") ?>...</label>
										<input id="scwaff_profirstblockstartsat" placeholder="<?php echo esc_html__("HH:MM", "scwaff-translate") ?>">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable scwaff_probookingrange">
										<div class="scwaff_probookingrange_header">
											<span class="scwaff_probookingrange_header_type"><?php echo esc_html__("Range Type", "scwaff-translate") ?></span>
											<span class="scwaff_probookingrange_header_from"><?php echo esc_html__("From", "scwaff-translate") ?></span>
											<span class="scwaff_probookingrange_header_to"><?php echo esc_html__("To", "scwaff-translate") ?></span>
											<span class="scwaff_probookingrange_header_bookable"><?php echo esc_html__("Bookable", "scwaff-translate") ?></span>
											<span class="scwaff_probookingrange_header_priority"><?php echo esc_html__("Priority", "scwaff-translate") ?></span>
										</div>
										<div class="scwaff_probookingrange_content">
											<div class="scwaff_probookingrange_content_item">
												<div class="scwaff_probookingrange_content_item_type">
													<select class="scwaff_probookingrange_content_item_type_select">
														<option value="custom" selected="selected"><?php echo esc_html__("Custom date range", "scwaff-translate") ?></option>
														<option value="months"><?php echo esc_html__("Range of months", "scwaff-translate") ?></option>
														<option value="weeks"><?php echo esc_html__("Range of weeks", "scwaff-translate") ?></option>
														<option value="days"><?php echo esc_html__("Range of days", "scwaff-translate") ?></option>
														<optgroup label="<?php echo esc_html__("Time Ranges", "scwaff-translate") ?>">
															<option value="time"><?php echo esc_html__("Time Range (all week)", "scwaff-translate") ?></option>
															<option value="time:1"><?php echo esc_html__("Monday", "scwaff-translate") ?></option>
															<option value="time:2"><?php echo esc_html__("Tuesday", "scwaff-translate") ?></option>
															<option value="time:3"><?php echo esc_html__("Wednesday", "scwaff-translate") ?></option>
															<option value="time:4"><?php echo esc_html__("Thursday", "scwaff-translate") ?></option>
															<option value="time:5"><?php echo esc_html__("Friday", "scwaff-translate") ?></option>
															<option value="time:6"><?php echo esc_html__("Saturday", "scwaff-translate") ?></option>
															<option value="time:7"><?php echo esc_html__("Sunday", "scwaff-translate") ?></option>
														</optgroup>
													</select>
												</div>
												<div class="scwaff_probookingrange_content_item_from">
													<div class="scwaff_probookingrange_content_item_from_custom">
														<input class="probookingrange_item_fromcustom">
													</div>
													<div class="scwaff_probookingrange_content_item_from_months" style="display:none">
														<select class="probookingrange_item_frommonths">
															<option value="1"><?php echo esc_html__("January", "scwaff-translate") ?></option>
															<option value="2"><?php echo esc_html__("Febuary", "scwaff-translate") ?></option>
															<option value="3"><?php echo esc_html__("March", "scwaff-translate") ?></option>
															<option value="4"><?php echo esc_html__("April", "scwaff-translate") ?></option>
															<option value="5"><?php echo esc_html__("May", "scwaff-translate") ?></option>
															<option value="6"><?php echo esc_html__("June", "scwaff-translate") ?></option>
															<option value="7"><?php echo esc_html__("July", "scwaff-translate") ?></option>
															<option value="8"><?php echo esc_html__("August", "scwaff-translate") ?></option>
															<option value="9"><?php echo esc_html__("September", "scwaff-translate") ?></option>
															<option value="10"><?php echo esc_html__("October", "scwaff-translate") ?></option>
															<option value="11"><?php echo esc_html__("November", "scwaff-translate") ?></option>
															<option value="12"><?php echo esc_html__("December", "scwaff-translate") ?></option>
														</select>
													</div>
													<div class="scwaff_probookingrange_content_item_from_weeks" style="display:none">
														<select class="probookingrange_item_fromweeks">
															<?php
															for($i = 1; $i<= 53; $i++){
																?><option value="<?php echo esc_attr($i) ?>"><?php echo esc_html__("Week", "scwaff-translate") ?> <?php echo esc_attr($i) ?></option><?php
															}
															?>
														</select>
													</div>
													<div class="scwaff_probookingrange_content_item_from_days" style="display:none">
														<select class="probookingrange_item_fromdays">
															<option value="1"><?php echo esc_html__("Monday", "scwaff-translate") ?></option>
															<option value="2"><?php echo esc_html__("Tuesday", "scwaff-translate") ?></option>
															<option value="3"><?php echo esc_html__("Wednesday", "scwaff-translate") ?></option>
															<option value="4"><?php echo esc_html__("Thursday", "scwaff-translate") ?></option>
															<option value="5"><?php echo esc_html__("Friday", "scwaff-translate") ?></option>
															<option value="6"><?php echo esc_html__("Saturday", "scwaff-translate") ?></option>
															<option value="7"><?php echo esc_html__("Sunday", "scwaff-translate") ?></option>
														</select>
													</div>
													<div class="scwaff_probookingrange_content_item_from_time" style="display:none">
														<input class="probookingrange_item_fromtime">
													</div>
												</div>
												<div class="scwaff_probookingrange_content_item_to">
													<div class="scwaff_probookingrange_content_item_to_custom">
														<input class="probookingrange_item_tocustom">
													</div>
													<div class="scwaff_probookingrange_content_item_to_months" style="display:none">
														<select class="probookingrange_item_tomonths">
															<option value="1"><?php echo esc_html__("January", "scwaff-translate") ?></option>
															<option value="2"><?php echo esc_html__("Febuary", "scwaff-translate") ?></option>
															<option value="3"><?php echo esc_html__("March", "scwaff-translate") ?></option>
															<option value="4"><?php echo esc_html__("April", "scwaff-translate") ?></option>
															<option value="5"><?php echo esc_html__("May", "scwaff-translate") ?></option>
															<option value="6"><?php echo esc_html__("June", "scwaff-translate") ?></option>
															<option value="7"><?php echo esc_html__("July", "scwaff-translate") ?></option>
															<option value="8"><?php echo esc_html__("August", "scwaff-translate") ?></option>
															<option value="9"><?php echo esc_html__("September", "scwaff-translate") ?></option>
															<option value="10"><?php echo esc_html__("October", "scwaff-translate") ?></option>
															<option value="11"><?php echo esc_html__("November", "scwaff-translate") ?></option>
															<option value="12"><?php echo esc_html__("December", "scwaff-translate") ?></option>
														</select>
													</div>
													<div class="scwaff_probookingrange_content_item_to_weeks" style="display:none">
														<select class="probookingrange_item_toweeks">
															<?php
															for($i = 1; $i<= 53; $i++){
																?><option value="<?php echo esc_attr($i) ?>"><?php echo esc_html__("Week", "scwaff-translate") ?> <?php echo esc_attr($i) ?></option><?php
															}
															?>
														</select>
													</div>
													<div class="scwaff_probookingrange_content_item_to_days" style="display:none">
														<select class="probookingrange_item_todays">
															<option value="1"><?php echo esc_html__("Monday", "scwaff-translate") ?></option>
															<option value="2"><?php echo esc_html__("Tuesday", "scwaff-translate") ?></option>
															<option value="3"><?php echo esc_html__("Wednesday", "scwaff-translate") ?></option>
															<option value="4"><?php echo esc_html__("Thursday", "scwaff-translate") ?></option>
															<option value="5"><?php echo esc_html__("Friday", "scwaff-translate") ?></option>
															<option value="6"><?php echo esc_html__("Saturday", "scwaff-translate") ?></option>
															<option value="7"><?php echo esc_html__("Sunday", "scwaff-translate") ?></option>
														</select>
													</div>
													<div class="scwaff_probookingrange_content_item_to_time" style="display:none">
														<input class="probookingrange_item_totime">
													</div>
												</div>
												<div class="scwaff_probookingrange_content_item_bookable">
													<select class="probookingrange_item_bookable">
														<option value="no"><?php echo esc_html__("No", "scwaff-translate") ?></option>
														<option value="yes"><?php echo esc_html__("Yes", "scwaff-translate") ?></option>
													</select>
												</div>
												<div class="scwaff_probookingrange_content_itempriority">
													<input class="probookingrange_item_priority">
												</div>
											</div>
										</div>
										<div class="scwaff_probookingrange_add">
											<span class="scwaff_probookingrange_add_button"><?php echo esc_html__("Add Range", "scwaff-translate") ?></span>
										</div>
									</div>
								</div>
								<div class="scwaff_prodata_content_right_item" id="scwaff_costs" style="display:none">
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_probasecost"><?php echo esc_html__("Base Cost", "scwaff-translate") ?></label>
										<input id="scwaff_probasecost">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_problockcost"><?php echo esc_html__("Block Cost", "scwaff-translate") ?></label>
										<input id="scwaff_problockcost">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_prodisplaycost"><?php echo esc_html__("Display Cost", "scwaff-translate") ?></label>
										<input id="scwaff_prodisplaycost">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable scwaff_prodata_costrange">
										<div class="scwaff_prodata_costrange_header">
											<span class="scwaff_prodata_costrange_header_type"><?php echo esc_html__("Range Type", "scwaff-translate") ?></span>
											<span class="scwaff_prodata_costrange_header_from"><?php echo esc_html__("From", "scwaff-translate") ?></span>
											<span class="scwaff_prodata_costrange_header_to"><?php echo esc_html__("To", "scwaff-translate") ?></span>
											<span class="scwaff_prodata_costrange_header_basecost"><?php echo esc_html__("Base Cost", "scwaff-translate") ?></span>
											<span class="scwaff_prodata_costrange_header_blockcost"><?php echo esc_html__("Block Cost", "scwaff-translate") ?></span>
										</div>
										<div class="scwaff_prodata_costrange_content">
											<div class="scwaff_prodata_costrange_content_item">
												<div class="scwaff_prodata_costrange_content_item_type">
													<select class="scwaff_prodata_costrange_content_item_type_select">
														<option value="custom" selected="selected"><?php echo esc_html__("Custom date range", "scwaff-translate") ?></option>
														<option value="months"><?php echo esc_html__("Range of months", "scwaff-translate") ?></option>
														<option value="weeks"><?php echo esc_html__("Range of weeks", "scwaff-translate") ?></option>
														<option value="days"><?php echo esc_html__("Range of days", "scwaff-translate") ?></option>
														<option value="time"><?php echo esc_html__("Time Range", "scwaff-translate") ?></option>
														<option value="persons">P<?php echo esc_html__("erson count", "scwaff-translate") ?></option>
														<option value="blocks"><?php echo esc_html__("Block count", "scwaff-translate") ?></option>
														<optgroup label="<?php echo esc_html__("Time Ranges", "scwaff-translate") ?>">
															<option value="time"><?php echo esc_html__("Time Range (all week)", "scwaff-translate") ?></option>
															<option value="time:1"><?php echo esc_html__("Monday", "scwaff-translate") ?></option>
															<option value="time:2"><?php echo esc_html__("Tuesday", "scwaff-translate") ?></option>
															<option value="time:3"><?php echo esc_html__("Wednesday", "scwaff-translate") ?></option>
															<option value="time:4"><?php echo esc_html__("Thursday", "scwaff-translate") ?></option>
															<option value="time:5"><?php echo esc_html__("Friday", "scwaff-translate") ?></option>
															<option value="time:6"><?php echo esc_html__("Saturday", "scwaff-translate") ?></option>
															<option value="time:7"><?php echo esc_html__("Sunday", "scwaff-translate") ?></option>
														</optgroup>
													</select>
												</div>
												<div class="scwaff_prodata_costrange_content_item_from">
													<div class="scwaff_prodata_costrange_content_item_from_custom">
														<input class="scwaff_proccif_custom">
													</div>
													<div class="scwaff_prodata_costrange_content_item_from_months" style="display:none">
														<select name="scwaff_proccif_months">
															<option value="1"><?php echo esc_html__("January", "scwaff-translate") ?></option>
															<option value="2"><?php echo esc_html__("Febuary", "scwaff-translate") ?></option>
															<option value="3"><?php echo esc_html__("March", "scwaff-translate") ?></option>
															<option value="4"><?php echo esc_html__("April", "scwaff-translate") ?></option>
															<option value="5"><?php echo esc_html__("May", "scwaff-translate") ?></option>
															<option value="6"><?php echo esc_html__("June", "scwaff-translate") ?></option>
															<option value="7"><?php echo esc_html__("July", "scwaff-translate") ?></option>
															<option value="8"><?php echo esc_html__("August", "scwaff-translate") ?></option>
															<option value="9"><?php echo esc_html__("September", "scwaff-translate") ?></option>
															<option value="10"><?php echo esc_html__("October", "scwaff-translate") ?></option>
															<option value="11"><?php echo esc_html__("November", "scwaff-translate") ?></option>
															<option value="12"><?php echo esc_html__("December", "scwaff-translate") ?></option>
														</select>
													</div>
													<div class="scwaff_prodata_costrange_content_item_from_weeks" style="display:none">
														<select class="scwaff_proccif_weeks">
															<?php
															for($i = 1; $i<= 53; $i++){
																?><option value="<?php echo esc_attr($i) ?>"><?php echo esc_html__("Week", "scwaff-translate") ?> <?php echo esc_attr($i) ?></option><?php
															}
															?>
														</select>
													</div>
													<div class="scwaff_prodata_costrange_content_item_from_days" style="display:none">
														<select name="scwaff_proccif_days">
															<option value="1"><?php echo esc_html__("Monday", "scwaff-translate") ?></option>
															<option value="2"><?php echo esc_html__("Tuesday", "scwaff-translate") ?></option>
															<option value="3"><?php echo esc_html__("Wednesday", "scwaff-translate") ?></option>
															<option value="4"><?php echo esc_html__("Thursday", "scwaff-translate") ?></option>
															<option value="5"><?php echo esc_html__("Friday", "scwaff-translate") ?></option>
															<option value="6"><?php echo esc_html__("Saturday", "scwaff-translate") ?></option>
															<option value="7"><?php echo esc_html__("Sunday", "scwaff-translate") ?></option>
														</select>
													</div>
													<div class="scwaff_prodata_costrange_content_item_from_time" style="display:none">
														<input class="scwaff_proccif_time" placeholder="<?php echo esc_html__("HH:MM", "scwaff-translate") ?>">
													</div>
													<div class="scwaff_prodata_costrange_content_item_from_perblock" style="display:none">
														<input class="scwaff_proccif_perblock">
													</div>
												</div>
												<div class="scwaff_prodata_costrange_content_item_to">
													<div class="scwaff_prodata_costrange_content_item_to_custom">
														<input class="scwaff_proccit_custom">
													</div>
													<div class="scwaff_prodata_costrange_content_item_to_months" style="display:none">
														<select name="scwaff_proccit_months">
															<option value="1"><?php echo esc_html__("January", "scwaff-translate") ?></option>
															<option value="2"><?php echo esc_html__("Febuary", "scwaff-translate") ?></option>
															<option value="3"><?php echo esc_html__("March", "scwaff-translate") ?></option>
															<option value="4"><?php echo esc_html__("April", "scwaff-translate") ?></option>
															<option value="5"><?php echo esc_html__("May", "scwaff-translate") ?></option>
															<option value="6"><?php echo esc_html__("June", "scwaff-translate") ?></option>
															<option value="7"><?php echo esc_html__("July", "scwaff-translate") ?></option>
															<option value="8"><?php echo esc_html__("August", "scwaff-translate") ?></option>
															<option value="9"><?php echo esc_html__("September", "scwaff-translate") ?></option>
															<option value="10"><?php echo esc_html__("October", "scwaff-translate") ?></option>
															<option value="11"><?php echo esc_html__("November", "scwaff-translate") ?></option>
															<option value="12"><?php echo esc_html__("December", "scwaff-translate") ?></option>
														</select>
													</div>
													<div class="scwaff_prodata_costrange_content_item_to_weeks" style="display:none">
														<select class="scwaff_proccit_weeks">
															<?php
															for($i = 1; $i<= 53; $i++){
																?><option value="<?php echo esc_attr($i) ?>"><?php echo esc_html__("Week", "scwaff-translate") ?> <?php echo esc_attr($i) ?></option><?php
															}
															?>
														</select>
													</div>
													<div class="scwaff_prodata_costrange_content_item_to_days" style="display:none">
														<select name="scwaff_proccit_days">
															<option value="1"><?php echo esc_html__("Monday", "scwaff-translate") ?></option>
															<option value="2"><?php echo esc_html__("Tuesday", "scwaff-translate") ?></option>
															<option value="3"><?php echo esc_html__("Wednesday", "scwaff-translate") ?></option>
															<option value="4"><?php echo esc_html__("Thursday", "scwaff-translate") ?></option>
															<option value="5"><?php echo esc_html__("Friday", "scwaff-translate") ?></option>
															<option value="6"><?php echo esc_html__("Saturday", "scwaff-translate") ?></option>
															<option value="7"><?php echo esc_html__("Sunday", "scwaff-translate") ?></option>
														</select>
													</div>
													<div class="scwaff_prodata_costrange_content_item_to_time" style="display:none">
														<input class="scwaff_proccit_time" placeholder="<?php echo esc_html__("HH:MM", "scwaff-translate") ?>">
													</div>
													<div class="scwaff_prodata_costrange_content_item_to_perblock" style="display:none">
														<input class="scwaff_proccit_perblock">
													</div>
												</div>
												<div class="scwaff_prodata_costrange_content_item_basecost">
													<select name="scwaff_prodata_costrange_content_item_basecost_select">
														<option selected="selected" value="">+</option>
														<option value="minus">-</option>
														<option value="times">×</option>
														<option value="divide">÷</option>
													</select>
													<input class="scwaff_prodata_costrange_content_item_basecost_input">
												</div>
												<div class="scwaff_prodata_costrange_content_item_blockcost">
													<select name="scwaff_prodata_costrange_content_item_blockcost_select">
														<option selected="selected" value="">+</option>
														<option value="minus">-</option>
														<option value="times">×</option>
														<option value="divide">÷</option>
													</select>
													<input class="scwaff_prodata_costrange_content_item_blockcost_input">
												</div>
											</div>
										</div>
										<div class="scwaff_prodata_costrange_add">
											<span class="scwaff_prodata_costrange_add_button"><?php echo esc_html__("Add Range", "scwaff-translate") ?></span>
										</div>
									</div>
								</div>
								<div class="scwaff_prodata_content_right_item" id="scwaff_persons" style="display:none">
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_prominpersons"><?php echo esc_html__("Min Persons", "scwaff-translate") ?></label>
										<input id="scwaff_prominpersons" min="1" step="1" type="number">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_promaxpersons"><?php echo esc_html__("Max Persons", "scwaff-translate") ?></label>
										<input id="scwaff_promaxpersons" min="1" step="1" type="number">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_promultipleallcosts"><?php echo esc_html__("Multiply all costs by person count", "scwaff-translate") ?></label>
										<input id="scwaff_promultipleallcosts" type="checkbox">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_procountpersonsasbookings"><?php echo esc_html__("Count persons as bookings", "scwaff-translate") ?></label>
										<input id="scwaff_procountpersonsasbookings" type="checkbox">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_proenablepersontypes"><?php echo esc_html__("Enable person types", "scwaff-translate") ?></label>
										<input id="scwaff_proenablepersontypes" type="checkbox">
									</div>
								</div>
								<div class="scwaff_prodata_content_right_item" id="scwaff_scresources" style="display:none">
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_proresourceslabel"><?php echo esc_html__("Label", "scwaff-translate") ?></label>
										<input id="scwaff_proresourceslabel">
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable">
										<label for="scwaff_proresourcesare"><?php echo esc_html__("Resources are", "scwaff-translate") ?>...</label>
										<select id="scwaff_proresourcesare" name="scwaff_proresourcesare">
											<option value="customer" selected="selected"><?php echo esc_html__("Customer selected", "scwaff-translate") ?></option>
											<option value="automatic"><?php echo esc_html__("Automatically assigned", "scwaff-translate") ?></option>
										</select>
									</div>
									<div class="scwaff_prodata_item scwaff_protype_bookable scwaff_proresources">
										<?php
										if($resources){
											?>
											<div class="scwaff_proresources_content">
												<div class="scwaff_proresources_content_header"><?php echo esc_html__("Resources", "scwaff-translate") ?></div>
												<div class="scwaff_proresources_content_res">
													<?php
													foreach($resources as $res){
														?>
														<div class="scwaff_proresources_content_res_item">
															<span class="scwaff_proresources_content_res_item_id"><input value="<?php echo esc_attr($res->ID) ?>" type="checkbox" class="scwaff_proresources_crii"></span>
															<span class="scwaff_proresources_content_res_item_name"><?php echo esc_attr($res->post_title) ?></span>
															<span class="scwaff_proresources_content_res_item_basecost" style="display:none"><input placeholder="<?php echo esc_html__("Base Cost", "scwaff-translate") ?>" class="scwaff_proresources_cribasec"></span>
															<span class="scwaff_proresources_content_res_item_blockcost" style="display:none"><input placeholder="<?php echo esc_html__("Block Cost", "scwaff-translate") ?>" class="scwaff_proresources_criblockc"></span>
														</div>
														<?php
													}
													?>
												</div>
											</div>
											<?php
										}
										?>
									</div>
								</div>
								<div class="scwaff_prodata_content_right_item" id="scwaff_commission" style="display:none">
									<div class="scwaff_prodata_item scwaff_protype_simple scwaff_protype_grouped scwaff_protype_external scwaff_protype_variable scwaff_protype_bookable">
										<label for="scwaff_procommission"><?php echo esc_html__("Commission(%)", "scwaff-translate") ?></label>
										<input id="scwaff_procommission" max="100" min="0" step="any" type="number">
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
				<section id="scwaff_images_content">
					<div class="scwaff_images_content">
						<div class="scwaff_images_content_feature">
							<div class="scwaff_images_content_feature_header"><?php echo esc_html__("Product Image", "scwaff-translate") ?></div>
							<div class="scwaff_images_content_feature_preview"></div>
							<div class="scwaff_images_content_feature_upload"><span><?php echo esc_html__("Upload", "scwaff-translate") ?></span></div>
						</div>
						<div class="scwaff_images_content_gallery">
							<div class="scwaff_images_content_gallery_header"><?php echo esc_html__("Product Gallery Images", "scwaff-translate") ?></div>
							<div class="scwaff_images_content_gallery_preview"></div>
							<div class="scwaff_images_content_gallery_upload"><span><?php echo esc_html__("Upload", "scwaff-translate") ?></span></div>
						</div>
					</div>
				</section>
				<img class="scwaff_ajax_loading" src="<?php echo SMARTCMS_SCWAFF_URL ."images/loader.gif" ?>">
				<div class="scwaff_ajax_loading_done"><?php echo esc_html__("Product Added!", "scwaff-translate") ?></div>
				<div class="scwaff_submit"><span class="scwaff_submit_button"><?php echo esc_html__("Submit Product", "scwaff-translate") ?></span></div>
			</div>
			<?php
		}else{
			echo wp_login_form( $args );
		}
	}
	
	function smartcms_scwaff_shortcode_manage_func($atts = array(), $content = null){
		global $wpdb;
		$current_user = wp_get_current_user();
		$userId = $current_user->ID;
		if($userId){
			wp_register_style( 'smartcms-scwaff-managestyle', SMARTCMS_SCWAFF_URL .'css/managestyle.css' );
			wp_enqueue_style( 'smartcms-scwaff-managestyle' );
			wp_register_style( 'smartcms-scwaff-font', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' );
			wp_enqueue_style( 'smartcms-scwaff-font' );
			
			wp_register_script('smartcms-scwaff-managescript', SMARTCMS_SCWAFF_URL .'js/managescript.js');
			wp_enqueue_script('smartcms-scwaff-managescript');
			
			$products = $wpdb->get_results("SELECT ID, post_title, post_date, post_status, guid FROM $wpdb->posts WHERE post_status = 'publish' and post_type = 'product' and post_author = '".$userId."'");
			
			?>
			<input class="smartcms_url" type="hidden" value="<?php echo SMARTCMS_SCWAFF_URL ?>">
			
			<div class="smartcms_manageproducts">
				<div class="smartcms_manageproducts_header">
					<span class="smartcms_manageproducts_header_id"><?php echo esc_html__("ID", "scwaff-translate") ?></span>
					<span class="smartcms_manageproducts_header_name"><?php echo esc_html__("Product Name", "scwaff-translate") ?></span>
					<span class="smartcms_manageproducts_header_date"><?php echo esc_html__("Date Uploaded", "scwaff-translate") ?></span>
					<span class="smartcms_manageproducts_header_status"><?php echo esc_html__("Product Status", "scwaff-translate") ?></span>
				</div>
				<div class="smartcms_manageproducts_content">
					<?php
					foreach($products as $pro){
						?>
						<div class="smartcms_manageproducts_content_item">
							<span class="smartcms_manageproducts_content_item_id"><?php echo esc_attr($pro->ID) ?></span>
							<span class="smartcms_manageproducts_content_item_name"><?php echo esc_attr($pro->post_title) ?></span>
							<span class="smartcms_manageproducts_content_item_date"><?php echo esc_attr(substr($pro->post_date, 0, 10)) ?></span>
							<span class="smartcms_manageproducts_content_item_status"><?php echo esc_attr($pro->post_status) ?></span>
							<span class="smartcms_manageproducts_content_item_view"><a href="<?php echo esc_attr($pro->guid) ?>" target="_blank"><i class="fas fa-external-link-alt"></i></a></span>
							<span class="smartcms_manageproducts_content_item_remove"><i class="fas fa-trash-alt"></i></span>
						</div>
						<?php
					}
					?>
				</div>
				<img class="scwaff_ajax_loading" src="<?php echo SMARTCMS_SCWAFF_URL ."images/loader.gif" ?>">
			</div>
			<?php
		}else{
			echo wp_login_form( $args );
		}
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		//$test_option =  $instance['test_option'];
		echo esc_attr($before_widget);
		echo esc_attr($before_title.$title.$after_title);

		global $wpdb;
		
	}
}