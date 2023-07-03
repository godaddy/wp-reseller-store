<?php
/**
 * GoDaddy Reseller Store Settings class.
 *
 * Manage custom filters for the reseller store plubin
 *
 * @class    Reseller_Store/Settings
 * @package  Reseller_Store/Settings
 * @category Class
 * @author   GoDaddy
 * @since    2.2.0
 */

namespace Reseller_Store;

if ( ! defined( 'ABSPATH' ) ) {

	// @codeCoverageIgnoreStart
	exit;
	// @codeCoverageIgnoreEnd
}

final class Settings {

	/**
	 * Post type slug.
	 *
	 * @since 2.2.0
	 *
	 * @var string
	 */
	const SLUG = 'reseller_product';

	/**
	 * Custom Post Type Page
	 *
	 * @since 2.2.0
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'edit.php?post_type=reseller_product';


	/**
	 * Array of Currencies.
	 *
	 * @since 2.2.0
	 *
	 * @var array
	 */
	static $currencies = [ 'default', 'USD', 'AED', 'AUD', 'CAD', 'CHF', 'CLP', 'CNY', 'COP', 'DKK', 'EUR', 'GBP', 'HKD', 'IDR', 'ILS', 'INR', 'JPY', 'KRW', 'MXN', 'MYR', 'NOK', 'NZD', 'PEN', 'PHP', 'PKR', 'PLN', 'SAR', 'SEK', 'SGD', 'THB', 'TWD', 'UAH', 'VND', 'ZAR' ]; // @codingStandardsIgnoreLine

	/**
	 * Array of markets.
	 *
	 * @since 2.2.0
	 *
	 * @var array
	 */
	static $markets = [ 'default','da-DK','de-AT','de-CH','de-DE','el-GR','en-AE','en-AU','en-CA','en-GB','en-HK','en-IE','en-IL','en-IN','en-PK','en-MY','en-NZ','en-US','en-PH','en-SG','en-ZA','es-AR','es-CL','es-CO','es-ES','es-MX','es-PE','es-US','es-VE','fi-FI','fr-BE','fr-CA','fr-CH','fr-FR','hi-IN','id-ID','it-CH','it-IT','ja-JP','ko-KR','mr-IN','nl-NL','nl-BE','nb-NO','pt-BR','pl-PL','pt-PT','ru-RU','sv-SE','ta-IN','th-TH','tr-TR','uk-UA','vi-VN','zh-SG','zh-HK','zh-TW' ];  // @codingStandardsIgnoreLine

	/**
	 * Array of product layouts.
	 *
	 * @since 2.2.0
	 *
	 * @var array
	 */
	static $layout_type = [ 'default', 'classic' ];  // @codingStandardsIgnoreLine

	/**
	 * Array of product image sizes.
	 *
	 * @since 2.2.0
	 *
	 * @var array
	 */
	static $image_size = [ 'default', 'icon', 'thumbnail', 'medium', 'large', 'full', 'none' ];  // @codingStandardsIgnoreLine

	/**
	 * Array of available tabs in settings.
	 *
	 * @since 2.2.0
	 *
	 * @var array
	 */
	static $available_tabs = [ 'setup_options', 'product_options', 'domain_options', 'localization_options' ];  // @codingStandardsIgnoreLine

