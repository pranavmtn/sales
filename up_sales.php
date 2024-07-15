  <?php
  /*
  Plugin Name: WooCommerce Sales Analytics Display
  Description: Fetches WooCommerce sales data using the Analytics API and displays it on the frontend.
  Version: 1.1
  Author: Your Name
  */
  
  // Ensure WooCommerce is active
  if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
      return;
  }
  
  // Add shortcode to display sales data
  add_shortcode('wc_sales_analytics', 'display_wc_sales_analytics');
  
  function display_wc_sales_analytics() {
      // Check if WooCommerce Admin is available
      if (!class_exists('\Automattic\WooCommerce\Admin\API\Reports\Orders\Stats\Query')) {
          return 'WooCommerce Admin is not active.';
      }
  
      // Fetch sales data
      $total_sales = get_total_sales();
      $total_orders = get_total_orders();
      $top_selling_products = get_top_selling_products(5);
  
      // Prepare output
      $output = '<div class="wc-sales-analytics">';
      $output .= '<h2>WooCommerce Sales Analytics</h2>';
      $output .= '<p>Total Sales: ' . wc_price($total_sales) . '</p>';
      $output .= '<p>Total Orders: ' . $total_orders . '</p>';
      $output .= '<h3>Top Selling Products</h3>';
      $output .= '<ul>';
      foreach ($top_selling_products as $product) {
          $output .= '<li>' . $product['name'] . ' - ' . $product['quantity'] . ' sold</li>';
      }
      $output .= '</ul>';
      $output .= '</div>';
  
      return $output;
  }
  
  function get_total_sales() {
      $query = new \Automattic\WooCommerce\Admin\API\Reports\Orders\Stats\Query(array(
          'period' => 'all',
      ));
      $result = $query->get_data();
      return $result->total_sales;
  }
  
  function get_total_orders() {
      $query = new \Automattic\WooCommerce\Admin\API\Reports\Orders\Stats\Query(array(
          'period' => 'all',
      ));
      $result = $query->get_data();
      return $result->orders_count;
  }
  
  function get_top_selling_products($limit = 5) {
      $query = new \Automattic\WooCommerce\Admin\API\Reports\Products\Query(array(
          'orderby' => 'items_sold',
          'order' => 'desc',
          'per_page' => $limit,
      ));
      $result = $query->get_data();
      
      $top_products = array();
      foreach ($result->data as $product) {
          $top_products[] = array(
              'name' => $product->name,
              'quantity' => $product->items_sold,
          );
      }
      return $top_products;
  }
  
  // Enqueue styles
  add_action('wp_enqueue_scripts', 'wc_sales_analytics_styles');
  
  function wc_sales_analytics_styles() {
      wp_enqueue_style('wc-sales-analytics-style', plugins_url('style.css', __FILE__));
  }