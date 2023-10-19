<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://vlxx
 * @since      1.0.0
 *
 * @package    Yay_Modify_Currency
 * @subpackage Yay_Modify_Currency/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Yay_Modify_Currency
 * @subpackage Yay_Modify_Currency/public
 * @author     Onyx <oni_chan_baka@gmail.com>
 */
class Yay_Modify_Currency_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_filter('woocommerce_get_price', array($this, 'custom_product_price'), 99, 2);
		add_filter('woocommerce_product_get_regular_price',array($this, 'custom_product_price'), 99, 2);
		// add_filter('woocommerce_product_variation_get_price',array($this, 'custom_product_price'), 99, 2);
		// add_filter('woocommerce_product_variation_get_regular_price', array($this, 'custom_product_price'), 99, 2);

		add_filter('woocommerce_variation_prices_price', [$this, 'adjustVariablePrice'], 99, 3);
		add_filter('woocommerce_variation_prices_regular_price', [$this, 'adjustVariablePrice'], 99, 3);

		add_filter('woocommerce_currency_symbol',array($this,'change_currency_symbol') , 10, 2);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/yay-modify-currency-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/yay-modify-currency-public.js', array('jquery'), $this->version, false);

		$post_ID = get_the_ID(); 

		$localize_data = array(
			'ajaxurl' => admin_url('admin-ajax.php'),
		);

		if ($post_ID){
			$localize_data['post_id'] = $post_ID;
		}

		wp_localize_script(
			$this->plugin_name,
			'modify_currency',
			$localize_data,
		);
	}

	public function handle_ajax_modify_currency()
	{
		$wp_json_error = array(
			'status' => 500,
			'message' => 'Bad request'
		);

		if (empty($_POST)) {
			return wp_send_json_error($wp_json_error);
		}

		$action = isset($_POST['action']) ? sanitize_text_field($_POST['action'])  : '';
		$currency = isset($_POST['currency']) ? sanitize_text_field($_POST['currency']) : '';
		$currency_exchange_rate = isset($_POST['currencyExchangeRate']) ? sanitize_text_field($_POST['currencyExchangeRate']) : '';
		$post_ID = isset($_POST['postID'])? intval($_POST['postID']) : '';

		if ('modify_currency' !== $action) {
			return wp_send_json_error($wp_json_error);
		}

		if (empty($currency) || empty($currency_exchange_rate) || empty($post_ID)) {
			return wp_send_json_error($wp_json_error);
		}

		update_post_meta($post_ID,'yay_currency', strtolower($currency) );
		update_post_meta($post_ID,'yay_currency_exchange_rate', $currency_exchange_rate );

		return wp_send_json_success(
			[
				'status'=>200,
				'message'=>'Change currency successfully'
			]
		);
	}

	public function custom_product_price($price, $product)
	{
		$currency_exchange_rate = get_post_meta( $product->id, 'yay_currency_exchange_rate', true ) ?: 1  ;

		// if ($product->is_on_sale()){
		// 	$price = floatval($product->get_sale_price()) * $currency_exchange_rate;
		// } else {
		// 	$price = floatval( $product->get_regular_price() ) * $currency_exchange_rate;
		// }

		return $price * $currency_exchange_rate;
	}

	public function adjustVariablePrice($price, $variation, $product) {
		$currency_exchange_rate = get_post_meta( $product->id, 'yay_currency_exchange_rate', true );

		if($currency_exchange_rate){
			return $price * $currency_exchange_rate;
		}

		return $price;
	}

	public function change_currency_symbol($symbol, $currency){
		$currency_symbols = array(
			'usd' => '$',
			'eur' => '€',
			'jpy' => '¥',
			'cny' => '¥',
			'krw' => '₩',
			'inr' => '₹',
		);

		$product_id = get_the_ID();

		$currency_meta = get_post_meta($product_id,'yay_currency',true);

		if($currency_meta){
			$symbol = $currency_symbols[$currency_meta];
		}

		return $symbol;
	}

	public function add_currency_switcher()
	{
		global $product;

		$product_id = $product->get_id();

		if(!$product_id){
			return;
		}

		$currency_options = array(
			'usd' => 'USD',
			'eur' => 'EUR',
            'jpy' => 'JPY',
            'cny' => 'CNY',
            'krw' => 'KRW',
            'inr' => 'INR',
		);

		$currency_meta = get_post_meta($product_id , 'yay_currency', true ) ? get_post_meta($product_id , 'yay_currency', true ) : 'usd'; 
		
		?>
		<label for="woo-currencies">Currency switcher: </label>
		<select name='woo-currencies' id="woo-currencies">
			<?php foreach($currency_options as $key=>$value) { ?>
				<option value="<?php echo esc_attr( $key ) ?>" <?php $key===$currency_meta ? esc_attr_e( "selected" ): ""  ?>><?php echo esc_html( $value ) ?></option>
			<?php } ?>
		</select>
		<?php
	}
}
