<?php
if ( !class_exists('LmfwpptAutoUpdatePlugin') ) {
	final class LmfwpptAutoUpdatePlugin{
		
		/**
		 * The plugin current version
		 * @var string
		 */
		private $current_version;

		/**
		 * The plugin main file
		 * @var string
		 */
		private $plugin;

		/**
		 * The plugin slug
		 * @var string
		 */
		private $plugin_slug;

		/**
		 * The plugin remote update path
		 * @var string
		 */
		private $remote_url;

		/**
		 * The plugin license menu
		 * @var string
		 */
		private $license_menu;

		function __construct( $args = [] ) {
			
			// Parse Args
			$args = wp_parse_args( $args, array(
				'plugin' => '',
				'plugin_slug' => '',
				'plugin_slug' => '',
				'current_version' => '',
				'remote_url' => '',
				'menu_type' => '',
				'parent_slug' => '',
				'page_title' => '',
				'menu_title' => '',
			) );
			
			// Set current version
			$this->current_version = sanitize_text_field( $args['current_version'] );

			// Set Plugin instance
			$this->plugin = sanitize_text_field( $args['plugin'] );

			// Set plugin slug
			$this->plugin_slug = sanitize_text_field( $args['plugin_slug'] );

			// Set remote url
			$this->remote_url = sanitize_text_field( $args['remote_url'] );

			// Set license menu args
			$this->license_menu = array(
				'menu_type' => sanitize_text_field( $args['menu_type'] ),
				'parent_slug' => sanitize_text_field( $args['parent_slug'] ),
				'page_title' => sanitize_text_field( $args['page_title'] ),
				'menu_title' => sanitize_text_field( $args['menu_title'] ),
			);

		
			//var_dump( $this->plugin_slug  );
			//var_dump( $this->remote_url  );

			add_action( 'admin_menu', [ $this, 'admin_menu' ] );

			//add_action( 'site_transient_update_plugins', [ $this, 'set_update_transient' ] );
			add_action( 'pre_set_site_transient_update_plugins', [ $this, 'set_update_transient' ] );
			
			// Check plugin updates info
			add_filter('plugins_api', [ $this, 'check_info' ], 20, 3);

			// Save License
			add_action( 'wp_ajax_validate_license', [ $this, 'save_license' ] );

			// Hook License Activation Form Field
			add_action( 'lmfwppt_license_activation_form_fields', [ $this, 'license_activation_fields' ] );

			// Admin License Notice
			add_action( 'admin_notices', [$this, 'key_admin_notice'] );

			// Force Check Updates
			if ( isset( $_GET['force-check'] ) || isset( $_GET['update'] ) && $_GET['update'] == 'force-check' ) {
				add_action( 'admin_init', [ $this, 'force_update_check' ], 20 );
			}
		
		}

		// Get remote URL
		public function get_remote_url(){
	    	return $this->remote_url . 'wp-json/license-manager/v1/licenses/';
	    }

	    // Current version of this plugin
	    public function get_current_version(){
	    	return $this->current_version;
	    }
	    
	    // Plugin Slug
	    public function get_plugin_slug(){
	    	return $this->plugin_slug;
	    }

	    // Plugin instances
	    public function get_plugin(){
	    	return $this->plugin;
	    }

	    // Plugin Menu Args
	    public function get_menu(){
	    	return $this->license_menu;
	    }

	    // Get license key
	    public function get_lkey(){
	    	// Plugin Slug
	        $plugin_slug = $this->get_plugin_slug();

	        // Get License key
	        return get_option($plugin_slug.'_key');
	    }

		/**
	     * Register admin menu
	     *
	     * @return void
	     */
	    public function admin_menu() {
	        
	        // Get menu args
	        $menu_args = $this->get_menu();

	        // Page Title
	        $page_title = $menu_args['page_title'];

	        // Menu Title
	        $menu_title = $menu_args['menu_title'];

	        // Capability
	        $capability = 'manage_options';

	        // Menu Slug
	        $menu_slug = $this->get_plugin_slug().'-license';

	        // Parent Menu Slug
	        $parent_slug = $menu_args['parent_slug'];

	        // Menu Type
	        $menu_type = $menu_args['menu_type'];

	        // Add menu or submenu
	        if ( $menu_type == 'sub_menu' ) {
	        	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, [ $this, 'license_key_settings_page' ] );
	        } elseif( $menu_type == 'menu' ){
	        	add_menu_page( $page_title, $menu_title, $capability, $menu_slug, [ $this, 'license_key_settings_page' ] );
	        }

	    }
	    // validate License
	    function validate_license(){

	    	// Don't proceed for empty key
	    	if ( empty( $this->get_lkey() ) ) {
	    		return false;
	    	}

	    	// Remote URL
	    	// $final_remote_url = add_query_arg( array(
            //     'product_slug' => $this->get_plugin_slug(),
            //     'license_key'  => $this->get_lkey(),
            //     'domain'       => esc_url( home_url() ),
            //     'action'       => 'validate_license',
		    // ), $this->get_remote_url() );

			$final_remote_url = wp_remote_get( 
				add_query_arg( 
					array(
						'product_slug' => $this->get_plugin_slug(),
						'license_key'  => $this->get_lkey(),
						'domain'       => esc_url( home_url() ),
					), 
					$this->get_remote_url() . 'validate'
				), 
				array(
					'timeout' => 20,
					'headers' => array(
						'Accept' => 'application/json'
					)
				)
			);

			// do nothing if we don't get the correct response from the server
			if( 
				is_wp_error( $final_remote_url )
				|| 200 !== wp_remote_retrieve_response_code( $final_remote_url )
				|| empty( wp_remote_retrieve_body( $final_remote_url ) )
			) {
				return false;	
			}

			// Decode
			$decode = json_decode( wp_remote_retrieve_body( $final_remote_url ) );

			// Key status
			update_option($this->get_plugin_slug().'_key_status', $decode);

			// Delete old transient
		    delete_transient( "lmfwppt_upgrade_".$this->get_plugin_slug() );

		    // Return
		    return isset( $decode->status ) && sanitize_text_field( $decode->status ) == "1" ? true : false;
	    }

	    // Handle License Key Saving
		function save_license() {

			// Action verify
			if ( ! isset( $_POST['action'] ) || $_POST['action'] != 'validate_license' ) {
				wp_die('invalid');
			}

			// Verify Nonce
			if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'lmfwppt-license-validation' ) ){
			   wp_die('invalid');
			}

			// Get posted license key
			$license_key = isset( $_POST['license-key'] ) ? sanitize_text_field( $_POST['license-key'] ) : '';

			// Redirect url
			$redirect_url = isset( $_POST['_wp_http_referer'] ) ? sanitize_text_field( $_POST['_wp_http_referer'] ) : '';


			// Plugin Slug
		    $plugin_slug = $this->get_plugin_slug();

		    // Save License Key
		    update_option($plugin_slug.'_key', $license_key);

		    // Delete Key Status
		    $this->delete_key_status();

		    // Validate License
		    if ( !$this->validate_license() ) {
		    	echo json_encode([
		    		'status' => false,
		    		'msg' => 'Invalid License',
		    	]);
		    	die();
		    }

		    // License Key Information
			$key_status = $this->get_key_status();


		    // JSON response
		    echo json_encode( $key_status );
			die();
		}

		// Get key status
		function get_key_status(){
			return get_option( $this->get_plugin_slug() . '_key_status');
		}

		// Key Admin Notice
		function key_admin_notice() {
			$key_data = $this->get_key_status();
			if( !isset($key_data->status) || $key_data->status != 1 ) : 
				do_action( $this->get_plugin_slug() . '_license_inactive_notice', $this->get_key_status() );
			else:
				do_action( $this->get_plugin_slug() . '_license_active_notice', $this->get_key_status() );
			endif; 
		}

		// Delete Key status
		function delete_key_status(){
			return update_option( $this->get_plugin_slug() . '_key_status', '');
		}

	    // License Setting Page
	    function license_key_settings_page(){
	    	// Get menu args
	        $menu_args = $this->get_menu();

	        // Page Title
	        $page_title = $menu_args['page_title'];

	        // Plugin Slug
	        $plugin_slug = $this->get_plugin_slug();

	        // Get License key
	        $license_key = get_option($plugin_slug.'_key');

	        // Get Key Status
	        $key_status = $this->get_key_status();

	        $notice_class = ' notice-error ';
	        if ( isset( $key_status->status ) && $key_status->status == true ) {
	        	$notice_class = ' notice-success ';
	        }

	        $msg = isset( $key_status->msg ) ? sanitize_text_field( $key_status->msg ) : '';

	    	?>
	    	<div class="wrap">
	    		<h1></h1>
	    		<div class="card">

					<h2 class="title"><?php echo esc_html( $page_title ); ?></h2>
	    			<?php do_action( 'lmfwppt_license_activation_form_fields' ); ?>
	    			
	    		</div>
	    	</div>
	    	<?php 
	    }

	    // License Activation Fields
	    function license_activation_fields(){

	        // Plugin Slug
	        $plugin_slug = $this->get_plugin_slug();

	        // Get License key
	        $license_key = get_option($plugin_slug.'_key');

	        // Get Key Status
	        $key_status = $this->get_key_status();

	        $notice_class = ' notice-error ';
	        if ( isset( $key_status->status ) && $key_status->status == true ) {
	        	$notice_class = ' notice-success ';
	        }

	        $msg = isset( $key_status->msg ) ? sanitize_text_field( $key_status->msg ) : '';

	    	?>
	    
	    	<?php do_action( 'lmfwppt_before_activation_form' ); ?>
	    	<?php do_action( 'lmfwppt_before_activation_form_'.$plugin_slug ); ?>
	    	<div class="lmfwppt-license-validation">

				<p class="form-field license-flex">
					<input type="text" name="license-key" class="lmfwppt-license-key large-text" value="<?php echo esc_attr( $license_key ); ?>">
					<button type="button" class="lmfwppt-license-submit-btn button button-primary">Validate</button>
				</p>

				<?php wp_nonce_field('lmfwppt-license-validation'); ?>
				<input type="hidden" name="plugin_slug" value="<?php echo esc_attr( $plugin_slug ); ?>">

				<div class="form-field activation-information">
					<div class="am-notice notice-alt is-dismissible <?php echo $notice_class; ?>"><strong><?php echo esc_html( $msg ); ?></strong></div>
				</div>

	    	</div>
	    	<?php do_action( 'lmfwppt_after_activation_form_'.$plugin_slug ); ?>
	    	<?php do_action( 'lmfwppt_after_activation_form' ); ?>
	    	

	    	<script>
	    		// Submit Trigger
	    		jQuery(document).on('click', '.lmfwppt-license-submit-btn', function(e){
	    			e.preventDefault();
	    			lmfwppt_license_activation_submit();
	    		});

	    		// Prevent other form submit
	    		jQuery(document).on('keydown', '.lmfwppt-license-key', function(e){
				    if( e.keyCode == 13 ) {
				    	lmfwppt_license_activation_submit();
				      	e.preventDefault();
				      	return false;
				    }
				});

	    			// Ajax Function
	    			function lmfwppt_license_activation_submit(){
	    				
	    				let $this = jQuery('.lmfwppt-license-validation');
	    				let $submitBtn = jQuery('.lmfwppt-license-submit-btn');

	    				if ( $submitBtn.hasClass('loading') ) {
	    					return;
	    				}

	    				var data = {
	    					'action': 'validate_license',
							'plugin_slug': jQuery('[name="plugin_slug"]', $this).val(),
							'_wpnonce': jQuery('[name="_wpnonce"]', $this).val(),
							'_wp_http_referer': jQuery('[name="_wp_http_referer"]', $this).val(),
							'license-key': jQuery('[name="license-key"]', $this).val(),
	    				}

	    				$submitBtn.prop('disabled', true).addClass('loading');

						// Send ajax
						jQuery.post(ajaxurl, data, function(response) {

							$submitBtn.prop('disabled', false).removeClass('loading');

							let data = JSON.parse( response );

							let notice = data.msg;
							let type = 'error';

							if( data.status == true ){
								type = 'success';
							}

							let notice_html = '<div class="notice notice-alt  notice-'+type+'"><strong>'+notice+'</strong></div>';
            				jQuery('.activation-information', $this).html(notice_html);
							jQuery(document).trigger('wp-updates-notice-added');
						});

	    			};
	    	</script>	

	    	<style>
	    		.am-notice{
	    			background: #fff;
				    border: 1px solid #c3c4c7;
				    border-left-width: 4px;
				    box-shadow: 0 1px 1px rgb(0 0 0 / 4%);
				    margin: 5px 0 15px;
				    padding: 1px 12px;
	    		}
	    		.license-flex{
	    			display: flex;
	    			align-items: center;
	    		}
	    	</style>
	    	<?php 
	    }


 		// Force Check
		public function force_update_check() {
			$transient_key = "lmfwppt_upgrade_".$this->get_plugin_slug();
			
			// just clean the cache when new plugin version is installed
			delete_transient( $transient_key );
			delete_transient( 'update_plugins' );
			delete_site_transient( 'update_plugins' );

		}

		// Get product info from remote
		public function get_product_info() {

			$transient_key = "lmfwppt_upgrade_".$this->get_plugin_slug();

		    $remote = get_transient( $transient_key );
		    
			// trying to get from cache first
			if( false == $remote ) {

				// Remote URL with params
				$final_remote_url = add_query_arg( array(
                    'product_slug' => $this->get_plugin_slug(),
                    'license_key' => $this->get_lkey(),
                    'lmfwppt-info' => 'true',
		        ), $this->get_remote_url() );

				$final_remote_url = wp_remote_get( 
					add_query_arg( 
						array(
							'product_slug' => $this->get_plugin_slug(),
							'license_key'  => $this->get_lkey(),
							'domain'       => esc_url( home_url() ),
						), 
						$this->get_remote_url() . 'info'
					), 
					array(
						'timeout' => 20,
						'headers' => array(
							'Accept' => 'application/json'
						)
					)
				);

				// do nothing if we don't get the correct response from the server
				if( 
					is_wp_error( $final_remote_url )
					|| 200 !== wp_remote_retrieve_response_code( $final_remote_url )
					|| empty( wp_remote_retrieve_body( $final_remote_url ) )
				) {
					return $transient;
				}

				// Decode
				$remote = json_decode( wp_remote_retrieve_body( $final_remote_url ) );

				// Set transient
				if ( $remote ) {
					set_transient( $transient_key, $remote, 43200 ); // 12 hours cache
				}
				/*if ( !is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) ) {
					set_transient( $transient_key, $remote, 43200 ); // 12 hours cache
				}*/
		 
			}

			return $remote;
		}


		// Set plugin update transient
		function set_update_transient( $transient ){

			//echo "<pre>"; print_r($transient); echo "</pre>"; 
		 
			if ( empty($transient->checked ) ) {
		        return $transient;
		    }

			$remote = $this->get_product_info();
		    
			if( $remote ) {
		 
				// your installed plugin version should be on the line below! You can obtain it dynamically of course 
				if( $remote && version_compare( $this->current_version, $remote->version, '<' ) && version_compare($remote->requires, get_bloginfo('version'), '<' ) ) {

					// Initial New object
					$res = new stdClass();

					$res->slug 			= $this->get_plugin_slug();
					$res->plugin 		= $this->get_plugin(); // it could be just YOUR_PLUGIN_SLUG.php if your plugin doesn't have its own directory
					$res->new_version 	= isset( $remote->version ) ? sanitize_text_field( $remote->version ) : '';
					$res->tested 		= isset( $remote->tested ) ? sanitize_text_field( $remote->tested ) : '';

					// Download URL Generate
					if( isset( $remote->download_link ) ) {
						$res->package =  add_query_arg( 
							array(
								'domain'       => urlencode( home_url() ),
							), 
							$remote->download_link
						);
					}
					
					// Push to transient
		           	$transient->response[$res->plugin] = $res;
		       
		        }
		 
			}
		    return $transient;
		}


		/*
		 * $res empty at this step
		 * $action 'plugin_information'
		 * $args stdClass Object ( [slug] => woocommerce [is_ssl] => [fields] => Array ( [banners] => 1 [reviews] => 1 [downloaded] => [active_installs] => 1 ) [per_page] => 24 [locale] => en_US )
		 */
		function check_info( $res, $action, $args ){

			// do nothing if this is not about getting plugin information
			if( 'plugin_information' !== $action ) {
				return false;
			}


			$plugin_slug = $this->get_plugin_slug(); // we are going to use it in many places in this function

			// do nothing if it is not our plugin
			if( $plugin_slug !== $args->slug ) {
				return false;
			}

			$remote = $this->get_product_info();

			if ( $remote ) {

				$res = new stdClass();

				$res->name 			= isset( $remote->name ) ? sanitize_text_field( $remote->name ) : '';
				$res->slug 			= isset( $plugin_slug ) ? sanitize_text_field( $plugin_slug ) : '';
				$res->version 		= isset( $remote->version ) ? sanitize_text_field( $remote->version ) : '';
				$res->tested 		= isset( $remote->tested ) ? sanitize_text_field( $remote->tested ) : '';
				$res->requires 		= isset( $remote->requires ) ? sanitize_text_field( $remote->requires ) : '';
				$res->author 		= isset( $remote->author ) ? sanitize_text_field( $remote->author ) : '';
				$res->download_link = isset( $remote->download_link ) ? sanitize_text_field( $remote->download_link ) : '';
				$res->trunk 		= isset( $remote->download_link ) ? sanitize_text_field( $remote->download_link ) : '';
				$res->requires_php 	= isset( $remote->requires_php ) ? sanitize_text_field( $remote->requires_php ) : '';
				$res->last_updated 	= isset( $remote->last_updated ) ? sanitize_text_field( $remote->last_updated ) : '';

				// Sections
				$sections = array();

				// Loop all sections from remote
				if ( isset( $remote->sections ) && !empty( $remote->sections ) ) {

					foreach( $remote->sections as $section ) {

						// Section Name
						$section_name = isset( $section->name ) ? sanitize_text_field( $section->name ) : '';

						// Section Content
						$section_content = isset( $section->content ) ? sanitize_text_field( $section->content ) : '';

						// If has section name
						if ( !empty( $section_name ) ) {
							$key = str_replace(' ', '_', $section_name);
							$sections[$key] = $section_content;
						}
					}
				}

				// Push section
				if ( !empty( $sections ) ) {
					$res->sections = $sections;
				}	
				

				
				/*if( !empty( $remote->sections->screenshots ) ) {
					$res->sections['screenshots'] = $remote->sections->screenshots;
				}*/

				// Banner URL - Low
				$banner_low_url = isset( $remote->banners->low ) ? sanitize_text_field( $remote->banners->low ) : '';

				// Banner URL - High
				$banner_high_url = isset( $remote->banners->high ) ? sanitize_text_field( $remote->banners->high ) : '';

				// Set Banner - Low
				if ( !empty( $banner_low_url ) ) {
					$res->banners['low'] = $banner_low_url;
				}

				// Set Banner - High
				if ( !empty( $banner_high_url ) ) {
					$res->banners['low'] = $banner_high_url;
				}

				
				// Return data
				return $res;

			}

			// Return false
			return false;

		}
    }
}