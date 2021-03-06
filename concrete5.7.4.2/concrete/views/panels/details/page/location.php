<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<section class="ccm-ui">
	<header><?php echo t('Location')?></header>
	<form method="post" action="<?php echo $controller->action('submit')?>" data-dialog-form="location" data-panel-detail-form="location">

		<?php echo Loader::helper('concrete/ui/help')->display('panel', '/page/location')?>
        <input type="hidden" name="cParentID" value="<?php echo $cParentID?>" />

        <?php if (!isset($sitemap) && $sitemap == false) { ?>
            <div style="min-height: 140px">
                <?php if ($c->isPageDraft()) { ?>
                    <p class="lead"><?php echo t('Where will this page live on the site?')?></p>
                <?php } else { ?>
                    <p class="lead"><?php echo t('Where does this page live on the site?')?></p>
                <?php } ?>

                <div id="ccm-panel-detail-location-display"></div>

                <button class="btn btn-info"type="button" name="location"><?php echo t('Choose Location')?></button>

            </div>
		<hr/>
        <?php } ?>

	<?php if ($c->isGeneratedCollection() || $c->isPageDraft()) { ?>

		<p class="lead"><?php echo t('Current Canonical URL')?></p>
		<div class="breadcrumb">
			<?php if ($c->isPageDraft()) { ?>
				<?php echo t('None. Pages do not have canonical URLs until they are published.')?>
			<?php } else { ?>
				<?php echo Loader::helper('navigation')->getLinkToCollection($c, true)?>
			<?php } ?>
		</div>

	<?php } else { ?>

		<p class="lead"><?php echo t('URLs to this Page')?></p>

		<div>
		<table class="table table-striped ccm-page-panel-detail-location-paths">
			<thead>
			<tr>
				<th></th>
				<th><?php echo t('Canonical')?></th>
				<th style="width: 100%"><?php echo t('Path')?></th>
				<th></th>
			</tr>
			</thead>
			<tbody></tbody>
		</table>

		<button class="btn btn-info pull-right" type="button" data-action="add-url"><?php echo t('Add URL')?></button>

		<br/><br/>
		<span class="help-block"><?php echo t('Note: Additional page paths are not versioned. They will be available immediately.')?></span>

		</div>

	<?php } ?>

	<?php if (isset($sitemap) && $sitemap) { ?>
		<input type="hidden" name="sitemap" value="1" />
	<?php } ?>

	</form>
	<div class="ccm-panel-detail-form-actions dialog-buttons">
		<button class="pull-left btn btn-default" type="button" data-dialog-action="cancel" data-panel-detail-action="cancel"><?php echo t('Cancel')?></button>
		<button class="pull-right btn btn-success" type="button" data-dialog-action="submit" data-panel-detail-action="submit"><?php echo t('Save Changes')?></button>
	</div>

</section>

<style type="text/css">
	table.ccm-page-panel-detail-location-paths td {
		vertical-align: middle !important;
	}
</style>

<script type="text/template" class="breadcrumb">
	<% if (parentID && parentID > 0) { %>
	<ol class="breadcrumb">
	  <li><a href="<%=parentLink%>" target="_blank"><%=parentName%></a></li>
	  <li class="active"><?php echo $c->getCollectionName()?></li>
	</ol>
	<% } else { %>
		<div class="breadcrumb">
		<?php echo t('A location has not yet been chosen.')?>
		</div>
	<% } %>
</script>

