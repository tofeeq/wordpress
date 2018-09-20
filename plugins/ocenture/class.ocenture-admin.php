<?php

use Ocenture\View as View;

class OcentureAdmin{
	
	protected $_initiated = false;
  	
  	//live client id
	public $clientId = '1052714';

	public function __construct() {
    	add_filter( 'post_updated_messages' , [$this, 'post_messages']  );
    	add_filter( 'post_row_actions', [	
    		$this, 'update_row_actions'
	    ], 10, 2 );

	   

	    //adding columns headings to listing for the status 
	    //manage_${post_type}_posts_columns
	    add_filter('manage_ocenture_posts_columns' , [$this, 'add_listing_columns']);



	    //making column sortable
	    //'manage_edit-{$post_type}_sortable_columns'
	    add_filter( 'manage_edit-ocenture_sortable_columns', [$this, 'sort_listing_columns'] );
	}

	public function init() {
		if ( ! $this->_initiated ) {
			$this->_init_hooks();
		}
	}

	/** corresponds to admin_init hook for wp **/
	public function admin_init() {
	}

	protected function _init_hooks() {
		$this->_initiated = true;
		$setting = get_option('freedom-settings');

		if ($setting['mode'] == 'sandbox') {
	        $this->clientId = '1052700';
	    } 

	    $this->registerPostType();

	   	//displaying actual columns in listings based on headings above
	    //manage_{$post_type}_posts_custom_column
	    add_action( 'manage_ocenture_posts_custom_column' , [$this, 'render_listing_columns'], 10, 2 );

	    //if want to just perform an admin action but not display page
	    //add_action('admin_action_view_ocenture_user', [$this, 'view_ocenture_user']);

	    //add a view page
	    add_action( 'admin_menu', [$this, 'admin_memus'] );

	    //saving the user data when admin edits
	    add_action( 'save_post', [$this, 'save_meta_box'] );

	    //before getting the data for listing sort it 
	    add_action( 'pre_get_posts', [$this, 'get_sorted_data_for_listing'] );
	}

