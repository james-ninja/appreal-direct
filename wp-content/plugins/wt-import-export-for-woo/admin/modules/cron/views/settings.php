<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<style type="text/css">
.wt_iew_cron_settings_page{ padding:15px; }
.cron_list_tb td, .cron_list_tb th{ text-align:center; vertical-align:middle; }
.wt_iew_delete_cron{ cursor:pointer; }

.wt_iew_cron_current_time{float:right; width:auto; font-size:12px; font-weight:normal;}
.wt_iew_cron_current_time span{ display:inline-block; width:85px; }
.cron_list_tb td a{ cursor:pointer; }
</style>
<div class="wt_iew_cron_settings_page">
	<h2 class="wp-heading-inline"><?php _e('Scheduled actions');?> <div class="wt_iew_cron_current_time"><b><?php _e('Current server time:');?></b> <span>--:--:-- --</span></div></h2>
	<p>
		<?php _e('Lists all the scheduled processes for import and export.'); ?><br />
		<?php _e('Disable or delete unwanted scheduled actions to reduce server load and reduce the chances for failure of actively scheduled actions.'); ?>
	</p>
	<?php
	Wt_Import_Export_For_Woo_Cron::list_cron();
	?>
</div>