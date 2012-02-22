<?
namespace Element;

global $css, $js;
$css[] = '/css/Antiloop.css';
$js[] = '/js/Antiloop.js';

define('URL_ANTILOOP_FETCHER', '/ajax/AjaxAntiloopFetcher.php');
define('DIR_ANTILOOP_LISTS', DIR_ADMIN_INCLUDE . 'antiloopLists/');

class Antiloop extends Common\Root
{
	const DEFAULT_PAGE = 1;
	const DEFAULT_LIMIT = 20;

	public
		$enforceLimit;


	public static function getAsDomObject($protocol, Array $params = null, Array $filters = null)
	{
		require_once DIR_ADMIN_INCLUDE . 'Functions.Antiloop.inc.php';

		global $User;

		if( !is_array($params) ) $params = array();
		if( !is_array($filters) ) $filters = array();

		### What params do we save?
		$legalParams = array('sort', 'sortReverse', 'filter');

		### Assign where we save user preferences for sorting etc.
		$userParams = (array)$userParams = &$User->preferences['antiloop']['viewParams'][$protocol];

		$params = array_merge($userParams, $params);
		#$params = array_intersect_key($params, array_flip($legalParams));

		if( !isset($params['filter']) || !is_array($params['filter']) )
			$params['filter'] = array();

		### Overwrite Session filter
		$params['filter'] = array_merge($params['filter'], $filters);

		### Imports params from queryString of host page
		if( isset($_GET['antiloopParams']) && $getParams = unserialize($_GET['antiloopParams']) )
			$params = array_merge($params, $getParams);


		$userParams = $params;

		$AjaxCall = new \AjaxCall($protocol, array(), URL_ANTILOOP_FETCHER);
		$Antiloop = new self($AjaxCall);
		$Antiloop->addClass($protocol)->addData('protocol', $protocol);

		$antiloopListFile = DIR_ANTILOOP_LISTS . str_replace('/', '', $protocol) . '.inc.php';

		if( is_file($antiloopListFile) )
		{
			### Just for convenience, we let filters be directly available
			$filter = $params['filter'];
			require $antiloopListFile;
			$Antiloop->params = $params;
		}

		return $Antiloop;
	}

	public function __construct(\AjaxCall $AjaxCall = null)
	{
		static $count;

		$this->addID(sprintf('antiloop%u', ++$count));
		$this->addClass('antiloop');

		$this->AjaxCall = $AjaxCall;
		$this->messages = $this->triggers = $this->filters = $this->fields = array();
		$this->MessageBox = new \Element\MessageBox();
		$this->limit = null;
		$this->page = 0;

		$this->enforceLimit = true;
	}

	public function __toString()
	{
		return $this->getHTML();
	}


	public function addFields()
	{
		$fields = func_get_args();
		foreach($fields as $Field)
			$this->addField($Field);

		return $this;
	}

	public function addField(Antiloop\Field\Common\Root $NewField, $beforeFieldName = null, $afterFieldName = null, $replaceFieldName = null)
	{
		if( $NewField instanceof Antiloop\Field\ID )
			$this->rowID = $NewField->name;

		$offset = 0;
		$spliceIndex = count($this->fields);
		if( $afterFieldName || $beforeFieldName || $replaceFieldName )
		{
			foreach($this->fields as $index => $Field)
			{
				if( in_array($Field->name, array($afterFieldName, $beforeFieldName, $replaceFieldName)) )
				{
					$spliceIndex = ($index + ($afterFieldName ? 1 : 0));
					if( $replaceFieldName ) $offset = 1;
					break;
				}
			}
		}

		array_splice($this->fields, $spliceIndex, $offset, array($NewField));
		return $this;
	}

	public function addFilters()
	{
		$filters = func_get_args();
		foreach($filters as $Filter)
		{
			$this->addFilter($Filter);
		}
		return $this;
	}

	public function addFilter(Antiloop\Filter\Common\Root $Filter)
	{
		$this->filters[] = $Filter;
		return $this;
	}

	public function addTriggers()
	{
		$triggers = func_get_args();
		foreach($triggers as $Trigger)
			$this->addTrigger($Trigger);

		return $this;
	}

