<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>
<?php
if ( class_exists('\Automattic\WooCommerce\Admin\FeaturePlugin') ) { // if Woocomerce Admin active
  echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_attr__('Dashboard', 'energyplus'), 'description' => '', 'buttons'=>'<a href="' . EnergyPlus_Helpers::admin_page('dashboard', array('action'=>'default')) . '" class="__A__Dashboard_Buttons __A__Selected">' . esc_html__('Overview', 'energyplus').'</a> <a href="' . EnergyPlus_Helpers::admin_page('dashboard', array('action'=>'wc-admin')) . '" class="__A__Dashboard_Buttons">' . esc_html__('Charts', 'energyplus'). ' <i class="fas fa-bookmark fae-woo"></i></a>'));
} else {
  echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_attr__('Dashboard', 'energyplus'), 'description' => '', 'buttons'=>''));
}
?>

<?php do_action('energyplus_need'); ?>

<meta http-equiv="refresh" content="1800"/>

<div class="grid __A__GP">
  <?php foreach ($map AS $widget_id => $widget) {  ?>
    <?php $widgetclass =  "Widgets__". sanitize_key($widget['type']); ?>
    <div class="__A__Widget __A__Widget_w<?php echo esc_attr($widget['w']) ?> __A__Widget_h<?php echo esc_attr($widget['h']) ?> __A__Widget_<?php echo esc_attr($widget['type']) ?>" data-w="<?php echo esc_attr($widget['w']) ?>" data-h="<?php echo esc_attr($widget['h']) ?>" data-id="<?php echo esc_attr($widget['id']) ?>" data-type="<?php echo esc_attr($widget['type']) ?>" id="__A__Widget_<?php echo esc_attr($widget['id']) ?>">
      <div class="item-content">
        <div class="__A__ControlButton"><a href="javascript:;" class="XX"><span class="dashicons dashicons-move"></span></a> <a href="javascript:;" class="__A__Widget_Settings_Button"><span class="dashicons dashicons-admin-tools"></span></a></div>
        <div class="__A__ControlSettings">
          <div class="d-flex align-items-center">

            <?php
            if (isset($settings[$widget['id']])) {
              $__settings = $_settings = $settings[$widget['id']];
            } else {
              $__settings = $_settings = array();
            }
            $_settings = $widgetclass::settings($_settings); ?>

            <?php foreach ((array)$_settings AS $setting_key => $setting) {  ?>
              <div class="__A__Widget_Settings_Div">
                <?php if (isset($setting['type']) && 'wh' === $setting['type']) {  ?>
                  <h6 class=" p-2"><?php echo esc_html($setting['title']) ?></h6>

                  <div class="row p-2">
                    <div class="col-5 d-flex align-items-center">
                      <?php esc_html_e('Width', 'energyplus'); ?>
                    </div>
                    <div class="col-6 d-flex align-items-center">
                      <select name="w" class="__A__Widget_Resize" data-id="<?php echo esc_attr($widget['id'])?>" id="__A__Widget_W_<?php echo esc_attr($widget['id'])?>">
                        <?php foreach ($setting['values'][0]['values'] AS $value) {  ?>
                          <option value="<?php echo esc_attr($value) ?>"  <?php if ((string)$value ===  $widget['w']) { echo " selected"; }?>><?php echo esc_attr($value)?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div  class="row  p-2">
                    <div class="col-5 d-flex align-items-center">
                      <?php esc_html_e('Height', 'energyplus'); ?>
                    </div>
                    <div class="col-6 d-flex align-items-center">

                      <select name="h" class="__A__Widget_Resize" data-id="<?php echo esc_attr($widget['id'])?>" id="__A__Widget_H_<?php echo esc_attr($widget['id']) ?>">
                        <?php foreach ($setting['values'][1]['values'] AS $value) {  ?>
                          <option value="<?php echo esc_attr($value) ?>" <?php if ((string)$value === $widget['h']) { echo " selected"; }?>><?php echo esc_attr($value)?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                <?php } ?>

                <?php if (isset($setting['type']) && 'button' === $setting['type']) {  ?>
                  <div class="pl-5">
                    <a href="<?php echo str_replace('%id%', $widget['id'], esc_url_raw($setting['link']))?>" name="__A__Widget_Settings_Button_<?php echo esc_attr($widget['id']). "_". esc_attr($setting_key) ?>" class="btn btn-primary trig __A__Widget_Settings_Button_<?php echo esc_attr($widget['id']). "_". esc_attr($setting_key) ?>"><?php echo esc_attr($setting['title'])?></a>
                  
                  </div>
                <?php } ?>

                <?php if (isset($setting['type']) && 'checkbox' === $setting['type']) {  ?>
                  <h6 class="p-2"><?php echo esc_attr($setting['title']) ?></h6>
                  <div class="d-flex1 align-items-center">

                    <?php foreach ($setting['values'] AS $value) {  ?>
                      <div class="m-2 float-left">
                        <input type="checkbox" name="__A__Widget_Settings_<?php echo esc_attr($widget['id']). "_". esc_attr($setting_key) ?>" class="__A__Widget_Settings_<?php echo esc_attr($widget['id']). "_". esc_attr($setting_key) ?>" value="<?php echo esc_attr($value['id'])?>" <?php if ('true' === $value['selected']) echo ' checked'; ?>><?php echo esc_attr($value['title'])?>

                      </div>
                    <?php } ?>

                  </div>
                  <script>
                  jQuery(function () {
                    "use strict";

                    jQuery(".__A__Widget_Settings_<?php echo esc_attr($widget['id']. "_". $setting_key) ?>").on( "click", function() {

                      jQuery.ajax({
                        type: 'POST',
                        url: '<?php echo admin_url("admin-ajax.php")?>',
                        data: {
                          _wpnonce: jQuery('input[name=_wpnonce]').val(),
                          _wp_http_referer: jQuery('input[name=_wp_http_referer]').val(),
                          _asnonce: EnergyPlusGlobal._asnonce,
                          action: 'energyplus_widgets',
                          a: 'settings',
                          id: '<?php echo esc_attr($widget['id'])?>',
                          set_id: jQuery(this).val(),
                          s: jQuery(this).prop('checked')
                        },
                        cache: false,
                        headers: {
                          'cache-control': 'no-cache'
                        },
                        success: function(response) {
                          window.reload_widgets();
                        }
                      }, 'json');
                    });
                  });
                  </script>
                <?php } ?>
              </div>
            <?php } ?>

          </div>
        </div>
        <div class="__A__Widget_Content">
          <?php $widgetclass::run($widget, $__settings); ?></div>
        <?php if(esc_attr($widget['h'])  == 1){
           ?>
       
          <div class="__A__Widget_Contentt">
          <div class="d-flexx flex-nowrap show_todays_orders">
       <?php
$bls_order_settings =  get_option('bls_admin_commission_order_settings');
  $today = date_i18n("Y-m-d");
   $orders = wc_get_orders( 
      array('numberposts' => -1,
      'orderby' =>'date',
       'date_created' => $today,
       'status'  => 'completed',
  ));

    $total = 0; $ship_total = 0; $payment_com_total = 0;
    foreach ( $orders as $order ) {
       $order_id =  $order->get_id();
       //echo $order->get_total();
         $total += $order->get_total();
        $shipping_method =$order->get_shipping_method();
        $shipping_method_id = $shipping_method['method_name'];
        $payment_method_title = $order->get_payment_method_title();
// shipping commission condition start
        $ship_fixed = 0;
        $ship_per = 0; $ship_per_val = 0;$ship_com  = 0;
if($shipping_method_id == 'R'){
  $ship_fixed = $bls_order_settings['local_pickup']['fixed'];
  $ship_per = $bls_order_settings['local_pickup']['percentage'];


   $ship_per_val = (($ship_per / 100) * $order->get_total());
  $ship_com     = $ship_fixed + $ship_per_val;


}elseif($shipping_method_id == 'D' || $shipping_method_id == 'C' ){
  $ship_fixed  = $bls_order_settings['distance_rate']['fixed'];
  $ship_per     = $bls_order_settings['distance_rate']['percentage'];

  $ship_per_val = (($ship_per / 100) * $order->get_total());
  $ship_com     = $ship_fixed + $ship_per_val;
}else{
  $ship_com = 0;

}
$ship_total+= $ship_com;
// shipping commission condition end

// payment commission condition start
if($payment_method_title  ==  'Contanti alla Consegna'){ 
$payment_fixed = $bls_order_settings['jetpack_custom_gateway']['fixed'];
$payment_per = $bls_order_settings['jetpack_custom_gateway']['percentage'];

$payment_percentage_cal = (($payment_per / 100) * $order->get_total());
$paymentcommission = $payment_fixed + $payment_percentage_cal;

}elseif($payment_method_title =='Contanti o Carta presso Ristorante' ){

$payment_fixed = $bls_order_settings['jetpack_custom_gateway_2']['fixed'];
$payment_per = $bls_order_settings['jetpack_custom_gateway_2']['percentage'];

$payment_percentage_cal = (($payment_per / 100) * $order->get_total());
$paymentcommission = $payment_fixed + $payment_percentage_cal;


}elseif($payment_method_title  == 'Carta o Bancomat alla Consegna'){
 
$payment_fixed = $bls_order_settings['jetpack_custom_gateway_3']['fixed'];
$payment_per = $bls_order_settings['jetpack_custom_gateway_3']['percentage'];
$payment_percentage_cal = (($payment_per / 100) * $order->get_total());
$paymentcommission = $payment_fixed + $payment_percentage_cal;

}elseif($payment_method_title == 'Shop as Client - Request payment by email' ){
 
 $payment_fixed = $bls_order_settings['shop_as_client_pro_request_payment_email']['fixed'];
$payment_per = $bls_order_settings['shop_as_client_pro_request_payment_email']['percentage'];

$payment_percentage_cal = (($payment_per / 100) * $order->get_total());
$paymentcommission = $payment_fixed + $payment_percentage_cal;
 
 }elseif($payment_method_title == 'PayPal' ){
 
 $payment_fixed = $bls_order_settings['paypal']['fixed'];
$payment_per = $bls_order_settings['paypal']['percentage'];

$payment_percentage_cal = (($payment_per / 100) * $order->get_total());
$paymentcommission = $payment_fixed + $payment_percentage_cal;
 
 }elseif($payment_method_title == 'Pagamento alla consegna' ){
 
  $payment_fixed = $bls_order_settings['cod']['fixed'];
  $payment_per = $bls_order_settings['cod']['percentage'];

 $payment_percentage_cal = (($payment_per / 100) * $order->get_total());
  $paymentcommission = $payment_fixed + $payment_percentage_cal;
 
  }
  $payment_com_total+= $paymentcommission;
}
  // payment commission condition end
   
  $currency_symbol =  get_woocommerce_currency_symbol();
  $shipping_pay_total = $ship_total + $payment_com_total;
  $today_order_total = $total - $shipping_pay_total;
  

?>
              <div class="__A__II ">
                <h2><?php echo $currency_symbol . round(abs($today_order_total),2); ?> </h2>
                <h4> Total for all day orders  </h4>
              </div>
        <div class="__A__II">
                <h2><?php 
 
                echo $currency_symbol . round($ship_total,2); ?>  </h2>
                <h4>Total Shipping commission  </h4>
              </div>
              <div class="__A__II">
                <h2><?php echo $currency_symbol. round($payment_com_total,2); ?> </h2>
                <h4>Total Payment commission</h4>
              </div>
              <div class="__A__II">
                <h2><?php echo $currency_symbol . round($shipping_pay_total,2); ?> </h2>
                <h4>Total Shipping & Payment commission</h4>
              </div>
          </div>
          </div> 
        <?php } ?>
        </div>
      </div>
    <?php } ?>
  </div>

  <div class="__A__Widget_Add __A__GP text-right"><a href="<?php echo EnergyPlus_Helpers::admin_page('dashboard', array('action'=>'widget_list'))?>" class="trig"><?php _e('Add or remove widgets', 'energyplus'); ?></a></div>

  <div id="energyplus-wp-notices" class="__A__GP">
    <?php apply_filters('admin_notices', array()); ?>
  </div>

  <p>&nbsp;</p>
<style type="text/css">
  .__A__Widget_h1{
    height: 210px !important;
  }
  @media (max-width: 820px){
.__A__Widget_h1 {
    height: auto !important;
}
}
</style>