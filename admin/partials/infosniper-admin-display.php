<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://connecticallc.com
 * @since      1.0.0
 *
 * @package    Infosniper
 * @subpackage Infosniper/admin/partials
 */
?>

<?php
//Delete DB data
if( isset( $_GET['deleteDB'] ) ) {
  $delete_db = sanitize_key( $_GET['deleteDB'] );
  if( $delete_db === "true" ) {
    $this->delete_database_table();
    //We have to redirect the page or else user will just end up repeatedly clearing DB.
    ?>
    <script>
      var thisurl = window.location.origin 
        + window.location.pathname + "?page=infosniper#top#logs";
      window.location.replace( thisurl );
    </script>
    <?php
  }
}

//Update database
if( isset( $_GET['updateDB'] ) ) {
  $update_db = sanitize_key( $_GET['updateDB'] );
  if( $update_db === "true" ) {
    $this->update_database_table();
  }
}
//Pull options
$options = get_option( $this->plugin_name );
//Put options into variables for ease of use.
$key             = $options['key'];
$selected        = $options['ip-count-timeframe'];
$option_selected = $selected;

if( ! isset( $selected ) || $selected === "" ) {
  $selected = "last-month";
}

$maptype         = $options['map-type'];
//If we dont have a maptype set in the options default to terrain.
if( ! isset( $maptype ) ) {
  $maptype = 'terrain';
}
$delete_options   = $options['delete-options'];
$delete_database  = $options['delete-database'];
$dnt_threats      = $options['dnt-threats'];
$invalid_key      = get_option( 'infosniper_invalid_key' );
$out_of_queries   = get_option( 'infosniper_key_no_queries' );

//Used by out of queries prompt. Disables option to stop scanning while out of queries.
if( isset( $_GET['recharge_key'] ) ) {
  $recharge_key = sanitize_key( $_GET['recharge_key'] );
  $out_of_queries = 0;
  delete_option( 'infosniper_key_no_queries' );
}
//Clear option if recharge key is set.
if ( isset( $recharge_key ) ) {
  delete_option( 'infosniper_key_no_queries' );
} 

//Create Table for second tab.
if( isset( $_GET['count'] ) ){
  $selected = $_GET['count'];
} else if ( isset( $_GET['date'] ) ){
  $selected = $_GET['date'];
}

$tableArray = $this->create_table( $selected );
$tableHTML = $tableArray[0];
$count     = $tableArray[1];

//If we dont have a key set we have no queries, otherwise we pull our queries from their stored option.
if( ! isset( $key ) || $key === "" ) {
  $queries = 0;
} else {
  $queries = floatval( get_option( 'infosniper_queries' ) );
}

$file_path = dirname( plugin_dir_url( __FILE__ ) );

?>