	public function addTrigger(Antiloop\Trigger $Trigger)
	{
		$this->triggers[] = $Trigger;
		return $this;
	}


	public function addAlert($msg)
	{
		$this->messages['alert'][] = $msg;
	}

	public function addError($msg)
	{
		$this->messages['error'][] = $msg;
	}

	public function addNotice($msg)
	{
		$this->messages['notice'][] = $msg;
	}


	public function dropField()
	{
		return $this->dropFields(func_get_args());
	}

	public function dropFields($fields)
	{
		if( is_array($fields) && count($fields) )
		{
			foreach($this->fields as $index => $Field)
				if( in_array($Field->name, $fields) ) unset($this->fields[$index]);
		}
		else
			$this->fields = array();

		return $this;
	}

	public function dropFilter($name)
	{
		return $this->dropFilters(array($name));
	}

	public function dropFilters()
	{
		$filters = func_get_args();

		if( count($filters) > 0 )
		{
			foreach($this->filters as $index => $Filter)
				if( in_array($Filter->name, $fields) ) unset($this->filters[$index]);
		}
		else
			$this->filters = array();

		return $this;
	}

	public function getHTML()
	{
		ob_start();
		?>
		<form action="<? echo $this->AjaxCall; ?>" method="post" <? echo $this->getAttributes(); ?>>
			<div class="control">
				<?
				if( count($this->filters) )
				{
					?>
					<ul class="filters tool">
						<?
						foreach($this->filters as $Filter)
						{
							$Filter->importParams((array)$this->params['filter']);
							?><li class="filter"><? echo $Filter; ?></li>
							<?
						}
						?>
					</ul>
					<?
				}

				if( count($this->triggers) )
				{
					?>
					<div class="triggers tool">
						<?
						echo \Element\Icon::custom('wrench_orange', _('Verktyg'));

						foreach($this->triggers as $Trigger)
						{
							echo $Trigger;
						}
						?>
					</div>
					<?
				}
				?>
			</div>

			<? echo $this->MessageBox; ?>

			<table class="content">
				<?
				$topCapHTML = ob_get_clean();

				$innerHTML = $this->getInnerHTML();

				ob_start();
				?>
				</tbody>
			</table>
		</form>
		<?
		$bottomCapHTML = ob_get_clean();

		return $topCapHTML . $innerHTML . $bottomCapHTML;
	}

