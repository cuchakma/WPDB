<?php
/**
 * Plugin Name: WPDB
 * Plugin URI:  www.facebook.com
 * Description: plugin to learn about WPDB
 * Version:     1.0.0
 * Author:      Cupid Chakma
 * Author URI:  www.facebook.com
 * Text Domain: wpdb
 * Domain Path: /languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package     WPDB
 * @author      Cupid Chakma
 * @copyright   2021 wpDeveloper
 * @license     GPL-2.0+
 *
 */
define('DBDEMO_DB_VERSION', "1.3");

function wpdb_init() {
    global $wpdb, $charset_collate;
    $table_name = $wpdb->prefix.'persons';
    $sql = "CREATE TABLE ".$table_name." (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(250),
        email VARCHAR(250),
        PRIMARY KEY (id)
    ) ". $charset_collate .";";
    require_once (ABSPATH.'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    if( empty( get_option( 'dbdemo_db_version' ) ) ) {
        add_option('dbdemo_db_version', DBDEMO_DB_VERSION);
    }

    if( get_option('dbdemo_db_version') != DBDEMO_DB_VERSION ) {
        $sql = "CREATE TABLE ".$table_name." (
            id INT NOT NULL AUTO_INCREMENT,
            name VARCHAR(250),
            email VARCHAR(250),
            age INT,
            PRIMARY KEY (id)
        ) ". $charset_collate .";";
        update_option('dbdemo_db_version', DBDEMO_DB_VERSION);
        dbDelta($sql);
    }
    dbdemo_load_data();
    
}

register_activation_hook(__FILE__, 'wpdb_init');

function dbdemo_load_data() {
    global $wpdb;
    $table_name = $wpdb->prefix.'persons';
    $wpdb->insert($table_name, array(
        'name'  => 'John Doe',
        'email' => 'john@doe.com'
    ));
}

function debdemo_flush_data() {
    global $wpdb;
    $table_name = $wpdb->prefix.'persons';
    $query = "TRUNCATE TABLE {$table_name} ";
    $wpdb->query($query);
}

register_deactivation_hook(__FILE__, "debdemo_flush_data");

function main_menu_page() {
    //wp_enqueue_style('fonts_icomoon');
    global $wpdb, $pagenow;
    if( $pagenow === 'admin.php' ) {
        $style_id = array(
            'fonts_icomoon',
            'owl_carousel',
            'bootstrap',
            'main_styles'
        );

        $script_id = array(
            'jquery-min',
            'popper',
            'bootstrap-js',
            'main_script'

        );

        foreach($style_id as $id){
            wp_enqueue_style($id);
        }
        
        foreach($script_id as $id) {
            wp_enqueue_script($script_id);
        }

    }

    $query = $wpdb->get_results("Select * from {$wpdb->prefix}persons");
    if( $query ) {
    foreach($query as $object) {
        ?>
            <div class="content">
                <div class="container">
                <h2 class="mb-5">Data Tables</h2>
                <div class="table-responsive custom-table-responsive">
                    <table class="table custom-table">
                            <thead>
                                <tr>  
                                    <th scope="col">
                                        <label class="control control--checkbox">
                                        <input type="checkbox"  class="js-check-all"/>
                                        <div class="control__indicator"></div>
                                        </label>
                                    </th>
                                    <th scope="col">ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr scope="row">
                                    <th scope="row">
                                        <label class="control control--checkbox">
                                        <input type="checkbox"/>
                                        <div class="control__indicator"></div>
                                        </label>
                                    </th>
                                    <td>
                                        <?php echo $object->id; ?>
                                    </td>
                                    <td><a href="#"><?php echo $object->name; ?></a></td>
                                    <td>
                                        <?php echo $object->email; ?>
                                        <small class="d-block">Far far away, behind the word mountains</small>
                                    </td>
                                </tr>
                            </tbody>
                    </table>
                </div>
            </div>
        <?php
    }
    }
}

function add_new_menu() {
    add_menu_page('DBDEMO', 'DB Demo', 'manage_options', 'dbdemo', 'main_menu_page');
}

add_action('admin_menu', 'add_new_menu');

function load_admin_side_styles() {
   /**
    * Register All Styles 
    */
   wp_register_style('fonts_icomoon', plugins_url('', __FILE__) . '/template/fonts/icomoon/style.css', false, rand());
   wp_register_style('owl_carousel',  plugins_url('', __FILE__) . '/template/css/owl.carousel.min.css', false, rand());
   wp_register_style('bootstrap',   plugins_url('', __FILE__) . '/template/css/bootstrap.min.css', false, rand());
   wp_register_style('main_styles', plugins_url('', __FILE__) . '/template/css/style.css', false, rand());

   /**
    * Register All Scripts
    */
   wp_register_script('jquery-min', plugins_url('', __FILE__) . '/template/js/jquery-3.3.1.min.js', array('jquery'), rand(), true);
   wp_register_script('popper', plugins_url('', __FILE__) . '/template/js/popper.min.js', false, rand(), true);
   wp_register_script('bootstrap-js', plugins_url('', __FILE__) . '/template/js/bootstrap.min.js', false, rand(), true);
   wp_register_script('main_script', plugins_url('', __FILE__) . '/template/js/main.js', false, rand(), true);
}

add_action('admin_enqueue_scripts', 'load_admin_side_styles');