	public function admin_memus() {
		//create a page to link with view link added 
		add_submenu_page( 
	        null,
	        'View Ocenture User',
	        'View Ocenture User',
	        'manage_options',
	        'view_ocenture_user',
	        [$this, 'view_ocenture_user']
	    );

		//create a page to link with view link added 
	    add_submenu_page( 
	        null,
	        'View Ocenture Log',
	        'View Ocenture Log',
	        'manage_options',
	        'view_ocenture_log',
	        [$this, 'view_ocenture_log']
	    );

	}
	/////////////// wordpress plugin admin configuration ////////////
	public function registerPostType() {
		//unregister_post_type( 'ocenture' );

		//labels of post type
    	$labels = array(
	      'name'     => _x( 'Ocenture', 'post type general name', 'ocenture' ),
	      'singular_name'     => _x( 'Ocenture', 'post type singular name', 'ocenture' ),
	      'menu_name'          => _x( 'Ocenture', 'admin menu', 'ocenture' ),
	      'name_admin_bar'     => _x( 'Ocenture', 'add new on admin bar', 'ocenture' ),
	      'add_new'            => _x( 'Add New', 'User', 'ocenture' ),
	      'add_new_item'       => __( 'Add New User', 'ocenture' ),
	      'new_item'           => __( 'New User', 'ocenture' ),
	      'edit_item'          => __( 'Edit User', 'ocenture' ),
	      'view_item'          => __( 'View User', 'ocenture' ),
	      'all_items'          => __( 'All Users', 'ocenture' ),
	      'search_items'       => __( 'Search Users', 'ocenture' ),
	      'not_found'          => __( 'No User found.', 'ocenture' ),
	      'not_found_in_trash' => __( 'No User found in Trash.', 'ocenture' )
	    );


	    //register / update post type
	    register_post_type( 'ocenture',
	      array(
	        'labels' =>  $labels,
	        'public' => false,
	        'publicly_queryable' => false,
	        'has_archive' => true,
	        'show_ui'            => true,
	        'show_in_menu'       => true,
	        'query_var'          => true,
	        'rewrite'            => [ 'slug' => 'ocenture' ],
	        'capability_type'    => 'post',
	        'has_archive'        => true,
	        'hierarchical'       => false,
	        'menu_position'      => null,
	        'supports'           => [ 'title' ],
	        'register_meta_box_cb' => [$this, 'metaboxes'],
	      )
	    );


	    /*
	    register post type for ocenture logs
	    */

	    //labels of post type
    	$labels = array(
	      'name'     		   => _x( 'Ocenture Log', 'post type general name', 'ocenture_log' ),
	      'singular_name'      => _x( 'Ocenture Log', 'post type singular name', 'ocenture_log' ),
	      'menu_name'          => _x( 'Ocenture Log', 'admin menu', 'ocenture_log' ),
	      'name_admin_bar'     => _x( 'Ocenture Log', 'add new on admin bar', 'ocenture_log' ),
	      //'add_new'            => _x( 'Add New', 'User', 'ocenture_log' ),
	      //'add_new_item'       => __( 'Add New User', 'ocenture_log' ),
	      //'new_item'           => __( 'New User', 'ocenture_log' ),
	      //'edit_item'          => __( 'Edit User', 'ocenture_log' ),
	      'view_item'          => __( 'View Log', 'ocenture_log' ),
	      'all_items'          => __( 'All Logs', 'ocenture_log' ),
	      'search_items'       => __( 'Search Logs', 'ocenture_log' ),
	      'not_found'          => __( 'No Log found.', 'ocenture_log' ),
	      'not_found_in_trash' => __( 'No Log found in Trash.', 'ocenture_log' )
	    );


	    //register / update post type
	    register_post_type( 'ocenture_log',
	      array(
	        'labels' 				=>  $labels,
	        'public' 				=> false,
	        'publicly_queryable' 	=> false,
	        'has_archive' 			=> true,
	        'show_ui'            	=> true,
	        'show_in_menu'       	=> 'edit.php?post_type=ocenture',
	        'query_var'          	=> true,
	        'rewrite'            	=> [ 'slug' => 'ocenture/log' ],
	        'map_meta_cap'			=> true,
	        'capability_type'    	=> 'post',
		    'hierarchical'       	=> false,
	        'menu_position'      	=> null,
	        'supports'           	=> [ 'title', 'editor' ],
	      )
	    );

  	}

  	//add a view link 
  	public function update_row_actions( $actions, $post ) {
  		if ( get_post_type() == 'ocenture' ) {

	  		//add view link, if action needed just replace page with action
	  		$action = '?page=view_ocenture_user&amp;post='.$post->ID;
	  		$url = wp_nonce_url(admin_url( "admin.php". $action ));
	  		$link = '<a href="'. $url .'" title="'
					. esc_attr__('View User', 'ocenture')
					. '">' .  esc_html__('View', 'ocenture') . '</a>';
	  		$actions['view'] = $link;
	  	} else if ( get_post_type() == 'ocenture_log' ) {
	  		$action = '?page=view_ocenture_log&amp;post='.$post->ID;
	  		$url = wp_nonce_url(admin_url( "admin.php". $action ));
	  		$link = '<a href="'. $url .'" title="'
					. esc_attr__('View Log', 'ocenture_log')
					. '">' .  esc_html__('View', 'ocenture_log') . '</a>';
	  		$actions['view'] = $link;
	  	}
	  	return $actions;
  	}