	public function getInnerHTML()
	{
		ob_start();
		?>
		<thead>
			<tr class="head">
				<?
				$sortField = $this->params['sort'];
				$sortReverse = (bool)$this->params['sortReverse'];

				foreach($this->fields as $Field)
				{
					?><th class="col<? if( $Field->class ) echo ' ', join(' ', (array)$Field->class); ?>"><?
					if( $headContent = $Field->getHead() )
					{
						echo $headContent;
					}
					else
					{
						if( $Field->icon )
						{
							$Icon = \Element\Icon::custom($Field->icon, $Field->caption);

							if( $Field->isSortable )
							{
								if( $isSorting = ($Field->name == $sortField) )
									$isNextSortReversed = !$sortReverse;
								else
									$isNextSortReversed = $Field->isSortReversed;

								#echo (int)$isSorting;

								$this->AjaxCall
									->setParam('sort', $Field->name)
									->setParam('sortReverse', (int)$isNextSortReversed);
								?><a href="<? echo $this->AjaxCall; ?>" class="sort pD"><? echo $Icon; ?></a><?

								if( $isSorting )
								{
									$isSortReversedNow = ($isSorting && !$sortReverse) || ($Field->isSortReversed && !$sortReverse);
									echo \Element\Icon::custom($isSortReversedNow ? 'sort_arrow_up' : 'sort_arrow_down', $isSortReversedNow ? _('Sorterar stigande') : _('Sorterar fallande'));
								}
							}
							else
							{
								echo $Icon;
							}
						}
					}
					?></th><?
				}
				?>
				<th>
					<a href="?antiloopParams=<? echo urlencode(serialize($this->params)); ?>" class="reload pD"><? echo \Element\Icon::custom('page_white_swoosh', _('Ladda om')); ?></a>
					<img class="loader" src="/layout/ajax_dot_loader.gif">
				</th>
			</tr>
		</thead>
		<tbody>
		<?
		$fieldCount = count($this->fields) + 1;

		try
		{
			$Dataset = $this->getPreparedDataset();

			foreach($this->messages as $type => $strings)
			{
				?>
				<tr class="row message <? echo $type; ?>">
					<td colspan="<? echo $fieldCount; ?>">
						<ul class="messages">
						<?
						foreach($strings as $string)
						{
							?>
							<li class="message"><? echo htmlspecialchars($string); ?></li>
							<?
						}
						?>
						</ul>
					</td>
				</tr>
				<?
			}

			if( ($rowCount = $Dataset->num_rows) == 0 ) throw New \Exception(_('Inga rader att returnera'));

			$i = 0;
			$rowNumber = 0;
			while( $dataRow = $Dataset->fetch_assoc() )
			{
				$i++;
				if( !is_null($this->limit) && $i > $this->limit ) break;
				?>
				<tr class="row <? echo $i % 2 == 0 ? 'even' : 'odd'; ?>" id="<? echo isset($dataRow[$this->rowID]) ? 'id_' . $dataRow[$this->rowID] : null; ?>">
					<?
					foreach($this->fields as $Field)
					{
						$class = array('col');
						if( isset($Field->class) ) $class[] = $Field->class;
						?>
						<td class="<? echo join(' ', $class); ?>"><? echo $Field->getContent($dataRow); ?></td>
						<?
					}
					$rowNumber++;
					?>
					<td></td>
				</tr>
				<?
			}

			?>
			<tr class="row summary">
				<td colspan="<? echo $fieldCount; ?>">
					<?
					if( $this->page > 0 )
					{
						?><a href="#" class="prevPage pD" rel="-1"><? echo \Element\Icon::custom('book_previous', _('Föregående sida')); ?></a><?
					}

					if( $rowCount > $rowNumber )
					{
						?><a href="#" class="nextPage pD" rel="1"><? echo \Element\Icon::custom('book_next', _('Nästa sida')); ?></a><?
					}

					if( $this->limit > 0 ) printf(_('Sida: %u') . '<br />', $this->page+1);
					printf(_('Antal rader: %u'), $rowNumber);
					?>
				</td>
			</tr>
			<?
		}
		catch(\Exception $e)
		{
			?>
			<tr class="error">
				<td colspan="<? echo $fieldCount; ?>"><? echo htmlspecialchars($e->getMessage()); ?></td>
			</tr>
			<?
		}

		?>
		</tbody>
		<?
		$html = ob_get_clean();

		return $html;
	}

	private function getPreparedDataset()
	{
		$this->limit = null; # Defaults to be ignorant about limit

		if( $this->Dataset instanceof \Query\Select )
		{
			if( isset($this->params['sort']) )
			{
				$sortField = $this->params['sort'];
				$isCollateOK = $isSortOK = false;
				foreach($this->fields as $Field)
				{
					if( $isSortOK = ($sortField == $Field->name) )
					{
						$isCollateOK = $Field->isTextual;
						break;
					}
				}
				if( $isSortOK ) $this->Dataset->addOrder($sortField, $this->params['sortReverse'], $isCollateOK);
			}

			$page = abs( isset($this->params['filter']['page']) ? $this->params['filter']['page'] : self::DEFAULT_PAGE ) - 1;

			if( $this->enforceLimit )
			{
				$limit = isset($this->params['filter']['limit']) ? abs($this->params['filter']['limit']) : self::DEFAULT_LIMIT;

				if( $limit <> 0 )
				{
					$this->limit = $limit;
					$this->page = $page;
					$this->Dataset->setLimit($page * $limit, $limit + 1);
				}
			}

			$query = (string)$this->Dataset;

			if( DEBUG )
				$this->addNotice($query);

			return \DB::queryAndFetchResult($query);
		}
	}

	public function setDataset($Dataset)
	{
		$this->Dataset = $Dataset;
		return $this;
	}

	public function setID($domID)
	{
		$this->addID($domID);
		return $this;
	}
}