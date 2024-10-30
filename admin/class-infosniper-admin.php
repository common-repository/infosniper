<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://connecticallc.com
 * @since      1.0.0
 *
 * @package    Infosniper
 * @subpackage Infosniper/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Infosniper
 * @subpackage Infosniper/admin
 * @author     ConnecticaLLC <info@connecticallc.com>
 */
class Infosniper_Admin {

  /**
   * The ID of this plugin.
   *
   * @since  1.0.0
   * @access private
   * @var    string  $plugin_name the ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since  1.0.0
   * @access private
   * @var    string  $version the current version of this plugin.
   */
  private $version;

  /**
   * Array containing regex used to identify browsers.
   * 
   * @since 1.1.0
   * @access private
   * @var array
   */
  private $browser_regex = array(
    'Firefox'           => '/firefox/i', 
    'Safari'            => '/safari/i',
    'Chrome'            => '/chrome/i',
    'Internet Explorer' => '/msie/i',
    'Edge'              => '/edge/i',
  );

  /**
   * Array containing ending for img tags for each browser.
   * 
   * @since 1.1.0
   * @access private
   * @var array
   */
  private $browser_logos = array(
    'Firefox'           => 'firefox-logo.png" alt="Firefox" title="Firefox">',
    'Safari'            => 'safari-logo.png" alt="Safari" title="Safari">',
    'Chrome'            => 'chrome-logo.png" alt="Chrome" title="Chrome">',
    'Internet Explorer' => 'ie-logo.png" alt="Internet Explorer" title="Internet Explorer">',
    'Edge'              => 'edge-logo.png" alt="Microsoft Edge" title="Microsoft Edge">',
    'Other'             => 'unknown-logo.png" alt="Unknown" title="Unknown">',
  );

  /**
   * Array containing regex to identify operating systems.
   * 
   * @since 1.1.0
   * @access private
   * @var array
   */
  private $os_regex = array( 
    'Windows' => '/windows nt/i',
    'Mac'     => '/mac os x/i',
    'Linux'   => '/linux/i',
    'Ubuntu'  => '/ubuntu/i',
  );

  /**
   * Array containing ending for img tags for each operating system.
   * 
   * @since 1.1.0
   * @access private
   * @var array
   */
  private $os_logos = array(
    'Windows' => 'windows-logo.png" alt="Windows" title="Windows">',
    'Mac'     => 'mac-logo.png" alt="Mac OS" title="Mac OS">',
    'Linux'   => 'linux-logo.png" alt="Linux" title="Linux">',
    'Ubuntu'  => 'ubuntu-logo.png" alt="Ubuntu" title="Ubuntu">',
    'Other'   => 'unknown-logo.png" alt="Unknown" title="Unknown">',
  );

  /**
   * Initialize the class and set its properties.
   *
   * @since 1.0.0
   * @param string $plugin_name the name of this plugin.
   * @param string $version     the version of this plugin.
   */
  public function __construct( $plugin_name, $version ) {

    $this->plugin_name = $plugin_name;
    $this->version = $version;

  }