<script type="text/template" class="pagePath">
	<tr>
		<td><% if (isAutoGenerated) { %>
			<i class="fa fa-link launch-tooltip" title="<?php echo t('This page path is automatically generated from URL slugs. You cannot change this path.')?>"></i>
		<% } else { %>
			<i class="fa fa-external-link"></i>
		<% } %></td>
		<td style="text-align: center"><input type="radio" name="canonical" value="<%=row%>" <% if (isCanonical) { %>checked<% } %> /></td>
		<td><% if (isAutoGenerated) { %><input type="hidden" name="generated" value="<%=row%>"><input type="hidden" name="path[<%=row%>]" value="<%=pagePath%>"><% } %>
			<input type="text" data-input="auto" class="form-control" <% if (isAutoGenerated) { %>disabled<% } else { %>name="path[]"<% } %> value="<%=pagePath%>" /></td>
		<td><% if (!isAutoGenerated) { %><a href="#" data-action="remove-page-path" class="icon-link"><i class="fa fa-trash-o"></i></a><% } %></td>
	</tr>
</script>

<script type="text/javascript">

var renderBreadcrumb = _.template(
    $('script.breadcrumb').html()
);
var renderPagePath = _.template(
    $('script.pagePath').html()
);

$(function() {

	$('button[name=location]').on('click', function() {
		jQuery.fn.dialog.open({
			width: '90%',
			height: '70%',
			modal: true,
			title: '<?php echo t("Choose New Page Parent")?>',
			href: '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_search_selector?cID=<?php echo $c->getCollectionID()?>'
		});
	});

	$('#ccm-panel-detail-location-display').html(renderBreadcrumb({
		parentLink: '<?php echo Loader::helper('navigation')->getLinkToCollection($parent);?>',
		parentName: '<?php echo $parent->getCollectionName()?>',
		parentID: '<?php echo $cParentID?>'
	}));

	ConcreteEvent.subscribe('SitemapSelectPage', function(e, data) {
		$('#ccm-panel-detail-location-display').html(renderBreadcrumb({
			parentLink: '<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?cID=' + data.cID,
			parentName: data.title,
			parentID: data.cID
		}));

		var container = $('form[data-panel-detail-form=location]');
		container.find('input[name=cParentID]').val(data.cID);
		$.fn.dialog.closeTop();
	});

	$('form[data-panel-detail-form=location]').on('click', 'a[data-action=remove-page-path]', function(e) {
		e.preventDefault();
		$(this).closest('tbody').find('input[type=radio]:first').prop('checked', true);
		$(this).closest('tr').remove();
	});

	$('button[data-action=add-url]').on('click', function() {
		var rows = $('table.ccm-page-panel-detail-location-paths tbody tr').length;
		$('table.ccm-page-panel-detail-location-paths tbody').append(
			renderPagePath({
				isAutoGenerated: false,
				isCanonical: false,
				pagePath: '',
				row: rows
			})
		);
	});

	<?php /*
    <? foreach($paths as $i => $path) { ?>
	$('table.ccm-page-panel-detail-location-paths tbody').append(
        renderPagePath({
			isAutoGenerated: <?=intval($path->isPagePathAutoGenerated())?>,
			isCanonical: <?=intval($path->isPagePathCanonical())?>,
			pagePath: '<?=$path->getPagePath()?>',
			row: <?=$i?>
		})
	);
    <? } ?>
	*/?>

	<?php // first, we render the URL as it would be displayed auto-generated ?>

	$('table.ccm-page-panel-detail-location-paths tbody').append(
		renderPagePath({
			isAutoGenerated: <?php echo intval($autoGeneratedPath->isPagePathAutoGenerated())?>,
			isCanonical: <?php echo intval($autoGeneratedPath->isPagePathCanonical())?>,
			pagePath: '<?php echo $autoGeneratedPath->getPagePath()?>',
			row: 0
		})
	);

	<?php // now we loop through all the rest of the page paths ?>

    <?php foreach($paths as $i => $path) { ?>
	$('table.ccm-page-panel-detail-location-paths tbody').append(
        renderPagePath({
			isAutoGenerated: <?php echo intval($path->isPagePathAutoGenerated())?>,
			isCanonical: <?php echo intval($path->isPagePathCanonical())?>,
			pagePath: '<?php echo $path->getPagePath()?>',
			row: <?php echo $i + 1?>
		})
	);
    <?php } ?>


});
</script>
