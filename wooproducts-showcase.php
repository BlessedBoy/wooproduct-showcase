<?php

/*
Plugin Name: WooProducts Showcase
Plugin URI: 
Description: Showcase your WooCommerce products in a nice carousel using a simple <code>shortcode</code>. It is very simple in this version, and doesn't allows too much customization. It is based in the excellent FlexSlider plugin (http://www.woothemes.com/flexslider/).
Version: 1.0
Author: Reydel Leon
Author URI: Your URL
License: GPL2

Copyright 2013  Reydel Leon  (email : nick.lucas.xxi@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Check if WooCommerce is active
 **/
if ( !class_exists('Woocommerce') and !class_exists('WooProductShowcase') ) {

    define('RWPS_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );
    define('RWPS_NAME', "WooProducts Showcase");
    define ("RWPS_VERSION", "1.0");

    require_once('admin/admin-screen.php');
    include_once('third_party/updater.php');

    wp_enqueue_script('flexslider', RWPS_PATH.'jquery.flexslider-min.js', array('jquery'));
    wp_enqueue_style('flexslider_css', RWPS_PATH.'flexslider.css');

    class WooProductShowcase {

        public function __construct() {
            // called just before the woocommerce template functions are included
//            add_action( 'init', array( $this, 'include_template_functions' ), 20 );

            // called only after woocommerce has finished loading
//            add_action( 'woocommerce_init', array( $this, 'woocommerce_loaded' ) );

            // called after all plugins have loaded
//            add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

            // indicates we are running the admin
//            if ( is_admin() ) {
//                 ...
//            }

            // indicates we are being served over ssl
//            if ( is_ssl() ) {
//                 ...
//            }

            // take care of anything else that needs to be done immediately upon plugin instantiation, here in the constructor
            add_action('wp_head', array($this, 'rwps_script'));
            add_shortcode('rwps_slider', array($this, 'rwps_insert_slider'));

            init_updater();
        }

        function init_updater() {
            if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
                $config = array(
                    'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
                    'proper_folder_name' => 'Wooproduct-showcase', // this is the name of the folder your plugin lives in
                    'api_url' => 'https://api.github.com/repos/blessedBoy/wooproduct-showcase', // the github API url of your github repo
                    'raw_url' => 'https://raw.github.com/blessedBoy/wooproduct-showcase/master', // the github raw url of your github repo
                    'github_url' => 'https://github.com/blessedBoy/wooproduct-showcase', // the github url of your github repo
                    'zip_url' => 'https://github.com/blessedBoy/wooproduct-showcase/zipball/master', // the zip url of the github repo
                    'sslverify' => true, // wether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
                    'requires' => '3.0', // which version of WordPress does your plugin require?
                    'tested' => '3.6.1', // which version of WordPress is your plugin tested up to?
                    'readme' => 'README.md', // which file to use as the readme for the version number
                    'access_token' => '', // Access private repositories by authorizing under Appearance > Github Updates when this example plugin is installed
                );
                new WP_GitHub_Updater($config);
            }
        }

            function rwps_script(){

                print '<script type="text/javascript" charset="utf-8">
          jQuery(window).load(function() {
            jQuery(\'.flexslider\').flexslider({
                animation: "'.synved_option_get('wooproduct_Showcase', 'animation').'",
                minItems: '.synved_option_get('wooproduct_Showcase', 'minItems').',
                maxItems: '.synved_option_get('wooproduct_Showcase', 'maxItems').',
                itemWidth: '.synved_option_get('wooproduct_Showcase', 'itemWidth').',
                itemMargin: '.synved_option_get('wooproduct_Showcase', 'itemMargin').',
                pauseOnHover: '.synved_option_get('wooproduct_Showcase', 'pauseOnHover').',
                move: '.synved_option_get('wooproduct_Showcase', 'move').',
                controlNav: '.synved_option_get('wooproduct_Showcase', 'controlNav').',
                prevText: "'.synved_option_get('wooproduct_Showcase', 'prevText').'",
                nextText: "'.synved_option_get('wooproduct_Showcase', 'nextText').'",
                controlsContainer: ".flex-container"
            });
          });
        </script>';

            }

            function rwps_get_slider() {

                $slider= '<div class="flexslider"><ul class="slides">';

                $rwps_query= "post_type=product";
                query_posts($rwps_query);


                if (have_posts()) : while (have_posts()) : the_post();
                    $img= get_the_post_thumbnail( $post->ID, 'large' );
                    $price = get_post_meta( get_the_ID(), '_regular_price', true);

                    $slider.='<li>
                <a href="'.get_permalink($product_id).'">'.$img.'</a>
                <div style="text-align: center;">'.get_the_title($product_id).'</div>
                </li>';

                endwhile; endif; wp_reset_query();
                $slider.= '</ul>
	</div>';

                return $slider;

            }

            /**add the shortcode for the slider for use in editor**/
            function rwps_insert_slider($atts, $content=null) {

                return  $this->rwps_get_slider();

            }

            /**add template tag- for use in themes**/
            function rwps_slider(){

                print $this->rwps_get_slider();
            }
        }

        // finally instantiate our plugin class and add it to the set of globals
    $GLOBALS['wooproduct_showcase'] = $rwps = new WooProductShowcase();
    }