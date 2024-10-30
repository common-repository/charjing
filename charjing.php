<?php
/*
  Plugin Name: Charjing
  Plugin URI: http://Charjing.com/
  Description: Charjing ,Products Billing and Subscription Management.
  Version: 1.0
  Author: Charjing
  Author URI: http://Charjing.com/
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly




add_action('init', 'Charjing_Init');
add_action('admin_menu', 'Charjing_menu');
add_filter('the_content', 'Charjing_Content', 10);

function Charjing_Init()
{
    $labels = array(
        'name' => __('Products'),
        'singular_name' => __('Product'),
        'add_new' => __('Add New'),
        'add_new_item' => __('Add New Product'),
        'edit_item' => __('Edit Product'),
        'new_item' => __('New Product'),
        'all_items' => __('All Products'),
        'view_item' => __('View Product'),
        'search_items' => __('Search Products'),
        'not_found' => __('No products found'),
        'not_found_in_trash' => __('No products found in the Trash'),
        'parent_item_colon' => '',
        'menu_name' => 'Charjing Products'
    );
    $args = array(
        'labels' => $labels,
        'description' => 'Holds Charjing products and product specific data',
        'public' => true,
        'menu_position' => 5,
        'supports' => array('title', 'editor', 'thumbnail'),
        'has_archive' => true,
    );
    register_post_type('chg-products', $args);

    if (@$_REQUEST["cmdConfirmOrder"])
    {
        include("processpayment.php");
    }
}

add_action('add_meta_boxes', 'Charjing_product_price_box');
add_action('save_post', 'Charjing_product_price_box_save');

function Charjing_product_price_box_save($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (!wp_verify_nonce(@$_POST['product_price_box_content_nonce'], plugin_basename(__FILE__)))
        return;

    if ('page' == $_POST['post_type'])
    {
        if (!current_user_can('edit_page', $post_id))
            return;
    } else
    {
        if (!current_user_can('edit_post', $post_id))
            return;
    }
    $product_price = $_POST['charjing'];
    $product_price["setup_price"]=sanitize_text_field($product_price["setup_price"]);
    $product_price["price"]=sanitize_text_field($product_price["price"]);
    $product_price["period_length"]=(int)$product_price["period_length"];
    update_post_meta($post_id, 'charjing_info', $product_price);
}

function Charjing_product_price_box()
{
    add_meta_box(
            'charjing_product_price_box', __('Product Details'), 'Charjing_product_price_box_content', 'chg-products', 'advanced', 'high'
    );
}

function Charjing_product_price_box_content($post)
{
    $charjing_info=  get_post_meta($post->ID,"charjing_info",true);
    wp_nonce_field(plugin_basename(__FILE__), 'product_price_box_content_nonce');
    ?>
    <div id="Charjing_product_info">
        <label class="">Setup Price:</label>
        <input type="text" id="title" value="<?php echo($charjing_info["setup_price"]); ?>" size="1" name="charjing[setup_price]"><br>

        <label class="">Product Price:</label>
        <input type="text" id="title" value="<?php echo(@$charjing_info["price"]); ?>" size="1" name="charjing[price]"><br>

        <label class="metabox-holder columns-2">Recurring:</label>
        <input type="checkbox" id="cb-select-2" name="charjing[is_recurring]" value="1" <?php if (@$charjing_info["is_recurring"] == "1") echo("checked"); ?>  onclick="if (this.checked) {
                    document.getElementById('lstRecurring').style.display = '';
                } else {
                    document.getElementById('lstRecurring').style.display = 'none';
                }"><br>

        <div id="lstRecurring" style=" <?php if (@$charjing_info["is_recurring"] != "1") echo("display:none"); ?>">
            <label class="">Period Length:</label>
            <input type="text" id="title" value="<?php echo(@$charjing_info["period_length"]); ?>" size="1" name="charjing[period_length]"><br>
            <label class="metabox-holder columns-2"> Period Length Option</label>
            <select id="title" name="charjing[period_option]" >
                <option value="Days">Days</option>
                <option value="Weeks" <?php if (@$charjing_info["period_option"] == "Weeks") echo("selected"); ?>>Weeks</option>
                <option value="Months" <?php if (@$charjing_info["period_option"] == "Months") echo("selected"); ?>>Months</option>
                <option value="Years" <?php if (@$charjing_info["period_option"] == "Years") echo("selected"); ?>>Years</option>
            </select>
        </div>
    </div>
    <style>
        #Charjing_product_info label
        {
            width:150px;padding:5px;
        }
        #Charjing_product_info input[type="text"],select
        {
            width:100px;
        }
    </style>
    <?php
}

function Charjing_menu()
{
    add_options_page('Charjing Options', 'Charjing Options', 'manage_options', 'Charjing', 'Charjing_plugin');
}

function Charjing_plugin()
{
    include("settings.php");
}

function Charjing_Content($contents)
{
    global $post;
    $charjing_setting = get_option("charjing");
    if (is_page($charjing_setting["checkout"]) || isset($_REQUEST["charjing_pay"]))
    {
        ob_start();
        include("checkout.php");
        $contents.= ob_get_contents();
        ob_end_clean();
    }
    if(is_single() && $post->post_type=="chg-products")
    {
        ob_start();
        include("products_front.php");
        $contents.= ob_get_contents();
        ob_end_clean();
    }
    return($contents);
}


?>