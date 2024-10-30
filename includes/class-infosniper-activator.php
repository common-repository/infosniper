<?php

/**
 * Fired during plugin activation
 *
 * @link       https://connecticallc.com
 * @since      1.0.0
 *
 * @package    Infosniper
 * @subpackage Infosniper/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Infosniper
 * @subpackage Infosniper/includes
 * @author     ConnecticaLLC <info@connecticallc.com>
 */
class Infosniper_Activator {

  /**
   * Fired during plugin activation.
   *
   * This function defines all code necessary to run during the plugin's activation.
   *
   * @since    1.0.0
   */
  public static function activate() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . "infoSniperAddresses";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
      ip          INT(10) UNSIGNED NOT NULL UNIQUE,
      agent       VARCHAR(200),
      user_type   ENUM('User', 'Bot', 'Admin', 'Potential Threat'),
      timestamp   INT(11),
      last_visit  INT(11),
      hostname    VARCHAR(100),
      provider    VARCHAR(100),
      country     VARCHAR(50),
      countrycode VARCHAR(50),
      countryflag VARCHAR(50), 
      state       CHAR(3),
      city        VARCHAR(50),
      areacode    CHAR(10),
      postalcode  CHAR(10),
      dmacode     CHAR(10),
      timezone    VARCHAR(50),
      gmtoffset   TINYINT,
      continent   ENUM('Asia', 'Africa', 'North America', 'South America', 'Antartica', 'Europe', 'Australia'),
      latitude    FLOAT(10, 6),
      longitude   FLOAT(10, 6),
      accuracy    SMALLINT,
      first_page  VARCHAR(100),
      last_page   VARCHAR(100)
    ) $charset_collate;";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option('infosniper_do_activation_redirect', true);
    add_option('infosniper_db_v1_0_1', true);
  }

}