  	//view screen for the user
  	public function view_ocenture_user ( ) {
  		$post = get_post($_GET['post']);
  		$meta = get_post_meta( $post->ID);

  		$args = [
  			'title' => $post->post_title,
  			'membership_id' => $meta['_membership_id'][0],
  			'rep_id' => $meta['_rep_id'][0],
  			'product_code' => $meta['_product_code'][0],
  			'status' => $meta['_status'][0],
  			'client_member_id' => $meta['_client_member_id'][0],
  			'fname' => $meta['_fname'][0],
  			'lname' => $meta['_lname'][0],
  			'email' => $meta['_email'][0],
  			'phone' => $meta['_phone'][0],
  			'address' => $meta['_address'][0],
  			'city' => $meta['_city'][0],
  			'state' => $meta['_state'][0],
  			'zip' => $meta['_zip'][0],
  			'date' => $meta['_date'][0],
  		];


  		View::render( 'admin/view', $args );
  	}


  	//view screen for the user
  	public function view_ocenture_log ( ) {
  		$post = get_post($_GET['post']);
  		$meta = get_post_meta( $post->ID);

  		$args = [
  			'title' => $post->post_title,
  			'content'	=> $post->post_content
  		];

  		View::render( 'admin/log', $args );
  	}

	//create meta boxes for the user fields
	public function metaboxes( $post ) {
		//for future reference
		add_meta_box('ocenture_meta', 'Subscription', [$this, 'render_ocenture_meta'], 'ocenture', 'normal', 'default');

		add_meta_box('ocenture_user_meta', 'User Info', [$this, 'render_ocenture_user_meta'], 'ocenture', 'normal', 'default');
	}

  	public function render_ocenture_meta( $post ) {
     
	    $status = esc_attr (
	            get_post_meta( $post->ID, '_status', true )
	        );

	    $id = esc_attr (
	            get_post_meta( $post->ID, '_membership_id', true )
	        );
	    
	    $rep_id = esc_attr (
	            get_post_meta( $post->ID, '_rep_id', true )
	        );
	    $product = esc_attr (
	            get_post_meta( $post->ID, '_product_code', true )
	        );
    
	    ?> 
	      <style>
	        .ocenturemeta label {
	          display: block;
	        }
	      </style>
	      <div class="ocenturemeta">
	        <p>
	          <label for="ocenture_membership_id">
	              <?php _e( 'Membership ID', 'ocenture' ); ?>
	          </label>
	          <input type="text" id="ocenture_membership_id" name="ocenture_membership_id" value="<?php echo $id; ?>" readonly /> 
	        </p>
	        <p>
	          <label for="ocenture_product_code">
	              <?php _e( 'Product Code', 'ocenture' ); ?>
	          </label>
	          <input type="text" id="ocenture_product_code" name="ocenture_product_code" value="<?php echo $product; ?>" readonly /> 
	        </p>
	        <p>
	          <label for="ocenture_rep_id">
	              <?php _e( 'Rep ID', 'ocenture' ); ?>
	          </label>
	          <input type="text" id="ocenture_rep_id" name="ocenture_rep_id" value="<?php echo $rep_id; ?>" readonly /> 
	        </p>
	        <p>
	          <label for="ocenture_status">
	              <?php _e( 'Status', 'ocenture' ); ?>
	          </label>
	          <select name="ocenture_status" id="ocenture_status">
	            <option value="1" <?php echo $status == 1 ? 'selected="selected"' : ''?>>Active</option>
	            <option value="0" <?php echo $status == 0 ? 'selected="selected"' : ''?>>Cancelled</option>
	          </select>
	        </p>
	      </div>
	    <?php
 	}

