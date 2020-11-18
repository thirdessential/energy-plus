<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
 function bls_energy_phone_order_fields( $order_id ){
    $custom_fields = get_option("phone-orders-for-woocommerce");
    if(isset($custom_fields['order_custom_fields']) && trim($custom_fields['order_custom_fields']) != ''){
        $custom_field_string = trim( $custom_fields['order_custom_fields'] );
        $fieldList = explode(PHP_EOL, $custom_field_string);

        if( count( $fieldList > 0 ) ){
            foreach( $fieldList as $single_field ){
                $_field = trim( $single_field );
                $field_data = explode("|", $_field );
                $field_name = $field_data[0];
                $field_key = $field_data[1];
                if( $field_key != 'delivery_date'){
                    continue;
                }
                $field_value = trim( get_post_meta( $order_id, $field_key, true ));
                $field_value = ( $field_value == '' ) ? 'NA': $field_value;
                echo '<br/><span class="">' . str_replace('*','', $field_name). ': ' . $field_value . '</span>';
            }

        }// endif fieldList

    }

}
function bls_energy_list_phone_order_fields( $order_id ){
    $custom_fields = get_option("phone-orders-for-woocommerce");
    if(isset($custom_fields['order_custom_fields']) && trim($custom_fields['order_custom_fields']) != ''){
        $custom_field_string = trim( $custom_fields['order_custom_fields'] );
        $fieldList = explode(PHP_EOL, $custom_field_string);

        if( count( $fieldList > 0 ) ){
            foreach( $fieldList as $single_field ){
                $_field = trim( $single_field );
                $field_data = explode("|", $_field );
                $field_name = $field_data[0];
                $field_key = $field_data[1];
                $field_value = trim( get_post_meta( $order_id, $field_key, true ));
                $field_value = ( $field_value == '' ) ? '': $field_value;
                if( '' != $field_value ):
                    echo '<div class="col-xs-12 col-sm-12 col-md-12 p10"><strong>' . str_replace('*','', $field_name). ':</strong> ' . $field_value . '</div>';
                endif;
            }

        }// endif fieldList

    }

}
// Start: Order Color Code //
function blsepad_order_color_code( $order ){
 
    $current_status = esc_attr($order['status']);
    $current_time = date_i18n('H:i');
    $pickup_time = get_post_meta($order['id'],"pickup_time", true);
    $pickup_date = get_post_meta($order['id'],"pickup_date", true);
     
    $original_pickup_date_time = $pickup_date .' '. $pickup_time;
    $current_date = date_i18n("Y-m-d");
    $current_date_time = date_i18n("Y-m-d H:i");
    
      $bls_add_pickup_time_order_status_min_2 = get_option('bls_add_pickup_time_order_status_min_2');
      
      $bls_second_time = 15;
      if(isset( $bls_add_pickup_time_order_status_min_2[$current_status])){
         $bls_second_time = $bls_add_pickup_time_order_status_min_2[$current_status];
      }
      
      $bls_second_time = (int) $bls_second_time;
      $bls_second_time = ( $bls_second_time > 0 ) ? $bls_second_time : 15; 
 
 
      // check green color condition
      //Taking original pickup time, means the one added 15 minutes before first delivery time when order arrived, when that pickup time comes add green border color here. Example original pickup time is 19.15, at 19.15 green border appears
      $green_color_time_number = $bls_second_time;
      $green_color_time_limit = date_i18n('Y-m-d H:i', strtotime('+'.$green_color_time_number.' minute', strtotime($original_pickup_date_time)) );
 

      $yellow_color_time_number = $bls_second_time * 2;  //Take number of second field added at point 1 and if that number of minutes passed after pickup time, make border yellow.Example pickup time 19.15, number added is 15, the border becomes yellow at 19.30
      $yellow_color_time_limit = date_i18n('Y-m-d H:i', strtotime('+'.$yellow_color_time_number.' minute', strtotime($original_pickup_date_time)) );
       

      $orange_color_time_number = $bls_second_time * 3; //the second time the number from before passes (15 minutes) the border becomes orange. Example becomes orange at 19.45 

      $orange_color_time_limit = date_i18n('Y-m-d H:i', strtotime('+'.$orange_color_time_number.' minute', strtotime($original_pickup_date_time)) );


      // The third time the time passes, the border becomes red. so it becomes red at 20.00. After the third time, the time stops. So it remains Red no matter how time goes by.
      $red_color_time_number = $bls_second_time * 4;
      $red_color_time_limit = date_i18n('Y-m-d H:i', strtotime('+'.$red_color_time_number.' minute', strtotime($original_pickup_date_time)) );


      $second_time = date('H:i',strtotime($pickup_time . ' +'.$bls_second_time. 'minutes'));

      $third_time = date('H:i',strtotime($second_time . ' +'.$bls_second_time. 'minutes'));

      $fourth_time = date('H:i',strtotime($third_time . ' +'.$bls_second_time. 'minutes'));

      $status_class = '';
      $status_time_class = '';
    if( strtotime( $current_date ) == strtotime( $pickup_date ) ){
        if( strtotime($current_date_time) >= strtotime( $original_pickup_date_time) &&  strtotime($current_date_time) <= strtotime( $green_color_time_limit) ){
            $status_class = 'bls_order_status_green';
            $status_time_class = 'bls_order_time_color_green';
        }else if( strtotime($current_date_time) > strtotime( $green_color_time_limit) &&  strtotime($current_date_time) <= strtotime( $yellow_color_time_limit)  ){
              $status_class = 'bls_order_status_yellow';
              $status_time_class = 'bls_order_time_color_yellow';
         }else if( strtotime($current_date_time) > strtotime( $yellow_color_time_limit) &&  strtotime($current_date_time) <= strtotime( $orange_color_time_limit)  ){
              $status_class = 'bls_order_status_orange';
              $status_time_class = 'bls_order_time_color_orange';
        }else if( strtotime($current_date_time) > strtotime( $orange_color_time_limit) ){
              $status_class = 'bls_order_status_red';
              $status_time_class = 'bls_order_time_color_red';
        }else{
              $status_class = '';
              $status_time_class = '';
        }
    }else{
          $status_class = 'bls_order_status_black';
          $status_time_class = 'bls_order_time_color_black';
    }
     
     // if order status cancelled show no color show
    if( 'cancelled' == $current_status){
        $status_class = '';
        $status_time_class = '';
    }

    return array('status_class' => $status_class, 'status_time_class' => $status_time_class);
     
}
// End : Order Color Code //
?>

