<?php

if ( !class_exists('WP_AZPayCheckout') ) {

	class WP_AZPayCheckout {

		protected static $instance = null;
		protected $azpay;
		protected static $config;
		protected $blockUser = false;
		protected $wpdb;

		protected static $cardOperators = array(

			'wpac_flag_visa' => array(
				[
					'title' => 'Cielo - Buy Page Loja',
					'value' => '1'
				],
				[
					'title' => 'Cielo - Buy Page Cielo',
					'value' => '2'
				],
				[
					'title' => 'Redecard - Komerci WebService',
					'value' => '3'
				],
				[
					'title' => 'Redecard - Komerci Integrado',
					'value' => '4'
				],
				[
					'title' => 'Elavon',
					'value' => '6'
				],
				[
					'title' => 'Stone',
					'value' => '20'
				],
				[
					'title' => 'Global Payments',
					'value' => '24'
				],
				[
					'title' => 'BIN',
					'value' => '25'
				],
				[
					'title' => 'GETNET',
					'value' => '22'
				]
			),

			'wpac_flag_mastercard' => array(
				[
					'title' => 'Cielo - Buy Page Loja',
					'value' => '1'
				],
				[
					'title' => 'Cielo - Buy Page Cielo',
					'value' => '2'
				],
				[
					'title' => 'Redecard - Komerci WebService',
					'value' => '3'
				],
				[
					'title' => 'Redecard - Komerci Integrado',
					'value' => '4'
				],
				[
					'title' => 'Elavon',
					'value' => '6'
				],
				[
					'title' => 'Stone',
					'value' => '20'
				],
				[
					'title' => 'Global Payments',
					'value' => '24'
				],
				[
					'title' => 'BIN',
					'value' => '25'
				],
				[
					'title' => 'GETNET',
					'value' => '22'
				]
			),

			'wpac_flag_amex' => array(
				[
					'title' => 'Cielo - Buy Page Loja',
					'value' => '1'
				],
				[
					'title' => 'Cielo - Buy Page Cielo',
					'value' => '2'
				]
			),

			'wpac_flag_diners' => array(
				[
					'title' => 'Cielo - Buy Page Loja',
					'value' => '1'
				],
				[
					'title' => 'Cielo - Buy Page Cielo',
					'value' => '2'
				]
			),

			'wpac_flag_discover' => array(
				[
					'title' => 'Cielo - Buy Page Loja',
					'value' => '1'
				],
				[
					'title' => 'Cielo - Buy Page Cielo',
					'value' => '2'
				]
			),

			'wpac_flag_elo' => array(
				[
					'title' => 'Cielo - Buy Page Loja',
					'value' => '1'
				],
				[
					'title' => 'Cielo - Buy Page Cielo',
					'value' => '2'
				]
			),

			'wpac_flag_aura' => array(
				[
					'title' => 'Cielo - Buy Page Loja',
					'value' => '1'
				],
				[
					'title' => 'Cielo - Buy Page Cielo',
					'value' => '2'
				]
			),

			'wpac_flag_jcb' => array(
				[
					'title' => 'Cielo - Buy Page Loja',
					'value' => '1'
				],
				[
					'title' => 'Cielo - Buy Page Cielo',
					'value' => '2'
				]
			)

		);

		public function __construct() {
			global $wpdb;

    	$this->wpdb = &$wpdb;
			$this::$config = get_option('wpac_plugin_options');

			add_shortcode('wpac_checkout', array( $this, 'shortcodeCheckout' ) );

			add_action('admin_menu', array( $this, 'adminMenu' ) );
			add_action('admin_post_wpac_save_option', array($this, 'saveOptions'));
			add_action('init', array( $this, 'registerSession' ) );
			add_action('wp_enqueue_scripts', array( $this, 'scriptsCheckout' ) );
			add_action('wp_ajax_wpac_response', array( $this, 'ajaxRequest' ) );
			add_action('wp_ajax_nopriv_wpac_response', array( $this, 'ajaxRequest' ) );

		}

		public static function getInstance() {

			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;

		}

		/**
		 * Plugin is activated.
		 */
		public static function activation() {
			global $wpdb;

			add_option('wpac_plugin_version', WPAC_VERSION);

			$table_name = $wpdb->prefix . 'azpayblockusers';

			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				remoteaddr text NOT NULL,
				useragent text NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

		}

		/**
		 * Plugin is deactivated.
		 */
		public static function deactivation() {

			delete_option('wpac_plugin_version');

		}

		/**
		 * Checkout view
		 */
		public static function checkoutPage() {

			require_once( WPAC_DIR . '/views/front.php' );

		}

		/**
		 * Shortcode
		 */
		public function shortcodeCheckout() {

			ob_start();

			self::checkoutPage();

			$content = ob_get_clean();
			wp_enqueue_style('wpac-style');
			wp_enqueue_script('wpac-script');

			return $content;

		}

		/**
		 * Admin menu
		 */
		public function adminMenu() {

	    add_menu_page(
	    	'AZPay Checkout',
	    	'AZPay',
	    	'manage_options',
	    	'wpac-menu-page',
	    	array( $this, 'adminPage' ),
	    	'dashicons-chart-pie',
	    	3
	    );

		}

		/**
		 * Admin view
		 */
		public function adminPage() {

			//$this::$config = get_option('wpac_plugin_options');

			if ( isset($_GET['update']) && $_GET['update'] == '1' ) {
				echo '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
					<p><strong>Configrações Salvas.</strong></p>
				</div>';
			}

			require_once( WPAC_DIR . '/views/admin.php' );

		}

		/**
		 * Process form submit
		 */
		public function saveOptions() {

			check_admin_referer('options_page_nonce', 'options_page_nonce_field');

			$wpac_options = get_option('wpac_plugin_options');

			if ( !empty($_POST) && isset($_POST['wpac_options_submit']) ) {

				if ( isset($_POST['wpac_merchantid']) )
					$wpac_options['merchantId'] = sanitize_text_field($_POST['wpac_merchantid']);

				if ( isset($_POST['wpac_merchantkey']) )
					$wpac_options['merchantKey'] = sanitize_text_field($_POST['wpac_merchantkey']);

				if ( isset($_POST['wpac_rebill']) )
					$wpac_options['rebill'] = sanitize_text_field($_POST['wpac_rebill']);

				if ( isset($_POST['wpac_titlecheckout']) )
					$wpac_options['titlecheckout'] = sanitize_text_field($_POST['wpac_titlecheckout']);

				if ( isset($_POST['wpac_titlebtn']) )
					$wpac_options['titlebtn'] = sanitize_text_field($_POST['wpac_titlebtn']);

				$paymentFlags = array(
					'visa' => array(
						'title' => 'Visa',
						'name'  => 'visa',
						'value' => sanitize_text_field($_POST['wpac_flag_visa'])
					),
					'mastercard' => array(
						'title' => 'Mastercard',
						'name'  => 'mastercard',
						'value' => sanitize_text_field($_POST['wpac_flag_mastercard'])
					),
					'amex' => array(
						'title' => 'Amex',
						'name'  => 'amex',
						'value' => sanitize_text_field($_POST['wpac_flag_amex']),
					),
					'diners' => array(
						'title' => 'Diners',
						'name'  => 'diners',
						'value' => sanitize_text_field($_POST['wpac_flag_diners']),
					),
					'discover' => array(
						'title' => 'Discover',
						'name'  => 'discover',
						'value' => sanitize_text_field($_POST['wpac_flag_discover']),
					),
					'elo' => array(
						'title' => 'Elo',
						'name'  => 'elo',
						'value' => sanitize_text_field($_POST['wpac_flag_elo']),
					),
					'aura' => array(
						'title' => 'Aura',
						'name'  => 'aura',
						'value' => sanitize_text_field($_POST['wpac_flag_aura']),
					),
					'jcb' => array(
						'title' => 'JCB',
						'name'  => 'jcb',
						'value' => sanitize_text_field($_POST['wpac_flag_jcb']),
					)
				);

				$wpac_options['flags'] = $paymentFlags;

				update_option('wpac_plugin_options', $wpac_options);

				wp_redirect(  admin_url('admin.php?page=wpac-menu-page&update=1') );

				exit;

			}

		}

		/**
		 * Session Start
		 */
		public function registerSession() {

			session_start();
			if ( $_SESSION['requestcount'] == null ) {
					$_SESSION['requestcount'] = 0;
			}

		}

		/**
		 * Select DB
		 */
		private function checkDatabase() {

			$tableName = $this->wpdb->prefix . 'azpayblockusers';
			$remoteAddr = $_SERVER['REMOTE_ADDR'];
			$userAgent = $_SERVER['HTTP_USER_AGENT'];
			$result = $this->wpdb->get_results( "SELECT * FROM $tableName WHERE remoteaddr = '$remoteAddr' AND useragent = '$userAgent'" );

			if (count($result) > 0) {
				return true;
			} else {
				return false;
			}

		}

		/**
		 * Check Block User
		 */
		private function checkBlockUser() {

			$check = $this->checkDatabase();

			if ($check) {

				$this->blockUser = true;

			} else {

				if ($_SESSION['requestcount'] >= 4) {

					$tableName = $this->wpdb->prefix . 'azpayblockusers';

					$this->wpdb->insert(
						$tableName,
						array(
							'time' => date('Y-m-d H:i:s'),
							'remoteaddr' => $_SERVER['REMOTE_ADDR'],
							'useragent' => $_SERVER['HTTP_USER_AGENT'],
						)
					);

					$this->blockUser = true;

				} else {
					$_SESSION['requestcount'] = $_SESSION['requestcount'] +1;
				}

			}

		}

		/**
		 * Process payment AZPay
		 */
		private function processPayment($data) {

			$this->azpay = new AZPay($this::$config['merchantId'], $this::$config['merchantKey']);

			$azpay = $this->azpay;

			$azpay->curl_timeout = 60;

			if ($data['transaction-request']['sale']) {

				//order
				$azpay->config_order['reference'] = $data['transaction-request']['sale']['order']['reference'];
				$azpay->config_order['totalAmount'] = $data['transaction-request']['sale']['order']['totalAmount'];


				//billing
				$azpay->config_billing['customerIdentity'] = $data['transaction-request']['sale']['billing']['customerIdentity'];
				$azpay->config_billing['name'] = $data['transaction-request']['sale']['billing']['name'];
				$azpay->config_billing['phone'] = $data['transaction-request']['sale']['billing']['phone'];
				$azpay->config_billing['email'] = $data['transaction-request']['sale']['billing']['email'];

				//credit card
				//$azpay->config_card_payments['acquirer'] = $data['transaction-request']['sale']['payment']['acquirer'];
				$azpay->config_card_payments['acquirer'] = WP_AZPayCheckout::$config['flags'][''.$data['transaction-request']['sale']['payment']['flag'].'']['value'];
				$azpay->config_card_payments['method'] = '1';
				$azpay->config_card_payments['amount'] = $data['transaction-request']['sale']['payment']['amount'];
				$azpay->config_card_payments['currency'] = $data['transaction-request']['sale']['payment']['currency'];
				$azpay->config_card_payments['numberOfPayments'] = $data['transaction-request']['sale']['payment']['numberOfPayments'];
				$azpay->config_card_payments['groupNumber'] = '0';
				$azpay->config_card_payments['flag'] = $data['transaction-request']['sale']['payment']['flag'];
				$azpay->config_card_payments['cardHolder'] = $data['transaction-request']['sale']['payment']['cardHolder'];
				$azpay->config_card_payments['cardNumber'] = $data['transaction-request']['sale']['payment']['cardNumber'];
				$azpay->config_card_payments['cardSecurityCode'] = $data['transaction-request']['sale']['payment']['cardSecurityCode'];
				$azpay->config_card_payments['cardExpirationDate'] = $data['transaction-request']['sale']['payment']['cardExpirationDate'];
				$azpay->config_card_payments['saveCreditCard'] = 'false';

				$operation = $azpay->sale();

			}

			if ($data['transaction-request']['rebil']) {

				//order
				$azpay->config_order['reference'] = $data['transaction-request']['rebil']['order']['reference'];
				$azpay->config_order['totalAmount'] = $data['transaction-request']['rebil']['order']['totalAmount'];

				//rebil
				$azpay->config_rebill['period'] = '3';
				$azpay->config_rebill['frequency'] = '1';
				$azpay->config_rebill['dateStart'] = $data['transaction-request']['rebil']['order']['dateStart'];
				$azpay->config_rebill['dateEnd'] = $data['transaction-request']['rebil']['order']['dateEnd'];

				//billing
				$azpay->config_billing['customerIdentity'] = $data['transaction-request']['rebil']['billing']['customerIdentity'];
				$azpay->config_billing['name'] = $data['transaction-request']['rebil']['billing']['name'];
				$azpay->config_billing['phone'] = $data['transaction-request']['rebil']['billing']['phone'];
				$azpay->config_billing['email'] = $data['transaction-request']['rebil']['billing']['email'];

				//credit card
				$azpay->config_card_payments['acquirer'] = WP_AZPayCheckout::$config['flags'][''.$data['transaction-request']['rebil']['payment']['flag'].'']['value'];
				$azpay->config_card_payments['method'] = '1';
				$azpay->config_card_payments['amount'] = $data['transaction-request']['rebil']['payment']['amount'];
				$azpay->config_card_payments['currency'] = $data['transaction-request']['rebil']['payment']['currency'];
				$azpay->config_card_payments['numberOfPayments'] = $data['transaction-request']['rebil']['payment']['numberOfPayments'];
				$azpay->config_card_payments['groupNumber'] = '0';
				$azpay->config_card_payments['flag'] = $data['transaction-request']['rebil']['payment']['flag'];
				$azpay->config_card_payments['cardHolder'] = $data['transaction-request']['rebil']['payment']['cardHolder'];
				$azpay->config_card_payments['cardNumber'] = $data['transaction-request']['rebil']['payment']['cardNumber'];
				$azpay->config_card_payments['cardSecurityCode'] = $data['transaction-request']['rebil']['payment']['cardSecurityCode'];
				$azpay->config_card_payments['cardExpirationDate'] = $data['transaction-request']['rebil']['payment']['cardExpirationDate'];
				$azpay->config_card_payments['saveCreditCard'] = 'false';

				$operation = $azpay->rebill();

			}

      try {
        $operation->execute();
        $xml_response = $azpay->response();
      } catch (AZPay_Error $e) {

          # HTTP 409 - AZPay Error
          $error = $azpay->responseError();
          $response_message = $error['error_message'];

					$response_array = array(
						'status'	=> false,
						'title'		=> $response_message
					);
					return json_encode($response_array);

      } catch (AZPay_Curl_Exception $e) {

          # Connection Error
          $response_message = $e->getMessage();

					$response_array = array(
						'status'	=> false,
						'title'		=> $response_message
					);
					return json_encode($response_array);

      } catch (AZPay_Exception $e) {

          # General Error
          $response_message = $e->getMessage();

					$response_array = array(
						'status'	=> false,
						'title'		=> $response_message
					);
					return json_encode($response_array);

      }

			$response_status = intval($xml_response->status);
			$response_title = Config::$STATUS_MESSAGES[$response_status]['title'];
			$response_message = Config::$STATUS_MESSAGES[$response_status]['message'];

			if ($response_status == 2 || $response_status == 4) {
				$status = false;
			} else {
				$status = true;
			}

			$response_array = array(
				'status'	=> $status,
				'title'		=> $response_title
			);

			return json_encode($response_array);

		}

		/**
		 * Ajax
		 */
		public function ajaxRequest() {

			$this->checkBlockUser();

			if ($this->blockUser) {

				$response_array = array(
					'status'	=> false,
					'title'		=> 'Você foi bloqueado devido a quantidade de transações'
				);

				echo json_encode($response_array);

				die();

			}

			if ( isset( $_POST['checkout_data'] ) ) {

				$data = $_POST['checkout_data'];

				$response = $this->processPayment($data);

				echo $response;

				die();

			}

		}

		/**
		 * Register script and style
		 */
		public function scriptsCheckout() {

			wp_register_style('wpac-style', WPAC_URL.'/assets/css/main.css');
			wp_register_script( 'wpac-script', WPAC_URL.'/assets/js/main.js', array('jquery') );
 			wp_localize_script( 'wpac-script', 'wpac_ajax_script', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'ajax_nonce' => wp_create_nonce('wpac_request_nonce') ) );

		}

	}

}
