<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://connecticallc.com
 * @since      1.0.0
 *
 * @package    Infosniper
 * @subpackage Infosniper/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Infosniper
 * @subpackage Infosniper/public
 * @author     ConnecticaLLC <info@connecticallc.com>
 */
class Infosniper_Public {

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
   * Amount of maps current on the page.
   * 
   * @since   1.0.0
   * @access  private
   * @var     int      $map_count  Amount of maps on current page.
   */
  private $map_count = 0;

  /**
   * Name of our wordpress database table.
   * 
   * @since   1.0.0
   * @access  private
   * @var     string   $table_name name of the database table.
   */
  private $table_name;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $plugin_name       The name of the plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct( $plugin_name, $version ) {

    global $wpdb;
    $this->plugin_name = $plugin_name;
    $this->version = $version;
    $this->map_count = 0;
    $this->table_name = $wpdb->prefix . "infoSniperAddresses";

  }

  private $bot_agents = array(
    "bot", 
    "wordpress", 
    "netsystemsresearch", 
    "google", 
    "curl", 
    "go-http", 
    "http banner", 
    "gdnplus", 
    "zgrab", 
    "lighthouse", 
    "gtmetrix",
    "spider",
    "7siters",
    "crawl",
    "dispatch",
    "facebook",
    "dlvr.it",
    "mixnode",
    "evc-batch",
    "winhttp",
    "libwww-perl",
    "httpclient",
    "bingpreview",
    "randomsurfer",
  );

  private $bot_hostnames = array(
    "scan",
    "googlebot",
    "internet-census",
    "shodan",
  );

  private $risky_pages = array(
    "wp-login.php",
    "xmlrpc.php",
    "wp-admin",
    "wp-json",
    "wp-content",
    "wp-includes",
  );

  	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

    wp_enqueue_style(
      'leaflet',
      plugin_dir_url( __FILE__ ) . 'css/leaflet.css',
      array(),
      $this->version,
      'all'
    );
		