	public function render_ocenture_user_meta( $post ) {
		$fname = esc_attr (
	            get_post_meta( $post->ID, '_fname', true )
	        );
	    $lname = esc_attr (
	            get_post_meta( $post->ID, '_lname', true )
	        );
	    $email = esc_attr (
	            get_post_meta( $post->ID, '_email', true )
	        );
    	$phone = esc_attr (
	            get_post_meta( $post->ID, '_phone', true )
	        );
    	$address = esc_attr (
	            get_post_meta( $post->ID, '_address', true )
	        );
    	$city = esc_attr (
	            get_post_meta( $post->ID, '_city', true )
	        );
    	$state = esc_attr (
	            get_post_meta( $post->ID, '_state', true )
	        );
    	$zip = esc_attr (
	            get_post_meta( $post->ID, '_zip', true )
	        );
    	
	    ?> 
	      <style>
	      	.ocenturemeta {
	      		width: 100%;
	      	}
	      	.ocenturemeta p {
	      		display: inline-block;
	      		width: 48%;
	      	}
	        .ocenturemeta label {
	          display: block;
	        }
	      </style>
	      <div class="ocenturemeta">
	        <p>
	          <label for="ocenture_fname">
	              <?php _e( 'First Name', 'ocenture' ); ?>
	          </label>
	          <input type="text" id="ocenture_fname" name="ocenture_fname" value="<?php echo $fname; ?>" /> 
	        </p>
	        <p>
	          <label for="ocenture_lname">
	              <?php _e( 'Last Name', 'ocenture' ); ?>
	          </label>
	          <input type="text" id="ocenture_lname" name="ocenture_lname" value="<?php echo $lname; ?>" /> 
	        </p>
	        <p>
	          <label for="ocenture_email">
	              <?php _e( 'Email', 'ocenture' ); ?>
	          </label>
	          <input type="text" id="ocenture_email" name="ocenture_email" value="<?php echo $email; ?>" /> 
	        </p>
	        <p>
	          <label for="ocenture_phone">
	              <?php _e( 'Phone', 'ocenture' ); ?>
	          </label>
	          <input type="text" id="ocenture_phone" name="ocenture_phone" value="<?php echo $phone; ?>" /> 
	        </p>
	        <p>
	          <label for="ocenture_address">
	              <?php _e( 'Address', 'ocenture' ); ?>
	          </label>
	          <input type="text" id="ocenture_address" name="ocenture_address" value="<?php echo $address; ?>" /> 
	        </p>
	        <p>
	          <label for="ocenture_city">
	              <?php _e( 'City', 'ocenture' ); ?>
	          </label>
	          <input type="text" id="ocenture_city" name="ocenture_city" value="<?php echo $city; ?>" /> 
	        </p>
	        <p>
	          <label for="ocenture_state">
	              <?php _e( 'State', 'ocenture' ); ?>
	          </label>
	          <input type="text" id="ocenture_state" name="ocenture_state" value="<?php echo $state; ?>" /> 
	        </p>
	        <p>
	          <label for="ocenture_zip">
	              <?php _e( 'Zip', 'ocenture' ); ?>
	          </label>
	          <input type="text" id="ocenture_zip" name="ocenture_zip" value="<?php echo $zip; ?>" /> 
	        </p>
	         
	      </div>
	    <?php
	}

	//add status column heading to listing
	public function add_listing_columns( $columns ) {
		$new_cols = [];
		foreach ($columns as $key => $heading ) {
			if ($key == 'date') {
				$new_cols['rep_id'] = __('Rep ID');
				$new_cols['status'] = __('Status');
				$new_cols['order_date'] = __('Order Date');
				$new_cols['date'] = __('Published');
			} else {
				$new_cols[$key] = $heading;
			}
		}
		return $new_cols;
	}

	//render column values in listing
	public function render_listing_columns(  $column, $post_id ) {
		 switch ( $column ) {
			case 'status':
				$status = get_post_meta( $post_id, '_status', true ); 
				echo '<strong>';
				echo $status == 1 ? 'Active' : 'Cancelled';
				echo '</strong>';
			break;
			case 'order_date':
				$date = get_post_meta( $post_id, '_date', true ); 
				echo '<strong>';
				echo date("Y-m-d H:i:s", $date);
				echo '</strong>';
			break;
		}
	}

	//sorting listing columns
	public function sort_listing_columns ( $columns ) {
		$columns['rep_id'] = 'rep_id';
		$columns['status'] = 'status';
		$columns['order_date'] = 'order_date';
	    return $columns;
	}

