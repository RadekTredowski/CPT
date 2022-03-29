<?
function job_post_type() {
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
