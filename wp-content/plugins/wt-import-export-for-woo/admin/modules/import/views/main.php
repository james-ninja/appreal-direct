<?php
/**
 * Main view file of import section
 *
 * @link            
 *
 * @package  Wt_Import_Export_For_Woo
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<?php
do_action('wt_iew_importer_before_head');
?>
<style type="text/css">
.wt_iew_import_step{ display:none; }
.wt_iew_import_step_loader{ width:100%; height:400px; text-align:center; line-height:400px; font-size:14px; }
.wt_iew_import_step_main{ float:left; box-sizing:border-box; padding:15px; padding-bottom:0px; width:95%; margin:30px 2.5%; background:#fff; box-shadow:0px 2px 2px #ccc; border:solid 1px #efefef; }
.wt_iew_import_main{ padding:20px 0px; }
</style>
<div class="wt_iew_view_log wt_iew_popup" style="text-align:left">
	<div class="wt_iew_popup_hd">
		<span style="line-height:40px;" class="dashicons dashicons-media-text"></span>
		<span class="wt_iew_popup_hd_label"><?php _e('History Details');?></span>
		<div class="wt_iew_popup_close">X</div>
	</div>
    <div class="wt_iew_log_container" style="padding:25px;">
		
	</div>
</div>
<?php
Wt_Iew_IE_Helper::debug_panel($this->module_base);
?>
<?php include WT_IEW_PLUGIN_PATH."/admin/views/_save_template_popup.php"; ?>

<h2 class="wt_iew_page_hd"><?php _e('Import'); ?><span class="wt_iew_post_type_name"></span></h2>

<?php
	if($requested_rerun_id>0 && $this->rerun_id==0)
	{
		?>
		<div class="wt_iew_warn wt_iew_rerun_warn">
			<?php _e('Unable to handle Re-Run request.');?>
		</div>
		<?php
	}
?>

<div class="wt_iew_loader_info_box"></div>
<div class="wt_iew_overlayed_loader"></div>

<div class="wt_iew_import_step_main">
	<?php
	foreach($this->steps as $stepk=>$stepv)
	{
		?>
		<div class="wt_iew_import_step wt_iew_import_step_<?php echo $stepk;?>" data-loaded="0"></div>
		<?php
	}
	?>
</div>
<script type="text/javascript">
/* external modules can hook */
function wt_iew_importer_validate(action, action_type, is_previous_step)
{
	var is_continue=true;
	<?php
	do_action('wt_iew_importer_validate');
	?>
	return is_continue;
}
function wt_iew_importer_reset_form_data()
{
	<?php
	do_action('wt_iew_importer_reset_form_data');
	?>
}
</script>