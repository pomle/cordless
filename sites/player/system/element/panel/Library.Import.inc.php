<?
namespace Cordless;

use \Asenine\DB;

echo Element\Library::head(_("Advanced Track Import"));
?>

<section>
	<h2><? echo _("Download from URL"); ?></h2>

	<form action="<? echo apiLink('Import.FetchURL'); ?>" method="POST">
		<div class="settings">

			<?
			echo Element\Table::inputs()
				->addRow(_("URL"), \Asenine\Element\Input::text('url')->size(64))
				->addRow(_('Artist'), \Asenine\Element\Input::text('artist')->size(32))
				->addRow(_('Title'), \Asenine\Element\Input::text('title')->size(32))
				->addRow(_("Ignore Header"), \Asenine\Element\Input::checkbox('ignoreHeader'))
				;
			?>

			<button type="submit" id="startImport"><? echo _("Fetch"); ?></button>
		</div>
	</form>

	<script type="text/javascript">
		$(document).ready(function() {

			$('#startImport').off('click').on('click', function(e) {
				e.preventDefault();
				var form = $(this).closest('form');
				var url = form.attr('action');
				var fetchURL = form.find('input[name=url]').val();

				var QueueItem = File.QueueItem = Cordless.Interface.importQueueAdd(fetchURL);

				var p = 0;
				var timer = setInterval(function() { QueueItem.setProgress(QueueItem.progress + ((1 - QueueItem.progress) * 0.025)); }, 250);

				$.ajax({
					'url': url,
					'type': 'POST',
					'data': form.serialize(),
					'dataType': 'json',
					'complete': function()
					{
						clearInterval(timer);

						QueueItem.setProgress(1);
						QueueItem.queueRemove();
					},
					'error': function(jqXHR, textStatus, errorThrown)
					{
						QueueItem.setCaption(textStatus, false);
					},
					'success': function(response)
					{
						QueueItem.setCaption(response.data, response.status);
					}
				});
			});
		});
	</script>
</section>