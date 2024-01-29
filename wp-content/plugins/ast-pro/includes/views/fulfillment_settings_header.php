<div class="zorem-layout__header">
	<h1 class="page_heading">
		<a href="<?php echo esc_url( $parent_url ); ?>" class="link decoration"><?php esc_html_e( $parent_label, 'ast-pro' ); ?></a> <span class="dashicons dashicons-arrow-right-alt2"></span> <?php esc_html_e( $heading, 'ast-pro' ); ?>
	</h1>				
	<img class="zorem-layout__header-logo" src="<?php echo esc_url( ast_pro()->plugin_dir_url() ); ?>assets/images/ast-logo.png">		
</div>
<div class="zorem-layout__sub-header">
	<h1 class="fulfillment_page_heading"><?php esc_html_e( $heading, 'ast-pro' ); ?></h1>
	<?php include 'activity_panel.php'; ?>		
</div>
