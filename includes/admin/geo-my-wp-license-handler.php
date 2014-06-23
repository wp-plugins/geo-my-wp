<?php
/**
 * License handler for GEO my WP
 *
 * This class should simplify the process of adding license information
 * to GEO my WP add-ons.
 * 
 * @author Pippin Williamson
 * @version 1.1
 */

if ( ! defined( 'ABSPATH' ) ) 
	exit; // Exit if accessed directly

if ( ! class_exists( 'GMW_License' ) ) :

/**
 * GMW_License Class
 */
class GMW_License {
	private $file;
	private $license;
	private $item_name;
	private $version;
	private $author;
	private $api_url = 'https://geomywp.com';

	/**
	 * Class constructor
	 *
	 * @param string  $_file
	 * @param string  $_item_name
	 * @param string  $_version
	 * @param string  $_author
	 * @param string  $_optname
	 * @param string  $_api_url
	 */
	function __construct( $_file, $_item_name, $_license, $_version, $_author, $_api_url = null ) {

		$settings = get_option('gmw_options');

		if ( isset( $settings['admin_settings']['updater_disabled'] ) )
			return;

		$this->licenses 	= get_option( 'gmw_license_keys' );
		$this->statuses 	= get_option('gmw_premium_plugin_status');
		$this->file         = $_file;
		$this->item_name    = $_item_name;
		$this->license_name = $_license;
		$this->license      = isset( $this->licenses[$_license] ) ? trim( $this->licenses[$_license] ) : '';
		$this->version      = $_version;
		$this->author       = $_author;
		$this->api_url      = is_null( $_api_url ) ? $this->api_url : $_api_url;

		// Setup hooks
		$this->includes();
		$this->auto_updater();
	}

	/**
	 * Include the updater class
	 *
	 * @access  private
	 * @return  void
	 */
	private function includes() {
		if ( ! class_exists( 'GMW_Premium_Plugin_Updater' ) )
			require_once 'geo-my-wp-updater.php';
	}

	/**
	 * Auto updater
	 *
	 * @access  private
	 * @global  array $edd_options
	 * @return  void
	 */
	private function auto_updater() {

		if ( empty( $this->license ) )
			return;

		if ( !isset( $this->statuses[$this->license_name] ) || 'valid' !== $this->statuses[$this->license_name] )
			return;

		// Setup the updater
		$gmw_updater = new GMW_Premium_Plugin_Updater(
				$this->api_url,
				$this->file,
				array(
						'version'   => $this->version,
						'license'   => $this->license,
						'item_name' => $this->item_name,
						'author'    => $this->author
				)
		);
	}
}

endif; // end class_exists check

if ( ! class_exists( 'GMW_License_Key' ) ) :

/**
 * GMW_License_Key Class
*/
class GMW_License_Key {
	
	private $file;
	private $license_name;
	