  /**
   * Register the stylesheets for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_styles( $hook ) {

    //We enqueue our logo css on every page so it shows up in the sidebar.
    wp_enqueue_style( 
      $this->plugin_name, 
      plugin_dir_url( __FILE__ ) . 'css/infosniper-admin-logo.css', 
      array(), 
      $this->version, 
      'all'
    );

    if( $hook !== 'toplevel_page_infosniper' ) {
      return;
    }

    wp_enqueue_style( 
      $this->plugin_name . '-display', 
      plugin_dir_url( __FILE__ ) . 'css/infosniper-admin.css', 
      array(), 
      $this->version, 
      'all'
    );

    wp_enqueue_style( 
      'leaflet', 
      plugin_dir_url( __FILE__ ) . 'css/leaflet.css', 
      array(), 
      $this->version, 
      'all'
    );

    wp_enqueue_style(
      'markercluster',
      plugin_dir_url( __FILE__ ) . 'css/MarkerCluster.css',
      array(),
      $this->version,
      'all'
    );

    wp_enqueue_style(
      'markerclusterDefault',
      plugin_dir_url( __FILE__ ) . 'css/MarkerCluster.Default.css',
      array(),
      $this->version,
      'all'
    );

    wp_enqueue_style( 
      'fontawesome', 
      plugin_dir_url( __FILE__ ) . 'css/css/fontawesome.css', 
      array(), 
      $this->version, 
      'all'
    );

    wp_enqueue_style( 
      'fontawesome-b', 
      plugin_dir_url( __FILE__ ) . 'css/css/brands.css', 
      array(), 
      $this->version, 
      'all'
    );

    wp_enqueue_style( 
      'fontawesome-r', 
      plugin_dir_url( __FILE__ ) . 'css/css/regular.css', 
      array(), 
      $this->version, 
      'all'
    );

    wp_enqueue_style( 
      'fontawesome-s', 
      plugin_dir_url( __FILE__ ) . 'css/css/solid.css', 
      array(), 
      $this->version, 
      'all'
    );

  }

	/**
	* Register the JavaScript for the admin area.
	*
	* @since    1.0.0
	*/
	public function enqueue_scripts( $hook ) {

    if( $hook !== 'toplevel_page_infosniper' ) {
      return;
    }

    wp_enqueue_script( 
      'leaflet', 
      plugin_dir_url( __FILE__ ) . 'js/leaflet.js', 
      array(), 
      $this->version, 
      false 
    );

    wp_enqueue_script(
      'tile-stamen',
      plugin_dir_url( __FILE__ ) . 'js/tile.stamen.js',
      array(),
      $this->version,
      false
    );

    wp_enqueue_script(
      'chart-min',
      plugin_dir_url( __FILE__ ) . 'js/chart.min.js',
      array(),
      $this->version,
      false
    );

		wp_enqueue_script( 
      $this->plugin_name, 
      plugin_dir_url( __FILE__ ) . 'js/infosniper-admin.js', 
      array( 'jquery' ), 
      $this->version, 
      false
    );

    wp_enqueue_script( 
      'leafletMarkerCluster', 
      plugin_dir_url( __FILE__ ) . 'js/leaflet.markercluster.js', 
      array( 'jquery' ), 
      $this->version, 
      false
    );
  }

  /**
   * Register the administration menu for this plugin in the dashboard menu.
   * 
   * @since 1.0.0
   */
  public function add_plugin_admin_menu() {
    //Add the primary menu page to the sidebar.
    add_menu_page( 
      'infoSNIPER', 
      'infoSNIPER', 
      'manage_options', 
      $this->plugin_name, 
      array($this, 'display_plugin_dashboard_page'), 
      'none'
    );
  }

  /**
   * Add settings action link to plugin page.
   * 
   * @since 1.0.0
   */
  public function add_action_links($links) {
    $settings_link = array(
      //Dashboard Link
      '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '#top#dashboard">' .
        __('Dashboard', $this->plugin_name) .
      '</a>',
      //Logs Link
      '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '#top#logs">' .
        __('User Logs', $this->plugin_name) . 
      '</a>',
      //Help Link
      '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '#top#help">' .
        __('Help', $this->plugin_name) . 
      '</a>',
      //Settings link
      '<a href="' . admin_url( 'admin.php?page=' . $this->plugin_name ) . '#top#settings">' . 
        __('Settings', $this->plugin_name) . 
      '</a>',
    );
    return array_merge( $settings_link, $links );
  }

  /**
   * Render our dashboard page.
   * 
   * @since 1.0.0
   */
  public function display_plugin_dashboard_page() {
    include_once( 'partials/infosniper-admin-display.php' );
  }

  /**
   * Update settings to php_settings.
   * 
   * @since 1.0.0
   */
  public function options_update() {
    register_setting(
      $this->plugin_name, 
      $this->plugin_name, 
      array( $this, 'validate' )
    );
  }

  /**
   * Validates settings from input form on settings page.
   *
   * @param  array $input the array of input settings being validated.
   *
   * @return array $valid array containing validated settings values to be
   *                      stored in worpdress.
   *
   * @access public
   * @since 1.0.0
   */
  public function validate( $input ) {
    //Validate ip-count-timeframe
    $valid_ip_counts = array( 'last-100', 'last-250', 'last-week', 'last-month', 'last-six-months', 'last-year' );
    $ip_counts_is_valid = in_array( $input['ip-count-timeframe'], $valid_ip_counts );
    if( $input['ip-count-timeframe'] === '' ) {
      $ip_count_timeframe = 'last-month';
    } else {
      $ip_count_timeframe = $input['ip-count-timeframe'];
    }
    //Validate map-type
    $valid_map_types = array( 'terrain', 'toner', 'watercolor' );
    $map_type_is_valid = in_array( $input['map-type'], $valid_map_types );
    //Validate delete-options
    $valid_checkbox_options = array( "true" );
    $checkbox_options_is_valid  = in_array( $input['delete-options'],     $valid_checkbox_options );
    $checkbox_database_is_valid = in_array( $input['delete-database'],    $valid_checkbox_options );
    $checkbox_track_is_valid    = in_array( $input['dnt-threats'],   $valid_checkbox_options );
    //Store in array
    $valid = array(
      'key'                => sanitize_text_field( $input['key'] ),
      'ip-count-timeframe' => $ip_count_timeframe,
      'map-type'           => ( $map_type_is_valid )          ? $input['map-type']           : 'terrain',
      'delete-options'     => ( $checkbox_options_is_valid )  ? $input['delete-options']     : false,
      'delete-database'    => ( $checkbox_database_is_valid ) ? $input['delete-database']    : false,
      'dnt-threats'        => ( $checkbox_track_is_valid )    ? $input['dnt-threats']   : false,
    );
    return $valid;
  }