<?php if (0 < count( $orders )) {  ?>
<!--  js to change current order status -->
<script type="text/javascript">
  jQuery(document).ready(function($){
     function ChangeOrderStatus(){
        var data_do = $('.Custom_Current_Order_Change_Statuses').attr('data-do');
      if( data_do == 'changestatus'){
        var data_status = $('.Custom_Current_Order_Change_Statuses').attr('data-status');
            var data_id = $('.Custom_Current_Order_Change_Statuses').attr('data-id');
        var data_text = $('.Custom_Current_Order_Change_Statuses').attr('data-text');
        var new_status = data_status.replace("wc-", "");
        var text_class = 'text-'+new_status;
        // get old order clas
        var old_status = $(".__A__Order_Actions .custom_badge_"+data_id).attr('data-status');
        // remove old class
        $(".__A__Order_Actions .custom_badge_"+data_id).removeClass('custom-badge-'+old_status);
        $(".__A__Order_Actions .custom_badge_"+data_id).removeClass('text-'+old_status);
        // add new class
        $(".__A__Order_Actions .custom_badge_"+data_id).addClass('custom-badge-'+new_status);
        $(".__A__Order_Actions .custom_badge_"+data_id).addClass('text-'+new_status);
        $(".__A__Order_Actions .custom_badge_"+data_id).attr('data-status',new_status);
        $(".__A__Order_Actions .custom_badge_"+data_id).text(data_text);

      }
  }

    $(".__A__Order_Items .__A__Order_Actions a.__A__Order_Change_Statuses").on("click", function(){

      $(".__A__Order_Items .__A__Order_Actions a.__A__Order_Change_Statuses").removeClass('Custom_Current_Order_Change_Statuses');
      $(this).addClass('Custom_Current_Order_Change_Statuses');
      setTimeout(function ( ){
        ChangeOrderStatus()
       },1500);
    })
 })
</script>
 <div class="preparation_time" style="margin-left:60%;color:#212529;font-size: 14px;">
    <?php
    // code  to show preperation time
    function bls_hide_show_preparation_time(){
          $bls_delivery_setting = get_option('bls_order_woo_delivery_setting');
          $bls_pickup_setting = get_option('bls_order_woo_pickup_setting');
          $bls_prep_time_setting = get_option('bls_order_woo_preparation_time_setting');
          $current_day = date_i18n('w');
          $current_time = strtotime(date_i18n('H:i'));
          $pickup_l_s = ($bls_pickup_setting[$current_day]['lunch_start_time']);
          $pickup_l_e = ($bls_pickup_setting[$current_day]['lunch_end_time']);

          $delivery_l_s = ($bls_delivery_setting[$current_day]['lunch_start_time']);
          $delivery_l_e = ($bls_delivery_setting[$current_day]['lunch_end_time']);

          $pickup_l_s_stm = strtotime($pickup_l_s);

          $pickup_l_e_stm = strtotime($pickup_l_e);

          $delivery_l_s_stm = strtotime($delivery_l_s);
          $delivery_l_e_stm = strtotime($delivery_l_e);

          $pickup_d_s = ($bls_pickup_setting[$current_day]['dinner_start_time']);
          $pickup_d_e = ($bls_pickup_setting[$current_day]['dinner_end_time']);
          $delivery_d_s = ($bls_delivery_setting[$current_day]['dinner_start_time']);
          $delivery_d_e = ($bls_delivery_setting[$current_day]['dinner_end_time']);

          $pickup_d_s_stm = strtotime($pickup_d_s);
          $pickup_d_e_stm = strtotime($pickup_d_e);
          $delivery_d_s_stm = strtotime($delivery_d_s);
          $delivery_d_e_stm = strtotime($delivery_d_e);

          $l_prepar_time = $bls_prep_time_setting[$current_day]['lunch_preparation_time'];
          $d_prepar_time = $bls_prep_time_setting[$current_day]['dinner_preparation_time'];
          // check for pickup lunch time
          if( $pickup_l_s_stm < $current_time &&  $pickup_l_e_stm > $current_time
            || $pickup_l_s_stm  == $current_time  || $pickup_l_e_stm  == $current_time ){
            // echo "Show Pickup Lunch Time";
            ?>
            Lunch Preperation Time :
            <a href="javascript:void(0)" class="bls_preperation_time_edit" data-id="<?php echo $l_prepar_time ;?>"  data-hash="<?php echo $l_prepar_time ;?>"><?php echo $l_prepar_time ;?> Minutes</a>
            <?php
      }else if( $delivery_l_s_stm <  $current_time &&  $delivery_l_e_stm > $current_time || $delivery_l_s_stm  == $current_time || $delivery_l_e_stm  == $current_time  ){
            //echo "Show Delivery Lunch Time";
            ?>
            Lunch Preperation Time :
            <a href="javascript:void(0)" class="bls_preperation_time_edit" data-id="<?php echo $l_prepar_time ;?>"  data-hash="<?php echo $l_prepar_time ;?>"><?php echo $l_prepar_time ;?> Minutes</a>
            <?php
          }else if( $pickup_d_s_stm < $current_time && $pickup_d_e_stm > $current_time || $pickup_d_s_stm  == $current_time ||  $pickup_d_e_stm == $current_time ){
            //echo "Show Pickup Dinner Time";
            echo "<br>";
            ?>
            Dinner Preperation Time :<a href="javascript:void(0)" class="bls_preperation_time_edit" data-id="<?php echo   $d_prepar_time ;?>"  data-hash="<?php echo $d_prepar_time ; ?>"><?php echo   $d_prepar_time ; ?> Minutes</a>
            <?php
          }else if( $delivery_d_s_stm < $current_time  &&  $delivery_d_e_stm > $current_time || $delivery_d_s_stm  == $current_time ||  $delivery_d_e_stm == $current_time ){
            ////echo "Show Delivery Dinner Time";
            ?>
            Dinner Preperation Time :<a href="javascript:void(0)" class="bls_preperation_time_edit" data-id="<?php echo   $d_prepar_time ;?>"  data-hash="<?php echo $d_prepar_time ; ?>"><?php echo   $d_prepar_time ; ?> Minutes</a>
            <?php
          }else{
            //echo "Shop Close";
            ?>
            <a href="javascript:void(0)">SHOP CLOSED</a>
            <?php
          }

    }
    //  Stop all order code  start
    function bls_order_stop_restart_button(){

    $bls_order_settings = get_option('bls_order_stop_restart_setting');
    $current_day = date_i18n('w');
    //first check type
    if(gettype($bls_order_settings) != 'array'){
          $bls_temp = array();
          for($i = 0; $i < 7; $i++) {
              $bls_temp[$i] = '';
          }
          update_option('bls_order_stop_restart_setting',$bls_temp);
    }
    $bls_order_settings = get_option('bls_order_stop_restart_setting');
    if($bls_order_settings[$current_day] == 'stop' || $bls_order_settings[$current_day] == '' ){
      $label = 'Stop All Orders';
      $label_value = 'stop_orders';
      ?>

    <button value="<?php echo $label;  ?>"  name="update_order" id="update_order" class="bls_order_on_off" data-id="<?php echo $label_value; ?>"><?php echo $label; ?></button>
    <?php
    }elseif($bls_order_settings[$current_day] == 'off'){
    $label = 'Restart Orders';
    $label_value = 'restart_orders';
      ?>
    <button value="<?php echo $label;  ?>" name="update_order"  id="update_order" class="bls_order_on_off" data-id="<?php echo $label_value; ?>"><?php echo $label; ?></button>
    <?php
      }
    }
      //  stop all order code end
    bls_hide_show_preparation_time();
 $bls_user = wp_get_current_user();
$allowed_roles = array(  'administrator' );
if ( array_intersect( $allowed_roles, $bls_user->roles ) ||  is_super_admin() == true ) {
    bls_order_stop_restart_button();
}
    ?>


  </div>
  <h2 class="badge badge-black badge-pill __A__Badge_Big_Title"><?php esc_html_e('Orders', 'energyplus'); ?></h2>
  <div id="energyplus-orders-1">
    <div class=" __A__GP __A__List_M1 __A__Container">
      <div class="col-xs-12 col-sm-12 col-md-12">
        <?php  foreach ($orders AS $order_group) {  ?>
          <h6><?php echo esc_html($order_group['title']) ?></h6>
          <?php foreach ($order_group['orders'] AS $order) {  ?>

            <div class="btnA __A__Item collapsed"  data-toggle="collapse" data-target="#order_<?php echo esc_attr($order['id'])?>" aria-expanded="false" aria-controls="order_<?php echo esc_attr($order['id'])?>"  id="item_<?php echo esc_attr($order['id'])?>">
               <!-- Start: Order Color Code -->
<? 
$bls_order_color = blsepad_order_color_code($order );
?>
<!-- End : Order Color Code -->
<div <?php echo 'class="'.$bls_order_color['status_class'].'"';?> ></div>
              <div class="liste  row d-flex align-items-center old98-row" style="display: none !important;">
                <div class="__A__Checkbox_Hidden">
                  <input type="checkbox" class="__A__Checkbox __A__StopPropagation"  data-id='<?php echo esc_attr($order['id'])  ?>' data-state='<?php echo esc_attr($order['status'])?>'>
                </div>
                <div class="text-center d-none d-sm-inline __A__Order_No" data-colname="<?php esc_html_e('Order No: ', 'energyplus'); ?>">
                  <span class="__A__Order_No __A__Strong"><?php echo esc_attr($order['id'])?></span>
                </div>
                <div class="col col-sm-2 col-md-1 energyplus-orders--item-badge text-center __A__Col_3">
                  <span class="siparisdurumu text-<?php echo esc_attr($order['status']);?>">
                    <span class="bg-custom bg-<?php echo esc_attr($order['status']);?>" aria-hidden="true">
                    </span><br>
                    <?php echo wc_get_order_status_name($order['status']); ?>
                  </span>
                  <span class="badge badge-pill __A__Display_None"><?php echo esc_attr($order['status']); ?></span>
                </div>
                <div class="__A__Col_Name col-7 col-sm-2">
                  <p class="energyplus-orders--name">
                    <?php echo EnergyPlus_Helpers::clean($order['shipping']['first_name'],$order['billing']['first_name']). " ".  EnergyPlus_Helpers::clean($order['shipping']['last_name'],$order['billing']['last_name']); ?>
                  </p>
                  <p class="energyplus-orders--address">
                    <?php if (isset($order['shipping']['country']))  {
                      if (isset(WC()->countries->states[EnergyPlus_Helpers::clean($order['shipping']['country'],$order['billing']['country'])][EnergyPlus_Helpers::clean($order['shipping']['state'],$order['billing']['state'])])) {
                        echo WC()->countries->states[EnergyPlus_Helpers::clean($order['shipping']['country'],$order['billing']['country'])][EnergyPlus_Helpers::clean($order['shipping']['state'],$order['billing']['state'])];
                      } else {
                        echo EnergyPlus_Helpers::clean($order['shipping']['state'],$order['billing']['state']);
                      }
                      echo esc_html(', ' . EnergyPlus_Helpers::clean($order['shipping']['city'],$order['billing']['city']));
                    } ?>
                  </p>
                </div>
                <div class="col col-sm-2 __A__Col_3"  data-colname='<?php esc_html_e('Details', 'energyplus'); ?>'>
                  <span class="__A__Order_No  d-inline d-lg-none">
                    <?php esc_html_e('Order No', 'energyplus'); ?>: <?php echo esc_attr($order['id'])?><br /><br />
                  </span>
                  <span><?php echo wc_format_datetime($order['date_created'], 'd M,');; ?></span>
                  <span><?php echo wc_format_datetime($order['date_created'], 'H:i'); ?><br /></span>
                  <?php echo esc_html($order['payment_method_title']) ?>
                </div>
                <div class="col col-sm-4 d-md-none d-lg-block __A__Order_Products __A__Col_3" data-colname='<?php esc_html_e('Products', 'energyplus'); ?>'>
                  <?php foreach ($order['line_items'] AS $item) {  ?>
                    <?php echo EnergyPlus_Helpers::product_image($item['product_id'],  $item['quantity'], 'width: 55px;'); ?>
                  <?php } ?>
                  <?php if ($order['customer_note'] && esc_attr($order['status']) !== "completed") { ?>
                  <div class="__A__Clear_Both"></div>
                  <div class="__A__Order_Customer_Notice bg-warning">
                    <?php printf(esc_html__('Note: %s', 'energyplus'), esc_html($order['customer_note']))?>
                  </div>
                  <?php } ?>
                </div>
                <div class="col __A__Col_Price __A__Col_3X text-right" data-colname='Price'>
                  <span class="energyplus-orders--item-price"><?php echo wc_price($order['total'],array('currency'=>$order['currency'])); ?></span>
                  <br>
                  <span class="badge badge-pill badge-<?php echo esc_attr($order['status']) ?> d-inline-block d-sm-none __A__Order_Status_R"><?php echo esc_attr($order['status']); ?></span>

                </div>
                <div class="col col-sm-1  __A__Actions text-center d-none">
                  <span class="dashicons dashicons-arrow-down-alt2 bthidden1" aria-hidden="true"></span>
                  <span class="dashicons dashicons-no-alt bthidden" aria-hidden="true"></span>
                </div>
              </div>
              <!-- Custom row html Start -->
              <div class="d-flex justify-content-around liste mt-4 row">
                  <div class="__A__Checkbox_Hidden">
                      <input type="checkbox" class="__A__Checkbox __A__StopPropagation"  data-id='<?php echo esc_attr($order['id'])  ?>' data-state='<?php echo esc_attr($order['status'])?>'>
                  </div>
                  <div class="col-liste-order">
                      <h3 class="col-heading"><strong>Order Id</strong></h3>
                      <div class="order_details-wr">
                        <p class="order_details-no mb-0"><?php
                          $external_order_id = get_post_meta($order['id'],"external_order_id", true);
                          if(trim($external_order_id) != ''){
                            $Optimus_Order_ID = trim($external_order_id);
                          }else{
                              $Optimus_Order_ID = $order['std']->get_order_number();
                          }
                          echo esc_attr($Optimus_Order_ID)?>
                        </p>
                        <p class="order_detail-date mb-0"><?php echo wc_format_datetime($order['date_created'], 'd F'); ?>
                          <span><?php echo wc_format_datetime($order['date_created'], 'Y'); ?></span>
                        </p>
                      </div>
                      <div class="energyplus-orders--item-badge cwc_order_status">
                          <span class="siparisdurumu text-<?php echo esc_attr($order['status']);?>"><span class="bg-custom bg-<?php echo esc_attr($order['status']);?>" aria-hidden="true"></span><br><?php echo wc_get_order_status_name($order['status']); ?></span>
                      </div>
                      <?php
                      $customer_id = $order['customer_id'];
                      $userInfo = get_user_by('id', $customer_id);
                      ?>
                  </div>
                  <div class="col-icon">
                    <img src="<?php echo EnergyPlus_Public.'img/laptop.png';?>">
                  </div>
                  <div class="col-liste">
                    <div class="col-tracking-wrap">
                      <div class="tracking-text">
                        <h3 class="col-heading"><strong>Customer</strong></h3>
                        <p><?php echo $userInfo->first_name .' '.$userInfo->last_name;?></p>
                        <p><a><?php echo $order['billing']['phone'];?></a></p>
                        <p><?php if (isset($order['shipping']['country']))  {
                          if (isset(WC()->countries->states[EnergyPlus_Helpers::clean($order['shipping']['country'],$order['billing']['country'])][EnergyPlus_Helpers::clean($order['shipping']['state'],$order['billing']['state'])])) {
                            echo WC()->countries->states[EnergyPlus_Helpers::clean($order['shipping']['country'],$order['billing']['country'])][EnergyPlus_Helpers::clean($order['shipping']['state'],$order['billing']['state'])];
                          } else {
                            echo EnergyPlus_Helpers::clean($order['shipping']['state'],$order['billing']['state']);
                          }
                          echo esc_html(', ' . EnergyPlus_Helpers::clean($order['shipping']['city'],$order['billing']['city']));
                        } ?></p>
                      </div>
                    </div>
                  </div>
                    <?php
                    $bls_pickup_date = get_post_meta($order['id'],"pickup_date", true);
                    $pickup_datetime = get_post_meta($order['id'],"pickup_date", true).' '.get_post_meta($order['id'],"pickup_time", true);
                    $pickup_time_show  = get_post_meta($order['id'],"pickup_time", true);
                    $blsepa_woo_api = new blsepa_woo_api();
                    $pickup_time_show = $blsepa_woo_api->bls_convert_timeslot_to_time_string($pickup_time_show);

                    $bls_order_type  = get_post_meta($order['id'] , "bls_order_type", true);
                    ?>
                  <div class="col-liste-small">
                      <h3 class="col-heading"><strong>Pickup</strong></h3>
                      <h5 class="color-blue mb-0 <?php echo $bls_order_color['status_time_class'];?>">
                      <?php
                        
                      if( $bls_pickup_date  == '' || $bls_pickup_date  == null ){
                          echo "NA";
                      }else{
                          echo date_i18n("H:i", strtotime($pickup_time_show));
                      }
                      ?>
                        </h5>
                      <p class="delivery_date mb-0 <?php echo $bls_order_color['status_time_class'];?>"><?php
                      if( $bls_pickup_date  == '' || $bls_pickup_date  == null ){
                          echo "NA";
                      }else{
                          echo date("d F Y", strtotime($pickup_datetime));
                      }
                   
                      ?></p>
                      <div class="col-status">
                        <h5>Status: <span class="payment_pay"></span><?php echo get_post_meta($order['id'],"task_status", true);?></h5>
                      </div>
                  </div>
                  <?php if($bls_order_type!= 'pickup'){
                    ?>
                  <div class="col-listeee">
                    <?php

                    $delivery_date = get_post_meta($order['id'],"delivery_date", true);
                    $delivery_datetime = get_post_meta($order['id'],"delivery_time", true)

                    ?>

                    <h3 class="col-heading"><strong>Delivery</strong></h3>
                    <h5 class="mb-0 color-orange"><?php echo $delivery_datetime; ?></h5>
                    <p class="delivery_date mb-0"><?php
                    echo date("d F Y", strtotime($delivery_date));
                    ?></p>
                    <div class="col-status">
                      <h5>Status: <span class="payment_pay"></span><?php echo get_post_meta($order['id'],"tookan_delivery_status", true);?></h5>
                    </div>
                  </div>
                  <?php }else{  ?>
                  <div class="col-listeee">
                    <div class="col-liste">
                      <div class="col-tracking-wrap">
                        <div class="tracking-text">
                          <h3 class="col-heading"><strong>Delivery</strong></h3>
                            <?php foreach ($order['shipping_lines'] AS $item) {  ?>
                              <p> <h3 class="col-heading"><?php echo esc_html($item['name'])?></h3>
                              </p>

                              <?php } ?>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php } ?>
                  <?php if($bls_order_type!= 'pickup'){
                     ?>
                  <div class="col-listeee">
                    <h3 class="col-heading"><strong>Rider</strong></h3>
                    <div class="tracking-wrap">
                      <?php
                      $agent_picture = get_post_meta($order['id'],"agent_picture", true);
                      if( '' != $agent_picture && (filter_var($agent_picture, FILTER_VALIDATE_URL)) ):
                      echo '<img src="'.$agent_picture.'" style="padding:0px;">';
                        endif;
                      ?>
                      <div class="tracking-text">
                        <p><?php echo get_post_meta($order['id'],"agent_name", true);?></p>
                        <p><a><?php echo get_post_meta($order['id'],"agent_phone", true);?></a></p>
                      </div>
                    </div>
                  </div>
                  <?php }else{  ?>
                <div class="col-listeee">
                  <h3 class="col-heading"><strong>Rider</strong></h3>
                  <div class="tracking-wrap  rider_not">

                    <div class="tracking-text">
                     <p>Not Available</p>
                    </div>
                  </div>
                </div>
                <?php  } ?>

                <div class="col-liste-small">
                  <h3 class="col-heading"><strong>Total</strong></h3>
                  <h5 class="mb-0 color-orange">
                    <?php 
                    $order_info = new WC_Order( $order['id']);
                    $vat_price_string = $order_info->get_formatted_order_total('incl');
                    $vat_price_string1 = $order_info->get_formatted_order_total();  
                    echo $vat_price_string1;
                  //echo wc_price($order['total'],array('currency'=>$order['currency'], 'price_format' => get_woocommerce_price_format())); ?></h5>
                  <p class="delivery_date mb-0"><?php echo esc_html($order['payment_method_title']) ?></p>
                  <div class="col-status">
                    <h5>Status: <span class="payment_pay">
                      <?php /*if (!empty($order['payment_method_title']) && $order['status'] != 'pending') {*/
                              echo get_order_status_paid_unpaid($order['status'],$order['id']);
                            /*}else{
                              echo "Not Paid";
                            } */ ?></span></h5>
                  </div>
                </div>
              </div>
              <!-- Custom row html End -->
              <!-- Start Inner single row HTML -->
            </div>
            <div class="collapse col-xs-12 col-sm-12 col-md-12 __A__Order_Details" id="order_<?php echo esc_attr($order['id'])?>">
              <div class="row __A__Order_Items">

                <div class="__A__Order_Close hello"></div>

                <div class="col-md-12 col-sm-12">

                  <div class="row">

                    <div class="col-md-5 col-sm-12 __A__Order_Address __A__Order_Actions">
                            <?php
                            $order_statuses['trash'] = esc_html__('Delete', 'energyplus');
                            $cond = EnergyPlus::option('reactors-tweaks-order-cond',array_keys(wc_get_order_statuses()));

                            if (isset($cond['wc-'.$order['status']])) {
                              foreach ($cond['wc-'.$order['status']] AS $key) {
                                if ('-' === $key) {
                                  continue;
                                } else if ('trash' === $key) {
                                  $next_statuses[] = $key;
                                } else {
                                  $next_statuses[] = $key;
                                }
                              }
                            } else {
                              $next_statuses = array_keys(wc_get_order_statuses());
                              $next_statuses[] = 'trash';
                            }

                          $order['next_statuses'] = $next_statuses; ?>
                      <?php  if (!empty($order['next_statuses']) && 'trash' !== $order['status']) {  ?>

                      <div class="row">
                        <!--   <h3>Current status</h3> -->
                        <div class="col-xs-12 col-sm-12 col-md-12 p20 heading"><strong>Order Status</strong></div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                          <div data-status="<?php echo esc_attr($order['status']);?>" class="__A__Order_Status badge custom_badge_<?php echo esc_attr($order['id'])?> custom-badge-<?php echo esc_attr($order['status']);?> px-3 py-2 rounded-pill text-capitalize font-weight-light mb-2 mt-2 text-<?php echo esc_attr($order['status']);?>">
                            <?php echo wc_get_order_status_name($order['status']); ?>
                          </div>
                          <h4><?php esc_html_e('Change to...', 'energyplus'); ?></h4>
                          <?php $order_statuses = wc_get_order_statuses(); $order_statuses['trash'] = esc_html__('  Delete', 'energyplus');

                          ?>
                          <?php foreach ($order['next_statuses'] AS $next_status) {

                          ?>
                            <a href="javascript:;" id="refresh_div" data-status="<?php echo esc_attr($next_status)?>" data-do='changestatus' data-id='<?php echo esc_attr($order['id'])?>' data-text="<?php echo esc_html($order_statuses[$next_status])?>" class="__A__Ajax_Button __A__StopPropagation __A__Order_Change_Statuses"><span  class="text-<?php echo str_replace('wc-','',esc_attr($next_status))?>">⬤</span><?php echo esc_html($order_statuses[$next_status])?></a>
                          <?php } ?>
                          <br />
                        </div>

                        <!-- TRash HTML START -->
                        <div class="row">
                          <?php  if ('trash' === $order['status']) {  ?>
                            <a href="javascript:;" data-status="restore" data-do='changestatus' data-id='<?php echo esc_attr($order['id'])?>' class="__A__Ajax_Button __A__StopPropagation"><?php esc_html_e('Restore order', 'energyplus'); ?></a>
                            <a href="javascript:;" data-status="deleteforever" data-do='changestatus' data-id='<?php echo esc_attr($order['id'])?>' class="__A__Ajax_Button __A__StopPropagation"><?php esc_html_e('Delete forever', 'energyplus'); ?></a>
                          <?php } else {  ?>
                            &nbsp;
                            <br />
                          <?php } ?>
                        </div>
                        <!-- Trash HTML END -->
                      </div>
                      <?php } ?>
                      <div class="row  order_details">
                        <div class="col-xs-12 col-sm-12 col-md-12 edit_order">
                          <h3>
                            <a href="<?php echo admin_url( 'post.php?post=' . esc_attr($order['id']). '&action=edit&energyplus_hide' );?>" class=" __A__Ajax_Btn_SP trig" data-hash="<?php echo esc_attr($order['id'])?>"><strong><?php esc_html_e('Edit Order', 'energyplus'); ?></strong></a>

                          </h3>
                          <p><strong>Numero Colli </strong>
                              <select>
                                  <option value="1">1</option>
                                  <option value="2">2</option>
                                  <option value="3">3</option>
                                  <option value="4">4</option>
                                  <option value="5">5</option>
                              </select>
                          </p>
                        </div>
                        <?php
                        $the_order = wc_get_order($order['id']);
                        $customer_order_notes = $the_order->get_customer_order_notes();
                        ?>
                        <?php if(!empty($order['customer_note'])):?>
                                <div class="customer_notes"><strong>Order Notes: </strong><p><?php echo $order['customer_note'];?></p></div>
                                <?php endif;?>
                        <?php if( count($customer_order_notes)  > 0 ):?>
                            <div class="customer_notes"><strong>Order Notes: </strong>
                            <?php foreach ($customer_order_notes as $key_con => $value_con) {
                               ?>
                               <p><?php echo $value_con->comment_content;?></p>  
                               <?php
                            }
                            ?>
                            </div>
                        <?php endif;?>        
                      </div>

                    </div>

                    <div class="col-md-7 col-sm-12 __A__StopPropagation __A__Order_Address">

                      <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 p20"><strong>Order Origin</strong></div>
                        <div class="col-xs-12 col-sm-12 col-md-12">POS Order</div>
                        <div class="col-xs-12 col-sm-12 col-md-12 p20"><strong>Customer</strong></div>
                        <div class="col-xs-12 col-sm-12 col-md-12 cust-name">
                          <div><?php echo $userInfo->first_name .' '.$userInfo->last_name;?>
                             <i class="fas fa-search "></i>
                          </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 p20">
                          <strong><?php esc_html_e('Telephone', 'energyplus'); ?></strong>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                          <a href="tel:<?php echo esc_attr($order['billing']['phone'])?>"><?php echo esc_html($order['billing']['phone'])?></a>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 p20"><strong><?php esc_html_e('E-Mail', 'energyplus'); ?></strong></div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                          <a href="mailto:<?php echo sanitize_email($order['billing']['email'])?>">
                            <?php echo sanitize_email($order['billing']['email'])?>
                          </a>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12">
                          <strong class="payment_status">Payment Status</strong>
                        </div>
                        <p class="col-xs-12 col-sm-12 col-md-12 pay">

                          <a href="javascript:void(0)" class="woo_payment_method_edit" data-id="<?php echo $order['id'];?>" data-hash="<?php echo esc_attr($order['id'])?>">  
                                  <?php
                                  if( esc_html($order['payment_method_title']) != '' ){
                                        echo esc_html($order['payment_method_title']);
                                    }else{
                                      echo "NA";
                                  }
                                ?>
                          </a>

                          <span class="payment_pay">
                            <?php /*if (!empty($order['payment_method_title']) && $order['status'] != 'pending') {*/
                              echo get_order_status_paid_unpaid($order['status'],$order['id']);
                            /*}else{
                              echo "Not Paid";
                            } */ ?>
                          </span>
                        </p>
                      </div>
                      <?php if ('' !== trim( $order['shipping']['address_1']) OR '' !== trim( $order['shipping']['address_2'])) {  ?>

                      <div class="row __A__StopPropagation">
                        <?php if (isset($order['shipping']['email'])) {  ?>
                          <div class="col-xs-12 col-sm-12 col-md-12 p20"><strong><?php esc_html_e('E-Mail', 'energyplus'); ?></strong></div>
                          <div class="col-xs-12 col-sm-12 col-md-12">
                            <a href="mailto:<?php echo sanitize_email($order['shipping']['email'])?>">
                              <?php echo sanitize_email($order['shipping']['email'])?>
                            </a>
                          </div>
                        <?php } ?>
                        <?php if (isset($order['shipping']['phone'])) {  ?>
                          <div class="col-xs-12 col-sm-12 col-md-12 p20"><strong><?php esc_html_e('Telephone', 'energyplus'); ?></strong></div>
                          <div class="col-xs-12 col-sm-12 col-md-12">
                            <a href="tel:<?php echo esc_attr($order['shipping']['phone'])?>">
                              <?php echo esc_html($order['shipping']['phone'])?>
                            </a>
                          </div>
                        <?php } ?>
                      </div>
                      <?php } ?>
                        <div> </div>
                      </div>
                  </div>
                </div>

                <div class="col-md-12 col-sm-12 Own-wrap">
                    <hr>
                  <div class="row">
                    <div class="col-md-5 col-sm-12  __A__Order_Id">
                      <div class="row order_details">
                        <div class="col-xs-12 col-sm-12 col-md-12 own_order_id">
                          <?php
                          $external_order_id = get_post_meta($order['id'],"external_order_id", true);
                          $external_order_date = get_post_meta($order['id'],"external_order_date", true);

                          ?>
                          <a href="javascript:void(0)" class="woo_external_order_edit" data-id="<?php echo $order['id'];?>"  data-hash="<?php echo esc_attr($external_order_id); ?>">
                          <h3><strong>External Order ID</strong></h3>
                          <div class="order_details-wr">
                            <?php if( trim($external_order_id) && trim($external_order_date)){?>
                              <p class="order_details-no"><?php echo $external_order_id;?></p>

                            <p class="order_detail-date"><?php echo date("d F Y", strtotime($external_order_date));?></p>
                            <?php } else{ echo "<p>NA</p>";}?>
                          </div>
                          </a>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 own_order_total">
                        <h3><strong>Order Total</strong></h3>
                        <?php
                        $tt_shipping = 0;
                        foreach ($order['shipping_lines'] AS $item) {
                          $tt_shipping = $tt_shipping + $item['total'];
                        }
                        $shipping_text = ($tt_shipping > 0 ) ? wc_price($tt_shipping, array('currency' => $order['currency'])) : "Free Shipping";
                        ?>
                          <h5 class="color-green">
                            <?php 
                             echo $vat_price_string;
                             //echo wc_price($order['total'],array('currency'=>$order['currency'], 'price_format' => get_woocommerce_price_format())); ?></h5>
                          <p class="shipping-head">Shipping</p>
                          <p class="shipping-detail"><?php echo $shipping_text;?></p>
                        </div>
                         <!--  optimus cost start -->
                            <div class="col-xs-12 col-sm-12 col-md-12 own_order_total">
                              <!-- optimus cosr calculation start -->
<?php
  $shopid =  get_option('woocommerce_store_id');
global $wpdb;
$result = $wpdb->get_results ( "SELECT * FROM bls_optimus_commission where order_id = '".$order['id']."' AND 
  shop_id = '".$shopid."' " );
    foreach ( $result as $print ) {
     $print->optimus_commission;
    }
  if($order['status'] == 'completed'){
   $optimus_cost = round($print->optimus_commission,2) . '€';
 }else{
  $optimus_cost = '<b>-</b>';
  }
?>
                            <h3><strong>Optimus Cost</strong></h3>

                              <h5 class="color-green"><?php echo $optimus_cost; ?></h5>
                              <p class="shipping-head"></p>
                              <p class="shipping-detail"></p>
                              <!-- optimus cost calculation end -->
                            </div>
                          
                            <!--  optimus cost end -->
                      </div>
                    </div>
                    <div class="col-md-7 col-sm-12 unique-wrap">
                      <!--  Unique Order Id End -->
                      <div class="row  unique_details">
                        <div class="col-xs-12 col-sm-12 col-md-12 own_order_id">
                          <h3><strong>Optimus Order ID</strong></h3>
                          <div class="order_details-wr">
                            <p class="order_details-no"><?php echo $order['std']->get_order_number();?></p>
                            <p class="order_detail-date"><?php echo wc_format_datetime($order['date_created'], 'd F'); ?><span><?php echo wc_format_datetime($order['date_created'], 'Y'); ?></span>
                            </p>
                          </div>
                        </div>

                      <div class="col-xs-12 col-sm-12 col-md-12"><h3><strong><?php esc_html_e('Shipping Address', 'energyplus'); ?></strong></h3></div>
                      <div class="col-xs-12 col-sm-12 col-md-12"><?php echo wp_kses_post($order['shipping_formatted'])?></div>
                       <div class="col-xs-12 col-sm-12 col-md-12" style="padding-top: 22px;">
                        <a href="javascript:void(0)" class="woo_shipping_method_edit" data-id="<?php echo $order['id'];?>" data-hash="<?php echo esc_attr($order['id'])?>"> 
                            <?php 
                            if (count( $order['shipping_lines'] ) > 0 ) {
                            foreach ($order['shipping_lines'] AS $item) {  ?>
                              <h4><?php echo esc_html($item['name'])?></h4>

                               <?php }

                              }else{
                                echo "<h4>NA</h4>";
                              }  ?>
                            </a>
                      <div class="fiyat">
                              <?php echo wc_price($item['total'], array('currency' => $order['currency'])); ?>
                            </div>

                            </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-12 col-sm-12 pick-wrap">
                  <hr>
                  <div class="row">
                    <div class="col-md-5 col-sm-12 __A__Order_picker">
                      <div class="row order_details">
                      <div class="col-xs-12 col-sm-12 col-md-12 pickup">
                          <h3><strong>Pick up</strong></h3>
                          <a href="javascript:void(0)" class="woo_pickup_edit" data-id="<?php echo $order['id'];?>" data-hash="<?php echo esc_attr($order['id'])?>">
                          <?php $pickup_datetime = get_post_meta($order['id'],"pickup_date", true).' '.get_post_meta($order['id'],"pickup_time", true);

                          ?>
                          <h5 class="color-blue"><?php
                           if( $bls_pickup_date  == '' || $bls_pickup_date  == null ){
                              echo "NA";
                          }else{
                             echo date_i18n("H:i", strtotime($pickup_time_show));
                          }
                          ?></h5>
                          <p class="delivery_date"><?php
                           if( $bls_pickup_date  == '' || $bls_pickup_date  == null ){
                              echo "NA";
                          }else{
                            echo date("d F Y", strtotime($pickup_datetime));
                          }
                          ?></p>
                            </a>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 pickup_status">
                          <h3><strong>Pickup Status</strong></h3>
                          <h5 class="delivery-state"><?php echo get_post_meta($order['id'],"task_status", true);?></h5>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-7 col-sm-12 unique-wrap">
                      <!--  Unique Order Id End -->
                      <div class="row  unique_details">
                        <div class="col-xs-12 col-sm-12 col-md-12 delivery">
                          <?php
                              $delivery_date = get_post_meta($order['id'],"delivery_date", true);
                              $delivery_datetime = get_post_meta($order['id'],"delivery_time", true)

                            ?>
                            <h3><strong>Delivery</strong></h3>
                            <h5><?php echo $delivery_datetime; ?></h5>
                            <p class="delivery_date"><?php
                              echo date("d F Y", strtotime($delivery_date));
                              ?></p>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 delievery_status">
                            <h3><strong>Delivery Status</strong></h3>
                            <h5 class="delivery-state color-greenish"><?php echo get_post_meta($order['id'],"tookan_delivery_status", true);?></h5>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-12 col-sm-12 track-wrap">
                    <hr>
                  <div class="row">
                    <div class="col-md-5 col-sm-12 __A__Order_track ">
                      <div class="row order_details">
                        <div class="col-xs-12 col-sm-12 col-md-12 own_order_id">
                          <?php
                          $pickup_tracking_link = get_post_meta($order['id'], 'tracking_link', true);
                          $pickup_tracking_link = ($pickup_tracking_link) ? $pickup_tracking_link :'#';
                          $delivery_tracing_link = get_post_meta($order['id'], 'delivery_tracing_link', true);
                          $delivery_tracing_link = ($delivery_tracing_link) ? $delivery_tracing_link :'#';

                          ?>
                          <h3 class="mb-3"><strong>Traking</strong></h3>
                          <div class="tracking-wrap"><a class="d-flex" href="<?php echo $pickup_tracking_link;?>"><img src="<?php echo EnergyPlus_Public.'img/home.png';?>">
                            <p>Shop</p></a>
                          </div>
                          <div class="tracking-wrap"><a class="d-flex" href="<?php echo $delivery_tracing_link;?>"><img src="<?php echo EnergyPlus_Public.'img/user.png';?>">
                            <p>Customer</p></a>
                          </div>
                        </div>

                      </div>
                    </div>
                    <div class="col-md-7 col-sm-12 driver-wrap">
                      <div class="row ">
                        <div class="col-xs-12 col-sm-12 col-md-12 driver">
                          <h3 class="mb-3"><strong>Driver</strong></h3>
                          <div class="tracking-wrap">
                            <?php
                              $agent_picture = get_post_meta($order['id'],"agent_picture", true);
                              if( '' != $agent_picture && (filter_var($agent_picture, FILTER_VALIDATE_URL)) ):
                              echo '<img src="'.$agent_picture.'" style="padding:0px;">';
                                endif;
                              ?>
                          <div class="tracking-text">
                            <p><?php echo get_post_meta($order['id'],"agent_name", true);?></p>
                            <p><a><?php echo get_post_meta($order['id'],"agent_phone", true);?></a></p>
                          </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Test 3 Start -->
                <div class="col-md-12 col-sm-12 test3">
                  <hr>
                  <?php  foreach ($order['line_items'] AS $item) { ?>
                    <div class="row __A__Order_Item">
                      <div class="col-3 col-sm-3 col-md-2"><img src="<?php echo get_the_post_thumbnail_url($item['product_id']); ?>" class="__A__Product_Image" ></div>
                      <div class="col-9 col-sm-9 col-md-10">
                        <h4><?php echo esc_html($item['name'])?></h4>
                        <?php
                          $formatted_meta_data = $item->get_formatted_meta_data();
                          if ($formatted_meta_data) {  ?>
                          <div class="__A__Order_Details_Variation">

                            <?php
                            foreach ($formatted_meta_data AS $meta) {
                              echo '<strong>' . esc_html($meta->display_key). '</strong>: <span class="badgex badge-pillx badge-blackx"> ' . wp_kses_data ( $meta->display_value ) . '</span> &nbsp; &nbsp;<br> ';
                            }
                            ?>
                          </div>
                          <?php } ?>
                          <div class="fiyat">
                            <?php 
                            $bls_tax_data = wc_tax_enabled() ? $item->get_taxes() : false;

                            echo wc_price(($item['subtotal']/$item['qty']), array('currency' => $order['currency'])); ?> x   <span class="badge badge-pill badge-danger"><?php echo esc_html($item['qty']); ?></span> =   
                            <?php 
                            $bls_total_tax = (isset( $item['total_tax'] ) ) ? $item['total_tax'] : 0;
                               $bls_total_tax = (float) $bls_total_tax;
                            if( ($bls_tax_data) && $bls_total_tax > 0 ){
                                  $bls_item_total = $item['subtotal'] + $bls_total_tax;
                                  echo wc_price($bls_item_total, array('currency' => $order['currency'])) ." ( <span> Inclusi IVA </span>".wc_price($bls_total_tax, array('currency' => $order['currency'])). " )";
                              }else{
                            echo wc_price($item['subtotal'], array('currency' => $order['currency']));
                           }
                            ?>
                          </div>
                      </div>
                      <div class="col-12"><hr></div>
                    </div>
                  <?php } ?>

                  <?php foreach ($order['coupon_lines'] AS $item) {  ?>
                    <div class="row __A__Order_Item">
                      <div class="col-3 col-sm-3 col-md-2"><div class="__A__Order_Item_Group" ><span class="dashicons dashicons-carrot"></span></div></div>
                      <div class="col-9 col-sm-9 col-md-10">
                        <h4 class="text-uppercase"><?php echo esc_html($item['code'])?></h4>

                        <div class="fiyat">
                          - <?php echo wc_price($item['discount'], array('currency' => $order['currency'])); ?>
                        </div>
                      </div>
                    </div>
                  <?php } ?>



                  <?php foreach ($order['tax_lines'] AS $item) {  ?>

                    <div class="row __A__Order_Item">
                      <div class="col-3 col-sm-3 col-md-2"><div  class="__A__Order_Item_Group"  >%</div></div>
                      <div class="col-9 col-sm-9 col-md-10">
                        <h4><?php echo esc_html($item['label'])?></h4>

                        <div class="fiyat">
                          <?php echo wc_price($item['tax_total']+$item['shipping_tax_total']+$item['discount_tax'], array('currency' => $order['currency'])); ?>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
                <!-- Test 3 End -->

              </div>
            </div>
            <!-- End Inner single row HTML -->
          <?php } ?>
        <?php } ?>
      </div>
    </div>
  </div>

  <!-- </div> -->

  <p>&nbsp;</p>
  <p>&nbsp;</p>

<?php } ?>

<!-- The Modal For External ID: START -->
<div id="myModal_woo_external_id" class="bls_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <span class="bls_close">&times;</span>
    <div class="modal-content-wrap">
      <p>Please Wait..</p>

    </div>
  </div>

</div>
<!-- The Modal For External ID: END -->
<!-- The Modal For Pickup: START -->
<div id="myModal_woo_pickup_edit" class="bls_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <span class="bls_close">&times;</span>
    <div class="modal-content-wrap">
      <p>Please Wait..</p>

    </div>
  </div>

</div>
<!-- The Modal For Pickup: END -->
<!-- The Modal For Delivery: START -->
<div id="myModal_woo_delivery_edit" class="bls_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <span class="bls_close">&times;</span>
    <div class="modal-content-wrap">
      <p>Please Wait..</p>

    </div>
  </div>

</div>
<!-- The Modal For Delivery: END -->
<!-- The Modal For Payment Method: START -->
<div id="myModal_woo_payment_method_edit" class="bls_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <span class="bls_close">&times;</span>
    <div class="modal-content-wrap">
      <p>Please Wait..</p>

    </div>
  </div>

</div>
<!-- The Modal For Payment Method: END -->

<!-- The Modal For Shipping Method: START -->
<div id="myModal_woo_shipping_method_edit" class="bls_modal">

  <!-- Modal content -->
  <div class="modal-content">
    <span class="bls_close">&times;</span>
    <div class="modal-content-wrap">
      <p>Please Wait..</p>

    </div>
  </div>

</div>
<!-- The Modal For Shipping Method: END -->

<style type="text/css">
  /* The Modal (background) */
  .bls_modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 99999; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
  }

  /* Modal Content/Box */
  .bls_modal .modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
  }
 .rider_not .tracking-text p{
    padding-left: 0px ;
  }
  /* The Close Button */
  .bls_modal .bls_close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
  }

  .bls_modal .bls_close:hover,
  .bls_modal .bls_close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
  }
