<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

	<?php ob_start(); ?>
	<?php echo Loader::element('permission/help');?>
	<?php $help = ob_get_contents(); ?>
	<?php ob_end_clean(); ?>
		<form method="post" action="<?php echo $view->action('save')?>" id="ccm-permission-list-form">
	
	<?php echo Loader::helper('validation/token')->output('save_permissions')?>
	<div class="ccm-pane-body">
	<?php
	$tp = new TaskPermission();
	if ($tp->canAccessTaskPermissions()) { ?>	
		<?php Loader::element('permission/lists/conversation', array('conversation' => null))?>
	<?php } else { ?>
		<p><?php echo t('You cannot access conversation permissions.')?></p>
	<?php } ?>
	</div>
	<div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" value="<?php echo t('Save')?>" class="btn btn-primary pull-right"><?php echo t('Save')?> <i class="icon-ok-sign icon-white"></i></button>
        </div>
    </div>
	</form>