  /**
   * Create a <td> tag with given id, class, count, and text.
   *
   * @param string $class class to be given to new <td> tag.
   * @param string $id    name of id to be given to <td> tag.
   * @param int    $count number of id to be given to <td> tag.
   * @param string $text  text to be put inside of <td> tag.
   *
   * @return string a string containing a valid html <td> tag using given parameters.
   *
   * @access public
   * @since 1.0.0
   */
  public function insert_td( $class, $id, $count, $text ) {
    //If no class is provided we dont need to have classText.
    if($class === "") {
      $classText = "";
    } else {
      $classText = "class='" . $class . "' ";
    }
    return "<td " . $classText . "id='" . $id . "-" . $count . "'>" . $text . "</td> ";
  }

  public function insert_user_log( $header, $slug, $count, $text ) {
    $html  = "<div class='info-sniper-content-group'>";
    $html .=   "<div class='info-sniper-content-title'>";
    $html .=     $header;
    $html .=   "</div>";
    $html .=   "<div id='" . $slug . "-" . $count . "'>";
    $html .=     $text;
    $html .=   "</div>";
    $html .= "</div>";
    return $html;
  }

  /**
   * Creates an <a> tag with an embedded image from $src with alt text $alt to a
   * Destination based on the case and the $data.
   *
   * @param string $case     determines what type of link to provide.
   *                         Valid values are 'info', 'google', and 'wiki'.
   * @param string $data     determines where the link goes to.
   * @param string $data_alt optional param that allows for custom alt text.
   *
   * @return string $link_HTML a string containing a valid HTML <a> tag based on the parameters.
   *
   * @access public
   * @since 1.0.0
   */
  public function insert_image_link( $case, $data, $data_alt = "") {
    if($data_alt === ""){
      $data_alt = $data;
    }
    //Our different use cases have different links and alt texts.
    switch( $case ) {
      case "info":
        $baseURL = 'https://talosintelligence.com/reputation_center/lookup?search=';
        $src = 'https://www.infosniper.net/images/information.png';
        $alt = 'Check ' . $data_alt . ' at TalosIntelligence.com';
        break;
      case "google":
        $baseURL = 'https://www.google.com/search?q=';
        $src = 'https://www.infosniper.net/images/google.png';
        $alt = 'Search' . $data_alt . ' on google.com';
        break;
      case "wiki":
        $baseURL = 'https://en.wikipedia.org/wiki/';
        $src = 'https://www.infosniper.net/images/wikipedia.png';
        $alt = 'Read about ' . $data_alt . ' on Wikipedia';
        break;
      default:
        return "";
    }

    //Add our data onto the appropriate url.
    $URL = $baseURL . $data;
    //Put everything together.
    $link_HTML = " <a href='" . $URL . "' target='_blank'>" .
                   "<img " .
                     "src='" . $src . "' " .
                     "alt='" . $alt . "' " .
                     "title='" . $alt . "' " .
                     "width=12 " .
                     "height=12 " .
                   ">" .
                 "</a>";

    return $link_HTML;
  }