<div class="wrap"><h2 id="infosniper-dummy-h2"></h2></div> <!-- Used to stop alerts from showing up halfway down the page like before -->
<div class="wrap info-sniper">
  <div id="infosniper-header">
      <div id="banner_ads">
          <!-- ADDED JG - 07/03 -->
          <?php echo $this->get_info( 'ads', $key ); ?>
      </div>
      <div id="logo"></div>
  </div>
  <div class="info-sniper-content">
    <div id="info-sniper-tabs" class="info-sniper-tabs-wrapper">
      <div class="left">
        <a id="dashboard-tab" class="info-sniper-tab info-sniper-tab-active" 
           href="#top#dashboard">
          Dashboard
        </a>
        <a id="logs-tab" class="info-sniper-tab" href="#top#logs">User Logs</a>
        <a id="settings-tab" class="info-sniper-tab" href="#top#settings">Settings</a>
        <a id="help-tab" class="info-sniper-tab" href="#top#help">Help</a>
      </div>
      <div class="right">
        <div id="credits-tab" class="info-sniper-tab">
          <span id="infosniper-queries-info">
            Credits remaining: <strong><?php echo number_format( $queries ); ?></strong>
          </span>
          <i class="fas fa-redo is-pntr" onlick="window.location.reload()"></i>
        </div>
      </div>        
    </div>

    <?php
    //If they have had a key and are out of queries display this message.
    if ( $out_of_queries ) {
    ?>
    <div id="infosniper-ads">
      You have run out of queries on your key. You can purchase a new one <a href="http://www.infosniper.net/lb.php" target="_blank">here!</a>
      <br>
      Once you have set your new key in the settings menu please press <a href="<?php echo $_SERVER["REQUEST_URI"]; ?>&recharge_key=TRUE">here.</a>
    </div>
    <?php
    //If they have no key display this message.
    } elseif( $queries === 0 ) {
    ?>
    <div id="infosniper-ads">
      You need a key to use the infosniper Wordpress plugin buy one or get a free trial 
      <a href="http://www.infosniper.net/free-trial/" target="_blank">
        here!
      </a>
    </div>
    <?php
    //If they are starting to run low on queries display this message.
    } elseif( $queries < 500 ) {
    ?>
    <div id="infosniper-ads">
      You are running low on queries. Renew your queries or buy a new key 
      <a href="http://www.infosniper.net/lb.php" target="_blank">
        here!
      </a>
    </div>
    <?php
    }
    ?>

    <div id="dashboard" class="info-sniper-tab-content info-sniper-active">
      <div id="dashboard-buttons">
        <!--Dashboard buttons for changing dashboard legend -->
        <div id="button-styling-wrap">
          <button class="dashboard-button" onclick="setLegend( 'country' );">Countries</button>
          <button class="dashboard-button" onclick="setLegend( 'state'   );">States</button>
          <button class="dashboard-button" onclick="setLegend( 'city'    );">Cities</button>
          <form id="info-sniper-dashboard-dropdown" method="get" action="">
            <input name="page" value="infosniper" style="display:none;"> 
            <select id="dashboard-count" name="count">
              <option value="last-100"        <?php if( $selected === "last-100" )        echo "selected" ?> >
                Last 100 Users
              </option>
              <option value="last-250"        <?php if( $selected === "last-250" )        echo "selected" ?> >
                Last 250 Users
              </option>
              <option value="last-week"       <?php if( $selected === "last-week" )       echo "selected" ?> >
                Last Week
              </option>
              <option value="last-month"      <?php if( $selected === "last-month" )      echo "selected" ?> >
                Last Month
              </options>
              <option value="last-six-months" <?php if( $selected === "last-six-months" ) echo "selected" ?> >
                Last Six Months
              </options>
              <option value="last-year"       <?php if( $selected === "last-year" )       echo "selected" ?> >
                Last Year
              </option>
              <option value="<?php echo $selected ?>" 
                <?php if( $selected !== "last-100" &&
                          $selected !== "last-250" &&
                          $selected !== "last-week" &&
                          $selected !== "last-month" &&
                          $selected !== "last-six-months" &&
                          $selected !== "last-year" ){
                          echo "selected";
                        } else {
                          echo 'style="display:none;"';
                        } ?> >
                Custom
              </option>
            </select>
          </form>
          <form id="info-sniper-dashboard-date" method="get" action="">
            <input name="page" value="infosniper" style="display:none;"> 
            <input id="dashboard-date" type="date" name="date"
            value="<?php
                      if( $selected === 'last-week' ) {
                        $time = strtotime( "-1 week" );
                        echo date( "Y-m-d", $time );
                      } elseif( $selected === 'last-month' ) {
                        $time = strtotime( "-1 month" );
                        echo date( "Y-m-d", $time );
                      } elseif( $selected === 'last-six-months' ) {
                        $time = strtotime( "-6 months" );
                        echo date( "Y-m-d", $time );
                      } elseif( $selected === 'last-year' ) {
                        $time = strtotime( "-1 year" );
                        echo date( "Y-m-d", $time );
                      } elseif( $selected === 'last-100' || $selected === 'last-250' ) {
                        //Do nothing.
                      } else {
                        echo $selected;
                      }
                      ?>">
          </form>
          <form id="info-sniper-dashboard-radio" method="get" action="">
            <?php 
            if( isset( $_GET['user-type'] ) ) {
              $radio_user_type = sanitize_key( $_GET['user-type'] );
            } else {
              $radio_user_type = "all";
            }
            ?>
            <input name="page" value="infosniper" style="display:none;"> 

            <input type="radio" id="user-type-1" name="user-type" value="all" onchange="this.form.submit()" 
            <?php if( $radio_user_type === "all" ) echo "Checked" ?>>
            <label for="user-type-1">All</label>

            <input type="radio" id="user-type-2" name="user-type" value="user" onchange="this.form.submit()"
            <?php if( $radio_user_type === "user" ) echo "Checked" ?>>
            <label for="user-type-2">Users</label>

            <input type="radio" id="user-type-3" name="user-type" value="bot" onchange="this.form.submit()"
            <?php if( $radio_user_type === "bot" ) echo "Checked" ?>>
            <label for="user-type-3">Bots</label>

            <input type="radio" id="user-type-4" name="user-type" value="potential threat" onchange="this.form.submit()"
            <?php if( $radio_user_type === "potential threat" ) echo "Checked" ?>>
            <label for="user-type-4">Potential Threats</label>

            <input type="radio" id="user-type-5" name="user-type" value="Admin" onchange="this.form.submit()"
            <?php if( $radio_user_type === "Admin" ) echo "Checked" ?>>
            <label for="user-type-5">Admins</label>
          </form>
        </div>
      </div>
      <div id="info-sniper-data-div">
        <div id="leaflet-map">
          <div id="progress"><div id="progress-bar"></div></div> 
        </div>
        <div class="chart-container" style="position: relative; height:400px;">
          <canvas id="mychart"></canvas>
        </div>
      </div>
      <?php 
      echo $this->generate_dashboard_table( 'geo', 'Country' );
      echo $this->generate_dashboard_table( 'page', 'Page' );
      ?>
    </div>

    <div id="logs" class="info-sniper-tab-content">
      <div id="dashboard-buttons-2">
        <div id="button-styling-wrap">
          <form id="info-sniper-logs-dropdown" method="get" action="">
            <input name="page" value="infosniper" style="display:none;"> 
            <select id="logs-count" name="count">
              <option value="last-100"        <?php if( $selected === "last-100" )        echo "selected" ?> >
                Last 100 Users
              </option>
              <option value="last-250"        <?php if( $selected === "last-250" )        echo "selected" ?> >
                Last 250 Users
              </option>
              <option value="last-week"       <?php if( $selected === "last-week" )       echo "selected" ?> >
                Last Week
              </option>
              <option value="last-month"      <?php if( $selected === "last-month" )      echo "selected" ?> >
                Last Month
              </options>
              <option value="last-six-months" <?php if( $selected === "last-six-months" ) echo "selected" ?> >
                Last Six Months
              </options>
              <option value="last-year"       <?php if( $selected === "last-year" )       echo "selected" ?> >
                Last Year
              </option>
              <option value="<?php echo $selected ?>" 
                <?php if( $selected !== "last-100" &&
                          $selected !== "last-250" &&
                          $selected !== "last-week" &&
                          $selected !== "last-month" &&
                          $selected !== "last-six-months" &&
                          $selected !== "last-year" ){
                            echo "selected";
                          } else {
                            echo 'style="display:none;"';
                          } ?> >
                Custom
              </option>
            </select>
          </form>
          <form id="info-sniper-logs-date" method="get" action="">
            <input name="page" value="infosniper" style="display:none;"> 
            <input id= "logs-date" type="date" name="date" 
            value="<?php
                      if( $selected === 'last-week' ) {
                        $time = strtotime( "-1 week" );
                        echo date( "Y-m-d", $time );
                      } elseif( $selected === 'last-month' ) {
                        $time = strtotime( "-1 month" );
                        echo date( "Y-m-d", $time );
                      } elseif( $selected === 'last-six-months' ) {
                        $time = strtotime( "-6 months" );
                        echo date( "Y-m-d", $time );
                      } elseif( $selected === 'last-year' ) {
                        $time = strtotime( "-1 year" );
                        echo date( "Y-m-d", $time );
                      } elseif( $selected === 'last-100' || $selected === 'last-250' ) {
                        //Do nothing.
                      } else {
                        echo $selected;
                      }
                      ?>">
          </form>
          <button class="dashboard-button" onclick="csvExport();">Export to CSV</button>
          <button class="dashboard-button clear-wrap-toggle" onclick="displayLogClear();">Clear Logs</button>
          <div id="log-clear-wrap">
            <div id="clear-message">
              Are you sure you want to clear your logs?
              <br>
              This cannot be undone. 
              <div id="clear-buttons">
                <button class="dashboard-button clear-wrap-toggle" onclick="displayLogClear();">Cancel</button>
                <form id="infosniper-delete-db-form" method="get" action="">
                  <input name="page" value="infosniper" style="display:none;">
                  <input name="deleteDB" value="true" style="display:none;">
                  <button class="dashboard-button" onclick="this.form.submit();">Clear Logs</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="info-table" class="user-log-container">
        <?php
        echo $tableHTML;
        ?>
        <div id="span-container">
          <div>
            <i id="left-full" class="fas fa-angle-double-left"></i>
            <i id="left-arrow" class="fas fa-angle-left"></i>
          </div>
          <div id="span-destination">
          </div>
          <div>
            <i id="right-arrow" class="fas fa-angle-right"></i>
            <i id="right-full" class="fas fa-angle-double-right"></i>
          </div>
        </div>
      </div>
    </div>

    <div id="help" class="info-sniper-tab-content">
      <div class="row">
        <div class="col-md-6">
          <h2>What do these logs mean?</h2>
          <ul>
            <li>IP Address   - The IP Address the user is visiting from</li>
            <li>Agent        - The user agent provided by the user, it tells us what browser they are on and if they are a bot</li>
            <li>Type         - Whether or not we believe the connecting user is a real user or a bot</li>
            <li>Hostname     - The hostname the IP Address is associated with</li>
            <li>Provider     - The ISP the connecting user is using</li>
            <li>Country      - The country the user is connected from</li>
            <li>Country Code - The country code for the country the user is connected from</li>
            <li>Flag         - The flag of the country the user connected from</li>
            <li>State        - The state/province the users IP Address is associated with</li>
            <li>City         - The city the users IP Address is associated with</li>
            <li>Area Code    - The area code where the IP Address is located</li>
            <li>Postal Code  - The postal code where the IP Address is located</li>
            <li>DMA Code     - The Designated Marketing Area of the area in which the IP Address is located</li>
            <li>Time Zone    - The timezone where the IP is located</li>
            <li>GMT Off Set  - Hours offset of Greenwich Mean Time</li>
            <li>Continent    - The Continent the IP address is associated with</li>
            <li>Latitude     - Latitude of the IP Address</li>
            <li>Longitude    - Longitude of the IP Address</li>
            <li>Accuracy     - Radius of our confidence circle in miles.</li>
          </ul>
          <h2>What are the circles on the map?</h2>
          <p>The circle on the map represents the area we believe the IP Address to have originated from. 
            It is impossible to say exactly where it originated but we are able to say that it is confidently within that radius.
            It is also important to note that a marker without a circle means that the confidence radius is higher than 35 miles and is not shown.
            These markers are not incredibly accurate and should only be understood as a regional location.
          <h2>How do I use shortcodes?</h2>
          <p>Our plugin comes with a variety of Shortcodes for use on your website. These include:
          <ul>
            <li>[is-Map]              - Displays a map of the last 5 users by default. Has several options</li>
            <li>[is-DisplayUser]      - Displays a map showing the current user.</li>
            <li>[is-DisplayUserTable] - Displays a map showing the current user along with a table showing info from their user log.</li>
            <li>[is-Address]          - Outputs the IP address of the current user.</li>
            <li>[is-Provider]         - Outputs the Provider of the current user.</li>
            <li>[is-Hostname]         - Outputs the hostname of the current user.</li>
            <li>[is-Timezone]         - Outputs the timezone of the current user.</li>
            <li>[is-City]             - Outputs the city of the current user.</li>
            <li>[is-State]            - Outputs the state of the current user.</li>
            <li>[is-Country]          - Outputs the country of the current user.</li>
            <li>[is-Continent]        - Outputs the continent of the current user.</li>
            <li>[is-Flag]             - Outputs the flag of the current user.</li>
            <li>[is-Latitude]         - Outputs the latitude of the current user.</li>
            <li>[is-Longitude]        - Outputs the longitude of the current user.</li>
            <li>[is-TLD]              - Outputs the TLD of the current user.</li>
            <li>[is-DMA]              - Outputs the DMA of the current user.</li>
            <li>[is-AreaCode]         - Outputs the area code of the current user.</li>
            <li>[is-PostalCode]       - Outputs the postal code of the current user.</li>
            <li>[is-GMT]              - Outputs the gmt offset of the current user.</li>
          </ul>
        </div>
        <div class="col-md-6">
        </div>
      </div>
    </div>

    <div id="settings" class="info-sniper-tab-content">
      <div class="row">
        <div class="col-md-6">
          <?php
          if( get_option( 'infosniper_db_v1_0_0' ) ) {
          ?>
          <h2>Your database needs to be updated. Please update here!
          <form method="get" action="">
            <input name="page" value="infosniper" style="display:none;">
            <input name="updateDB" value="true" style="display:none;">
            <?php submit_button( 'Update Database', 'primary', 'submit-db-update', TRUE ); ?>     
          </form>
          <?php
          }
          ?>
          <form id="info-sniper-form" method="post" name="info_snipe_options" action="options.php#top#settings">
          <?php
          //Put our settings into wordpress.
          settings_fields( $this->plugin_name );
          do_settings_sections( $this->plugin_name );
          ?>
            <table class="form-table">
              <tbody>
                <tr class="api-label">
                  <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-key">API Key</label>
                  </th>
                  <td>
                    <input 
                      type="password"
                      id="<?php echo $this->plugin_name; ?>-key" 
                      name="<?php echo $this->plugin_name; ?>[key]" 
                      value="<?php if( ! empty( $key ) ) echo $key; ?>"/>
                    <div id="key-visibility-toggle" onclick="keyVisibleToggle( '<?php echo $this->plugin_name; ?>-key' )">
                      <i id="key-visible" class="far fa-eye is-pntr" style="display: none;"></i>
                      <i id="key-hidden"  class="far fa-eye-slash is-pntr"></i>
                    </div>
                    <div id="key-validate">
                      <div id="key-check" class="key-status" >
                        <a id="key-check-btn" class="btn btn-black outline small">Validate Key</a>
                      </div>
                      <div id="key-valid" class="key-status" style="display: none;">
                        Valid Key
                        <i class="fas fa-check"></i>
                      </div>
                      <div id="key-invalid" class="key-status" style="display: none;">
                        Invalid Key
                        <i class="fas fa-times"></i>
                      </div>
                    </div>
                    <?php 
                    if( $invalid_key ) { 
                      echo "<p> Invalid Key"
                    ;} 
                    ?>
                    </td>
                    </tr>
                    <tr class="api-label-desc">
                        <td colspan="2">
                    <div id="<?php echo $this->plugin_name; ?>-key-description" class="description"> 
                      <em id="freetrial-question">Don't have a key?</em>
                      <a href="https://www.infosniper.net/free-trial.php" target="_blank" 
                         class="btn btn-black outline small">
                        Start Free Trial
                      </a> 
                      <a href="https://www.infosniper.net/lb.php" target="_blank" 
                         class="btn btn-black small">
                        Purchase Credits
                      </a>
                    </div>
                  </td>
                </tr>
                <tr>
                  <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-amount">IP's to Scan</label>
                  </th>
                  <td>
                    <select id="options-count" name="<?php echo $this->plugin_name; ?>[ip-count-timeframe]">
                      <option value="last-100"        <?php if( $option_selected === "last-100" )        echo "selected" ?> >
                        Last 100 Users
                      </option>
                      <option value="last-250"        <?php if( $option_selected === "last-250" )        echo "selected" ?> >
                        Last 250 Users
                      </option>
                      <option value="last-week"       <?php if( $option_selected === "last-week")       echo "selected" ?> >
                        Last Week
                      </option>
                      <option value="last-month"      <?php if( $option_selected === "last-month")      echo "selected" ?> >
                        Last Month
                      </options>
                      <option value="last-six-months" <?php if( $option_selected === "last-six-months") echo "selected" ?> >
                        Last Six Months
                      </options>
                      <option value="last-year"       <?php if( $option_selected === "last-year")       echo "selected" ?> >
                        Last Year
                      </option>
                      <option value="<?php echo $option_selected ?>" 
                        <?php if( $selected !== "last-100" &&
                                  $selected !== "last-250" &&
                                  $selected !== "last-week" &&
                                  $selected !== "last-month" &&
                                  $selected !== "last-six-months" &&
                                  $selected !== "last-year" ){
                                    echo "selected";
                                  } else {
                                    echo 'style="display:none;"';
                                  } ?> >
                        Custom
                      </option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-map-type">Map Art</label>
                  </th>
                  <td>
                    <select id="infosniper-map-type-selector" name="<?php echo $this->plugin_name; ?>[map-type]">
                      <option value="terrain"    <?php if( $maptype === "terrain")    echo "selected" ?>>
                        Terrain
                      </option>
                      <option value="toner"      <?php if( $maptype === "toner")      echo "selected" ?>>
                        Toner
                      </option>
                      <option value="watercolor" <?php if( $maptype === "watercolor") echo "selected" ?>>
                        Watercolor
                      </option>
                    </select>
                    <div id="terrain-preview" class="infosniper-tile-preview">
                      <img src="<?php echo $file_path ?>/css/images/terrain.png" 
                           alt="Terrain Tiles">
                    </div>
                    <div id="toner-preview" class="infosniper-tile-preview">
                      <img src="<?php echo $file_path ?>/css/images/toner.png" 
                           alt="Toner Tiles">
                    </div>
                    <div id="watercolor-preview" class="infosniper-tile-preview">
                      <img src="<?php echo $file_path ?>/css/images/watercolor.jpg" 
                           alt="Watercolor Tiles">
                    </div>
                    <div id="infosniper-map-type-picture" width="256px" height="256px"></div>
                  </td>
                </tr>
                <tr>
                  <th scope="row">
                    <label for="<?php echo $this->plugin_name;?>-delete-options">
                      Reset options on uninstall?
                    </label>
                  </th>
                  <td>
                    <input type="checkbox" name="<?php echo $this->plugin_name; ?>[delete-options]" 
                           value="true" <?php if( $delete_options ) echo "checked"; ?>/>
                  </td>
                </tr>
                <tr>
                  <th scope="row">
                    <label for="<?php echo $this->plugin_name;?>-delete-database">
                      Clear user logs on uninstall?
                    </label>
                  </th>
                  <td>
                    <input type="checkbox" name="<?php echo $this->plugin_name; ?>[delete-database]" 
                           value="true" <?php if( $delete_database ) echo "checked"; ?>/>
                  </td>
                </tr>
                <tr>
                  <th scope="row">
                    <label for="<?php echo $this->plugin_name; ?>-dnt-threats">
                      Dont track potential threats?
                    </label> 
                  </th>
                  <td>
                    <input type="checkbox" name="<?php echo $this->plugin_name; ?>[dnt-threats]"
                           value="true" <?php if( $dnt_threats ) echo "checked"; ?>/>
                  </td>
                </tr>
              </tbody>
            </table>
            <?php submit_button( 'Save all changes', 'primary', 'submit', TRUE ); ?>
          </form>
        </div>
        <div class="col-md-6">
          <?php echo $this->get_info( 'info', $key ); ?>
        </div>
      </div>
    </div>
  </div>
  <script>
    //Define new leafletJS icons for our own use.
    var currentLogPage;
    var myLogMap, myLogMapIndex, myLogMapRemoved;
    var userIcon  = createLeafletMarkerIcon( '<?php echo $file_path ?>', 'marker-icon-user' );
    var infilIcon = createLeafletMarkerIcon( '<?php echo $file_path ?>', 'marker-icon-infil' );
    var botIcon   = createLeafletMarkerIcon( '<?php echo $file_path ?>', 'marker-icon-bot' );
    var adminIcon = createLeafletMarkerIcon( '<?php echo $file_path ?>', 'marker-icon-admin' );
    var ipCount = <?php echo $count ?>;
    var selectedUserType = "<?php echo $radio_user_type ?>"
    //Initialize leaflet map.
    var my_map = L.map( 'leaflet-map', {
      maxZoom: 13,
      minZoom: 2
    }).setView([0, 0], 2);
    var bounds = [];
    var maptype = <?php echo "'" . $maptype . "'"; ?>;
    createMap( my_map, <?php echo "'" . $maptype . "'"; ?> );
    var geotype = "country";
    var globalCountry;
    var globalCountryUsers;
    var countries = [];
    generateCountryArray( ipCount );
    var ctx = document.getElementById( 'mychart' ).getContext( '2d' );
    var myChart = createChart( ctx );
    populatePageTable();
    setLegend( "country" );
  </script>
</div>
