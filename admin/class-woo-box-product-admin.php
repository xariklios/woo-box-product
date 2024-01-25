<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://valtzis.gr
 * @since      1.0.0
 *
 * @package    Woo_Box_Product
 * @subpackage Woo_Box_Product/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Box_Product
 * @subpackage Woo_Box_Product/admin
 * @author     Charis Valtzis <charisvaltzis@gmail.com>
 */
class Woo_Box_Product_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woo_Box_Product_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woo_Box_Product_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woo-box-product-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woo_Box_Product_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woo_Box_Product_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woo-box-product-admin.js', array('jquery'), time(), false);

    }

    function wbp_add_product_type($product_type_options){

        $product_type_options["package"] = [
            "id"            => "_package",
            "wrapper_class" => "show_if_package",
            "label"         => "Package",
            "description"   => "Description",
            "default"       => "no",
        ];

        return $product_type_options;
    }


    /**
     * @return void
     */
    function wbp_create_pieces_per_box_input()
    {
        global $post;

        // Custom field value
        $custom_field_value = get_post_meta($post->ID, '_package_qty', true);

        echo '<div class="options_group">';

        // Custom field label
        woocommerce_wp_hidden_input(
            array(
                'id' => '_package_qty',
                'label' => __('Pieces per Package', 'your-text-domain'),
                'placeholder' => __('Pieces Per Package', 'your-text-domain'),
                'desc_tip' => 'true',
                'description' => __('Add how many products are in each package', 'your-text-domain'),
                'value' => $custom_field_value,
            )
        );

        echo '</div>';
    }


    /**
     * @param $post_id
     * @return void
     */
    function wbp_save_pieces_per_box_input($post_id)
    {

        $custom_field = isset($_POST['_package_qty']) ? sanitize_text_field($_POST['_package_qty']) : '';
        update_post_meta($post_id, '_package_qty', $custom_field);
    }


    /**
     * @param $item_id
     * @param $item
     * @param $order_id
     * @return void
     */
    function wbp_modify_quantity_based_on_purchase($item_id, $item, $order_id)
    {
        if (!$item->get_data()['product_id']) {
            return;
        }

        $product_id = $item->get_data()['product_id'];
        $package_qty = get_post_meta($product_id, '_package_qty', true);

        // Product does not have packages
        if (!isset($package_qty)) {
            return;
        }

        $variation_id = $item->get_data()['variation_id'];
        $quantity = $item->get_data()['quantity'];

        $variation = wc_get_product($variation_id);
        $variation_attributes = $variation->get_variation_attributes();


        foreach ($variation_attributes as $key => $val) {
            if ($key == 'attribute_posotita') {
                $product = wc_get_product($product_id);

                $variations = $product->get_available_variations();
                $variations_id = wp_list_pluck($variations, 'variation_id');

                // Create a new array to store variations to keep
                $variations_to_keep = array();

                foreach ($variations_id as $key => $var_id) {
                    if ($var_id != $variation_id) {
                        $variations_to_keep[] = $var_id;
                    }
                }


                // if bought paketo, decrease temaxio quantity by package quantity
                if ($val == 'Πακέτο') {
                    $current_stock = get_post_meta($variations_to_keep[0], '_stock', true);
                    $new_stock = $current_stock - $package_qty * $quantity;
                    if ($new_stock < 0) {
                        $new_stock = 0;
                    }
                    update_post_meta($variations_to_keep[0], '_stock', $new_stock);
                    // if bought temaxio, calculate how many packages still left
                } elseif ($val == 'Τεμάχιο') {
                    $current_stock = get_post_meta($variation_id, '_stock', true);
                    $new_quantity = $current_stock - $quantity;
                    update_post_meta($variations_to_keep[0], '_stock', intdiv($new_quantity, $package_qty));
                }
            }
        }

    }

    /**
     * @param $tabs
     * @return mixed
     */
    function wbp_add_package_data_tab($tabs){
        $tabs['package'] = [
            'label'    => __( 'Package', 'woocommerce' ),
            'target'   => 'wbp_package_product_data_panel',
            'class'    => array( 'show_if_package' ),
            'priority' => 10,
        ];

        return $tabs;
    }

    /**
     * @return void
     */
    function wbp_package_data_panel()
    {
        global $woocommerce, $post;
        ?>
        <div id="wbp_package_product_data_panel" class="panel woocommerce_options_panel">
            <?php
            woocommerce_wp_text_input(
                array(
                    'id' => '_sef_custom_ean_code',
                    'label' => __('Ean code', 'woocommerce'),
                    'desc_tip' => 'true',
                    'description' => __('Enter your custom ean-barcode number', 'woocommerce'),
                    'type' => 'text',
                )
            );

            woocommerce_wp_text_input(
                array(
                    'id' => '_sef_custom_mpn_code',
                    'label' => __('Mpn code', 'woocommerce'),
                    'desc_tip' => 'true',
                    'description' => __('Enter your custom mpn number', 'woocommerce'),
                    'type' => 'text',
                )
            );

            woocommerce_wp_select(
                array(
                    'id' => '_sef_product_availabilities',
                    'label' => __('Availability', 'woocommerce'),
                    'options' => \Classes\Utils::sef_skroutz_availabilities(true,$post)

                )
            );

            ?>
        </div>
        <?php
    }
}
