<?php
if (!defined('ABSPATH'))
  exit;

if (!class_exists('OCWMA_front')) {

  class OCWMA_front
  {

    protected static $instance;


    function get_adress_book_endpoint_url($address_book)
    {
      $url = wc_get_endpoint_url('edit-address', 'shipping', get_permalink());
      return add_query_arg('address-book', $address_book, $url);
    }


    function ocwma_wc_address_book_add_to_menu($items)
    {
      foreach ($items as $key => $value) {
        if ('edit-address' === $key) {
          $items[$key] = __('Address Book', 'woo-address-book');
        }
      }
      return $items;
    }


    function ocwma_popup_div_footer()
    {
?>
      <div id="ocwma_billing_popup" class="ocwma_billing_popup_class">
      </div>
      <div id="ocwma_shipping_popup" class="ocwma_shipping_popup_class">
      </div>
    <?php
    }

    //custom mt

    function ocwma_my_account_endpoint_content_billing()
    {
      $user_id       = get_current_user_id();
      global $wpdb;
      $tablename = $wpdb->prefix . 'ocwma_billingadress';
      $user = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='billing' AND userid=" . $user_id);
      echo '<div class="ocwma_table_custom">';
      echo '<div class="ocwma_table_bill">';
      if (!is_checkout()) {
        echo '<h2>Billing Details</h2>';
      }
      if (!empty($user)) {

        foreach ($user as $row) {
          $userdata_bil = $row->userdata;
          $defalt_addd = $row->Defalut;
          $user_data = unserialize($userdata_bil);
          if ($defalt_addd == 1) {
            $checked = "checkeddd";
          } else {
            $checked = "";
          }
          $user_data = unserialize($userdata_bil);
          echo '<div class="ocwma_bill_table ' . $row->id . '" data-billid="' . $row->id . '">';
          echo '<h4>' . $user_data['reference_field'] . '</h4>';
          echo '<table class="">';
          // echo '<tr>';
          // echo '<td >' .$user_data['reference_field'] .'</td>';
          //  echo '</tr>';  
          echo '<tr>';
          echo '<td >' . $user_data['billing_first_name'] . '&nbsp' . $user_data['billing_last_name'] . '</td>';
          echo '</tr>';
          echo '<td>' . $user_data['billing_company'] . '</td>';
          echo '</tr>';
          echo '<tr>';
          echo '<td>' . $user_data['billing_address_1'] . '</td>';
          echo '</tr>';
          echo '<tr>';
          //echo '<td>' .$user_data['billing_address_2'] .'</td>';
          echo '</tr>';
          echo '<tr>';
          echo '<td>' . $user_data['billing_city'] . '</td>';
          echo '</tr>';
          // echo '<tr>';
          //echo '<td>' .$user_data['billing_country'] .'</td>';
          //  echo '</tr>';
          echo '<tr>';
          echo '<td>' . $user_data['billing_postcode'] . ', ' . $user_data['billing_state'] . '</td>';
          echo '</tr>';
          // echo '<tr>';
          // echo '<td>' .$user_data['billing_state'] .'</td>';
          // echo '</tr>';
          echo '</tr>';
          echo '<tr><td><button class="form_option_edit" data-id="' . $user_id . '"  data-eid-bil="' . $row->id . '">edit</button></td><td></td></tr>';
          echo '</table>';
          echo '</div>';
        }
      }
      echo '</div>';

      echo '</div>';

    ?>

    <?php
    }

    function ocwma_my_account_endpoint_content_shipping()
    {
      $user_id       = get_current_user_id();
      global $wpdb;
      $tablename = $wpdb->prefix . 'ocwma_billingadress';
      $user = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='billing' AND userid=" . $user_id);
      echo '<div class="ocwma_table_custom">';
      $user_shipping = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='shipping' AND userid=" . $user_id);
      echo '<div class="ocwma_table_ship">';
      if (!is_checkout()) {
        echo '<h2>Shipping Details</h2>';
      }
      if (!empty($user_shipping)) {

        foreach ($user_shipping as $row) {
          $userdata_ship = $row->userdata;
          $defalt_addd = $row->Defalut;
          // $user_data = unserialize($userdata_bil);
          if ($defalt_addd == 1) {
            $checked = "checkeddd";
          } else {
            $checked = "";
          }
          $user_data = unserialize($userdata_ship);

          echo '<div class="ocwma_ship_table ' . $row->id . '" data-shipid="' . $row->id . '">';
          echo '<h4>' . $user_data['reference_field'] . '</h4>';
          echo '<table class="">';
          // echo '<tr>';
          //echo '<td ><h4>'.$user_data['reference_field'] .'</h4></td>';
          //echo '</tr>';  
          echo '<tr>';
          echo '<td >' . $user_data['shipping_first_name'] . '&nbsp' . $user_data['shipping_last_name'] . '</td>';
          echo '</tr>';
          echo '<td>' . $user_data['shipping_company'] . '</td>';
          echo '</tr>';
          echo '<tr>';
          echo '<td>' . $user_data['shipping_address_1'] . '</td>';
          echo '</tr>';
          echo '<tr>';
          //echo '<td>' .$user_data['shipping_address_2'] .'</td>';
          echo '</tr>';
          echo '<tr>';
          echo '<td>' . $user_data['shipping_city'] . '</td>';
          echo '</tr>';
          // echo '<tr>';
          // echo '<td>' .$user_data['shipping_country'] .'</td>';
          // echo '</tr>';
          echo '<tr>';
          echo '<td>' . $user_data['shipping_postcode'] . ', ' . $user_data['shipping_state'] . '</td>';
          echo '</tr>';
          // echo '<tr>';
          // echo '<td>' .$user_data['shipping_state'] .'</td>';
          // echo '</tr>';
          echo '<tr><td><button class="form_option_ship_edit" data-id="' . $user_id . '"  data-eid-ship="' . $row->id . '">edit</button></td></tr>';
          echo '</table>';
          echo '</div>';
        }
      }
      echo '</div>';
      echo '</div>';

    ?>
    <?php
    }

    function ocwma_my_account_endpoint_content()
    {
      $user_id       = get_current_user_id();
      global $wpdb;
      $tablename = $wpdb->prefix . 'ocwma_billingadress';
      $user = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='billing' AND userid=" . $user_id);

     /* echo '<pre>';
      print_r($user);
      echo '</pre>';*/

      $userarray1 = array();
      $userarray2 = array();
      foreach ( $user as $method ){
        if($method->Defalut){
          $userarray1[] = $method;
        }
      }
      foreach ( $user as $method ){
        if(!$method->Defalut){
          $userarray2[] = $method;
        }
      }
      
      $user = array_merge($userarray1, $userarray2);

    /* echo '<pre>';
      print_r($userarray);
      echo '</pre>';*/

      echo '<div class="ocwma_table_custom col-md-12">';
      if (!is_checkout()) {
        echo '<h2>Billing Details</h2>';
      }
      echo '<div class="ocwma_table_bill row">';

      if (!empty($user)) {

        foreach ($user as $row) {
          $userdata_bil = $row->userdata;
          $defalt_addd = $row->Defalut;
          $user_data = unserialize($userdata_bil);
          $address_active_defalut = '';
          if ($defalt_addd == 1) {
            $checked = "checkeddd";
            $address_title_default = "*Default";
            $address_active_defalut = "defalut_address_div";
          } else {
            $checked = "";
            $address_title_default = "Make Default";
          }
          $user_data = unserialize($userdata_bil);
          echo '<div class="col-md-6 '.$address_active_defalut.'">';
          echo '<div class="ocwma_bill_table ' . $row->id . '" data-billid="' . $row->id . '">';
          echo '<h4>' . $user_data['reference_field'] . '</h4>';

          /*if ($defalt_addd == 1) {
            echo '<div class="address-action-btn-defalut"><button class="defalut_address ' . $checked . '"  data-value="' . $defalt_addd . '" data-add_id="' . $row->id . '"  data-type="billing">'.$address_title_default.'</button></div>';
          }*/

          echo '<table class="">';
          // echo '<tr>';
          // echo '<td >' .$user_data['reference_field'] .'</td>';
          // echo '</tr>';
          echo '<tr>';
          echo '<td >' . $user_data['billing_first_name'] . '&nbsp' . $user_data['billing_last_name'] . '</td>';
          echo '</tr>';
          echo '<td>' . $user_data['billing_company'] . '</td>';
          echo '</tr>';
          echo '<tr>';
          echo '<td>' . $user_data['billing_address_1'] . '</td>';
          echo '</tr>';
          //echo '<tr>';
          //echo '<td>' .$user_data['billing_address_2'] .'</td>';
          // echo '</tr>';
          echo '<tr>';
          echo '<td>' . $user_data['billing_city'] . '</td>';
          echo '</tr>';
          // echo '<tr>';
          //echo '<td>' .$user_data['billing_country'] .'</td>';
          //  echo '</tr>';
          echo '<tr>';
          echo '<td>' . $user_data['billing_postcode'] . ', ' . $user_data['billing_state'] . '</td>';
          echo '</tr>';
          // echo '<tr>';
          // echo '<td>' .$user_data['billing_state'] .'</td>';
          // echo '</tr>';
          echo '</table>';
        //  if ($defalt_addd == 1) {
          //  echo '<div class="address-action-btn"><button class="form_option_edit" data-id="' . $user_id . '"  data-eid-bil="' . $row->id . '"><i class="fas fa-pen" aria-hidden="true"></i></button></td><td><a href="?action=delete_ocma&did=' . $row->id . '"><i class="fa fa-trash" aria-hidden="true"></i></a></div>';
         // } else {
            echo '<div class="address-action-btn"><button class="defalut_address ' . $checked . '"  data-value="' . $defalt_addd . '" data-add_id="' . $row->id . '"  data-type="billing">'.$address_title_default.'</button><button class="form_option_edit" data-id="' . $user_id . '"  data-eid-bil="' . $row->id . '"><i class="fas fa-pen" aria-hidden="true"></i></button></td><td><a href="?action=delete_ocma&did=' . $row->id . '"><i class="fa fa-trash" aria-hidden="true"></i></a></div>';
          //}
         
          echo '</div>';
          echo '</div>';
        }
      }
   
      echo '</div>';
      ?>
      <div class="billling-button">
        <button class="form_option_billing btn primary-btn" data-id="<?php echo $user_id; ?>"><?php echo get_option('ocwma_head_title', 'Add Billing Address'); ?></button>
      </div>
      <?php
      echo '</div>';

      echo '<div class="ocwma_table_custom col-md-12">';
      $user_shipping = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='shipping' AND userid=" . $user_id);

      $shipuserarray1 = array();
      $shipuserarray2 = array();
      foreach ( $user_shipping as $method ){
        if($method->Defalut){
          $shipuserarray1[] = $method;
        }
      }
      foreach ( $user_shipping as $method ){
        if(!$method->Defalut){
          $shipuserarray2[] = $method;
        }
      }
      
      $user_shipping = array_merge($shipuserarray1, $shipuserarray2);

      if (!is_checkout()) {
        echo '<h2>Shipping Details</h2>';
      }
      echo '<div class="ocwma_table_ship row">';

      if (!empty($user_shipping)) {

        foreach ($user_shipping as $row) {
          $userdata_ship = $row->userdata;
          $defalt_addd = $row->Defalut;
          $address_active_defalut = '';
          $user_data = unserialize($userdata_bil);
          if ($defalt_addd == 1) {
            $checked = "checkeddd";
            $address_title_default = "*Default";
            $address_active_defalut = "defalut_address_div";
          } else {
            $checked = "";
            $address_title_default = "Make Default";
          }
          $user_data = unserialize($userdata_ship);
          echo '<div class="col-md-6 '.$address_active_defalut.'">';
          echo '<div class="ocwma_ship_table ' . $row->id . '" data-shipid="' . $row->id . '">';
          echo '<h4>' . $user_data['reference_field'] . '</h4>';
         /* if ($defalt_addd == 1) {
            echo '<div class="address-action-btn-default"><button class="defalt_addd_shipping ' . $checked . '"  data-value="' . $defalt_addd . '" data-add_id="' . $row->id . '"  data-type="shipping"  >'.$address_title_default.'</button> </div>';
          }*/
          echo '<table class="">'; 
          echo '<tr>';
          echo '<td >' . $user_data['shipping_first_name'] . '&nbsp' . $user_data['shipping_last_name'] . '</td>';
          echo '</tr>';
          echo '<td>' . $user_data['shipping_company'] . '</td>';
          echo '</tr>';
          echo '<tr>';
          echo '<td>' . $user_data['shipping_address_1'] . '</td>';
          echo '</tr>';
          //echo '<tr>';
          //echo '<td>' .$user_data['shipping_address_2'] .'</td>';
          // echo '</tr>';
          echo '<tr>';
          echo '<td>' . $user_data['shipping_city'] . '</td>';
          echo '</tr>';
          // echo '<tr>';
          // echo '<td>' .$user_data['shipping_country'] .'</td>';
          // echo '</tr>';
          echo '<tr>';
          echo '<td>' . $user_data['shipping_postcode'] . ', ' . $user_data['shipping_state'] . '</td>';
          echo '</tr>';
          // echo '<tr>';
          // echo '<td>' .$user_data['shipping_state'] .'</td>';
          // echo '</tr>';
          echo '</table>';
        //  if ($defalt_addd == 1) {
         //   echo '<div class="address-action-btn"><button class="form_option_ship_edit" data-id="' . $user_id . '"  data-eid-ship="' . $row->id . '"><i class="fas fa-pen" aria-hidden="true"></i></button></td><td><a href="?action=delete-ship&did-ship=' . $row->id . '"><i class="fa fa-trash" aria-hidden="true"></i></a></div>';
         // } else {
            echo '<div class="address-action-btn"><button class="defalt_addd_shipping ' . $checked . '"  data-value="' . $defalt_addd . '" data-add_id="' . $row->id . '"  data-type="shipping"  >'.$address_title_default.'</button> <button class="form_option_ship_edit" data-id="' . $user_id . '"  data-eid-ship="' . $row->id . '"><i class="fas fa-pen" aria-hidden="true"></i></button></td><td><a href="?action=delete-ship&did-ship=' . $row->id . '"><i class="fa fa-trash" aria-hidden="true"></i></a></div>';
          //}
          
          echo '</div>';
          echo '</div>';
        }
      }

      echo '</div>';
      ?>
      <div class="shipping-button">
        <button class="form_option_shipping btn primary-btn" data-id="<?php echo $user_id; ?>" ><?php echo get_option('ocwma_head_title_ship', 'Add Shipping Address'); ?></button>
      </div>
      <?php
      echo '</div>';

      ?>
      <?php
    }


    function ocwma_billing_popup_open()
    {

      $user_id = sanitize_text_field($_REQUEST['popup_id_pro']);
      $edit_id = sanitize_text_field($_REQUEST['eid-bil']);

      global $wpdb;
      $tablename = $wpdb->prefix . 'ocwma_billingadress';
      if (empty($edit_id)) {

        $user = $wpdb->get_results("SELECT count(*) as count FROM {$tablename} WHERE type='billing'  AND userid=" . $user_id);
        $save_adress = $user[0]->count;
        $max_count = get_option('ocwma_max_adress', '10');
        if ($save_adress >= $max_count) {
          echo '<div class="ocwma_modal-content">';
          echo '<span class="ocwma_close">&times;</span>';
          echo "<h3 class='ocwma_border'>you can add maximum  " . get_option('ocwma_max_adress', '10') . " addresses !</h3>";
          echo '</div>';
          echo '</div>';
        } else {
          echo '<div class="ocwma_modal-content step_form">';
          echo '<span class="ocwma_close">&times;</span>';

          $address_fields = wc()->countries->get_address_fields(get_user_meta(get_current_user_id(), 'billing_country', true));

          //echo '<pre>';
          //print_r($address_fields);

      ?>
          <form method="post" id="oc_add_billing_form">
            <div class="ocwma_woocommerce-address-fields">
              <div class="ocwma_woocommerce-address-fields_field-wrapper">
                <input type="hidden" name="type" value="billing">
                <p class="form-row form-row-wide" id="reference_field" data-priority="30">
                  <label for="reference_field" class="">
                    <b>Reference Name:</b>
                    <abbr class="required" title="required">*</abbr>
                  </label>
                  <span class="woocommerce-input-wrapper">
                    <input type="text" class="input-text form-control" name="reference_field" id="oc_refname">
                  </span>
                </p>
                <?php
                // echo '<pre>';
                // print_r($address_fields);
                // echo '</pre>';
                $address_fields['billing_city']['class'][0] = 'form-row-first';
                $address_fields['billing_state']['class'][0] = 'form-row-last';
                $address_fields['billing_postcode']['class'][0] = 'form-row-first';
                $address_fields['billing_phone']['class'][0] = 'form-row-last';

                foreach ($address_fields as $key => $field) {
                  /* if($field['autocomplete'] == 'address-line1'){
                                              $field['input_class'][] = 'form-control address_autofill';
                                            }*/
                  $field['input_class'][] = 'form-control';
                  // echo '<pre>';
                  // print_r($field);
                  // echo '</pre>';
                  woocommerce_form_field($key, $field, wc_get_post_data_by_key($key));
                }
                ?>
              </div>
              <p>
                <button type="submit" name="add_billing" id="oc_add_billing_form_submit" class="button btn primary-btn" value="ocwma_billpp_save_option"><?php esc_html_e('Save Address', 'fr-address-book-for-woocommerce') ?></button>
              </p>
            </div>
          </form>
        <?php
          echo '</div>';
          echo '</div>';
        }
      } else {
        // echo $edit_id;
        ob_start();
        ?>
        <div class="ocwma_modal-content step_form">
          <span class="ocwma_close">&times;</span>
          <?php
          $user = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='billing' AND userid=" . $user_id . " AND id=" . $edit_id);
          $user_data = unserialize($user[0]->userdata);
          $address_fields = wc()->countries->get_address_fields(get_user_meta(get_current_user_id(), 'billing_country', true));
          ?>
          <form method="post" id="oc_edit_billing_form">
            <div class="ocwma_woocommerce-address-fields">
              <div class="ocwma_woocommerce-address-fields_field-wrapper">
                <input type="hidden" name="userid" value="<?php echo $user_id ?>">
                <input type="hidden" name="edit_id" value="<?php echo  $edit_id ?>">
                <input type="hidden" name="type" value="billing">
                <p class="form-row form-row-wide" id="reference_field" data-priority="30">
                  <label for="reference_field" class="">
                    <b>Reference Name:</b>
                    <abbr class="required" title="required">*</abbr>
                  </label>
                  <span class="woocommerce-input-wrapper">
                    <input type="text" class="input-text form-control" id="oc_refname" name="reference_field" value="<?php echo $user_data['reference_field'] ?>">
                  </span>
                </p>
                <?php
 
                $address_fields['billing_city']['class'][0] = 'form-row-first';
                $address_fields['billing_state']['class'][0] = 'form-row-last';
                $address_fields['billing_postcode']['class'][0] = 'form-row-first';
                $address_fields['billing_phone']['class'][0] = 'form-row-last';
                //echo '<pre>';
               // print_r( $address_fields);
              // echo '</pre>';
                foreach ($address_fields as $key => $field) {
                  $field['input_class'][] = 'form-control';
                  woocommerce_form_field($key, $field, $user_data[$key]);
                }
                ?>
              </div>
              <p>
                <button type="submit" name="add_billing_edit" id="oc_edit_billing_form_submit" class="button btn primary-btn" value="ocwma_billpp_save_option"><?php esc_html_e('Update Address', 'fr-address-book-for-woocommerce') ?></button>
              </p>
            </div>
          </form>

        </div>
        </div>

        <?php
        $edit_html = ob_get_clean();

        $return_arr[] = array("html" => $edit_html);
        echo json_encode($return_arr);
      }
      die();
    }


    function ocwma_shipping_popup_open()
    {

      $user_id = sanitize_text_field($_REQUEST['popup_id_pro']);
      $edit_id = sanitize_text_field($_REQUEST['eid-ship']);
      //echo $edit_id;
      global $wpdb;
      $tablename = $wpdb->prefix . 'ocwma_billingadress';
      if (empty($edit_id)) {
        $user = $wpdb->get_results("SELECT count(*) as count FROM {$tablename} WHERE type='shipping'  AND userid=" . $user_id);
        $save_adress = $user[0]->count;
        $max_count = get_option('ocwma_max_adress', '10');
        if ($save_adress >= $max_count) {
          echo '<div class="ocwma_modal-content">';
          echo '<span class="ocwma_close">&times;</span>';
          echo "<h3 class='ocwma_border'>you can add maximum  " . get_option('ocwma_max_adress', '10') . " addresses ! !</h3>";
          echo '</div>';
          echo '</div>';
        } else {
          echo '<div class="ocwma_modal-content step_form">';
          echo '<span class="ocwma_close">&times;</span>';
          $countries = new WC_Countries();
          if (!isset($country)) {
            $country = $countries->get_base_country();
          }
          if (!isset($user_id)) {
            $user_id = get_current_user_id();
          }
          $address_fields = WC()->countries->get_address_fields($country, 'shipping_');
        ?>
          <form method="post" id="oc_add_shipping_form">
            <div class="ocwma_woocommerce-address-fields">
              <div class="ocwma_woocommerce-address-fields_field-wrapper">
                <input type="hidden" name="type" value="shipping">
                <p class="form-row form-row-wide" id="reference_field" data-priority="30">
                  <label for="reference_field" class="">
                    <b>Reference Name:</b>
                    <abbr class="required" title="required">*</abbr>
                  </label>
                  <span class="woocommerce-input-wrapper">
                    <input type="text" class="input-text form-control" id="oc_refname" name="reference_field">
                  </span>
                </p>
                <?php
                $address_fields['shipping_city']['class'][0] = 'form-row-first';
                $address_fields['shipping_state']['class'][0] = 'form-row-last';


                foreach ($address_fields as $key => $field) {
                  $field['input_class'][] = 'form-control';
                  woocommerce_form_field($key, $field, wc_get_post_data_by_key($key));
                }
                ?>
              </div>
              <p>
                <button type="submit" name="add_shipping" id="oc_add_shipping_form_submit" class="button btn primary-btn" value="ocwma_shippp_save_optionn"><?php esc_html_e('Save Address', 'address-book-for-woocommerce') ?></button>
              </p>
            </div>
          </form>
        <?php
          echo '</div>';
          echo '</div>';
        }
      } else {
        echo '<div class="ocwma_modal-content step_form">';
        echo '<span class="ocwma_close">&times;</span>';
        $user = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='shipping' AND userid=" . $user_id . " AND id=" . $edit_id);
        $user_data = unserialize($user[0]->userdata);
        $countries = new WC_Countries();
        if (!isset($country)) {
          $country = $countries->get_base_country();
        }
        if (!isset($user_id)) {
          $user_id = get_current_user_id();
        }
        $address_fields = WC()->countries->get_address_fields($country, 'shipping_');
        ?>
        <form method="post" id="oc_edit_shipping_form">
          <div class="ocwma_woocommerce-address-fields">
            <div class="ocwma_woocommerce-address-fields_field-wrapper">
              <input type="hidden" name="type" value="shipping">
              <input type="hidden" name="userid" value="<?php echo $user_id ?>">
              <input type="hidden" name="edit_id" value="<?php echo $edit_id ?>">
              <p class="form-row form-row-wide" id="reference_field" data-priority="30">
                <label for="reference_field" class="">
                  <b>Reference Name:</b>
                  <abbr class="required" title="required">*</abbr>
                </label>
                <span class="woocommerce-input-wrapper">
                  <input type="text" class="input-text form-control" id="oc_refname" name="reference_field" value="<?php echo $user_data['reference_field'] ?>">
                </span>
              </p>
              <?php
              $address_fields['shipping_city']['class'][0] = 'form-row-first';
              $address_fields['shipping_state']['class'][0] = 'form-row-last';


              foreach ($address_fields as $key => $field) {
                $field['input_class'][] = 'form-control';
                woocommerce_form_field($key, $field, $user_data[$key]);
              }
              ?>
            </div>
            <p>
              <button type="submit" name="add_shipping_edit" class="button btn primary-btn" id="oc_edit_shipping_form_submit" value="ocwma_shippp_save_optionn"><?php esc_html_e('Update Address', 'address-book-for-woocommerce') ?></button>
            </p>
          </div>
        </form>
      <?php
        echo '</div>';
        echo '</div>';
      }
      die();
    }
    /* billigdata */

    function ocwma_billing_data_select()
    {
      $user_id = get_current_user_id();
      $select_id = sanitize_text_field($_REQUEST['sid']);
      WC()->session->set('default_billing_address', $select_id);
      global $wpdb;
      $tablename = $wpdb->prefix . 'ocwma_billingadress';
      $user = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='billing' AND userid=" . $user_id . " AND id=" . $select_id);
      $user_data = unserialize($user[0]->userdata);
      echo json_encode($user_data);
      exit();
    }
    /* shipping */

    function ocwma_shipping_data_select()
    {
      $user_id = get_current_user_id();
      $select_id = sanitize_text_field($_REQUEST['sid']);
      WC()->session->set('default_shipping_address', $select_id);
      global $wpdb;
      $tablename = $wpdb->prefix . 'ocwma_billingadress';
      $user = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='shipping' AND userid=" . $user_id . " AND id=" . $select_id);
      $user_data = unserialize($user[0]->userdata);
      echo json_encode($user_data);
      exit();
    }




    function OCWMA_all_billing_address()
    {
      $user_id  = get_current_user_id();
      global $wpdb;
      $tablename = $wpdb->prefix . 'ocwma_billingadress';
      $user = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='billing' AND userid=" . $user_id);
      ?>
      <?php if (!empty($user) && count($user) > 0) {  ?>
        <label>Choose Other Address</label>
        <select class="ocwma_select">
          <?php
          foreach ($user as $row) {
            $userdata_bil = $row->userdata;
            $user_data = unserialize($userdata_bil);

          ?> <option value="<?php echo $row->id ?>"> <?php echo $user_data['reference_field'] ?></option><?php }
                                                                                                          ?>
        </select>
      <?php } ?>
      <button class="form_option_billing" data-id="<?php echo $user_id; ?>" style="background-color: <?php echo get_option('ocwma_btn_bg_clr', '#000000') ?>; color: <?php echo get_option('ocwma_font_clr', '#ffffff') ?>; padding: <?php echo get_option('ocwma_btn_padding', '8px 10px') ?>; font-size: <?php echo get_option('ocwma_font_size', '15') . "px" ?>;"><?php echo get_option('ocwma_head_title', 'Add Billing Address'); ?></button>

    <?php
    }


    function   OCWMA_all_shipping_address()
    {
      $user_id  = get_current_user_id();
      global $wpdb;
      $tablename = $wpdb->prefix . 'ocwma_billingadress';
    ?>

      <?php
      $user = $wpdb->get_results("SELECT * FROM {$tablename} WHERE type='shipping' AND userid=" . $user_id);
      if (!empty($user) && count($user) > 0) {
      ?>
        <label>Choose Other Address</label>
        <select class="ocwma_select_shipping">
          <?php
          foreach ($user as $row) {
            $userdata_bil = $row->userdata;
            $user_data = unserialize($userdata_bil);

          ?> <option value="<?php echo $row->id ?>"> <?php echo $user_data['reference_field'] ?></option><?php }
                                                                                                          ?>
        </select>
      <?php } ?>
      <button class="form_option_shipping" data-id="<?php echo $user_id; ?>" style="background-color: <?php echo get_option('ocwma_btn_bg_clr', '#000000') ?>; color: <?php echo get_option('ocwma_font_clr', '#ffffff') ?>; padding: <?php echo get_option('ocwma_btn_padding', '8px 10px') ?>; font-size: <?php echo get_option('ocwma_font_size', '15') . "px" ?>;"><?php echo get_option('ocwma_head_title_ship', 'Add Shipping Address'); ?></button>

<?php
    }

    function OCWMA_save_options()
    {
      global $wpdb;
      $tablename = $wpdb->prefix . 'ocwma_billingadress';

      if (isset($_REQUEST['action']) && $_REQUEST['action'] == "delete_ocma") {
        $delete_id = sanitize_text_field($_REQUEST['did']);
        $session_bill_address_id = WC()->session->get('default_billing_address');
        if ($session_bill_address_id == $delete_id) {
          WC()->session->__unset('default_billing_address');
        }
        $sql = "DELETE  FROM {$tablename} WHERE id='" . $delete_id . "'";
        $wpdb->query($sql);
        wp_safe_redirect(wc_get_endpoint_url('edit-address', '', wc_get_page_permalink('myaccount')));
        exit;
      }

      if (isset($_REQUEST['action']) && $_REQUEST['action'] == "delete-ship") {
        $delete_id = sanitize_text_field($_REQUEST['did-ship']);
        $session_ship_address_id = WC()->session->get('default_shipping_address');
        if ($session_ship_address_id == $delete_id) {
          WC()->session->__unset('default_shipping_address');
        }
        $sql = "DELETE  FROM {$tablename} WHERE id='" . $delete_id . "'";

        $wpdb->query($sql);
        wp_safe_redirect(wc_get_endpoint_url('edit-address', '', wc_get_page_permalink('myaccount')));
        exit;
      }
    }


    function ocwma_validate_billing_form_fields_func()
    {
      global $wpdb;
      $tablename = $wpdb->prefix . 'ocwma_billingadress';

      $address_fields = wc()->countries->get_address_fields(get_user_meta(get_current_user_id(), 'billing_country', true));

      $ocwma_userid = get_current_user_id();

      $billing_data = array();
      $field_errors = array();

      $billing_data['reference_field'] = sanitize_text_field($_REQUEST['reference_field']);

      if ($_REQUEST['reference_field'] == '') {
        $field_errors['oc_refname'] = '1';
      }

      foreach ($address_fields as $key => $field) {
        $billing_data[$key] = sanitize_text_field($_REQUEST[$key]);

        if ($_REQUEST[$key] == '') {
          if ($field['required'] == 1) {
            $field_errors[$key] = '1';
          }
        }
      }

      unset($field_errors['billing_state']);

      if (empty($field_errors)) {
        $billing_data_serlized = serialize($billing_data);

          // custom activity log 
          if ( is_plugin_active( 'wp-security-audit-log-premium/wp-security-audit-log.php' ) ) {
            $user = wp_get_current_user();
            $event_id = 9907;
            if ( $event_id === 9907 ) {
                $wsal = WpSecurityAuditLog::GetInstance();
                $user_name_data = $user->first_name.' '.$user->last_name;
                $wsal->alerts->Trigger(
                  9907, array(
                        'TargetUsername' => $user ? $user_name_data : false,
                        'NewValue'       => sanitize_text_field( implode(", ",$billing_data) ),
                        'OldValue'       => sanitize_text_field( 'No Availabe' ),
                        'EditUserLink'   => add_query_arg( 'user_id', $ocwma_userid, admin_url( 'user-edit.php' ) ),
                        'Roles'          => is_array( $user->roles ) ? implode( ', ', $user->roles ) : $user->roles,
                    )
                );
            }
            }
            //custom end

        $wpdb->insert($tablename, array(
          'userid' => $ocwma_userid,
          'userdata' => $billing_data_serlized,
          'type' => sanitize_text_field($_REQUEST['type']),
        ));

        $added = 'true';
      } else {
        $added  = 'false';
      }

      $return_arr = array(
        "added" => $added,
        "field_errors" => $field_errors
      );

      echo json_encode($return_arr);
      exit;
    }

    function ocwma_validate_shipping_form_fields_func()
    {
      global $wpdb;
      $tablename = $wpdb->prefix . 'ocwma_billingadress';

      $countries = new WC_Countries();
      $country = $countries->get_base_country();

      $address_fields = WC()->countries->get_address_fields($country, 'shipping_');

      $ocwma_userid = get_current_user_id();

      $billing_data = array();
      $field_errors = array();

      $billing_data['reference_field'] = sanitize_text_field($_REQUEST['reference_field']);

      if ($_REQUEST['reference_field'] == '') {
        $field_errors['oc_refname'] = '1';
      }

      foreach ($address_fields as $key => $field) {
        $billing_data[$key] = sanitize_text_field($_REQUEST[$key]);

        if ($_REQUEST[$key] == '') {
          if ($field['required'] == 1) {
            $field_errors[$key] = '1';
          }
        }
      }

      unset($field_errors['shipping_state']);

      if (empty($field_errors)) {
        $billing_data_serlized = serialize($billing_data);

          //custom activity log hp
          if ( is_plugin_active( 'wp-security-audit-log-premium/wp-security-audit-log.php' ) ) {
            $user = wp_get_current_user();
            $event_id = 9908;
            if ( $event_id === 9908 ) {
                $wsal = WpSecurityAuditLog::GetInstance();
                $user_name_data = $user->first_name.' '.$user->last_name;  
                $wsal->alerts->Trigger(
                  9908, array(
                        'TargetUsername' => $user ? $user_name_data : false,
                        'NewValue'       => sanitize_text_field( implode(", ",$billing_data) ),
                        'OldValue'       => sanitize_text_field( 'No Availabe' ),
                        'EditUserLink'   => add_query_arg( 'user_id', $ocwma_userid, admin_url( 'user-edit.php' ) ),
                        'Roles'          => is_array( $user->roles ) ? implode( ', ', $user->roles ) : $user->roles,
                    )
                );
            }
          }
            //custom end

        $wpdb->insert($tablename, array(
          'userid' => $ocwma_userid,
          'userdata' => $billing_data_serlized,
          'type' => sanitize_text_field($_REQUEST['type']),
        ));

        $added = 'true';
      } else {
        $added  = 'false';
      }

      $return_arr = array(
        "added" => $added,
        "field_errors" => $field_errors
      );

      echo json_encode($return_arr);
      exit;
    }


    function ocwma_validate_edit_billing_form_fields_func()
    {
      global $wpdb;
      $tablename = $wpdb->prefix . 'ocwma_billingadress';

      $address_fields = wc()->countries->get_address_fields(get_user_meta(get_current_user_id(), 'billing_country', true));

      $edit_id = sanitize_text_field($_REQUEST['edit_id']);

      $ocwma_userid = get_current_user_id();

      $billing_data = array();
      $field_errors = array();

      $billing_data['reference_field'] = sanitize_text_field($_REQUEST['reference_field']);

      if ($_REQUEST['reference_field'] == '') {
        $field_errors['oc_refname'] = '1';
      }

      foreach ($address_fields as $key => $field) {
        $billing_data[$key] = sanitize_text_field($_REQUEST[$key]);

        if ($_REQUEST[$key] == '') {
          if ($field['required'] == 1) {
            $field_errors[$key] = '1';
          }
        }
      }

      unset($field_errors['billing_state']);

      if (empty($field_errors)) {
        $billing_data_serlized = serialize($billing_data);
          //custom hp
          if ( is_plugin_active( 'wp-security-audit-log-premium/wp-security-audit-log.php' ) ) {
            $user = wp_get_current_user();
            $event_id = 9907;
            if ( $event_id === 9907 ) {
                $wsal = WpSecurityAuditLog::GetInstance();
                $user_get_old = $wpdb->get_results( "SELECT * FROM {$tablename} WHERE type='billing' AND userid=".$ocwma_userid." AND id=".$edit_id);
                $user_data_old = unserialize($user_get_old[0]->userdata);
                $user_name_data = $user->first_name.' '.$user->last_name;
                $wsal->alerts->Trigger(
                  9907, array(
                        'TargetUsername' => $user ? $user_name_data  : false,
                        'NewValue'       => sanitize_text_field( implode(", ",$billing_data) ),
                        'OldValue'       => sanitize_text_field( implode(", ",$user_data_old) ),
                        'EditUserLink'   => add_query_arg( 'user_id', $ocwma_userid, admin_url( 'user-edit.php' ) ),
                        'Roles'          => is_array( $user->roles ) ? implode( ', ', $user->roles ) : $user->roles,
                    )
                );
            }
          }
          //custom end

        $condition = array(
          'id' => $edit_id,
          'userid' => $ocwma_userid,
          'type' => sanitize_text_field($_REQUEST['type'])
        );

        $wpdb->update($tablename, array(
          'userdata' => $billing_data_serlized
        ), $condition);

        $added = 'true';
      } else {
        $added  = 'false';
      }

      $return_arr = array(
        "added" => $added,
        "field_errors" => $field_errors
      );

      echo json_encode($return_arr);
      exit;
    }


    function ocwma_validate_edit_shipping_form_fields_func()
    {
      global $wpdb;
      $tablename = $wpdb->prefix . 'ocwma_billingadress';

      $edit_id = sanitize_text_field($_REQUEST['edit_id']);

      $countries = new WC_Countries();
      $country = $countries->get_base_country();

      $address_fields = WC()->countries->get_address_fields($country, 'shipping_');

      $ocwma_userid = get_current_user_id();

      $billing_data = array();
      $field_errors = array();

      $billing_data['reference_field'] = sanitize_text_field($_REQUEST['reference_field']);

      if ($_REQUEST['reference_field'] == '') {
        $field_errors['oc_refname'] = '1';
      }

      foreach ($address_fields as $key => $field) {
        $billing_data[$key] = sanitize_text_field($_REQUEST[$key]);

        if ($_REQUEST[$key] == '') {
          if ($field['required'] == 1) {
            $field_errors[$key] = '1';
          }
        }
      }

      unset($field_errors['shipping_state']);

      if (empty($field_errors)) {
        $billing_data_serlized = serialize($billing_data);

          //custom activity log hp
          if ( is_plugin_active( 'wp-security-audit-log-premium/wp-security-audit-log.php' ) ) {
            $user = wp_get_current_user();
            $event_id = 9908;
            if ( $event_id === 9908 ) {
                $wsal = WpSecurityAuditLog::GetInstance();

                $user_get_old = $wpdb->get_results( "SELECT * FROM {$tablename} WHERE type='shipping' AND userid=".$ocwma_userid." AND id=".$edit_id);
                $user_data_old = unserialize($user_get_old[0]->userdata);
                $user_name_data = $user->first_name.' '.$user->last_name;
                $wsal->alerts->Trigger(
                  9908, array(
                        'TargetUsername' => $user ? $user_name_data : false,
                        'NewValue'       => sanitize_text_field( implode(", ",$billing_data) ),
                        'OldValue'       => sanitize_text_field( implode(", ",$user_data_old) ),
                        'EditUserLink'   => add_query_arg( 'user_id', $ocwma_userid, admin_url( 'user-edit.php' ) ),
                        'Roles'          => is_array( $user->roles ) ? implode( ', ', $user->roles ) : $user->roles,
                    )
                );
            }
          }
          //custom end

        $condition = array(
          'id' => $edit_id,
          'userid' => $ocwma_userid,
          'type' => sanitize_text_field($_REQUEST['type'])
        );
        $wpdb->update($tablename, array(
          'userdata' => $billing_data_serlized
        ), $condition);

        $added = 'true';
      } else {
        $added  = 'false';
      }

      $return_arr = array(
        "added" => $added,
        "field_errors" => $field_errors
      );

      echo json_encode($return_arr);
      exit;
    }
    function ocwma_default_address()
    {

      global $wpdb;

      $tablename = $wpdb->prefix . 'ocwma_billingadress';

      $defaltadd_id = ($_REQUEST['defalteaddd_id']);
      $dealteadd_type = $_REQUEST['dealteadd_type'];
      $ocwma_userid = get_current_user_id();


      $condition = array(
        'userid' => $ocwma_userid,
        'type' => $dealteadd_type,
      );
      $wpdb->update(
        $tablename,
        array(
          'Defalut' => '0',

        ),
        $condition
      );

      $condition = array(
        'id' => $defaltadd_id,
        'type' => $dealteadd_type,
      );

      $wpdb->update(
        $tablename,
        array(
          'Defalut' => '1',
        ),
        $condition
      );

      exit;
    }

    function ocwma_default_address_shipping()
    {

      global $wpdb;

      $tablename = $wpdb->prefix . 'ocwma_billingadress';

      $defaltadd_id = ($_REQUEST['defalteaddd_id']);
      $dealteadd_type = $_REQUEST['dealteadd_type'];
      $ocwma_userid = get_current_user_id();


      $condition = array(
        'userid' => $ocwma_userid,
        'type' => $dealteadd_type,
      );
      $wpdb->update(
        $tablename,
        array(
          'Defalut' => '0',

        ),
        $condition
      );

      $condition = array(
        'id' => $defaltadd_id,
        'type' => $dealteadd_type,
      );

      $wpdb->update(
        $tablename,
        array(
          'Defalut' => '1',
        ),
        $condition
      );

      exit;
    }


    function init()
    {
      global $wpdb;
      $charset_collate = $wpdb->get_charset_collate();
      $tablename = $wpdb->prefix . 'ocwma_billingadress';
      $sql = "CREATE TABLE $tablename (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                userid TEXT NOT NULL,
                userdata TEXT NOT NULL,
                type TEXT NOT NULL,
                Defalut int  DEFAULT '0',
                PRIMARY KEY (id)
            ) $charset_collate;";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

      add_filter('woocommerce_account_menu_items', array($this, 'ocwma_wc_address_book_add_to_menu'), 10);
      add_action('woocommerce_account_edit-address_endpoint', array($this, 'ocwma_my_account_endpoint_content'));

      //custom
      add_action('woocommerce_before_checkout_billing_form', array($this, 'ocwma_my_account_endpoint_content_billing'));
      add_action('woocommerce_before_checkout_shipping_form', array($this, 'ocwma_my_account_endpoint_content_shipping'));
      //custom end

      add_action('wp_footer', array($this, 'ocwma_popup_div_footer'));
      add_action('wp_ajax_productscommentsbilling', array($this, 'ocwma_billing_popup_open'));
      add_action('wp_ajax_nopriv_productscommentsbilling', array($this, 'ocwma_billing_popup_open'));
      add_action('wp_ajax_productscommentsshipping', array($this, 'ocwma_shipping_popup_open'));
      add_action('wp_ajax_nopriv_productscommentsshipping', array($this, 'ocwma_shipping_popup_open'));
      add_action('woocommerce_before_checkout_billing_form', array($this, 'OCWMA_all_billing_address'));
      add_action('woocommerce_before_checkout_shipping_form', array($this, 'OCWMA_all_shipping_address'));
      add_action('wp_ajax_productscommentsbilling_select', array($this, 'ocwma_billing_data_select'));
      add_action('wp_ajax_nopriv_productscommentsbilling_select', array($this, 'ocwma_billing_data_select'));
      add_action('wp_ajax_productscommentsshipping_select', array($this, 'ocwma_shipping_data_select'));
      add_action('wp_ajax_nopriv_productscommentsshipping_select', array($this, 'ocwma_shipping_data_select'));
      add_action('wp_ajax_ocwma_validate_billing_form_fields', array($this, 'ocwma_validate_billing_form_fields_func'));
      add_action('wp_ajax_nopriv_ocwma_validate_billing_form_fields', array($this, 'ocwma_validate_billing_form_fields_func'));
      add_action('wp_ajax_ocwma_validate_shipping_form_fields', array($this, 'ocwma_validate_shipping_form_fields_func'));
      add_action('wp_ajax_nopriv_ocwma_validate_shipping_form_fields', array($this, 'ocwma_validate_shipping_form_fields_func'));
      add_action('wp_ajax_ocwma_validate_edit_billing_form_fields', array($this, 'ocwma_validate_edit_billing_form_fields_func'));
      add_action('wp_ajax_nopriv_ocwma_validate_edit_billing_form_fields', array($this, 'ocwma_validate_edit_billing_form_fields_func'));
      add_action('wp_ajax_ocwma_validate_edit_shipping_form_fields', array($this, 'ocwma_validate_edit_shipping_form_fields_func'));
      add_action('wp_ajax_nopriv_ocwma_validate_edit_shipping_form_fields', array($this, 'ocwma_validate_edit_shipping_form_fields_func'));
      add_action('wp_ajax_ocwma_default_address', array($this, 'ocwma_default_address'));
      add_action('wp_ajax_nopriv_ocwma_default_address', array($this, 'ocwma_default_address'));
      add_action('wp_ajax_ocwma_default_address_shipping', array($this, 'ocwma_default_address_shipping'));
      add_action('wp_ajax_nopriv_ocwma_default_address_shipping', array($this, 'ocwma_default_address_shipping'));
      add_action('init',  array($this, 'OCWMA_save_options'));
    }


    public static function instance()
    {
      if (!isset(self::$instance)) {
        self::$instance = new self();
        self::$instance->init();
      }
      return self::$instance;
    }
  }

  OCWMA_front::instance();
}
