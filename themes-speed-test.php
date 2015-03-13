<?php
/**
 *
 * @link              http://www.wpspeedster.com
 * @since             1.0
 * @package           Themes_Speed_Test
 *
 * @wordpress-plugin
 * Plugin Name:       Themes Speed Test
 * Plugin URI:        http://blog.wpspeedster.com/themes-speed-test-plugin/
 * Description:       This plugin shows Google PageSpeed scores for the themes installed from the official WordPress theme repository.
 * Version:           1.0
 * Author:            Csaba Kissi
 * Author URI:        http://www.wpspeedster.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       themes-speed-test
 * Domain Path:       /languages
 */

register_activation_hook(__FILE__, 'activate_themes_speed_test');
register_deactivation_hook(__FILE__, 'deactivate_themes_speed_test');

function activate_themes_speed_test()
{
}

function deactivate_themes_speed_test()
{
}

add_action('admin_menu', 'add_appearance_menu');

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'theme_speed_test_plugin_settings_link' );

function theme_speed_test_plugin_settings_link($links) {
    $settings_link = '<a href="themes.php?page=themes_speed_test">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}


function add_appearance_menu()
{
    $menu = add_theme_page('Themes Speed Test', 'Themes Speed Test', 'manage_options', 'themes_speed_test', 'TSTlistThemes');
    add_action('admin_footer-'.$menu, 'add_action_javascript' );
}

function add_action_javascript() { ?>
    <script type="text/javascript" >
    	jQuery(document).ready(function($) {
            $('.error').html();
            $('.more-details').hide();
            $(".spinner").show();
            var slugs=[];
            $('.theme-name').each(function(i, obj) {
                slugs.push($(obj).attr('id'));
            });
    		var data = {
    			'slugs': slugs
    		};
            $.ajax({
                    url: 'http://api.wpspeedster.com/v1/themes/info',
                    type: "POST",
                    crossDomain: true,
                    data: data,
                    dataType: "json",
                    success:function(result){
                        $.each( result, function( key, value ) {
                            $(".spinner").hide();
                            $('#scores-'+key).html('Home: ' + value.split("|")[0] + '% | Post: '+ value.split("|")[1] + '%');
                            $('#'+key+'-action').show();
                        });
                    },
                    error:function(xhr,status,error){
                        $(".spinner").hide();
                        $('.error').html('<p><?php _e( 'Could not fetch PageSpeed data', '' ); ?></p>');
                        $('.error').show();
                    }
                });
    	});
    </script>
<?php
}

function TSTListThemes()
{
    $themes = wp_get_themes();
    ?>
    <div class="wrap">
        <h2 style="margin-bottom: 10px">Themes Speed Test Scores
            <span class="title-count theme-count"><?php echo count($themes); ?></span>
        </h2>
        <div class="error hidden">
        </div>
        <div id="welcome-panel" class="welcome-panel">
            <div class="spinner"></div>
            <p class="about-description">We’ve tested over 2500 Free Wordpress Themes for Speed</p>

            <div class="welcome-panel-column-container">
                <div class="welcome-panel-column">
                    <a class="button button-primary button-hero load-customize hide-if-no-customize"
                       href="http://www.wpspeedster.com/top/overall">Overall Top Scorers</a>
                    <label for="wp-filter-search-input" class="screen-reader-text">Search Theme Speed Test</label>
                    <form style="margin-top: 10px" method="post" action="http://www.wpspeedster.com/search">
                        <p>
                        	<label class="screen-reader-text" for="post-search-input">Search</label>
                        	<input type="search" id="post-search-input" name="term" value="" placeholder="Search Theme Speed Test" style="width:220px;">
                        	<input type="submit" name="" id="search-submit" class="button" value="Search">
                        </p>
                    </form>
                </div>
                <div class="welcome-panel-column">
                    <h4>Top Scorers: Desktop</h4>
                    <ul>
                        <li><a href="http://www.wpspeedster.com/top/home-desktop"
                               class="welcome-icon dashicons-admin-home">Tested Blogs' Home Page</a></li>
                        <li><a href="http://www.wpspeedster.com/top/post-desktop"
                               class="welcome-icon welcome-write-blog">Tested Blogs' Sample Post Page</a></li>
                    </ul>
                </div>
                <div class="welcome-panel-column welcome-panel-last">
                    <h4>Top Scorers: Mobile</h4>
                    <ul>
                        <li><a href="http://www.wpspeedster.com/top/home-mobile"
                               class="welcome-icon dashicons-admin-home">Tested Blogs' Home Page</a></li>
                        <li><a href="http://www.wpspeedster.com/top/post-mobile"
                               class="welcome-icon welcome-write-blog">Tested Blogs' Sample Post Page</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="theme-browser rendered">
            <div class="themes">
                <?php foreach ($themes as $slug => $theme) { ?>
                    <div class="theme active" tabindex="0" aria-describedby="<?php echo $slug ?>-action <?php echo $slug ?>-name">

                        <div class="theme-screenshot">
                            <img src="<?php echo get_theme_root_uri() . '/' . $slug ?>/screenshot.png" alt="">
                        </div>

                        <a href="http://www.wpspeedster.com/speedtest/<?php echo $slug; ?>"><span class="more-details"
                                                                                                  id="<?php echo $slug; ?>-action">Speed Test Details</span></a>

                        <div class="theme-author">By <?php echo $theme->display('Author', false) ?></div>


                        <h3 class="theme-name" id="<?php echo $slug ?>">
                            <span><?php echo $theme->display('Name', false); ?></span>
                        </h3>


                        <div class="theme-actions">
                            <span class="button button-secondary activate" id="scores-<?php echo $slug ?>">Theme not tested</span>
                        </div>

                    </div>
                <?php } ?>
            </div>
        </div>
        <br class="clear">
    </div>
<?php
}