	/**
	 * Class constructor
	 *
	 */
	function __construct( $file ,$item_name, $license_name ) {

		$settings 			= get_option('gmw_options');
		$this->addons 		= get_option( 'gmw_addons' );
		$this->licenses	 	= get_option( 'gmw_license_keys' );
		$this->statuses  	= get_option('gmw_premium_plugin_status');
		$this->file      	= basename( dirname( $file ) );
		$this->item_name	= $item_name;
		$this->license_name = $license_name;
		
		$this->messages = array(
				'activate'				=> __( 'Please enter your license key and click the "Activate" button. The license key is required for automatic updated.', 'GJM' ),
				'activated'				=> __( 'Your license for %s plugin successfully activated. Thank you for your support!', 'GJM' ),
				'deactivated'			=> __( 'Your license for %s plugin successfully deactivated.', 'GJM' ),
				'valid'					=> __( 'License is activated. Thank you for your support!', 'GJM' ),
				'expired' 				=> __( 'Your license has expired. Please renew your license in order to keep getting its updates and support.', 'GJM' ),
				'no_activations_left' 	=> __( 'Your license has no activations left. Click <a href="http://geomywp.com/purchase-history/" target="_blank" >here</a> to manager your license activations.', 'GJM' ),
				'missing'				=> __( 'Something is wrong with the key you entered. Please check your key and try again.', 'GJM' ),
				'retrieve_key'			=> __( 'Lost or forgot your license key? <a href="http://geomywp.com/purchase-history/" target="_blank" >Retrieve it here.</a>')
		);
		
		// Setup hooks
		add_action( 'admin_init', array( $this, 'activate_license' ) );
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );	
		add_action( 'after_plugin_row', array( $this, 'license_key_output' ) );
	}
	
	/**
	 * Activate License.
	 *
	 * @access private
	 * @return void
	 */
	public function activate_license() {
		 
		if ( !isset( $_POST['gmw_license_key_activate'] ) || $_POST['gmw_license_key_activate'] != $this->license_name )
			return;
	
		$add_on = $_POST['gmw_license_key_activate'];
		
		//if license key field is empty 
		if ( empty( $_POST['gmw_license_keys'][$add_on] ) ) {
			
			unset( $this->licenses[$add_on] );
			
			update_option( 'gmw_license_keys', $this->licenses );
			
			return;
		}
		
		// run a quick security check
		if ( !check_admin_referer( $add_on, $add_on ) )
			return;
	
		$license_key = ( isset( $_POST['gmw_license_keys'][$add_on] ) ) ? sanitize_text_field( $_POST['gmw_license_keys'][$add_on] ) : '';
	
		if ( isset( $license_key ) && !empty( $license_key ) ) {
	
			$this_license = trim( $license_key );
			$this_name    = ucwords( str_replace( '_', ' ', $add_on ) );
		
			// data to send in our API request
			$api_params = array(
					'edd_action' => 'activate_license',
					'license'    => $this_license,
					'item_name'  => urlencode( $this_name ) // the name of our product in EDD
			);
		
			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, GMW_REMOTE_SITE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
		
			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;
		
			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			
			if ( $license_data->license == 'valid' ) {	
				$message = $license_data->license;
				add_action( 'admin_notices', array( $this, 'activated_key_notice' ) );
			} else {
				$message = $license_data->error;
				add_action( 'admin_notices', array( $this, 'activation_error_notice' ) );
			}

			$this->statuses[$add_on] = $message;
			$this->licenses[$add_on] = $this_license;
			
			// $license_data->license will be either "active" or "inactive"
			update_option( 'gmw_premium_plugin_status', $this->statuses );
			update_option( 'gmw_license_keys', $this->licenses );
	
		}
	
	}
	
	/**
	 * deactivate License.
	 *
	 * @access private
	 * @return void
	 */
	public function deactivate_license() {
	
		// listen for our activate button to be clicked
		if ( !isset( $_POST['gmw_license_key_deactivate'] ) || $_POST['gmw_license_key_deactivate'] != $this->license_name )
			return;
	
		$add_on = $_POST['gmw_license_key_deactivate'];
	
		// run a quick security check
		if (!check_admin_referer( $add_on, $add_on ) )
			return; // get out if we didn't click the Activate button
	
		$license_key = ( isset( $_POST['gmw_license_keys'][$add_on] ) ) ? sanitize_text_field( $_POST['gmw_license_keys'][$add_on] ) : '';
		
		if ( isset( $license_key ) && !empty( $license_key ) ) {
	
			$this_license = trim( $license_key );
			$this_name    = ucwords( str_replace( '_', ' ', $add_on ) );
		
			$api_params = array(
					'edd_action' => 'deactivate_license',
					'license'    => $this_license,
					'item_name'  => urlencode($this_name) // the name of our product in EDD
			);
		
			// Call the custom API.
			$response = wp_remote_get( add_query_arg( $api_params, GMW_REMOTE_SITE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );
		
			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;
		
			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			
			if ( $license_data->license ==  ( 'deactivated' || 'failed' )  ) {
				unset( $this->statuses[$add_on] );
				update_option( 'gmw_premium_plugin_status', $this->statuses );
			}
	
		}
				
		add_action( 'admin_notices', array( $this, 'deactivated_key_notice' ) );
	
	}
	
	/**
	 * Activation success notice
	 */
	public function activated_key_notice() {
		?>
		<div class="updated">
			<p><?php printf( $this->messages['activated'], $this->item_name ); ?></p>
		</div>
		<?php
	}
	
	/**
	 * Dectivation success notice
	 */
	public function deactivated_key_notice() {
		?>
		<div class="updated">
			<p><?php printf( $this->messages['deactivated'], $this->item_name ); ?></p>
		</div>
		<?php
	}
	
	/**
	 * Activation error notice
	 */
	public function activation_error_notice() {
		?>
		<div class="error">
			<?php $message = ( isset( $this->statuses[$this->license_name] ) && array_key_exists( $this->statuses[$this->license_name], $this->messages ) ) ? $this->messages[$this->statuses[$this->license_name]] : $this->messages['missing']; ?>						
			<p><?php echo $message; ?></p>
		</div>
		<?php
	}

	/**
	 * Display license key field
	 *
	 */
	public function license_key_output( $file ) {
	
		if ( basename( dirname( $file ) ) != $this->file )
			return;
		?>
	
		<tr id="<?php echo esc_attr( $this->file ); ?>-licence-key-row" class="active gmw-licence-key-wrapper">
			
			<td class="plugin-update" colspan="3">
				
				<div class="gmw-license-key-fields-wrapper">
				
					<form method="post">
														
						<?php 
						//if license key activate
						if ( isset( $this->statuses[$this->license_name] ) 
							&& $this->statuses[$this->license_name] !== false 
							&& $this->statuses[$this->license_name] == 'valid' 
							&& isset( $this->licenses[$this->license_name] ) 
							&& !empty($this->licenses[$this->license_name] ) ) 
						{ 
						?>			
							<div class="gmw-license-valid-wrapper">
								
								<span>License Key: </span>
								<input class="gmw-license-key-input-field" disabled="disabled" type="text" size="30"
									value="<?php if ( isset( $this->licenses[$this->license_name] ) && !empty( $this->licenses[$this->license_name] ) ) echo $this->licenses[$this->license_name]; ?>" />
					
								<input type="hidden"
									name="gmw_license_keys[<?php echo $this->license_name; ?>]"
									value="<?php if ( isset( $this->licenses[$this->license_name] ) && !empty( $this->licenses[$this->license_name] ) ) echo $this->licenses[$this->license_name]; ?>" />
					
								<!-- show deactivate license button -->
								<button type="submit"
									class="button-secondary activate-license-btn"
									style="padding: 0 9px !important;"
									name="gmw_license_key_deactivate"
									title="<?php _e('Deactivate License Key', 'GJM'); ?>"
									value="<?php echo $this->license_name; ?>"><?php _e( 'Deactivate', 'GJM' ); ?></button>
								
								<p class="description"><?php echo $this->messages['valid']; ?></p>
							</div> <!-- if status invalid --> 
						
						<?php } else { ?>
					
							<?php 
							$class   = '';
							$message = $this->messages['activate'];
							
							if ( isset( $this->licenses[$this->license_name] ) && !empty( $this->licenses[$this->license_name] ) && isset( $this->statuses[$this->license_name] ) ) {
								$class 	  = 'gmw-license-error';
								$message  = ( array_key_exists( $this->statuses[$this->license_name], $this->messages ) ) ? $this->messages[$this->statuses[$this->license_name]] : $this->messages['missing'];	
								$message .= '<br />';
								$message .= $this->messages['retrieve_key'];
							} 
							?>
							
							<div class="gmw-license-invalid-wrapper <?php echo $class; ?>">
								
								<span>License Key: </span>
									
								<input 
									onkeydown="if (event.keyCode == 13) { jQuery(this).closest('form').find('.activate-license-btn').click(); return false; }"
									class="gmw_license_keys gmw-addon-short-input"
									name="gmw_license_keys[<?php echo $this->license_name; ?>]" type="text"
									class="regular-text"
									size="30"
									placeholder="<?php _e('license key', 'GJM'); ?>"
									value="<?php if (isset($this->licenses[$this->license_name]) && !empty($this->licenses[$this->license_name])) echo $this->licenses[$this->license_name]; ?>" />
						
								<button 
									type="submit"
									class="gmw-license-key-button button-secondary activate-license-btn"
									name="gmw_license_key_activate"
									title="<?php _e('Activate License Key', 'GJM'); ?>"
									style="padding: 0 8px !important;"
									value="<?php echo $this->license_name; ?>"><?php _e( 'activate', 'GJM' ); ?></button>
								
								<br />
								<p class="description"><?php echo $message; ?></p>
									
							</div> 
							
						<?php } ?> 
							
						<?php wp_nonce_field( $this->license_name, $this->license_name ); ?>
						
					</form>
					
				</div>
			</td>
			<script>
				jQuery(function(){
					jQuery('tr#<?php echo esc_attr( $this->file ); ?>-licence-key-row').prev().addClass('gmw-license-key-wrapper');
				});
			</script>
		</tr>
		<?php 
	}
}

endif; // end class_exists check