	/**
	 * Class constructor.
	 *
	 * @since 2.2.0
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'reseller_register_settings' ) );
		add_action( 'admin_menu', array( $this, 'register' ) );
		add_action( 'wp_ajax_rstore_options_save', array( __CLASS__, 'save' ) );
		add_action( 'wp_ajax_rstore_product_import', array( __CLASS__, 'import' ) );

		$product_layout_type = rstore_get_option( 'product_layout_type' );
		if ( ! empty( $product_layout_type ) ) {
			add_filter(
				'rstore_product_layout_type',
				function() {
					return rstore_get_option( 'product_layout_type' );
				}
			);
		}

		$product_image_size = rstore_get_option( 'product_image_size' );
		if ( ! empty( $product_image_size ) ) {
			add_filter(
				'rstore_product_image_size',
				function() {
					return rstore_get_option( 'product_image_size' );
				}
			);
		}

		$product_button_label = rstore_get_option( 'product_button_label' );
		if ( ! empty( $product_button_label ) ) {
			add_filter(
				'rstore_product_button_label',
				function() {
					return rstore_get_option( 'product_button_label' );
				}
			);
		}

		$product_text_cart = rstore_get_option( 'product_text_cart' );
		if ( ! empty( $product_text_cart ) ) {
			add_filter(
				'rstore_product_text_cart',
				function() {
					return rstore_get_option( 'product_text_cart' );
				}
			);
		}

		$product_text_more = rstore_get_option( 'product_text_more' );
		if ( ! empty( $product_text_more ) ) {
			add_filter(
				'rstore_product_text_more',
				function() {
					return rstore_get_option( 'product_text_more' );
				}
			);
		}

		$product_show_title = rstore_get_option( 'product_show_title' );
		if ( ! empty( $product_show_title ) ) {
			add_filter(
				'rstore_product_show_title',
				function() {
					return false;
				}
			);
		}

		$product_show_content = rstore_get_option( 'product_show_content' );
		if ( ! empty( $product_show_content ) ) {
			add_filter(
				'rstore_product_show_content',
				function() {
					return false;
				}
			);
		}

		$product_show_price = rstore_get_option( 'product_show_price' );
		if ( ! empty( $product_show_price ) ) {
			add_filter(
				'rstore_product_show_price',
				function() {
					return false;
				}
			);
		}

		$product_redirect = rstore_get_option( 'product_redirect' );
		if ( ! empty( $product_redirect ) ) {
			add_filter(
				'rstore_product_redirect',
				function() {
					return false;
				}
			);
		}

		$product_content_height      = rstore_get_option( 'product_content_height' );
		$product_full_content_height = rstore_get_option( 'product_full_content_height' );
		if ( ! empty( $product_content_height ) || ! empty( $product_full_content_height ) ) {
			add_filter(
				'rstore_product_content_height',
				function() {

					$product_full_content_height = rstore_get_option( 'product_full_content_height' );
					if ( $product_full_content_height ) {
						return 0;
					}

					return intval( rstore_get_option( 'product_content_height' ) );

				}
			);
		}

		$domain_title = rstore_get_option( 'domain_title' );
		if ( ! empty( $domain_title ) ) {
			add_filter(
				'rstore_domain_title',
				function() {
					return rstore_get_option( 'domain_title' );
				}
			);
		}

		$domain_text_placeholder = rstore_get_option( 'domain_text_placeholder' );
		if ( ! empty( $domain_text_placeholder ) ) {
			add_filter(
				'rstore_domain_text_placeholder',
				function() {
					return rstore_get_option( 'domain_text_placeholder' );
				}
			);
		}

		$domain_text_search = rstore_get_option( 'domain_text_search' );
		if ( ! empty( $domain_text_search ) ) {
			add_filter(
				'rstore_domain_text_search',
				function() {
					return rstore_get_option( 'domain_text_search' );
				}
			);
		}

		$domain_transfer_title = rstore_get_option( 'domain_transfer_title' );
		if ( ! empty( $domain_transfer_title ) ) {
			add_filter(
				'rstore_domain_transfer_title',
				function() {
					return rstore_get_option( 'domain_transfer_title' );
				}
			);
		}

		$domain_transfer_text_placeholder = rstore_get_option( 'domain_transfer_text_placeholder' );
		if ( ! empty( $domain_transfer_text_placeholder ) ) {
			add_filter(
				'rstore_domain_transfer_text_placeholder',
				function() {
					return rstore_get_option( 'domain_transfer_text_placeholder' );
				}
			);
		}

		$domain_transfer_text_search = rstore_get_option( 'domain_transfer_text_search' );
		if ( ! empty( $domain_transfer_text_search ) ) {
			add_filter(
				'rstore_domain_transfer_text_search',
				function() {
					return rstore_get_option( 'domain_transfer_text_search' );
				}
			);
		}

		$domain_page_size = rstore_get_option( 'domain_page_size' );
		if ( ! empty( $domain_page_size ) ) {
			add_filter(
				'rstore_domain_page_size',
				function() {
					return rstore_get_option( 'domain_page_size' );
				}
			);
		}

		$domain_modal = rstore_get_option( 'domain_modal' );
		if ( ! empty( $domain_modal ) ) {
			add_filter(
				'rstore_domain_modal',
				function() {
					return rstore_get_option( 'domain_modal' );
				}
			);
		}

		$sync_ttl = rstore_get_option( 'sync_ttl' );
		if ( ! empty( $sync_ttl ) ) {
			add_filter(
				'rstore_sync_ttl',
				function() {
					return rstore_get_option( 'sync_ttl' );
				}
			);
		}

		$product_isc = rstore_get_option( 'product_isc' );
		$market      = rstore_get_option( 'api_market' );
		$currency    = rstore_get_option( 'api_currency' );
		if ( ! empty( $market ) || ! empty( $currency ) || ! empty( $product_isc ) ) {
			add_filter( 'rstore_api_query_args', array( $this, 'rstore_api_query_args_filter' ), 10, 2 );
		}

	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @action admin_enqueue_scripts
	 * @since  2.2.0
	 */
	public function admin_enqueue_scripts() {

		if ( ! rstore_is_admin_uri( self::PAGE_SLUG, false ) ) {

			return;

		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'reseller-store-settings-js', Plugin::assets_url( "js/admin-settings{$suffix}.js" ), array( 'jquery' ), rstore()->version, true );

		$args = array(
			'privateLabelId' => rstore_get_option( 'pl_id' ),
			'fields'         => 'domain%2C%20displayName%2C%20homeUrl',

		);

		$data = array(
			'urls' => array(
				'api' => esc_url_raw( rstore()->api->url( 'api', 'settings', $args ) ),
			),
		);

		wp_localize_script( 'reseller-store-settings-js', 'rstore', $data );

	}