		wp_enqueue_style( 
      $this->plugin_name, 
      plugin_dir_url( __FILE__ ) . 'css/infosniper-public.css', 
      array(), 
      $this->version, 
      'all' 
    );

	}
	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

	}

  /**
   * Check if an input matches a specific set of patterns.
   * 
   * @param $pattern_array
   * @param $input
   * @return bool
   */
  public function does_match_pattern( $pattern_array, $input ) {
    $input = strtolower( $input );
    foreach( $pattern_array as $pattern ) {
      //Return if the input matches the current pattern.
      if( strstr( $input, $pattern ) ) {
        return true;
      }
    }
    return false;
  }

  /**
   * Query infoSNIPER API for selected ip address.
   * 
   * @param string $ip  ip address to be queried.
   * @param string $key key to be used in the query.
   * 
   * @return array $results an array containing all results from the API call.
   * 
   * @access public
   * @since 1.0.0
   */
  public function query_API( $ip, $key ) {
    //Create the base of our query URL.
    $queryURL = "http://www.infosniper.net/xml.php?ip_address=" . $ip;
    //Append key to url if given.
    if( $key != "" ) {
      $queryURL = $queryURL . "&k=" . $key;
    }
    //Send query to api.
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

    return $results;
  }

  /**
   * Checks if scanning has been selected and scans if it has been.
   * 
   * @access public
   * @since 1.0.0
   */
  public function scan_ips() {
    //Get options
    $options            = get_option( $this->plugin_name );
    $invalid_key        = get_option( 'infosniper_invalid_key' );
    $key_out_of_queries = get_option( 'infosniper_key_no_queries' );
    $previous_queries   = get_option( 'infosniper_queries' );
    $key = $options['key'];

    if( isset( $key_out_of_queries ) && $key_out_of_queries ) {
      if( $previous_queries !== 0 ) {
        delete_option( 'infosniper_key_no_queries' );
      } else {
        return;
      }
    }

    if( ! isset( $key ) || ( isset( $invalid_key ) && $key === $invalid_key ) || $key === "" ) {
      return;
    }
	  
    delete_option( 'infosniper_invalid_key' );
	  
    //Get our table address.
    global $wpdb;
    //Create sql query.
    $sql = "SELECT INET_NTOA(ip), agent FROM " . $this->table_name . " WHERE provider IS NULL LIMIT 5";
    
    $results = $wpdb->get_results( $sql, ARRAY_A );

    //Iterate over results and query the ip addresses.
    foreach ( $results as $row ) {
      //Grab the IP we are scanning
      $ip    = $row['INET_NTOA(ip)'];
      $agent = $row['agent'];
      //Send our IP to the infoSNIPER api.
      $api_results = $this->query_API( $ip, $key );  
      //If our query fails move on to next.
      if( $api_results === 0 ) {
        continue;
      }
      if( $api_results['provider'] === 'Invalid Key: Purchase one here: http://www.infosniper.net/lb.php' ) {
        $this->valid_key = False;
        add_option( 'infosniper_invalid_key', $key );
        break; 
      }
      if( $api_results['provider'] === 'Quota exceeded' ) {
        add_option( 'infosniper_key_no_queries', true );
        update_option( 'infosniper_queries', 0 );
        break;
      }
      //Store our results in our prepared variables.
      //We cast everything to string to sanitize for $wpdb->prepare.
      //We use substr to truncate potentially too long strings.
      $ip          = (string) $ip;
      $hostname    = substr( (string) $api_results["hostname"],    0, 100 );
      $provider    = substr( (string) $api_results["provider"],    0, 100 );
      $country     = substr( (string) $api_results["country"],     0, 50  );
      $countrycode = substr( (string) $api_results["countrycode"], 0, 50  );
      $countryflag = substr( (string) $api_results["countryflag"], 0, 50  );
      $state       = substr( (string) $api_results["state"],       0, 3   );
      $city        = substr( (string) $api_results["city"],        0, 50  );
      $areacode    = substr( (string) $api_results["areacode"],    0, 10  );
      $postalcode  = substr( (string) $api_results["postalcode"],  0, 10  );
      $dmacode     = substr( (string) $api_results["dmacode"],     0, 10  );
      $timezone    = substr( (string) $api_results["timezone"],    0, 50  );
      $gmtoffset   = (string) $api_results["gmtoffset"];
      $continent   = (string) $api_results["continent"];
      $latitude    = (string) $api_results["latitude"];
      $longitude   = (string) $api_results["longitude"];
      $accuracy    = (string) $api_results["accuracy"];
      $queries     = $api_results["queries"];

      //Store queries in options.
      update_option( 'infosniper_queries', $queries );

      //Prepare our new query for updating the DB.
      $attributes = "hostname = %s,
                     provider = %s,
                     country = %s,
                     countrycode = %s,
                     countryflag = %s,
                     state = %s,
                     city = %s,
                     areacode = %d,
                     postalcode = %d,
                     dmacode = %d,
                     timezone = %s,
                     gmtoffset = %d,
                     continent = %s,
                     latitude = %f,
                     longitude = %f,
                     accuracy = %d";

      $page = sanitize_text_field(
        ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
        "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"
      );

      $sql = "UPDATE " . $this->table_name .
      " SET " . $attributes . " 
      WHERE ip = INET_ATON(%s)";

      $prepared = $wpdb->prepare(
        $sql,
        $hostname,
        $provider,
        $country,
        $countrycode,
        $countryflag,
        $state,
        $city,
        $areacode,
        $postalcode,
        $dmacode,
        $timezone,
        $gmtoffset,
        $continent,
        $latitude,
        $longitude,
        $accuracy,
        $ip
      );
      
      //Send our query to the database.
      $wpdb->query( $prepared );
    }
  }

  /**
   * Register current visitors IP to DB.
   * Also registers their user_type, user agent, and the first page they visited.
   * 
   * @access public
   * @since 1.0.0
   */
  public function record_ip() {  
    global $wpdb;
    //Grab users IP Address, and user agent.
    $ip = $_SERVER['REMOTE_ADDR'];
    //Escape html to prevent any xss through the user agent.
    $agent = esc_html__( $_SERVER['HTTP_USER_AGENT'] );
    $agent = substr( $agent, 0, 200 );
    //Determine if we think that they are a bot.
    $page = sanitize_text_field(
      ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
      "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"
    );

    $type = $this->does_match_pattern( $this->risky_pages, $page ) ? 'Potential Threat' : 'User';
    $type = $this->does_match_pattern( $this->bot_agents, $agent ) ? 'Bot'              : $type;
    $type = current_user_can( 'manage_options' )                   ? 'Admin'            : $type;

    $page = substr( $page, 0, 100 );
    //Create our prepared SQL query.
    $sql = "INSERT IGNORE INTO " . $this->table_name . " (ip, agent, user_type, first_page, timestamp) VALUES (INET_ATON(%s), %s, %s, %s, %d)";
    //Prepare our query.
    $prepared = $wpdb->prepare( $sql, array( $ip, $agent, $type, $page, time() ) );
    //Execute our prepared query.
    $wpdb->query( $prepared );

    //Scan our stored IP's using the infoSNIPER API.
    $this->scan_ips();

    //Grab user_type from database for current ip.
    $sql = "SELECT user_type,
                   hostname
            FROM " . $this->table_name . "
            WHERE ip = INET_ATON(%s)";

    $prepared = $wpdb->prepare( $sql, array( $ip ) );
    $result   = $wpdb->get_row( $prepared, ARRAY_A );
    
    $type     = $result['user_type'];
    $hostname = $result['hostname'];

    $type = $this->does_match_pattern( $this->risky_pages, $page )       ? 'Potential Threat' : $type;
    $type = $this->does_match_pattern( $this->bot_agents, $agent )       ? 'Bot'              : $type;
    $type = $this->does_match_pattern( $this->bot_hostnames, $hostname ) ? 'Bot'              : $type;
    $type = current_user_can( 'manage_options' )                         ? 'Admin'            : $type;

    //Update the last_visit and last_page fields
    $sql = "UPDATE " . $this->table_name . 
           " SET last_visit = %d, " . 
              " last_page = %s,
                user_type = %s " . 
           "WHERE ip = INET_ATON(%s)";
    $prepared = $wpdb->prepare( $sql, array( time(), $page, $type, $ip ) );

    $wpdb->query( $prepared );
  }

  /**
   * Defines a DONOTCACHE constant to prevent caches by cache plugins to avoid leaking personal info.
   * 
   * @access public
   * @since 1.0.0
   */
  public function do_not_cache() {
    if( ! defined( 'DONOTCACHEPAGE' ) ) {
      define( 'DONOTCACHEPAGE', true );
    }
  }

  /**
   * Begin output for a leafletjs map
   * 
   * @access public
   * @since 1.0.0 
   */
  public function init_leaflet_map() {
    $options = get_option( $this->plugin_name );
    $maptype = $options['map-type'];
    ?>
    <div id="leaflet-map-<?php echo $this->map_count ?>" class="leaflet-map">
      <script>
        var <?php echo 'my_map_' . $this->map_count ?> = L.map('leaflet-map-<?php echo $this->map_count ?>', {
          maxZoom: 13,
          minZoom: 2
        }).setView([0, 0], 2);
        var bounds = [];
        var terrainLayer = new L.StamenTileLayer(<?php echo "'" . $maptype . "'"; ?>);
        <?php echo 'my_map_' . $this->map_count ?>.addLayer(terrainLayer);
    <?php
  }

  /**
   * Generates javascript code for creating a new leafletjs marker.
   * 
   * @access public
   * @since 1.0.0
   */
  public function create_leaflet_marker( $latitude, $longitude, $flag, $type, $ip, $timestamp, $provider, 
                                         $city, $state, $country, $accuracy) {
    ?>
    var marker = L.marker(
                       [ <?php echo $latitude . ',' . $longitude ?>]
                    ).addTo(<?php echo 'my_map_' . $this->map_count ?>);
    marker.bindPopup(
      '<img src="' + '<?php echo $flag ?>' + '"> <u>' + '<?php echo $type ?>' + '</u>' +
      '<br>' +
      'IP: ' + '<?php echo $ip ?>' +
      '<br>' +
      'Last Visit: ' + '<?php echo $timestamp ?>' + 
      '<br>' +
      'ISP: ' + '<?php echo $provider ?>' +
      '<br>' +
      '<?php echo $city ?>' + ', ' + '<?php echo $state ?>' + ', ' + '<?php echo $country ?>' 
    );
    bounds.push( [ <?php echo $latitude . ',' . $longitude ?> ] );
    <?php
  }

  /**
   * Generates final code for our leaflet map.
   * 
   * @access public
   * @since 1.0.0
   */
  public function end_leaflet_map() {
    ?>
    bounds = L.latLngBounds( bounds ).pad( 0.1 );
    <?php echo 'my_map_' . $this->map_count ?>.fitBounds( bounds );
    </script>
    </div>
    <?php
  }

  /**
   * Register our shortcode with wordpress
   * 
   * @access public
   * @since 1.0.0
   */
  public function register_shortcodes() {
    add_shortcode( 'is-Map',              array( $this, 'display_map' ) );
    add_shortcode( 'is-DisplayUser',      array( $this, 'display_user' ) );
    add_shortcode( 'is-DisplayUserTable', array( $this, 'display_user_table' ) );
    add_shortcode( 'is-Address',          array( $this, 'display_address' ) );
    add_shortcode( 'is-Provider',         array( $this, 'display_provider' ) );
    add_shortcode( 'is-Hostname',         array( $this, 'display_hostname' ) );
    add_shortcode( 'is-Timezone',         array( $this, 'display_timezone' ) );
    add_shortcode( 'is-City',             array( $this, 'display_city' ) );
    add_shortcode( 'is-State',            array( $this, 'display_state' ) );
    add_shortcode( 'is-Country',          array( $this, 'display_country' ) );
    add_shortcode( 'is-Continent',        array( $this, 'display_continent' ) );
    add_shortcode( 'is-Flag',             array( $this, 'display_countryflag' ) );
    add_shortcode( 'is-Latitude',         array( $this, 'display_latitude' ) );
    add_shortcode( 'is-Longitude',        array( $this, 'display_longitude' ) );
    add_shortcode( 'is-TLD',              array( $this, 'display_countrycode' ) );
    add_shortcode( 'is-DMA',              array( $this, 'display_dmacode' ) );
    add_shortcode( 'is-AreaCode',         array( $this, 'display_areacode'  ));
    add_shortcode( 'is-PostalCode',       array( $this, 'display_postalcode' ) );
    add_shortcode( 'is-GMT',              array( $this, 'display_gmt' ) );
    add_shortcode( 'is-UserAgent',        array( $this, 'display_user_agent' ) );
  }

  /**
   * Print the IP address to the screen.
   * 
   * @return string the ip address of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_address() {
    //Prevent cacheing plugins from cacheing this page.
    $this->do_not_cache();
    return $_SERVER['REMOTE_ADDR'];
  }

  /**
   * Gets a specific attribute from the database of the current user.
   * 
   * @param string $attribute the database field being searched for.
   * 
   * @return string the result of the database query.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_attribute( $attribute ) {
    //Prevent cacheing plugins from cacheing this page.
    $this->do_not_cache();

    global $wpdb;

    $sql = "SELECT $attribute
            FROM " . $this->table_name . "
            WHERE INET_NTOA(ip) = %s";

    $prepared = $wpdb->prepare( $sql, array( $_SERVER['REMOTE_ADDR'] ) );

    $result = $wpdb->get_row( $prepared, ARRAY_A );

    return $result[ $attribute ];
  }

  /**
   * Print the accuracy to the screen.
   * 
   * @return string the accuracy value of the current user.
   *
   * @access public 
   * @since 1.0.0
   */
  public function display_accuracy() {
    $attribute = 'accuracy';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the user agent to the screen.
   * 
   * @return string the user agent of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_user_agent() {
    $attribute = 'agent';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the areacode to the screen.
   * 
   * @return string the areacode of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_areacode() {
    $attribute = 'areacode';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the city to the screen.
   * 
   * @return string the city of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_city() {
    $attribute = 'city';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the continent to the screen.
   * 
   * @return string the continent of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_continent() {
    $attribute = 'continent';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the country to the screen.
   * 
   * @return string the country of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_country() {
    $attribute = 'country';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the countryflag to the screen.
   * 
   * @return string the country flag of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_countryflag() {
    $attribute = 'countryflag';
    return '<img src="' . $this->display_attribute( $attribute ) . '" width=20 height=13>';
  }

  /**
   * Print the countrycode to the screen.
   * 
   * @return string the country code of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_countrycode() {
    $attribute = 'countrycode';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the dmacode to the screen.
   * 
   * @return string the DMA code of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_dmacode() {
    $attribute = 'dmacode';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the gmt offset to the screen.
   * 
   * @return string the gmt offset of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_gmt() {
    $attribute = 'gmtoffset';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the hostname to the screen.
   * 
   * @return string the hostname of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_hostname() {
    $attribute = 'hostname';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the latitude to the screen.
   * 
   * @return string the latitude of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_latitude() {
    $attribute = 'latitude';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the longitude to the screen.
   * 
   * @return string the longitude of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_longitude() {
    $attribute = 'longitude';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the map to the screen.
   * 
   * @param array  $atts shortcode attributes.
   *                     Accepts addresses, latitude, longitude, city,
   *                     country, or state.
   * @param string $tag
   * 
   * @return string html with a leafletjs map that contains the specified users..
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_map( $atts, $tag ) {
    //Prevent cacheing plugins from cacheing this page.
    $this->do_not_cache();
    global $wpdb;
    
    $options = get_option( $this->plugin_name );
    $dnt_threats = $options['dnt-threats'];
    //normalize attributes

    //Merge default attributes with user attributes
    $map_atts = shortcode_atts( array(
      'addresses' => 5,
      'latitude' => NULL,
      'longitude' => NULL,
      'city' => NULL,
      'country' => NULL,
      'state' => NULL
    ), $atts, $tag );

    $mode = "basic";
    if( ! is_null( $map_atts['latitude'] ) && ! is_null( $map_atts['longitude'] ) ) {
      $mode = "latiLong";
    } elseif ( ! is_null( $map_atts['country'] ) ) {
      $mode = "country";
    } elseif ( ! is_null( $map_atts['state'] ) ) {
      $mode = "state";
    } elseif ( ! is_null( $map_atts['city'] ) ) {
      $mode = "city";
    }

    switch( $mode ) {
      case "latiLong":
        $sql = "SELECT INET_NTOA(ip), 
                       provider,
                       timestamp,
                       country,
                       state,
                       city,
                       latitude,
                       longitude,
                       accuracy,
                       countryflag,
                       user_type,
                       last_visit
                FROM " . $this->table_name . "
                WHERE (latitude BETWEEN '" . $map_atts['latitude'] - .5 . "' AND '" . $map_atts['latitude'] + .5 . "')
                  AND (longitude BETWEEN '" . $map_atts['longitude'] - .5 . "' AND '" . $map_atts['longitude'] + .5 . "')
                  AND (user_type = 'User')";
        break;
      case "country":
        $sql = "SELECT INET_NTOA(ip),
                       provider,
                       timestamp,
                       country,
                       state,
                       city,
                       latitude,
                       longitude,
                       accuracy,
                       countryflag,
                       user_type,
                       last_visit
                FROM " . $this->table_name . "
                WHERE country = '" . $map_atts['country'] . "'
                AND (user_type = 'User')";
        break;
      case "state":
        $sql = "SELECT INET_NTOA(ip),
                       provider,
                       timestamp,
                       country,
                       state,
                       city,
                       latitude,
                       longitude,
                       accuracy,
                       countryflag,
                       user_type,
                       last_visit
                FROM " . $this->table_name . "
                WHERE state = '" . $map_atts['state'] . "'
                AND (user_type = 'User')";
        break;
      case "city":
        $sql = "SELECT INET_NTOA(ip),
                       provider,
                       timestamp,
                       country,
                       state,
                       city,
                       latitude,
                       longitude,
                       accuracy,
                       countryflag,
                       user_type,
                       last_visit
                FROM " . $this->table_name . "
                WHERE city = '" . $map_atts['city'] . "'
                AND (user_type = 'User')";
        break;
      default:
        //Construct our query.
        $sql = "SELECT INET_NTOA(ip),
                       provider,
                       timestamp,
                       country,
                       state,
                       city,
                       latitude,
                       longitude,
                       accuracy,
                       countryflag,
                       user_type,
                       last_visit
                FROM " . $this->table_name . "
                WHERE (provider IS NOT NULL) 
                  AND (accuracy < 35)
                  AND (user_type = 'User')
                ORDER BY timestamp DESC 
                LIMIT " . $map_atts['addresses'];
    }
    $results = $wpdb->get_results($sql, ARRAY_A);

    //start output.
    ob_start();
    $this->init_leaflet_map();
    foreach ( $results as $row ) {
      $timezone = get_option( 'timezone_string' );
      date_default_timezone_set( $timezone );
      $timestamp = date( "m/d/y @ h:i A", $row['last_visit'] );
      $this->create_leaflet_marker($row['latitude'], $row['longitude'], $row['countryflag'], $row['user_type'], $_SERVER['REMOTE_ADDR'], $timestamp, 
                                   $row['provider'], $row['city'], $row['state'], $row['country'], $row['accuracy'] );
    }
    $this->end_leaflet_map();
    $this->map_count++;
    return ob_get_clean();
  }

  /**
   * Print the postalcode to the screen.
   * 
   * @return string the postalcode of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_postalcode() {
    $attribute = 'postalcode';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the provider to the screen.
   * 
   * @return string the provider of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_provider() {
    $attribute = 'provider';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the state to the screen.
   * 
   * @return string the state of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_state() {
    $attribute = 'state';
    return $this->display_attribute( $attribute );
  }

  /**
   * Print the timezone to the screen.
   * 
   * @return string the time zone of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_timezone() {
    $attribute = 'timezone';
    return $this->display_attribute( $attribute );
  }

  /**
   * Displays a leafletjs map with the location of the current user.
   * 
   * @return string html for a leafletjs map with the location of the current user.
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_user() {
    //Prevent cacheing plugins from cacheing this page.
    $this->do_not_cache();
    $options = get_option( $this->plugin_name );
    $dnt_threats = $options['dnt-threats'];
    global $wpdb;

    $sql = "SELECT INET_NTOA(ip),
                   timestamp,
                   provider,
                   country,
                   state,
                   city,
                   latitude,
                   longitude,
                   accuracy,
                   countryflag,
                   user_type,
                   last_visit
            FROM " . $this->table_name . "
            WHERE INET_NTOA(ip) = '" . $_SERVER['REMOTE_ADDR'] . "'";

    $row = $wpdb->get_row( $sql, ARRAY_A );

    //Begin HTML output
    ob_start();
    $this->init_leaflet_map();
    if( $dnt_threats ) {
      if( $row['user_type'] === 'Potential Threat' ) {
        $type = 'User';
      } else {
        $type = $row['user_type'];
      }
    } else {
      $type = $row['user_type'];
    }
    $timezone = get_option( 'timezone_string' );
    date_default_timezone_set( $timezone );
    $timestamp = date( "m/d/y @ h:i A", $row['last_visit'] );
    $this->create_leaflet_marker($row['latitude'], $row['longitude'], $row['countryflag'], $type, $_SERVER['REMOTE_ADDR'], $timestamp, 
                                 $row['provider'], $row['city'], $row['state'], $row['country'], $row['accuracy'] );
    $this->end_leaflet_map();
    $this->map_count++;
    return ob_get_clean();
  }

  /**
   * Generates a leafletjs map with the current user's location and a table
   * containing the rest of the data on them.
   * 
   * @return string html for a leafletjs map and a table containign the user's
   *                information
   * 
   * @access public
   * @since 1.0.0
   */
  public function display_user_table() {
    //Prevent cacheing plugins from cacheing this page.
    $this->do_not_cache();
    $options = get_option( $this->plugin_name );
    $dnt_threats = $options['dnt-threats'];
    global $wpdb;

    $sql = "SELECT *
            FROM " . $this->table_name . "
            WHERE INET_NTOA(ip) = '" . $_SERVER['REMOTE_ADDR'] . "'";

    $row = $wpdb->get_row( $sql, ARRAY_A );
    //Begin HTML output
    ob_start();
    $this->init_leaflet_map();
    if( $dnt_threats ) {
      if( $row['user_type'] === 'Potential Threat' ) {
        $type = 'User';
      } else {
        $type = $row['user_type'];
      }
    } else {
      $type = $row['user_type'];
    }
    $timezone = get_option( 'timezone_string' );
    date_default_timezone_set( $timezone );
    $timestamp = date( "m/d/y @ h:i A", $row['last_visit'] );
    $this->create_leaflet_marker($row['latitude'], $row['longitude'], $row['countryflag'], $type, $_SERVER['REMOTE_ADDR'], $timestamp, 
                                 $row['provider'], $row['city'], $row['state'], $row['country'], $row['accuracy'] );
    $this->end_leaflet_map();
    $this->map_count++;
    ?>
    <table width="100%" align="center">
      <tbody>
        <tr>
          <th scope="col"><strong>Field</strong></th>
          <th scope="col"><strong>Your Information</strong></th>
        </tr>  
        <tr>
          <td>IP Address</td>
          <td><?php echo $_SERVER['REMOTE_ADDR']; ?></td>
        </tr>
        <tr>
          <td>User Agent</td>
          <td><?php echo $row['agent']; ?></td>
        </tr>
        <tr>
          <td>Provider</td>
          <td><?php echo $row['provider']; ?></td>
        </tr>
        <tr>
          <td>Hostname</td>
          <td><?php echo $row['hostname']; ?></td>
        </tr>
        <tr>
          <td>Timezone</td>
          <td><?php echo $row['timezone']; ?></td>
        </tr>
        <tr>
          <td>Country Flag</td>
          <td><?php echo '<img src="' . $row['countryflag'] . '" width=20 height=13>'; ?></td>
        </tr>
        <tr>
          <td>City</td>
          <td><?php echo $row['city']; ?></td>
        </tr>
        <tr>
          <td>State(Code)</td>
          <td><?php echo $row['state']; ?></td>
        </tr>
        <tr>
          <td>Country</td>
          <td><?php echo $row['country']; ?></td>
        </tr>
        <tr>
          <td>Continent</td>
          <td><?php echo $row['continent']; ?></td>
        </tr>
        <tr>
          <td>Latitude</td>
          <td><?php echo $row['latitude']; ?></td>
        </tr>
        <tr>
          <td>Longitude</td>
          <td><?php echo $row['longitude']; ?></td>
        </tr>
        <tr>
          <td>Country Code</td>
          <td><?php echo $row['countrycode']; ?></td>
        </tr>
        <tr>
          <td>DMA Code</td>
          <td><?php echo $row['dmacode']; ?></td>
        </tr>
        <tr>
          <td>Area Code</td>
          <td><?php echo $row['areacode']; ?></td>
        </tr>
        <tr>
          <td>Postal Code</td>
          <td><?php echo $row['postalcode']; ?></td>
        </tr>
        <tr>
          <td>GMT Offset</td>
          <td><?php echo $row['gmtoffset']; ?></td>
        </tr>
      </tbody>
    </table>
    <?php
    return ob_get_clean();
  }
}
