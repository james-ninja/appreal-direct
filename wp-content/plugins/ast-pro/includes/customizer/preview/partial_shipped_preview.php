<?php 
// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}
?>
	<head>	
		<meta charset="<?php bloginfo('charset'); ?>" />
		<meta name="viewport" content="width=device-width" />
		<style type="text/css" id="ast_designer_custom_css">.woocommerce-store-notice.demo_store, .mfp-hide {display: none;}</style>
	</head>
	<style>
	.ast_preview_body #template_container, .ast_preview_body #template_header_image, .ast_preview_body #template_header, .ast_preview_body #template_body, .ast_preview_body #template_footer {
		width:100% !important;
		min-width:320px !important;
		max-width:600px;
	}
	</style>
	<body class="ast_preview_body">
		<div id="overlay"></div>
		<div id="ast_preview_wrapper" style="display: block;">
			<?php $this->preview_partial_shipped_email(); ?>
		</div>
		<?php
		do_action( 'woomail_footer' );
		wp_footer(); 
		?>
	</body>
</html>