  /**
   * Generate our user table on the settings page.
   *
   * @param string $selected the selected time frame in which to generate the table from.
   *
   * @return array an array containing valid HTML for the user table and an 
   *               integer count of how many ips are in the table.
   *
   * @access public
   * @since 1.0.0
   */
  public function create_table( $selected ) {
    global $wpdb;
    $options = get_option( $this->plugin_name );
    $dnt_threats = $options['dnt-threats'];
    //Costruct our table name
    $table_name = $wpdb->prefix . "infoSniperAddresses";
    //Construct our query.
    $sql = "SELECT 
              INET_NTOA(ip),
              agent,
              latitude,
              longitude,
              city,
              state,
              country,
              countrycode,
              countryflag,
              user_type,
              last_visit,
              last_page,
              provider
            FROM " . $table_name;
    //Select our date based on selected option. 
    switch( $selected ) {
      default: //Fall through to last-week case
        $timezone = get_option( 'timezone_string' );
        $dateTime = new DateTime( $selected, new DateTimeZone( $timezone ) );
        $date = $dateTime->format('U');
        unset( $dateTime );
        break;
      case "last-100":
        $limit = 100;
        break;
      case "last-250":
        $limit = 250;
      case "last-week":
        $date = time() - (7 * 24 * 60 * 60);  //Format to unix timestamp format.
        break;
      case "last-month":
        $date = time() - (30 * 24 * 60 * 60);
        break;
      case "last-six-months":
        $date = time() - (180 * 24 * 60 * 60);
        break;
      case "last-year":
        $date = time() - (365 * 24 * 60 * 60);
        break;
    }
    //Make sure we only get results in the specified period.
    if( isset( $date ) && $date ) {
      $sql  = $sql . " WHERE last_visit > '" . $date . "'";
    }
    //Pre sort the data for ease of use.
    $sql .= " ORDER BY last_visit DESC";
    //Make sure we only get the specified amount.
    if( isset( $limit ) && $limit ) {
      $sql .= " LIMIT " . $limit;
    }
    //Limit users for performance if necessary.
    //$sql  = $sql . " LIMIT 1000";
    //Query the database
    $result = $wpdb->get_results( $sql, ARRAY_A );
    //Count ip addresses for indexing our html table.
    $count = 0;
    //Generate head and opening <tbody> tag.
    $tableHTML = '';
  
    $table_data = array();
    //Loop through results and print to table.
    foreach( $result as $row ){

      //Identify the browser, we start with 'Other' and overwrite if we find a match.
      $browser = 'Other';
      foreach( $this->browser_regex as $browser_name => $regex ) {
        if( preg_match( $regex, $row['agent'] ) ) {
          $browser = $browser_name;
        }
      }
      //Get the img tag from the array.
      $browser = $this->browser_logos[ $browser ];

      //Identify the operating system. Start with 'Other' and overwrite if we
      //find a match.
      $os = 'Other';
      foreach( $this->os_regex as $os_name => $regex ) {
        if( preg_match( $regex, $row['agent'] ) ) {
          $os = $os_name;
        }
      }
      //Get the img tag from the array.
      $os = $this->os_logos[ $os ];

      //Replace our countryflag result with the unknown flag if we have no flag.
      if ( $row['countryflag'] != "https://www.infosniper.net/country_flags/.gif" &&
           $row['countryflag'] != "") {
        $flag = $row['countryflag'];
      } else {
        $flag = 'https://www.infosniper.net/country_flags/unknown.gif';
      }

      //If we arent tracking threats display them as users.
      if( $dnt_threats ) {
        if( $row['user_type'] === 'Potential Threat' ) {
          $type = 'User';
        } else {
          $type = $row['user_type'];
        }
      } else {
        $type = $row['user_type'];
      }

      //Set the timezone so our date displays correctly.
      $timezone = get_option( 'timezone_string' );
      date_default_timezone_set( $timezone );
      $date = date( "m/d/y", $row['last_visit'] );
      $time = date( "h:i A", $row['last_visit'] );
      

      //Set typeclass that is uses in class naming for coloring.
      if( $type === "Potential Threat" ) {
        $typeclass = 'Threat';
      } else {
        $typeclass = $type;
      }

      //Determine logo based on type.
      switch( $type ) {
        case 'User':
          $userLogo = '<i class="fas fa-user"></i>';
          break;
        case 'Admin':
          $userLogo = '<i class="fas fa-user-shield"></i>';
          break;
        case 'Bot':
          $userLogo = '<i class="fas fa-robot"></i>';
          break;
        case 'Potential Threat':
          $userLogo = '<i class="fas fa-user-ninja"></i>';
      }

      //Start Row.
      $tableHTML .= "<div class='user-log' id='user-log-" . $count . "'>";
      $tableHTML .= "<div class='user-info-basic type-" . $typeclass . "'>";
      $tableHTML .= "<div class='user-info-left'>";
      //Insert ip.
      $tableHTML .= "<div class='user-info-ip'>" . '<i class="fas fa-info"></i> ' . $row['INET_NTOA(ip)'] . "</div>";
      //Insert time.
      $tableHTML .= "<div class='user-info-time'>" . '<i class="far fa-clock"></i> ' . $date . " @ " . $time . "</div>";
      //Insert usertype
      $tableHTML .= "<div class='user-info-usertype'>" . $userLogo . " " . $row['user_type'] . "</div>";
      //insert browser
      $tableHTML .= "<div class='user-info-browser'>" . '<img class="log-logo" src="' . dirname( plugin_dir_url( __FILE__ ) ) . "/admin/css/images/" . $browser . "</div>";
      //insert os.
      $tableHTML .= "<div class='user-info-os'>" . '<img class="log-logo" src="' . dirname( plugin_dir_url( __FILE__ ) ) . "/admin/css/images/" . $os . "</div>";
      $tableHTML .= "</div>";
      $tableHTML .= "<div class='user-info-right'>";
      //insert flag
      $tableHTML .= "<img src='" . $flag . "' width=20 height=13>  ";
      //insert city if there is one
      if( $row['city'] !== 'n/a' && $row['city'] !== '' ) {
        $tableHTML .= $row['city'] . ", ";
      }
      //insert state if there is one.
      if( $row['state'] !== 'n/a' && $row['state'] !== '' ) {
        $tableHTML .= $row['state'] . ', ';
      }
      //insert countrycode.
      $tableHTML .= $row['countrycode'];
      $tableHTML .= "</div>";
      $tableHTML .= "</div>";
      //start extended view
      $tableHTML .= "<div id='user-info-" . $count . "' class='user-info-extended' data-user-log-index='" . $count . "'>";
      $tableHTML .= "<div id='mapid-" . $count . "' class='info-sniper-map'></div>";
      $tableHTML .= "<div class='info-sniper-content-log'>";
    
      //Insert timestamp.
      $tableHTML .= $this->insert_user_log( 'Last Visit Date', 'ltime', $count, $date );
      $tableHTML .= $this->insert_user_log( 'Last Visit Time', 'lhour', $count, $time );

      //Insert last page.
      $last_page = $row['last_page'] . " " . '<a href="JavaScript:newPopup(' . "'" . $row['last_page'] . "'" . ');">⧉</a>';
      $tableHTML .= $this->insert_user_log( 'Last Page Visited', 'lpage', $count, $last_page );

      //Insert user type.
      $tableHTML .= $this->insert_user_log( 'User Type', 'type', $count, $type );

      //Insert browser.
      $tableHTML .= $this->insert_user_log( 'Browser', 'browser', $count, '<img class="log-logo" src="' . dirname( plugin_dir_url( __FILE__ ) ) . "/admin/css/images/" . $browser );

      //Insert operating system.
      $tableHTML .= $this->insert_user_log( 'Operating System', 'os', $count, '<img class="log-logo" src="' . dirname( plugin_dir_url( __FILE__ ) ) . "/admin/css/images/" . $os );

      //Insert ip.
      $innerText = $row['INET_NTOA(ip)'] . $this->insert_image_link( 'info', $row['INET_NTOA(ip)'] );
      $tableHTML .= $this->insert_user_log( 'IP Address', 'ip', $count, $innerText  );

      //Insert hostname.
      //if( $row['hostname'] != "" && $row['hostname'] != 'n/a'){
      //  $innerText = $row['hostname'] . $this->insert_image_link( 'info', $row['hostname'] );
      //} else {
      //  $innerText = $row['hostname'];
      //}
      $innerText = '';
      //$tableHTML .= $this->insert_user_log( 'Hostname', 'host', $count, $innerText );

      //Insert provider.
      if( $row['provider'] != "" && $row['provider'] != 'n/a') {
        $innerText = $row['provider'] . $this->insert_image_link( 'google', $row['provider'] );
      } else {
        $innerText = $row['provider'];
      }
      $tableHTML .= $this->insert_user_log( 'Provider', 'pro', $count, $innerText );

      //Insert country.
      if( $row['country'] != "" && $row['country'] != 'n/a') {
        $innerText = $row['country'] . $this->insert_image_link( 'wiki', $row['country'] );
      } else {
        $innerText = $row['country'];
      }
      $tableHTML .= $this->insert_user_log( 'Country', 'coun', $count, $innerText );

      //Insert countrycode.
      if( $row['countrycode'] != "" && $row['countrycode'] != 'n/a') {
        $innerText = $row['countrycode'] . $this->insert_image_link( 
          'wiki' , 
          'Country_code_top-level_domain', 
          'Top Level Domains'
        );
      } else {
        $innerText = $row['countrycode'];
      }
      $tableHTML .= $this->insert_user_log( 'Country Code', 'code', $count, $innerText );

      //Insert flag.
      $tableHTML .= $this->insert_user_log( 'Flag', 'flag', $count, "<img src='" . $flag . "' width=20 height=13>" );

      //Insert state.
      if( $row['state'] != "" && $row['state'] != 'n/a') {
        $innerText = $row['state'] . $this->insert_image_link( 'wiki', $row['state'] );
      } else {
        $innerText = $row['state'];
      }
      $tableHTML .= $this->insert_user_log( 'State', 'stat', $count, $innerText );

      //Insert city.
      if( $row['city'] != "" && $row['city'] != 'n/a') {
        $innerText = $row['city'] . $this->insert_image_link( 'wiki', $row['city'] );
      } else {
        $innerText = $row['city'];
      }
      $tableHTML .= $this->insert_user_log( 'City', 'city', $count, $innerText );

      //Insert areacode.
      //$tableHTML .= $this->insert_user_log( 'Area Code', 'area', $count, '' );

      //Insert postal code.
      //$tableHTML .= $this->insert_user_log( 'Postal Code', 'post', $count, '' );

      //Insert dma code.
      //$tableHTML .= $this->insert_user_log( 'DMA Code', 'dma', $count, '' );

      //Insert timezone.
      //if( $row['timezone'] != "" && $row['timezone'] != 'n/a') {
      //  $innerText = $row['timezone'] . $this->insert_image_link( 
      //   'wiki', 
      //   'List_of_tz_database_time_zones', 
      //   'Timezones'
      //  );
      //} else {
      //  $innerText = $row['timezone'];
      //}
      //$tableHTML .= $this->insert_user_log( 'Timezone', 'zone', $count, '' );

      //Insert gmt offset.
      //$tableHTML .= $this->insert_user_log( 'GMT Off-Set', 'gmt', $count, '' );

      //Insert continent.
      //if( $row['continent'] != "" && $row['continent'] != 'n/a') {
      //  $innerText = $row['continent'] . $this->insert_image_link( 'wiki', 'Continent' );
      //} else {
      //  $innerText = $row['continent'];
      //}
      //$tableHTML .= $this->insert_user_log( 'Continent', 'cont', $count, '' );

      //Insert latitude.
      $tableHTML .= $this->insert_user_log( 'Latitude', 'lati', $count, $row['latitude'] );

      //Insert longitude.
      $tableHTML .= $this->insert_user_log( 'Longitude', 'long', $count, $row['longitude'] );

      //Insert accuracy.
      //$tableHTML .= $this->insert_user_log( 'Accuracy', 'acc', $count, '' );

      //Insert date.
      //$tableHTML .= $this->insert_user_log( 'First Visit Date', 'time', $count, '' );//date( "m/d/y", $row['timestamp'] ) );
      //$tableHTML .= $this->insert_user_log( 'First Visit Time', 'hour', $count, '' );//date( "h:i A", $row['timestamp'] ) );

      //Insert first page.
      //$first_page = $row['last_page'] . " " . '<a href="JavaScript:newPopup(' . "'" . $row['first_page'] . "'" . ');">⧉</a>';
      //$tableHTML .= $this->insert_user_log( 'First Page Visited', 'fpage', $count, '' );

      //Insert user agent.
      $tableHTML .= $this->insert_user_log( 'User Agent', 'agen', $count, $row['agent'] );

      //End Row.
      $tableHTML .= "</div>";
      $tableHTML .= "</div>";
      $tableHTML .= "</div>";

      $count++;

      if ( ! array_key_exists( $row['countrycode'], $table_data ) ) {
        $table_data[ $row['countrycode'] ] = 1;
      } else {
        $table_data[ $row['countrycode'] ]++;
      }
    }

    return array( $tableHTML, $count );
  }