</style>

<script type="text/javascript">
  //external order id js
  jQuery(document).ready(function($){
    jQuery(".woo_external_order_edit").on("click", function(){
      console.log("Please wait")
    jQuery(".bls_modal").css("display", "none");
    jQuery("#myModal_woo_external_id").css("display", "block");
    var order_id = jQuery(this).attr("data-id");
    var data = {
     'action': 'bls_woo_external_id',
     'external_id_action': 'get_data',
     'order_id': order_id
    };
    jQuery.ajax({
      url: cstmf_object.ajaxurl,
      type: 'post',
      data: data,
      dataType: 'json',
      success: function(response){

         console.log("response", response)
         if(response.status == 'success'){
            jQuery("#myModal_woo_external_id .modal-content-wrap").html('');
            jQuery("#myModal_woo_external_id .modal-content-wrap").html(response.response_data);
         }else{
            jQuery("#myModal_woo_external_id .modal-content-wrap").html('');
            jQuery("#myModal_woo_external_id .modal-content-wrap").html("<p>Something wrong</p>");
         }

      }
    });
    jQuery("#myModal_woo_external_id").css("display", "block");
    })
    jQuery("#myModal_woo_external_id .bls_close").on("click", function(){
        jQuery(".bls_modal").css("display", "none");

    })
// update code for external order id
  jQuery(document).on('click','#update_external_id_data', function(){
    // jQuery("#update_external_id_data").live("click", function(){
        var external_id = jQuery("input#update_external_id").val();
        var external_date = jQuery("input#update_external_date").val();
        var order_id = jQuery("input#update_on_order_id").val();
        var data = {
        'action': 'bls_woo_external_id',
        'external_id_action': 'update_data',
        'order_id': order_id,
        'external_id':external_id,
        'external_date' : external_date
        };
        jQuery.ajax({
          url: cstmf_object.ajaxurl,
          type: 'post',
          data: data,
          dataType: 'json',
          success: function(response){
                if(response.status == "success"){
                    jQuery("#myModal_woo_external_id .modal-content-wrap").html('');
                    jQuery("#myModal_woo_external_id .modal-content-wrap").html(response.message);

                    setTimeout(function(){
                    console.log("Hai 12");
                      window.trigGlobal.slideReveal('hide');
                        jQuery("#inbrowser--loading").addClass('d-flex').removeClass('d-none');
                        jQuery(".__A__Trig_Close").addClass('d-none');
                        jQuery("#inbrowser").attr('src', 'about:blank');
                        // jQuery("#myModal_woo_external_id .bls_close").trigger("click")
                        console.log("Hai");
                        window.location.reload();
                    }, 1000);


                }
            }
        });

    })

    // Show Option when click on Pickup date and time
    jQuery(".woo_pickup_edit").on("click", function(){
        jQuery(".bls_modal").css("display", "none");
          jQuery("#myModal_woo_pickup_edit").css("display", "block");
        var order_id = jQuery(this).attr("data-id");
        var data = {
        'action': 'bls_woo_pickup_edit',
        'pickup_edit_action': 'get_data',
        'order_id': order_id
        };
        jQuery.ajax({
          url: cstmf_object.ajaxurl,
          type: 'post',
          data: data,
          dataType: 'json',
          success: function(response){

            console.log("response", response)
            if(response.status == 'success'){
                jQuery("#myModal_woo_pickup_edit .modal-content-wrap").html('');
                jQuery("#myModal_woo_pickup_edit .modal-content-wrap").html(response.response_data);
            }else{
                jQuery("#myModal_woo_pickup_edit .modal-content-wrap").html('');
                jQuery("#myModal_woo_pickup_edit .modal-content-wrap").html("<p>Something wrong</p>");
            }

          }
        });
        jQuery("#myModal_woo_pickup_edit").css("display", "block");
    })
    jQuery("#myModal_woo_pickup_edit .bls_close").on("click", function(){
        jQuery(".bls_modal").css("display", "none");

    })
// update pickup date  and time on click
  jQuery(document).on('click','#update_pickup_data', function(){
    // jQuery("#update_pickup_data").live("click", function(){
        var pickup_date = jQuery("input#update_pickup_date").val();
        var pickup_time = jQuery("input#update_pickup_time").val();
        var order_id = jQuery("input#update_on_order_id").val();
        var data = {
        'action': 'bls_woo_pickup_edit',
        'pickup_edit_action': 'update_data',
        'order_id': order_id,
        'pickup_date':pickup_date,
        'pickup_time':pickup_time
        };
        jQuery.ajax({
          url: cstmf_object.ajaxurl,
          type: 'post',
          data: data,
          dataType: 'json',
          success: function(response){
                if(response.status == "success"){
                    jQuery("#myModal_woo_pickup_edit .modal-content-wrap").html('');
                    jQuery("#myModal_woo_pickup_edit .modal-content-wrap").html(response.message);

                    setTimeout(function(){
                    console.log("Hai 12");
                        // jQuery("#myModal_woo_external_id .bls_close").trigger("click")
                        console.log("Hai");
                        window.location.reload();
                    }, 1000);


                }
            }
        });

    })

    // Show Option when click on Delivery date and time
    jQuery(".woo_delivery_edit").on("click", function(){
        jQuery(".bls_modal").css("display", "none");
        jQuery("#myModal_woo_delivery_edit").css("display", "block");
        var order_id = jQuery(this).attr("data-id");
        var data = {
        'action': 'bls_woo_delivery_edit',
        'delivery_edit_action': 'get_data',
        'order_id': order_id
        };
        jQuery.ajax({
          url: cstmf_object.ajaxurl,
          type: 'post',
          data: data,
          dataType: 'json',
          success: function(response){

            console.log("response", response)
            if(response.status == 'success'){
                jQuery("#myModal_woo_delivery_edit .modal-content-wrap").html('');
                jQuery("#myModal_woo_delivery_edit .modal-content-wrap").html(response.response_data);
            }else{
                jQuery("#myModal_woo_delivery_edit .modal-content-wrap").html('');
                jQuery("#myModal_woo_delivery_edit .modal-content-wrap").html("<p>Something wrong</p>");
            }

          }
        });
        jQuery("#myModal_woo_delivery_edit").css("display", "block");
    })
    jQuery("#myModal_woo_delivery_edit .bls_close").on("click", function(){
        jQuery(".bls_modal").css("display", "none");

    })

//  update delievery date and time on click
    jQuery(document).on('click','#update_delivery_data', function(){
    // jQuery("#update_delivery_data").live("click", function(){
        // var delivery_date = jQuery("input#update_delivery_date").val();
        var delivery_date = jQuery("#update_delivery_date").val();
        var delivery_time = jQuery("#update_delivery_time").val();
        var order_id = jQuery("input#update_on_order_id").val();
        console.log("update_delivery_time", delivery_date);
        console.log("update_delivery_date", delivery_time);
        var data = {
        'action': 'bls_woo_delivery_edit',
        'delivery_edit_action': 'update_data',
        'order_id': order_id,
        'delivery_date': delivery_date,
        'delivery_time': delivery_time
        };
        jQuery.ajax({
          url: cstmf_object.ajaxurl,
          type: 'post',
          data: data,
          dataType: 'json',
          success: function(response){
                if(response.status == "success"){
                    jQuery("#myModal_woo_delivery_edit .modal-content-wrap").html('');
                    jQuery("#myModal_woo_delivery_edit .modal-content-wrap").html(response.message);

                    setTimeout(function(){
                    console.log("Hai 12");
                        // jQuery("#myModal_woo_external_id .bls_close").trigger("click")
                        console.log("Hai");
                        window.location.reload();
                    }, 1000);


                }
            }
        });

    });

  });

 jQuery(document).ready(function(){

jQuery('.bls_close').on("click", function(){
 jQuery("#myModal_woo_bls_time").css("display", "none");
});
// show preperation lunch and dinner time in popup on click
jQuery(".bls_preperation_time_edit").on("click", function(){

    jQuery(".bls_modal").css("display", "none");
    jQuery("#myModal_woo_bls_time").css("display", "block");
     var prep_time = jQuery(this).attr("data-id");
    var data = {
     'action': 'bls_woo_preperation_time',
     'prep_time_action': 'get_data',
     'prep_time': prep_time
    };


     jQuery.ajax({
      url: cstmf_object.ajaxurl,
      type: 'post',
      data: data,
      dataType: 'json',

      success: function(response){

        // console.log("response", response)
         if(response.status == 'success'){

        console.log('get data');
         jQuery("#myModal_woo_bls_time .modal-content-wrap").html('');
       jQuery("#myModal_woo_bls_time .modal-content-wrap").html(response.response_data);
         }else{
            jQuery("#myModal_woo_bls_time .modal-content-wrap").html('');
            jQuery("#myModal_woo_bls_time .modal-content-wrap").html("<p>Something wrong</p>");
         }

      }

    });
     jQuery("#bls_preperation_time_edit").css("display", "block");
  });
// update preperation time on update button
 jQuery(document).on('click','#update_pep_time_data', function(){
 // jQuery("#update_pep_time_data").live("click", function(){
  //alert('123');
  console.log('clicked');
        var update_lunch_time = jQuery("input#update_lunch_time").val();
        var update_dinner_time = jQuery("input#update_dinner_time").val();

        var data = {
        'action': 'bls_woo_preperation_time',
         'prep_time_action': 'update_data',
        'update_dinner_time':update_dinner_time,
        'update_lunch_time':update_lunch_time
        };
        jQuery.ajax({
          url: cstmf_object.ajaxurl,
          type: 'post',
          data: data,
          dataType: 'json',
          contentType: "application/json; charset=utf-8",

          success: function(response){
              console.log("response", response)
                if(response.status == "success"){
                  console.log('hello hii test');
                    jQuery("#myModal_woo_bls_time .modal-content-wrap").html('');
                    jQuery("#myModal_woo_bls_time .modal-content-wrap").html(response.message);

                    setTimeout(function(){
                    console.log("skjndsjkf");

                        console.log("sasfafaf");
                        window.location.reload();
                    }, 1000);


                }
            }
        });

    })
  });
