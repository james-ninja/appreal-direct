<?php get_header();?>

<?php 
if(is_user_logged_in()){
    get_template_part( 'templates-parts/frontpage-logged-in' );
}else{
    get_template_part( 'templates-parts/frontpage-logged-out' );
}
?>

<?php get_footer(); ?>  