  /**
   * Pulls information from our infoSNIPER API for dynamic content.
   * 
   * @param string $action the action needed from the API.
   *                       Valid values are 'info' and 'ads'.
   * @param string $key    the users API key.
   * 
   * @return string $data a string of valid HTML that contains content provided 
   *                      by the API to display.
   * 
   * @access public
   * @since 1.0.0
   */
  public function get_info( $action, $key ) {
    $body = array(
      'action' => $action,
      'k'      => $key,
    );
    $args = array(
      'body'        => $body,
      'timeout'     => '5',
      'redirection' => '5',
      'httpversion' => '1.0',
      'blocking'    => true,
      'headers'     => array(),
      'cookies'     => array(),
    );
    $url = 'https://www.infosniper.net/plugin/infosniper_plugin_ads.php';
    $response = wp_remote_post( $url, $args );
    return $response[ 'body' ];
  }

  /**
   * Redirects the user to the infoSNIPER plugin settings page if the option is 
   * true. 
   * Called on non-bulk activation.
   * 
   * @access public
   * @since 1.0.0
   */
  public function infosniper_setup_redirect() {
    if ( get_option( 'infosniper_do_activation_redirect') ) {
      delete_option( 'infosniper_do_activation_redirect' );
      //Only redirect if it is not a bulk activation.
      if( ! isset( $_GET['activate-multi'] ) ) {
        wp_redirect( "admin.php?page=infosniper#top#settings" );
      }
    }
  }

