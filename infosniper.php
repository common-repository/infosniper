<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://connecticallc.com
 * @since             1.0.0
 * @package           Infosniper
 *
 * @wordpress-plugin
 * Plugin Name:       infoSNIPER
 * Plugin URI:        https://infosniper.net
 * Description:       Track incoming IP Addresses and display information about them using infoSNIPER.net
 * Version:           1.3.0
 * Author:            ConnecticaLLC
 * Author URI:        https://connecticallc.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       infosniper
 * Domain Path:       /languages
 *
 * This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License along
 *  with this program; if not, write to the Free Software Foundation, Inc.,
 *  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'INFOSNIPER_VERSION', '1.3.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-infosniper-activator.php
 */
function activate_infosniper() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-infosniper-activator.php';
  Infosniper_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-infosniper-deactivator.php
 */
function deactivate_infosniper() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-infosniper-deactivator.php';
  Infosniper_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_infosniper' );
register_deactivation_hook( __FILE__, 'deactivate_infosniper' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-infosniper.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_infosniper() {

  $plugin = new Infosniper();
  $plugin->run();

}
run_infosniper();
