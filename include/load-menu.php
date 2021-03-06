<?php
/**
 * Pre-load the navigation menu as a JSON object
 *
 * @package WPgurusWPtheme
 */

/**
 * Class wrapper for menu loading
 */
class Initial_LoadMenu {
	/**
	 * Set up actions
	 */
	public function __construct() {
		add_filter( 'wp_enqueue_scripts', array( $this, 'print_data' ),20 );
	}

	/**
	 * Adds the json-string data to the react app script
	 */
	public function print_data() {
		$menu_data = sprintf(
			'var InitialMenu = %s;'.PHP_EOL.'var SitePaths = %s;',
			$this->add_json_data(),
      $this->add_json_paths()
		);
		wp_add_inline_script( WPGURUS_APP, $menu_data, 'before' );
	}
  function add_json_paths(){
		$domain_url = wpgurus_domain_url();
		$paths = array(
      'home' => trailingslashit( apply_filters('wpgurus_theme_home_url',home_url()) ),
			'root' => $domain_url, //public/functions.php
      'logo' => apply_filters('wpgurus_theme_logo', get_template_directory_uri().'/images/icons.svg'), //TODO: make dynamic.
      'currentRoute'=> wp_unslash($_SERVER['REQUEST_URI'])
		);
		$others = apply_filters('wpgurus_themes_add_sitepaths', array());
		foreach($others as $key=>$path){
			$paths[$key] = $path;
		}
    return wp_json_encode( $paths );
  }
	/**
	 * Dumps the current query response as a JSON-encoded string
	 */
	public function add_json_data() {
    $menus = apply_filters('wpgurus_theme_vuejs_menu', get_nav_menu_locations());
    $data = array('enabled' => true);
    if(!class_exists( 'WP_REST_Menus' )){
      $data = array(
        'enabled' => false,
        'error' => 'Plugin WP REST API Menus needs to be <a href="'.admin_url('plugin-install.php?tab=plugin-information&plugin=wp-api-menus').'">installed</a> and activated'
      );
    }
    foreach($menus as $location=>$menu_id){
			$menu_data = $this->get_menu_data($menu_id);
	    $data[$location] = $menu_data;
    }
		if(apply_filters('wpgurus_theme_multilingual', false)){
			$languages = apply_filters('wpgurus_theme_language_menu', array());
      if(!empty($languages)){
        $data['languages']=$languages;
      }
		}
		return wp_json_encode( $data );
	}

	/**
	 * Gets menu data from the JSON API server
	 *
	 * @return array
	 */
	public function get_menu_data($menu_id) {
		$menu = array();

		$request = new \WP_REST_Request();
		$request['context'] = 'view';
		$request->set_url_params(array('id'=> $menu_id));
		//$request['location'] = $location;

		if ( class_exists( 'WP_REST_Menus' ) ) {
			$menu_api = new WP_REST_Menus();
			$menu = $menu_api->get_menu( $request );
		}
		return $menu;
	}
}