  /**
   * Generates a dashboard table with the provided prefix and title.
   * 
   * @param string $prefix string that prefixes identifiers and classes.
   * @param string $head   string that goes in the table head.
   * 
   * @return string valid html table built from parameters
   * 
   * @access public
   * @since 1.0.0
   */
  public function generate_dashboard_table( $prefix , $head ) {
    //Start the table tag.
    $tableHTML  = '<table id="' . $prefix . '-table">';
    //Create the head of the table.
    $tableHTML .= '<thead>';
    $tableHTML .= '<th id="table-head">' . $head . '</th>';
    $tableHTML .= '<th>Users</th>';
    $tableHTML .= '</thead>';
    //Loop 10 times and create 10 rows in the table.
    for( $i = 0; $i < 10; $i++ ) {
      $tableHTML .= '<tr id="' . $prefix . '-row-' . $i . '">';
      $tableHTML .= '<th class="' . $prefix . '" id="' . $prefix . '-' . $i . '"></th>';
      $tableHTML .= '<th id="' . $prefix . '-users-' . $i . '"></th>';
      $tableHTML .= '</tr>';
    }
    //End the table tag.
    $tableHTML .= "</table>";
    
    return $tableHTML;
  }

  /**
   * Updates the database table with appropriate logic.
   * 
   * @access public
   * @since 1.0.1
   */
  public function update_database_table() {
    //Update database.
    if( get_option( 'infosniper_db_v1_0_0' ) ) {
      global $wpdb;
      $table_name = $wpdb->prefix . "infoSniperAddresses";
      $sql = "ALTER TABLE " . $table_name . " 
              MODIFY COLUMN user_type enum( 'User', 'Bot', 'Admin', 'Infiltrator', 'Potential Threat' )";
      $wpdb->query( $sql );
      $sql = "UPDATE " . $table_name . "
              SET user_type = 'Potential Threat'
              WHERE user_type = 'Infiltrator'";
      $wpdb->query( $sql );
      $sql = "ALTER TABLE " . $table_name . " 
              MODIFY COLUMN user_type enum( 'User', 'Bot', 'Admin', 'Potential Threat' )";
      $wpdb->query( $sql ); 

      delete_option( 'infosniper_db_v1_0_0' );
    }
  }