</script>

<!-- js for collaspse checkedbox -->
<script>
  jQuery(".__A__Item.btnA").on( "click",function(){
    jQuery(".__A__Item.btnA").addClass('collapsed').attr("aria-expanded", false);
     jQuery(this).removeClass('collapsed').attr("aria-expanded", true);
  });
</script>
<!-- js for collaspse checkedbox end -->
<!--  js start for stop all orders  -->
<script type="text/javascript">
jQuery(document).ready(function($){
  jQuery(document).on('click','.bls_order_on_off', function(){
 // jQuery(".bls_order_on_off").live("click", function(){
 var order_text = jQuery(this).attr('data-id');
   var label = jQuery(this).val();
   console.log(label);
       //console.log(order_text);
        var data = {
        'action': 'bls_update_button',
         'update_order_text': 'update_data',
         'order_text':order_text,
         'label':label
        };
        jQuery.ajax({
          url: cstmf_object.ajaxurl,
          type: 'post',
          data: data,
          dataType: 'json',
          success: function(response){
              console.log("response", response)
                if(response.status == "success"){
                  if(order_text == 'stop_orders'){
                    jQuery('.bls_order_on_off').text('Restart Orders');
                       jQuery('.bls_order_on_off').attr('data-id','restart_orders');
                  }else{
                    jQuery('.bls_order_on_off').text('Stop All Orders');
                    jQuery('.bls_order_on_off').attr('data-id','stop_orders');
                  }


                  //jQuery('.bls_order_on_off').text('Restart Order');

                 setTimeout(function(){

                    }, 1000);


                }
            }
        });

    })
  });
</script>
<!--  js end for stop all orders  -->
<!-- modal popup for preperation time -->
<div id="myModal_woo_bls_time" class="bls_modal"  >

  <!-- Modal content -->
  <div class="modal-content">
    <span class="bls_close">&times;</span>
    <div class="modal-content-wrap">
      <p>Loading</p>

    </div>
  </div>
</div>