	//sort the data before it appears in listing
	public function get_sorted_data_for_listing ( $query ) {
		if ($query->get('post_type') != 'ocenture')
			return $query;

	    $orderby = $query->get( 'orderby' );
	 	
	 	switch( $orderby ) {
	 		case 'status':
	 			//meta key is _status
	            $query->set( 'meta_key', '_status' );
	            //sort by meta value
	            $query->set( 'orderby', 'meta_value' );
	        break;
			case 'rep_id':
	 			//meta key is _status
	            $query->set( 'meta_key', '_rep_id' );
	            //sort by meta value
	            $query->set( 'orderby', 'meta_value' );
	        break;

	        case 'order_date':
	 			//meta key is _status
	            $query->set( 'meta_key', '_date' );
	            //sort by meta value
	            $query->set( 'orderby', 'meta_value' );
	        break;
	    }
	}
	//save the user data 	
  	public function save_meta_box( $post_id ) {
    	$fields = [
    		'ocenture_membership_id', 
    		'ocenture_product_code',
    		'ocenture_status', 
    		'ocenture_fname',
    		'ocenture_lname',
    		'ocenture_email',
    		'ocenture_phone',
    		'ocenture_address',
    		'ocenture_city',
    		'ocenture_state',
    		'ocenture_zip',
    		'ocenture_rep_id'
    	];

	    foreach ($fields as $field) {
	      if (array_key_exists($field, $_POST)) {
	          $post_value = $_POST[$field];

	          $meta_field = str_replace("ocenture", "", $field);

	          if ($meta_field == '_status') {
	            $this->updateOcentureStatus($post_id, $post_value);
	          }

	          update_post_meta(
	              $post_id,
	              $meta_field,
	              $post_value
	          );
	      }
	    }
  	}

  	public function updateOcentureStatus($post_id, $status) {
	    $originalStatus = get_post_meta($post_id, "_status", 1);
	    if ($originalStatus == $status) {
	      //echo "nothing changed"; die;
	      update_option('ocenture_update_msg', '');
	      return ;
	    }

	    require_once( OCENTURE_PLUGIN_DIR . 'lib/Ocenture.php' );

	    $ocenture = new Ocenture_Client($this->clientId);

	    $membership_id = get_post_meta( $post_id, '_membership_id', 1);
	    $client_member_id = get_post_meta( $post_id, '_client_member_id', 1);

	    $params = [
	      'args' => [
	        "ClientMemberID"  => $client_member_id,
	        "MembershipID"  => $membership_id
	      ]
	    ];

    

	    if ($status == 1) {
	      //reactivate
	      $result = $ocenture->reactivateAccount($params);
	      update_option('ocenture_update_msg', $result->Status);
	    } else {
	      //cancel
	      $result = $ocenture->cancelAccount($params);
	      update_option('ocenture_update_msg', $result->Status);
	    }

	    Logger::log($params);
	    Logger::log($result);

  	} 


  /////////////// wordpress plugin messages ////////////
  	public function post_messages($messages) {
    
	    $post             = get_post();
	    $post_type        = get_post_type( $post );
	    $post_type_object = get_post_type_object( $post_type );
	    $ocenture_msg = get_option('ocenture_update_msg');
	    $msg = $ocenture_msg ? : 'User updated.';

	    $messages['ocenture'] = array(
	      0  => '', // Unused. Messages start at index 1.
	      1  => __( $msg, 'ocenture' ),
	      2  => __( $msg, 'ocenture' ),
	      3  => __( $msg, 'ocenture' ),
	      4  => __( $msg, 'ocenture' ),
	      /* translators: %s: date and time of the revision */
	      6  => __( $msg, 'ocenture' ),
	      7  => __( $msg, 'ocenture' ),
	      8  => __( $msg, 'ocenture' ),
	      9  => sprintf(
	        __( 'User scheduled for: <strong>%1$s</strong>.', 'ocenture' ),
	        // translators: Publish box date format, see http://php.net/date
	        date_i18n( __( 'M j, Y @ G:i', 'ocenture' ), strtotime( $post->post_date ) )
	      ),
	      10 => __( 'User draft updated.', 'ocenture' )
	    );

    	return $messages;
  	}
}