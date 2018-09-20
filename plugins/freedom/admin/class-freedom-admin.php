<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Freedom
 * @subpackage Freedom/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Freedom
 * @subpackage Freedom/admin
 * @author     Your Name <email@example.com>
 */
class Freedom_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */


	private $capability = "manage_options";
	private $post_type_order = "order";

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Freedom_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Freedom_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Freedom_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Freedom_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );

	}

	/** hooked by wp admin_init **/
	public function admin_init() {
		$this->register_setting();
	}

	/** hooked by wp init **/
	public function init() {
		//	$this->add_read_capabilities();
		$this->register_post_type();

		add_filter( 'post_row_actions', [	
    		$this, 'update_row_actions'
	    ], 10, 2 );

	}

	/*public function add_read_capabilities() {
		

	}*/

	public function register_post_type() {
		//labels of post type
    	$labels = array(
	      'name'     		   => _x( 'Freedom Log', 'post type general name', 'freedom_log' ),
	      'singular_name'      => _x( 'Freedom Log', 'post type singular name', 'freedom_log' ),
	      'menu_name'          => _x( 'Freedom Log', 'admin menu', 'freedom_log' ),
	      'name_admin_bar'     => _x( 'Freedom Log', 'add new on admin bar', 'freedom_log' ),
	      //'add_new'            => _x( 'Add New', 'User', 'freedom_log' ),
	      //'add_new_item'       => __( 'Add New User', 'freedom_log' ),
	      //'new_item'           => __( 'New User', 'freedom_log' ),
	      //'edit_item'          => __( 'Edit User', 'freedom_log' ),
	      'view_item'          => __( 'View Log', 'freedom_log' ),
	      'all_items'          => __( 'All Logs', 'freedom_log' ),
	      'search_items'       => __( 'Search Logs', 'freedom_log' ),
	      'not_found'          => __( 'No Log found.', 'freedom_log' ),
	      'not_found_in_trash' => __( 'No Log found in Trash.', 'freedom_log' )
	    );


	    //register / update post type
	    register_post_type( 'freedom_log',
	      array(
	        'labels' 				=>  $labels,
	        'public' 				=> false,
	        'publicly_queryable' 	=> false,
	        'has_archive' 			=> true,
	        'show_ui'            	=> true,
	        'show_in_menu'       	=> $this->plugin_name .'-settings',
	        'query_var'          	=> true,
	        'rewrite'            	=> [ 'slug' => 'freedom/log' ],
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
  		if ( get_post_type() != 'freedom_log' )
  			return $actions;

  		//add view link, if action needed just replace page with action
  		$action = '?page=view_freedom_log&amp;post='.$post->ID;
  		$url = wp_nonce_url(admin_url( "admin.php". $action ));
  		$link = '<a href="'. $url .'" title="'
				. esc_attr__('View Log', 'freedom_log')
				. '">' .  esc_html__('View', 'freedom_log') . '</a>';
  		$actions['view'] = $link;
  		return $actions;
  	}



  	//view screen for the user
  	public function view_freedom_log ( ) {
  		$post = get_post($_GET['post']);
  		$meta = get_post_meta( $post->ID);

  		$args = [
  			'title' => $post->post_title,
  			'content'	=> $post->post_content
  		];

  		include( plugin_dir_path( __FILE__ ) . 'partials/freedom-admin-log.php' );
  	}

  	/** hooked by wp admin_menu **/
	public function register_menu() {
		
		//provider setting link in the admin setting menu
		//pagetitle, menutitle, cap, menu-slug, callback
		add_options_page( 'Freedom Options', 'Freedom', $this->capability, $this->plugin_name .'-settings', array($this, 'display_setting_page') );
		

		//add_menu_page( string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '', string $icon_url = '', int $position = null )
		//add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '' )

		$page_title = apply_filters( 
        		$this->plugin_name . '-settings-page-title', 
        		esc_html__( 'Freedom', 'freedom-settings' ) 
        	);

        $menu_title = apply_filters( 
	        	$this->plugin_name . '-settings-menu-title', 
	        	esc_html__( 'Freedom', 'freedom-settings' ) 
        	);

        $menu_slug = $this->plugin_name . '-settings';
        $function = array($this, 'display_setting_page');


        add_menu_page($page_title, $menu_title, $this->capability, $menu_slug, $function);

		add_submenu_page( $menu_slug, $page_title, "Settings", $this->capability, $menu_slug, $function);

		//$menu_slug = 'edit.php?post_type=' . $this->post_type_order;

		add_submenu_page( 
	        null,
	        'View Freedom Log',
	        'View Freedom Log',
	        'manage_options',
	        'view_freedom_log',
	        [$this, 'view_freedom_log']
	    );

	}


	public function display_setting_page() {
		include( plugin_dir_path( __FILE__ ) . 'partials/freedom-admin-display.php' );
	}

	public function register_setting() {

		register_setting( 
			$this->plugin_name . '-settings', //both might be same 
			$this->plugin_name . '-settings' //should be same as above
		); 

		add_settings_section(
	        $this->plugin_name . '-settings', //section id
	        'API Mode', //title of section
	        array($this, 'settings_section_cb'), //callback
	        $this->plugin_name .'-settings' //slug of the page exactly same as $menu_slug
	    );

		// register a new field in the "wporg_settings_section" section, inside the "reading" page
	    add_settings_field(
	        $this->plugin_name . '-settings_mode', //id
	        'Sandbox', //title
	        array($this, 'settings_field_cb'), //callback
	        $this->plugin_name .'-settings', //menu_slug
	        $this->plugin_name . '-settings' //section
	    );

	    add_settings_field(
	        $this->plugin_name . '-settings_key', //id
	        'Subscription ID', //title
	        array($this, 'settings_field_key'), //callback
	        $this->plugin_name .'-settings', //menu_slug
	        $this->plugin_name . '-settings' //section
	    );
	}

	// section content cb
	function settings_section_cb()
	{
	    echo '<p>API Mode Setting</p>';
	}
	 
	// field content cb
	function settings_field_cb()
	{
	    // get the value of the setting we've registered with register_setting()
	    $setting = $this->get_option('mode');
	    // output the field
	    ?>
	    <input type="checkbox" name="<?php 
	    	echo $this->plugin_name; ?>-settings[mode]" value="sandbox" <?php 
	    		echo $setting == 'sandbox' ? 'checked="checked"' : '' ?>>
	    <?php
	}

	// field box for subscription id
	function settings_field_key()
	{
	    // get the value of the setting we've registered with register_setting()
	    $setting = $this->get_option('key');
	    // output the field
	    ?>
	    <input type="text" name="<?php 
	    	echo $this->plugin_name; 
	    	?>-settings[key]" value="<?php 
	    		echo $setting ?>" class="regular-text code" />
	    
	    <?php
	}

	function get_option($option) {
		$options = get_option($this->plugin_name . '-settings');
		return isset($options[$option]) ? $options[$option] : '';
	}

}
