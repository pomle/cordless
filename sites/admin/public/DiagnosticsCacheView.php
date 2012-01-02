<?
define('ACCESS_POLICY', 'AllowViewDiagnostics');

require_once '../Init.inc.php';

$pageTitle = _('Cache');

if( !class_exists('Cache') )
	\Element\Page::error("No Cache Module Installed");


$IOCall = new \Element\IOCall('Cache');

$CacheControl = new \Element\IOControl($IOCall);
$CacheControl
	->createButton('read', 'eye', 'Read')
	->createButton('write', 'disk', 'Write', 'This overwrites cache key')
	->createButton('flush', 'delete', 'Delete', 'This purges cache key');

$EventControl = new \Element\IOControl($IOCall);
$EventControl
	->createButton('triggerEvent', 'lightning', 'Trigger', 'This triggers cacheEvent')
	->createButton('showEvent', 'eye', 'Show');

$stats = Cache::getStats();
$CacheStats = new \Element\Table();
$CacheStats
	->addRow('Uptime', Format::elapsedTime($stats["uptime"]))
	->addRow('Bytes', Format::fileSize($stats["bytes"]))
	->addRow('Limit', Format::fileSize($stats["limit_maxbytes"]))
	->addRow('Usage', round(($stats["bytes"] / $stats["limit_maxbytes"]) * 100, 2) . '%')
	->addRow('Hits', $stats["get_hits"])
	->addRow('Misses', $stats["get_misses"]);

require HEADER;

echo $IOCall->getHead();
?>
<fieldset>
	<legend><? echo Cache::PROVIDER; ?></legend>

	<? echo $CacheStats; ?>

	<table>
		<tr>
			<td><? echo 'Key'; ?></td>
			<td><? echo \Element\Input::text('cacheKey')->size(40); ?></td>
		</tr>
		<tr>
			<td><? echo 'Data'; ?></td>
			<td><? echo \Element\TextArea::small('cacheData'); ?></td>
		</tr>
	</table>

	<? echo $CacheControl; ?>
</fieldset>
<?
echo $IOCall->getFoot();

echo $IOCall->getHead();
?>
<fieldset>
	<legend><? echo 'Cache Events'; ?></legend>

		<table>
			<tr>
				<td><? echo 'Event'; ?></td>
				<td><?
					$cacheEvents = \Manager\Cache::getEventNames();
					$Select = new \Element\SelectBox('eventName');
					$Select->addItemsFromArray($cacheEvents, true);
					//asort($Select->items, SORT_LOCALE_STRING);
					echo $Select;
				?></td>
			</tr>
			<tr>
				<td><? echo 'Args'; ?></td>
				<td><?
					$i = 0;
					for(;;)
					{
						echo \Element\Input::text(sprintf('eventArgs[%u]', $i++))->size(5), ' ';
						if( $i > 5 ) break;
					}
				?></td>
			</tr>
		</table>

		<? echo $EventControl; ?>

</fieldset>
<?
echo $IOCall->getFoot();

require FOOTER;