	/**
	 * Register the settings page.
	 *
	 * @action init
	 * @since  2.2.0
	 */
	public function register() {

		add_submenu_page(
			self::PAGE_SLUG,
			esc_html__( 'Reseller Store Settings', 'reseller-store' ),
			esc_html__( 'Settings', 'reseller-store' ),
			'manage_options',
			rstore_prefix( 'settings' ),
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register the api request args
	 *
	 * @action init
	 * @since  2.2.0
	 *
	 * @param array  $args      Query string args for api url.
	 * @param string $url_key  Type of url to add args for.
	 *
	 * @return array
	 */
	public function rstore_api_query_args_filter( $args, $url_key ) {

		$product_isc = rstore_get_option( 'product_isc' );
		$market      = rstore_get_option( 'api_market' );
		$currency    = rstore_get_option( 'api_currency' );

		if ( ! empty( $product_isc ) && 'cart_api' === $url_key ) {
			$args['isc'] = $product_isc;
		}

		if ( ! empty( $market ) && 'default' !== $market ) {
			$args['marketId'] = $market;
		}

		if ( ! empty( $currency ) && 'default' !== $currency ) {
			$args['currencyType'] = $currency;
		}

		return $args;

	}

	/**
	 * Get the current tab the admin is on
	 *
	 * @since  2.2.0
	 */
	public function get_active_tab() {

		$active_tab = filter_input( INPUT_GET, 'tab' );

		if ( in_array( $active_tab, self::$available_tabs, true ) ) {
			return $active_tab;
		}

		return self::$available_tabs[0];
	}

	/**
	 * Build settings array
	 *
	 * @since  2.2.0
	 *
	 * @param string $active_tab The tab the admin is currently on.
	 * @return array
	 */
	public static function reseller_settings( $active_tab ) {

		$settings = array();

		switch ( $active_tab ) {
			case 'domain_options':
				$settings[] = array(
					'name'        => 'domain_title',
					'label'       => esc_html__( 'Domain title', 'reseller-store' ),
					'type'        => 'text',
					'placeholder' => '',
					'description' => esc_html__( 'Override the title text. Empty field means no override set.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'domain_text_placeholder',
					'label'       => esc_html__( 'Registration placeholder text', 'reseller-store' ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Find your perfect domain name', 'reseller-store' ),
					'description' => esc_html__( 'Override the placeholder text for domain registration. Empty field means no override set.', 'reseller-store' ),
				);
				$settings[] = array(
					'name'        => 'domain_text_search',
					'label'       => esc_html__( 'Domain search button', 'reseller-store' ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Search', 'reseller-store' ),
					'description' => esc_html__( 'Override the domain search button text. Empty field means no override set.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'domain_page_size',
					'label'       => esc_html__( 'Page size', 'reseller-store' ),
					'type'        => 'number',
					'description' => esc_html__( 'Override the number of results returned forß the advanced domain search.  Empty field means no override set.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'domain_modal',
					'label'       => esc_html__( 'Display results in a modal', 'reseller-store' ),
					'type'        => 'checkbox',
					'checked'     => 0,
					'description' => esc_html__( 'Display the results in a popup modal for the advanced domain search. Unchecked will default to no modal.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'domain_transfer_title',
					'label'       => esc_html__( 'Domain transfer title', 'reseller-store' ),
					'type'        => 'text',
					'placeholder' => '',
					'description' => esc_html__( 'Override the domain transfer title text. Empty field means no override set.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'domain_transfer_text_placeholder',
					'label'       => esc_html__( 'Transfer placeholder text', 'reseller-store' ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Enter domain to transfer', 'reseller-store' ),
					'description' => esc_html__( 'Override the domain transfer placeholder text. Empty field means no override set.', 'reseller-store' ),
				);
				$settings[] = array(
					'name'        => 'domain_transfer_text_search',
					'label'       => esc_html__( 'Transfer button', 'reseller-store' ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Transfer', 'reseller-store' ),
					'description' => esc_html__( 'Override the title text. Empty field means no override set.', 'reseller-store' ),
				);

				break;

			case 'localization_options':
				$settings[] = array(
					'name'        => 'api_currency',
					'label'       => esc_html__( 'Currency', 'reseller-store' ),
					'type'        => 'select',
					'list'        => self::$currencies,
					'description' => esc_html__( 'Set the currency to display on your storefront.', 'reseller-store' ),
				);
				$settings[] = array(
					'name'        => 'api_market',
					'label'       => esc_html__( 'Market', 'reseller-store' ),
					'type'        => 'select',
					'list'        => self::$markets,
					'description' => esc_html__( 'Set the market and language.', 'reseller-store' ),
				);

				break;

			case 'product_options':
				$settings[] = array(
					'name'        => 'product_layout_type',
					'label'       => esc_html__( 'Layout type', 'reseller-store' ),
					'type'        => 'select',
					'list'        => self::$layout_type,
					'description' => esc_html__( 'Set product widget layout. Classic layout will display price and cart button at the bottom of widget.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'product_image_size',
					'label'       => esc_html__( 'Image Size', 'reseller-store' ),
					'type'        => 'select',
					'list'        => self::$image_size,
					'description' => esc_html__( 'Global override for the product image size. Default means no override set.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'product_show_title',
					'label'       => esc_html__( 'Show product title', 'reseller-store' ),
					'type'        => 'checkbox',
					'checked'     => 1,
					'description' => esc_html__( 'Default value is checked.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'product_show_content',
					'label'       => esc_html__( 'Show post content', 'reseller-store' ),
					'type'        => 'checkbox',
					'checked'     => 1,
					'description' => esc_html__( 'Default value is checked.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'product_show_price',
					'label'       => esc_html__( 'Show product price', 'reseller-store' ),
					'type'        => 'checkbox',
					'checked'     => 1,
					'description' => esc_html__( 'Default value is checked.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'product_button_label',
					'label'       => esc_html__( 'Button text', 'reseller-store' ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Add to cart', 'reseller-store' ),
					'description' => esc_html__( 'Override the Add to cart button text. Empty field means no override set.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'product_redirect',
					'label'       => esc_html__( 'Redirect to cart', 'reseller-store' ),
					'type'        => 'checkbox',
					'checked'     => 1,
					'description' => esc_html__( 'Default value is checked to redirect to cart after adding item.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'product_text_cart',
					'label'       => esc_html__( 'Cart link text', 'reseller-store' ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Continue to cart', 'reseller-store' ),
					'description' => esc_html__( 'Override cart link text. Empty field means no override set.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'product_full_content_height',
					'label'       => esc_html__( 'Set content height', 'reseller-store' ),
					'type'        => 'checkbox',
					'checked'     => 1,
					'description' => esc_html__( 'Default value checked. Uncheck to display full content in widget', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'product_content_height',
					'label'       => esc_html__( 'Content height', 'reseller-store' ),
					'type'        => 'number',
					'description' => esc_html__( 'Override the product description content height (in pixels).  Empty field means no override set.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'product_text_more',
					'label'       => esc_html__( 'Product permalink text', 'reseller-store' ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'More info', 'reseller-store' ),
					'description' => esc_html__( 'Override the permalink text. Empty field means no override set.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'        => 'product_isc',
					'label'       => esc_html__( 'Promo code', 'reseller-store' ),
					'type'        => 'text',
					'placeholder' => '',
					'description' => esc_html__( 'Enter an ISC promo code.', 'reseller-store' ),
				);

				break;

			default:
				$settings[] = array(
					'name'        => 'pl_id',
					'label'       => esc_html__( 'Reseller Id', 'reseller-store' ),
					'type'        => 'label',
					'description' => esc_html__( 'The reseller id identifies your storefront. Re-activate plugin to change this value.', 'reseller-store' ),
				);

				$settings[] = array(
					'name'  => 'last_sync',
					'label' => esc_html__( 'Last API Sync', 'reseller-store' ),
					'type'  => 'time',
				);
				$settings[] = array(
					'name'  => 'next_sync',
					'label' => esc_html__( 'Next API Sync', 'reseller-store' ),
					'type'  => 'time',
				);

				$settings[] = array(
					'name'        => 'sync_ttl',
					'label'       => esc_html__( 'API Sync TTL (seconds)', 'reseller-store' ),
					'type'        => 'number',
					'description' => esc_html__( 'Reseller store will check the api for changes periodically. The default is 4 hours (14400 seconds).', 'reseller-store' ),
				);

				break;
		}

		return $settings;
	}

	/**
	 * Register settings
	 *
	 * @since  2.2.0
	 */
	public function reseller_register_settings() {

		$settings = $this->reseller_settings( $this->get_active_tab() );
		foreach ( $settings as $setting ) {
			register_setting( 'reseller_settings', $setting['name'] );
		}
	}

	/**
	 * Admin settings ui
	 *
	 * @since  2.2.0
	 *
	 * @param string $active_tab Tab name to render content for.
	 */
	public function settings_output( $active_tab = null ) {

		$settings = self::reseller_settings( $active_tab );

		?>

		<div class="wrap">
			<h1> <?php esc_html_e( 'Reseller Store Settings', 'reseller-store' ); ?> </h1>

			<h2 class="nav-tab-wrapper">
				<a href="?post_type=reseller_product&page=rstore_settings&tab=setup_options" class="nav-tab <?php echo 'setup_options' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Setup', 'reseller-store' ); ?></a>
				<a href="?post_type=reseller_product&page=rstore_settings&tab=product_options" class="nav-tab <?php echo 'product_options' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Product Settings', 'reseller-store' ); ?></a>
				<a href="?post_type=reseller_product&page=rstore_settings&tab=domain_options" class="nav-tab <?php echo 'domain_options' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Domain Search Settings', 'reseller-store' ); ?></a>
				<a href="?post_type=reseller_product&page=rstore_settings&tab=localization_options" class="nav-tab <?php echo 'localization_options' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Localization', 'reseller-store' ); ?></a>
			</h2>

			<?php
			if ( 'setup_options' === $active_tab ) {
				$this->import_button();
				$this->branding_info_block();
			}
			?>

			<form id="rstore-options-form" >
				<input type="hidden" name="action" value="rstore_options_save" />
				<?php
				echo '<input type="hidden" name="option_page" value="' . esc_attr( $active_tab ) . '" />';
				wp_nonce_field( "$active_tab-options" );
				?>
				<table class="form-table">
					<tbody>
				<?php
				foreach ( $settings as $setting ) {
					switch ( $setting['type'] ) {
						case 'label':
							echo '<tr>';
							echo '<th><label for="' . esc_attr( $setting['name'] ) . '">' . esc_html( $setting['label'] ) . '</label></th>';
							echo '<td><label id="' . esc_attr( $setting['name'] ) . '" >' . esc_attr( rstore_get_option( $setting['name'] ) ) . '</label>';
							break;
						case 'text':
							echo '<tr>';
							echo '<th><label for="' . esc_attr( $setting['name'] ) . '">' . esc_html( $setting['label'] ) . '</label></th>';
							echo '<td><input type="text" id="' . esc_attr( $setting['name'] ) . '" name="' . esc_html( $setting['name'] ) . '" value="' . esc_attr( rstore_get_option( $setting['name'] ) ) . '" placeholder="' . esc_attr( $setting['placeholder'] ) . '" class="regular-text rstore-setting-text">';
							break;
						case 'number':
							echo '<tr>';
							echo '<th><label for="' . esc_attr( $setting['name'] ) . '">' . esc_html( $setting['label'] ) . '</label></th>';
							echo '<td><input type="number" id="' . esc_attr( $setting['name'] ) . '" name="' . esc_attr( $setting['name'] ) . '" value="' . esc_attr( rstore_get_option( $setting['name'] ) ) . '" class="regular-text">';
							break;
						case 'time':
							$sync_time = get_date_from_gmt( gmdate( 'Y-m-d H:i:s', rstore_get_option( $setting['name'] ) ), get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );
							echo '<tr>';
							echo '<th><label for="' . esc_attr( $setting['name'] ) . '">' . esc_html( $setting['label'] ) . '</label></th>';
							echo '<td><label id="' . esc_attr( $setting['name'] ) . '" >' . esc_html( $sync_time ) . '</label>';
							break;
						case 'checkbox':
							$name    = rstore_get_option( $setting['name'] );
							$checked = $setting['checked'] ? empty( $name ) : ! empty( $name );
							echo '<tr>';
							echo '<th><label for="' . esc_attr( $setting['name'] ) . '">' . esc_html( $setting['label'] ) . '</label></th>';
							echo '<td><input type="checkbox" id="' . esc_attr( $setting['name'] ) . '" name="' . esc_attr( $setting['name'] ) . '" value="1" ' . checked( $checked, true, false ) . '  />';
							break;
						case 'select':
							echo '<tr>';
							echo '<th><label for="' . esc_attr( $setting['name'] ) . '">' . esc_html( $setting['label'] ) . '</label></th>';
							echo '<td><select title="' . esc_attr( $setting['label'] ) . '" id="' . esc_attr( $setting['name'] ) . '" name="' . esc_attr( $setting['name'] ) . '" >';
							foreach ( $setting['list'] as $item ) {
								if ( rstore_get_option( $setting['name'] ) === $item ) {
									echo '<option selected="selected" value="' . esc_attr( $item ) . '">' . esc_html( $item ) . '</option>';
								} else {
									echo '<option value="' . esc_attr( $item ) . '">' . esc_html( $item ) . '</option>';
								}
							}
							echo '</select>';
							break;
					}
					if ( array_key_exists( 'description', $setting ) ) {
						echo '<p class="description" id="tagline-description">' . esc_html( $setting['description'] ) . '</p></td>';
					}
					echo '</td></tr>';
				}
				?>
					</tbody>
				</table>
				<p class="submit">
					<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'reseller-store' ); ?></button>
					<label id="rstore-options-save-error"></label>
					<img src="<?php echo esc_url( includes_url( 'images/spinner-2x.gif' ) ); ?>" class="rstore-spinner">
				</p>
			</form>
		</div>

		<?php

	}

	/**
	 * Render the plugin settings page
	 *
	 * @since  2.2.0
	 */
	public function render_settings_page() {

		if ( ! rstore_is_admin_uri( self::PAGE_SLUG, false ) ) {

			return;

		}

		$currentCurrency = rstore_get_option('rstore_api_currency');
		$currencies = Settings::$currencies;
		if (!in_array($currentCurrency, $currencies)) {
			rstore_update_option('rstore_api_currency', 'USD');
			rstore_delete_option( 'next_sync' );
		}

		$active_tab = $this->get_active_tab();

		$this->settings_output( $active_tab );
	}

	/**
	 * Generate import button
	 *
	 * @since  2.2.0
	 */
	public function import_button() {
		?>
		<div class="card">
			<h2 class="title"><?php esc_html_e( 'Check for new products', 'reseller-store' ); ?></h2>
			<p><?php esc_html_e( 'Check API for new products. Note: This is will not update the content for any of your existing products that have been imported.', 'reseller-store' ); ?></p>
			<div class="wrap">
				<form id='rstore-product-import'>
					<input type="hidden" name="action" value="rstore_product_import">
					<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( \Reseller_Store\Setup::install_nonce() ) ); ?>">
					<button type="submit" class="button link" ><?php esc_html_e( 'Import new products', 'reseller-store' ); ?></button>
					<img src="<?php echo esc_url( includes_url( 'images/spinner-2x.gif' ) ); ?>" class="rstore-spinner">
					<label id="rstore-product-import-error"></label>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Generate branding info block
	 *
	 * @since  2.2.0
	 */
	public function branding_info_block() {
		?>
		<table id="rstore-branding-info" class="form-table">
			<tbody>
			<tr>
				<th><label for="displayName"><?php esc_html_e( 'Display Name', 'reseller-store' ); ?></label></th>
				<td><label id="displayName" ></label><p class="description" id="tagline-description"><?php esc_html_e( 'Display name set in the Reseller Control Center', 'reseller-store' ); ?></p></td>
			</tr>
			<tr>
				<th><label for="homeUrl"><?php esc_html_e( 'Home Url', 'reseller-store' ); ?></label></th>
				<td><label id="homeUrl" ></label><p class="description" id="tagline-description"><?php esc_html_e( 'The home url is set in the Reseller Control Center should be set as your WordPress site.', 'reseller-store' ); ?></p></td>
			</tr>
			<tr>
				<th><label for="customDomain"><?php esc_html_e( 'Storefront Domain', 'reseller-store' ); ?></label></th>
				<td><label id="customDomain" ></label><p class="description" id="tagline-description"><?php esc_html_e( 'The custom domain is set in the Reseller Control Center and identifies the standard storefront and checkout pages. Set as a sub-domain of your WordPress site (e.g. shop).', 'reseller-store' ); ?></p></td>
			</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Save Reseller Store Settings
	 *
	 * @action wp_ajax_rstore_options_save
	 * @since  2.2.0
	 */
	public static function save() {

		$nonce      = filter_input( INPUT_POST, '_wpnonce' );
		$active_tab = filter_input( INPUT_POST, 'option_page' );

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, "$active_tab-options" ) ) {
			return wp_send_json_error(
				esc_html__( 'Error: Invalid Session. Refresh the page and try again.', 'reseller-store' )
			);
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return wp_send_json_error(
				esc_html__( 'Error: Current user cannot manage options.', 'reseller-store' )
			);
		}

		if ( ! in_array( $active_tab, self::$available_tabs, true ) ) {
			return wp_send_json_error(
				esc_html__( 'Error: Invalid options sent to server.', 'reseller-store' )
			);
		}

		$settings = self::reseller_settings( $active_tab );
		foreach ( $settings as $setting ) {

			if ( 'time' === $setting['type'] || 'label' === $setting['type'] ) {
				continue;
			}

			$val = filter_input( INPUT_POST, $setting['name'] );

			if ( 'number' === $setting['type'] ) {
				$val = absint( $val );
			}

			if ( 'checkbox' === $setting['type'] && 1 === $setting['checked'] ) {

				if ( empty( $val ) ) {
					$val = 1;
				} else {
					$val = null;
				}
			}

			if ( empty( $val ) || 'default' === $val ) {
				rstore_delete_option( $setting['name'] );
			} else {
				rstore_update_option( $setting['name'], $val );
			}
		}

		rstore_delete_option( 'next_sync' ); // force a rsync update.

		wp_send_json_success();
	}

	/**
	 * Call the setup import function
	 *
	 * @since 2.2.0
	 */
	public static function import() {
		if ( class_exists( '\Reseller_Store\Setup' ) ) {

			\Reseller_Store\Setup::import();
		}
	}
}
