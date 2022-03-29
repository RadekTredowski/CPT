<?
$args = array(
  'post_type' => 'booking',
  'posts_per_page' => 3
);
$booking = new WP_Query( $args );
 
if( $booking->have_posts() ){
  while( $booking->have_posts() ) {
    $booking->the_post();
    the_title(); 
  }
}