<?php

/**
 * Plugin Name: HTML WP - Menu Addon
 * Text Domain: html-wp-menu
 * Domain Path: /html-wp-menu/
 * Description: A menu addon of HTML WP plugin.Create Dynamic menu from HTML.
 * Author: Krishnendu Paul
 * Version: 1.0.0
 * Copyright 2022 Krishnendu Paul (email :krshpaul@gmail.com)
*/
if ( ! defined( 'ABSPATH' ) ) {
   die( 'Invalid request.' );
}
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( ! function_exists( 'wp_handle_upload' ) )
{
require_once( ABSPATH . 'wp-admin/includes/file.php' );
}
require_once( ABSPATH . 'wp-admin/includes/image.php' );


     
 



 if ( ! class_exists( 'HTMLWP_MENU_Plugin' ) ) :
class HTMLWP_MENU_Plugin {

   /**
    * Constructor.
    */
   public function __construct() {
      // register_activation_hook( __FILE__, array( $this , 'activate' ) );
   }

   public static function activate() {
     // core();
  //  wp_die('jhjhj');
       //add_action( 'init', 'core' );
    update_option('plugin_status', 'active');
       
   }
   /**
    * Intialize action after plugin loaded.
    */
   public static function init_actions() {
     
      
      add_action('admin_menu', 'HTMLWP_MENU_Plugin::setup_menu');
      if(!class_exists('HTMLWP_Plugin'))
      {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        update_option('plugin_status', 'inactive');
        add_action('admin_notices', array( 'HTMLWP_MENU_Plugin', 'HTML_admin_notices' ) );
       // exit();
      }
 if ( is_user_logged_in() ) {
         if ( current_user_can( 'edit_others_posts' ) ) {
    add_action('htmlwpaddoninput', 'HTMLWP_MENU_Plugin::htmlwpaddoninput');
    add_action('wp_ajax_htmlwpmenu_save', 'HTMLWP_MENU_Plugin::htmlwpmenu_save');
    add_action('htmlwpaddonfunc', 'HTMLWP_MENU_Plugin::htmlwpaddonfunc');
}
}
    if((get_option('theme-menuboots')!='')):
    //bootstrap menu support
    add_filter( 'nav_menu_css_class' , 'HTMLWP_MENU_Plugin::special_nav_class' , 10 , 2);
    add_filter( 'nav_menu_css_class', 'HTMLWP_MENU_Plugin::nav_menu_css_class', 5, 4 );
    add_filter( 'nav_menu_link_attributes', 'HTMLWP_MENU_Plugin::nav_menu_link_attributes', 5, 4 );
    add_filter( 'nav_menu_submenu_css_class', 'HTMLWP_MENU_Plugin::nav_menu_submenu_css_class', 5, 3 );
      endif;


    }
    public static function htmlwpaddoninput() {
       ?>
        <div class="formbold-mb-5">
        <input
          type="checkbox"
          name="menuyes"
          id="menuyes"
          class="formbold-form-input addchk"
        />Do you want to import menu from html?
        </div>
        <?php if((get_option('theme-header-name')=='')): ?>
        
        
        <script type="text/javascript">
            jQuery('#menuyes').click(function(){
                if(jQuery('#menuyes:checked').length==1)
                {
                    alert('You have to update header menu or footer menu id atleast to use this option.');
                jQuery('#submit').attr('disabled','disabled');
                }else{
                
                 jQuery('#submit').removeAttr('disabled');
                }
            });
        </script>
    <?php 
     else: ?>
        <script type="text/javascript">
            jQuery('#menuyes').click(function(){
                // alert('You have to update header menu or footer menu id atleast to use this option.');
                jQuery('#submit').removeAttr('disabled');
            });
        </script>
        <?php
     endif; ?>
       <?php
    }
    public static function htmlwpaddonfunc() {
        if(isset($_POST['menuyes']) && isset( $_POST['action'] )
         && isset( $_POST['nonce'] )
         && 'htmlwp_upload_file' === $_POST['action']
         && wp_verify_nonce( $_POST['nonce'], 'htmlwp_upload_file_action' ))
        {
            $header_menu_option=get_option('theme-header-name');
            $footer_menu_option=get_option('theme-footer-name');
            global $hd;
            global $ft;
            global $hd_dest;
            global $foot_dest;
            global $theme_name;
            // create header menu
            if($header_menu_option!='')
            {
           
             preg_match('~<div id="'.$header_menu_option.'"[^>]*>(.*?)</div>~si', $hd, $ul_hd);
            // print_r($ul_hd);
             if($ul_hd!=null)
             {
             

                $menuname = 'Header Menu-'.$theme_name .'-'.rand(000,10000);
                $hmenulocation = 'primary';
                // Does the menu exist already?
                $menu_exists = wp_get_nav_menu_object( $menuname );

                // If it doesn't exist, let's create it.
                if( !$menu_exists){
                    $menu_id = wp_create_nav_menu($menuname);

                    $dom = new DOMDocument();
                    $dom->loadHTML($ul_hd[1]);
                    $xp = new DOMXPath($dom);
                    $postalCodesList = $dom->getElementsByTagName('ul');
                    $class_ul=$postalCodesList->item(0)->getAttribute('class');
                    $xdoc = new DOMXPath($dom);
           
                    $menu_created=0;
                    foreach ($postalCodesList->item(0)->getElementsByTagName('li') as $postalCodesList) {
                      //  echo $postalCodesList->nodeValue.'<br />'; 
                       $mainAnc = $xp->query('a', $postalCodesList)[0];  
                       $href = $mainAnc->getAttribute('href');    
                   
                      
                       $args = array(
                            'meta_query' => array(
                                array(
                                    'key' => '_html_tpl',
                                    'value' => trim($href)
                                )
                            ),
                            'post_type' => 'page',
                            'posts_per_page' => -1
                        );
                        // print_r($args);
                       if(is_array(get_posts($args))){
                         $posts = get_posts($args)[0]->ID;
                         //print_r($posts);
                         if($posts!=''){
                     $parent_item = wp_update_nav_menu_item($menu_id, 0, array(
                            'menu-item-title' => str_replace('(current)', '', $postalCodesList->nodeValue),
                            'menu-item-object-id' => $posts,
                            'menu-item-object' => 'page',
                            'menu-item-status' => 'publish',
                            'menu-item-type' => 'post_type',
                        ));  
                     $menu_created++;
                        }
                        }
                        else
                        {
                           
                        } 
                        if($href=='' || $href=='#')
                        {
                            $parent_item = wp_update_nav_menu_item($menu_id, 0, array(
                            'menu-item-title' => str_replace('(current)', '', $postalCodesList->nodeValue),
                            'menu-item-classes' => '',
                            'menu-item-url' => $href, 
                            'menu-item-status' => 'publish'
                        ));  
                         $menu_created++; 
                        }
                      // sub menu (future update)
                      //   $lis = $postalCodesList->getElementsByTagName('li');   
                     //   if($lis->length>0)
                     //   {
                     //    $secUls = $postalCodesList->getElementsByTagName('ul');
                     //   // print_r($ul2);
                     //     // foreach ($ul2->item(0)->getElementsByTagName('li') as $secUls) {
                     //     // //$lis->nodeValue.'<br />'; 
                     //     // }
                     //    $sub_menu_created=0;
                     //    foreach ($secUls->item(0)->getElementsByTagName('li') as $secUl) {
                     //           $subAnc = $xp->query('a', $secUl)[0];  
                     //           $hrefsub = $subAnc->getAttribute('href'); 
                     //           $args1 = array(
                     //                'meta_query' => array(
                     //                    array(
                     //                        'key' => '_html_tpl',
                     //                        'value' => trim($hrefsub)
                     //                    )
                     //                ),
                     //                'post_type' => 'page',
                     //                'posts_per_page' => -1
                     //            );
                     //            // print_r($args1);
                     //           if(is_array(get_posts($args1))){
                     //             $posts1 = get_posts($args1)[0]->ID;
                     //             //print_r($posts1);
                     //             if($posts1!=''){
                     //                if($parent_item!=''){
                     //         $sub_item = wp_update_nav_menu_item($menu_id, 0, array(
                     //                'menu-item-title' => $secUl->nodeValue,
                     //                'menu-item-object-id' => $posts1,
                     //                'menu-item-object' => 'page',
                     //                'menu-item-status' => 'publish',
                     //                'menu-item-type' => 'post_type',
                     //                'menu-item-parent-id' => $parent_item
                     //            ));  
                     //        }
                     //         $sub_menu_created++;
                     //    }
                     //    }   
                     //     if($href=='' || $href=='#')
                     //    {
                     //        $parent_item = wp_update_nav_menu_item($menu_id, 0, array(
                     //        'menu-item-title' => str_replace('(current)', '', $postalCodesList->nodeValue),
                     //        'menu-item-classes' => '',
                     //        'menu-item-url' => $href, 
                     //        'menu-item-status' => 'publish'
                     //    ));  
                     // $menu_created++; 
                     //    }
                     //    }
                     //   }     
                    }
                    if($menu_created!=0)
                    {
                        $class_ul_of='';
                        if($class_ul!='')
                        {
                            $class_ul_of=$class_ul;
                        }
                      $new_hd=str_replace($ul_hd[0], '<?php 
$args = array(
    \'menu_class\' => '."'".$class_ul_of."'".',        
    \'menu\' => '."'".$hmenulocation."'".'
);
wp_nav_menu( $args ); 
?>', $hd);
                       
            //  header create
            $fp=fopen($hd_dest,'w');
            fwrite($fp, urldecode(htmlspecialchars_decode($new_hd)));
            fclose($fp);
                    }
                    // if( !has_nav_menu( $hmenulocation ) ){
                    $locations = get_theme_mod('nav_menu_locations');
                    $locations[$hmenulocation] = $menu_id;
                    set_theme_mod( 'nav_menu_locations', $locations );
                    // }
                }
               
             }
             else
             {

             }
           
            }
            if($footer_menu_option!='')
            {
                preg_match('~<div id="'.$footer_menu_option.'"[^>]*>(.*?)</div>~si', $ft, $ul_ft);
            // print_r($ul_ft);
             if($ul_ft!=null)
             {
             

                $menuname = 'Footer Menu-'.$theme_name .'-'.rand(000,10000);
                $hmenulocation = 'footer';
                // Does the menu exist already?
                $menu_exists = wp_get_nav_menu_object( $menuname );

                // If it doesn't exist, let's create it.
                if( !$menu_exists){
                    $menu_id = wp_create_nav_menu($menuname);

                    $dom = new DOMDocument();
                    $dom->loadHTML($ul_ft[1]);
                    $xp = new DOMXPath($dom);
                    $postalCodesList = $dom->getElementsByTagName('ul');
                    $class_ul=$postalCodesList->item(0)->getAttribute('class');
                    $menu_created=0;
                    foreach ($postalCodesList->item(0)->getElementsByTagName('li') as $postalCodesList) {
                        
                       $mainAnc = $xp->query('a', $postalCodesList)[0];  
                       $href = $mainAnc->getAttribute('href');    
                    
                      
                       $args = array(
                            'meta_query' => array(
                                array(
                                    'key' => '_html_tpl',
                                    'value' => trim($href)
                                )
                            ),
                            'post_type' => 'page',
                            'posts_per_page' => -1
                        );
                        // print_r($args);
                       if(is_array(get_posts($args))){
                         $posts = get_posts($args)[0]->ID;
                         //print_r($posts);
                         if($posts!=''){
                     wp_update_nav_menu_item($menu_id, 0, array(
                            'menu-item-title' => str_replace('(current)', '', $postalCodesList->nodeValue),
                            'menu-item-object-id' => $posts,
                            'menu-item-object' => 'page',
                            'menu-item-status' => 'publish',
                            'menu-item-type' => 'post_type',
                        ));  
                     $menu_created++;
                        }
                        } 

                        if($href=='' || $href=='#')
                        {
                            $parent_item = wp_update_nav_menu_item($menu_id, 0, array(
                            'menu-item-title' => str_replace('(current)', '', $postalCodesList->nodeValue),
                            'menu-item-classes' => '',
                            'menu-item-url' => $href, 
                            'menu-item-status' => 'publish'
                        ));  
                         $menu_created++; 
                        }
                               
                    }
                    if($menu_created!=0)
                    {
                        $class_ul_of='';
                        if($class_ul!='')
                        {
                            $class_ul_of=$class_ul;
                        }
                      $new_ft=str_replace($ul_ft[0], '<?php 
$args = array(
    \'menu_class\' => '."'".$class_ul_of."'".',        
    \'menu\' => '."'".$hmenulocation."'".'
);
wp_nav_menu( $args ); 
?>', $ft);
                       
            //  footer create
            $fp=fopen($ft_dest,'w');
            fwrite($fp, urldecode(htmlspecialchars_decode($new_ft)));
            fclose($fp);
                    }
                    // if( !has_nav_menu( $hmenulocation ) ){
                    $locations = get_theme_mod('nav_menu_locations');
                    $locations[$hmenulocation] = $menu_id;
                    set_theme_mod( 'nav_menu_locations', $locations );
                    // }
                }
                
             }
             else
             {

             }
            }
        }
    }
    public static function setup_menu() {
    add_menu_page('HTML WP Menu', 'HTML WP Menu', 'manage_options', 'HTMLWPMENU-setings', 'HTMLWP_MENU_Plugin::setting_page', 'dashicons-menu');
    }
    public static function HTML_admin_notices() {
      echo esc_html('<div class="error"><p>HTML WP Menu Plugin will work only with HTML WP Plugin. </p></div>');
    }
    public static function deactivate() {
        update_option('plugin_status', 'inactive');   
    }
    public static function setting_page()
    {
      include( plugin_dir_path( __FILE__ ) . 'template/page_setting.php');
    }
    public static function htmlwpmenu_save(){

 if ( isset( $_POST['action'] )
         && isset( $_POST['nonce'] )
         && 'htmlwpmenu_save' === $_POST['action']
         && wp_verify_nonce( $_POST['nonce'], 'htmlwp_menu_upload_file_action' ) ) 
    {

        



    $theme_header_name=sanitize_text_field($_POST['theme-header-name']);
    $theme_footer_name=sanitize_text_field($_POST['theme-footer-name']);
    $menuboots=sanitize_text_field($_POST['menuboots']);
    $option_header=update_option('theme-header-name', $theme_header_name);
    $option_footer=update_option('theme-footer-name', $theme_footer_name);
    if(isset($menuboots))
    {
        $menuboots_option=update_option('theme-menuboots', $menuboots);
    }
    


    if(isset($option_header) || isset($option_footer) )
    {
         echo json_encode(array('success'=>true,'message'=>esc_html('Option\'s saved')));
    }

    

    wp_die();
}
    }



/**
 * Bootstrap Nav
 *
 * Check if nav menu has a `nav` or `navbar-nav` CSS class.
 *
 * @param stdClass $nav_menu An object of wp_nav_menu() arguments.
 *
 * @return bool
 */


public static function special_nav_class ($classes, $item) {
  if (in_array('current-menu-item', $classes) ){
    $classes[] = ' active ';
  }
  return $classes;
}
public static function is_nav_menu_nav( $nav_menu )
{
    return preg_match( '/(^| )(nav|navbar-nav)( |$)/', $nav_menu->menu_class ); 
}

/**
 * CSS Class
 *
 * Add custom CSS classes to the nav menu item.
 *
 * @param array    $classes   Array of the CSS classes.
 * @param WP_Post  $item      The current menu item.
 * @param stdClass $nav_menu  An object of wp_nav_menu() arguments.
 * @param int      $depth     Depth of menu item.
 *
 * @return array
 */
public static function nav_menu_css_class( $classes, $item, $nav_menu, $depth )
{
    if ( ! self::is_nav_menu_nav( $nav_menu ) ) 
    {
        return $classes;
    }

    if ( $depth == 0 ) 
    {
        if ( in_array( 'menu-item-has-children', $item->classes ) ) 
        {
            $classes[] = 'dropdown';
        }

        else
        {
            $classes[] = 'nav-item';
        }
    }

    return $classes;
}



/**
 * Link Attributes
 *
 * Alter nav menu item link attributes.
 *
 * @param array    $atts      The HTML attributes applied to the menu item's <a> element.
 * @param WP_Post  $item      The current menu item.
 * @param stdClass $nav_menu  An object of wp_nav_menu() arguments.
 * @param int      $depth     Depth of menu item.
 *
 * @return array
 */
public static function nav_menu_link_attributes( $atts, $item, $nav_menu, $depth )
{
    if ( ! self::is_nav_menu_nav( $nav_menu ) ) 
    {
        return $atts;
    }

    // Make sure 'class' attribute is set.
    if ( ! isset( $atts['class'] ) ) $atts['class'] = '';

    // Nav link
    if ( $depth == 0 ) 
    {
        $atts['class'] .= ' nav-link';

        // Dropdown
        if ( in_array( 'menu-item-has-children', $item->classes ) ) 
        {
            $atts['href'] = '#';
            $atts['class'] .= ' dropdown-toggle';
            $atts['data-toggle']   = 'dropdown';
            $atts['aria-haspopup'] = 'true';
            $atts['aria-expanded'] = 'false';
        }
    }

    // Dropdown item
    else if ( $depth == 1 )
    {
        $atts['class'] .= ' dropdown-item';
    }

    // Active
    if ( $item->current || $item->current_item_ancestor || $item->current_item_parent ) 
    {
        $atts['class'] .= ' active';
    }

    // Sanitize 'class' attribute.
    $atts['class'] = trim( $atts['class'] );

    return $atts;
}



/**
 * Submenu CSS Class
 *
 * Add custom CSS classes to the nav menu submenu.
 *
 * @param array    $classes   Array of the CSS classes that are applied to the menu <ul> element.
 * @param stdClass $nav_menu  An object of wp_nav_menu() arguments.
 * @param int      $depth     Depth of menu item.
 *
 * @return array
 */
public static function nav_menu_submenu_css_class( $classes, $nav_menu, $depth )
{
    if ( self::is_nav_menu_nav( $nav_menu ) ) 
    {
        $classes[] = 'dropdown-menu';
    }

    return $classes;
}






}
add_action('plugins_loaded', array( 'HTMLWP_MENU_Plugin', 'init_actions' ) );
//add_action( 'activate_plugin', array( 'HTMLWP_MENU_Plugin', 'activate' ) );
register_activation_hook(__FILE__, 'HTMLWP_MENU_Plugin::activate' );
register_deactivation_hook(__FILE__, 'HTMLWP_MENU_Plugin::deactivate');

endif;
