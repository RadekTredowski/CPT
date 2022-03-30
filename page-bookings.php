<?php /**
 * @version 1.0
 * @package KopernikBookingSystem/Main
 * @author Waldek&Radek
 *
 * Plugin Name: Kopernik Booking System
 * Description: System do rezerwacji
 * Version: 1.0.0
 * Author: Radosław Trędowski & Waldemar Graban
 * Licence GPLv3
 * Licence URI: https://www.gnui.org/lincenes/gppl-3.0.html
 * Requires at least: 5.3
 * Tested up to: 5.7
 * Requires PHP: 7.2
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly



/**
 * Show Content
 * Update Content
 * Define Slug
 * Define where to show
 */
class wtrtkbs_Page_Bookings extends wtrtkbs_Page_Structure {
        
    private $listing_table;
    
    public function __construct() {
        
        parent::__construct();

        // Redefine TAGs Names,  becasue 'tab' slug already used in the system  for definition  of active toolbar.
        $this->tags['tab']    = 'view_mode';
        $this->tags['subtab'] = 'bottom_nav';
    }
    
    
    public function in_page() {
        return 'wtrtkbs';
    }

    function booking_post_type() {
    $labels = array(
        'name'                => 'Rezerwacje',
        'singular_name'       => 'Rezerwacje',
        'menu_name'           => 'Rezerwacje',
        'all_items'           => 'Wszystkie rezerwacje',
        'view_item'           => 'Zobacz rezerwacje',
        'add_new_item'        => 'Dodaj rezerwację',
        'add_new'             => 'Dodaj nową',
        'edit_item'           => 'Edytuj rezerwcje',
        'update_item'         => 'Aktualizuj',
        'search_items'        => 'Szukaj rezerwacji',
        'not_found'           => 'Nie znaleziono',
        'not_found_in_trash'  => 'Nie znaleziono'
    ); 
    $args = array(
        'label' => 'booking',
        'rewrite' => array(
            'slug' => 'rezerwacja'
        ),
        'description'         => 'Rezerwacja',
        'labels'              => $labels,
        'supports'            => array( 'title', 'thumbnail'),
        'taxonomies'          => array(),
        'hierarchical'        => false,
        'public'              => true, 
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 6,
        'menu_icon'           => 'dashicons-calendar-alt',
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
    );
    register_post_type( 'job', $args );
} 
add_action( 'init', 'job_post_type', 0 );
        
       
        
        return $labels;        
    }


    public function content() {                
        
        wtrtkbs_check_request_paramters();                                         //Cleanup REQUEST parameters        //FixIn:6.2.1.4
        
        do_action( 'wtrtkbs_hook_booking_page_header', 'booking' );                // Define Notices Section and show some static messages, if needed.
        
        if ( ! wtrtkbs_is_mu_user_can_be_here( 'activated_user' ) ) return false;  // Check if MU user activated,  otherwise show Warning message.

        if ( ! wtrtkbs_set_default_resource_to__get() ) return false;                  // Define default booking resources for $_GET  and  check if booking resource belong to user.
        
        ?><span class="wpdevelop"><?php                                         // BS UI CSS Class
        
        make_bk_action( 'wtrtkbs_write_content_for_modals' );                      // Content for modal windows
        
        wtrtkbs_js_for_bookings_page();                                            // JavaScript functions
        
        wtrtkbs_welcome_panel();                                                   // Welcome Panel (links)        



        /* Executed in \core\admin\page-bookings.php on hook wtrtkbs_define_nav_tabs
         * 
         * Get saved filters set, (if its not set in request yet). Like "tab"  & "view_mode" and set to $_REQUEST  
         * If we have "saved" filter-set, then LOAD and set it to REQUEST, if REQUEST was not setting previously 
         * It skip "wh_booking_type" param, load it in next  code line     
         */
        //wtrtkbs_set_default_saved_params_to_request_for_booking_listing( 'default' );          

        make_bk_action( 'wtrtkbs_check_request_param__wh_booking_type' );          // Setting $_REQUEST['wh_booking_type'] - remove empty and duplicates ID of booking resources in this list

        make_bk_action( 'check_for_resources_of_notsuperadmin_in_booking_listing' );    // If "Regular User",  then filter resources in $_REQUEST['wh_booking_type'] to show only resources of this user

        //   T o o l b a r s   /////////////////////////////////////////////////
        wtrtkbs_bookings_toolbar();                                                

     
        ?><div class="clear" style="height:40px;"></div><?php
//debuge($_REQUEST);
        $args = wtrtkbs_get_clean_paramas_from_request_for_booking_listing();      // Get safy PARAMS from REQUEST
        echo '<textarea id="bk_request_params" style="display:none;">', serialize( $args ), '</textarea>';
//debuge($args);        
        ////////////////////////////////////////////////////////////////////////
        // B O O K I N G    L I S T I N G    P A G E     
        ////////////////////////////////////////////////////////////////////////
        $bk_listing = wtrtkbs_get_bookings_objects( $args );                       // Get Bookings structure
        $bookings           = $bk_listing[ 'bookings' ];
        $booking_types      = $bk_listing[ 'resources' ];
        $bookings_count     = $bk_listing[ 'bookings_count' ];
        $page_num           = $bk_listing[ 'page_num' ];
        $page_items_count   = $bk_listing[ 'count_per_page' ];
                
//debuge( '$args, $_REQUEST, $bk_listing', $args, $_REQUEST, $bk_listing );
        
        $this->listing_table = new wtrtkbs_Booking_Listing_Table( $bookings, $booking_types );
        $this->listing_table->show();
        

        wtrtkbs_show_pagination($bookings_count, $page_num, $page_items_count);   // Show Pagination  

        wtrtkbs_show_booking_footer();           
        
        ?></span><!-- wpdevelop class --><?php 
        
 

    }

}
add_action('wtrtkbs_menu_created', array( new wtrtkbs_Page_Bookings() , '__construct') );    // Executed after creation of Menu



/** Trick here to  overload default REQUST parameters before page is loading */
function wtrtkbs_define_listing_page_parameters( $page_tag ) {
    
    // $page_tag - here can be all defined in plugin menu pages
    // So  we need to  check activated page. By default its inside of $_GET['page'], 
    
    // Execute it only  for Booking Listing & Timeline admin pages.
    //if (  ( isset( $_GET[ 'page' ] ) ) && ( $_GET[ 'page' ] == 'wtrtkbs' )  ) {                

    if ( wtrtkbs_is_bookings_page() ) {                                            // We are inside of this page. Menu item selected. 
        // Get saved filters set, (if its not set in request yet), like "tab"  & "view_mode" and overload $_REQUEST    
        wtrtkbs_set_default_saved_params_to_request_for_booking_listing( 'default' );          
    }
}
// We are set  9  to  execute early  than hook in wtrtkbs_Admin_Menus
add_action('wtrtkbs_define_nav_tabs', 'wtrtkbs_define_listing_page_parameters', 1  );             // This Hook fire in the class wtrtkbs_Admin_Menus for showing page content of specific menu                