  /**
   * Deletes the database table if requested.
   * 
   * @access public
   * @since 1.0.1
   */
  public function delete_database_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . "infoSniperAddresses";
    $sql = "TRUNCATE TABLE " . $table_name;
    $wpdb->query( $sql );
  }

  /**
   * Loads data dynamically in user logs.
   * 
   * @access public
   * @since 1.3.0
   */
  public function load_data() {
    global $wpdb;

    $ip = $_POST[ 'ipAddress' ];

    $table_name = $wpdb->prefix . "infoSniperAddresses";
    $sql = "SELECT hostname,
                   areacode,
                   postalcode,
                   dmacode,
                   timezone,
                   gmtoffset,
                   continent,
                   accuracy,
                   timestamp,
                   first_page
            FROM " . $table_name . "
            WHERE INET_NTOA(ip) = '" . $ip . "'";
    $result = $wpdb->get_row( $sql, ARRAY_A );

    //Process Special Cases
    if( $result['hostname'] != "" && $result['hostname'] != 'n/a'){
      $hostname = $result['hostname'] . $this->insert_image_link( 'info', $result['hostname'] );
    } else {
      $hostname = $result['hostname'];
    }

    if( $result['timezone'] != "" && $result['timezone'] != 'n/a') {
      $timezone = $result['timezone'] . $this->insert_image_link( 
       'wiki', 
       'List_of_tz_database_time_zones', 
       'Timezones'
      );
    } else {
      $timezone = $result['timezone'];
    }

    if( $result['continent'] != "" && $result['continent'] != 'n/a') {
      $continent = $result['continent'] . $this->insert_image_link( 'wiki', 'Continent' );
    } else {
      $continent = $result['continent'];
    }

    $firstPage = $result['first_page'] . " " . '<a href="JavaScript:newPopup(' . "'" . $result['first_page'] . "'" . ');">⧉</a>';

    $date = date( "m/d/y", $result['timestamp'] );
    $time = date( "h:i A", $result['timestamp'] );

    $json_result = array(
      'status'     => 200,
      'action'     => $_POST[ 'action' ],
      'ipAddress'  => $_POST[ 'ipAddress' ],
      'hostname'   => $hostname,
      'areacode'   => $result[ 'areacode' ],
      'postalcode' => $result[ 'postalcode' ],
      'dmacode'    => $result[ 'dmacode' ],
      'timezone'   => $timezone,
      'gmtoffset'  => $result[ 'gmtoffset' ],
      'continent'  => $continent,
      //'accuracy'   => $result[ 'accuracy' ],
      'firstPage' => $firstPage,
      'firstDate' => $date,
      'firstTime' => $time
    );

    wp_die( json_encode( $json_result ) );
  } 

  /**
   * Calls API with provided key to test if it is valid against the infoSNIPER IP
   * 
   * @access public
   * @since 1.3.0
   */
  public function validate_key() {
    //$options = get_option( $this->plugin_name );
    //$key = $options['key'];
    $key = $_POST['key'];

    //We ping the infosniper website for validation
    $ip = '104.28.17.5';
  
    //Create the base of our query URL.
    $queryURL = "http://www.infosniper.net/xml.php?ip_address=" . $ip . "&k=" . $key;
    $response = wp_remote_get( $queryURL );
    if( is_wp_error( $response ) ) {
      return 0;
    }
  
    $xml_string = $response[ 'body' ];
    $status = wp_remote_retrieve_response_code( $response );
    if( $status !== 200 ) {
      //Invalid API Response
      return 0;
    }
  
    //Verify that we received a valid string.
    libxml_use_internal_errors( true );
    $simple_xml_error = simplexml_load_string( $xml_string );
    if( ! $simple_xml_error ) {
      //Invalid xml received return.
      return 0;
    }
    
    //Convert our string to XML.
    $xml = new SimpleXMLElement( $xml_string );
  
    //Initalize results array.
    $results = array(
      'ipaddress'   => esc_html__( $xml->result[0]->ipaddress ),
      'hostname'    => esc_html__( $xml->result[0]->hostname ),
      'provider'    => esc_html__( htmlentities ( $xml->result[0]->provider ) ),
      'country'     => esc_html__( htmlentities ( $xml->result[0]->country  ) ),
      'countrycode' => esc_html__( $xml->result[0]->countrycode ),
      'countryflag' => esc_html__( $xml->result[0]->countryflag ),
      'areacode'    => esc_html__( $xml->result[0]->areacode ),
      'postalcode'  => esc_html__( $xml->result[0]->postalcode ),
      'dmacode'     => esc_html__( $xml->result[0]->dmacode ),
      'timezone'    => esc_html__( $xml->result[0]->timezone ),
      'gmtoffset'   => esc_html__( $xml->result[0]->gmtoffset ),
      'continent'   => esc_html__( $xml->result[0]->continent ),
      'latitude'    => esc_html__( $xml->result[0]->latitude ),
      'longitude'   => esc_html__( $xml->result[0]->longitude ),
      'queries'     => esc_html__( $xml->result[0]->queries ),
      'accuracy'    => esc_html__( $xml->result[0]->accuracy ),
      'state'       => esc_html__( htmlentities ( $xml->result[0]->state    ) ),
      'city'        => esc_html__( htmlentities ( $xml->result[0]->city     ) ),
    );
  
    //Final check for malformed information
    if( $results['queries'] === '' ){
      return 0;
    }
  
    if( $results['provider'] === 'Invalid Key: Purchase one here: http://www.infosniper.net/lb.php' ) {
      $valid = False;
    } else {
      $valid = True;
      //remove_option( 'infosniper_invalid_key' );
    }

    if( $results['queries'] > 0 ){
      //delete_option( 'infosniper_key_no_queries' );
    }
  
    $json_result = array(
      'queries' => $results['queries'],
      'valid'   => $valid
    );

    if( $valid ){
      update_option( 'infosniper_queries', $results['queries'] );
    }
    
    wp_die( json_encode( $json_result ) );
  }
}
