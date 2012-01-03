<?
namespace Element\Antiloop;

defaultSort($params, 'timeCreated', true);

$Stmt = new \Query\Select("SELECT
		ID as mediaID,
		timeCreated,
		fileHash AS mediaHash,
		fileOriginalName,
		fileSize,
		mediaType
	FROM Media");

if( $filter['search'] )
{
	$search = $filter['search'];

	if( is_numeric($search) )
	{
		$Stmt->addWhere('ID = %u', $search);
		point($Antiloop, $search);
	}
	elseif( $search[0] == '@' )
	{
		$search = substr($search, 1);

		$comp = str_replace('*', '%', $search);

		$query = \DB::prepareQuery("SELECT DISTINCT
				pm.mediaID
			FROM
				ProductMedia pm
				JOIN Products p ON p.ID = pm.productID
				JOIN ProductsLocale pl ON pl.productID = pm.productID
			WHERE
				pl.title LIKE %S",
			$comp);

		$productMediaIDs = \DB::queryAndFetchArray($query);

		$query = \DB::prepareQuery("SELECT DISTINCT
				a.mediaID
			FROM
				Articles a
				JOIN ArticlesLocale al ON al.articleID = a.ID
			WHERE
				IFNULL(al.name, a.internalName) LIKE %S",
			$comp);

		$articleMediaIDs = \DB::queryAndFetchArray($query);

		$Stmt->addWhere('ID IN %a', array_unique(array_merge($productMediaIDs, $articleMediaIDs)));
		$Antiloop->addNotice(sprintf(_('Sökning på produkt/artikel: "%s"'), $search));
	}
	elseif( strlen($search) == 32 )
	{
		$Stmt->addWhere('fileHash = %s', $search);
		$Antiloop->addNotice(sprintf(_('Sökning på mediaHash: "%s"'), $search));
	}
	else
	{
		$Stmt->addWhere('fileOriginalName LIKE %S', str_replace('*', '%', $search));
		$Antiloop->addNotice(sprintf(_('Sökning på orginalfilnamn: "%s"'), $search));
	}
}

$mediaTypes = \Manager\Dataset\Media::getTypes();

if( $filter['type'] )
{

	$Stmt->addWhere('mediaType = %s', $filter['type']);
	$Antiloop->addNotice(sprintf(_('Filtrerar på mediatyp "%s"'), $mediaTypes[$filter['type']] ?: $filter['type']));
}

$typeMap = \Dataset\Media::getTypeMap();


$Antiloop
	->setDataset($Stmt)
	->addFilters
	(
		Filter\Select::fromArray('type', 'variants', $mediaTypes, true),
		Filter\Search::text(),
		Filter\Slice::pagination()
	)
	->addFields
	(
		Field::id('mediaID'),
		Field::thumb(),
		Field\Map::icon('mediaType', _('Typ'), 'variants', $typeMap),
		Field::date('timeCreated', _('Senast ändrad'), 'time'),
		Field::text('fileOriginalName', _('Orginalfilnamn'), 'attach'),
		Field\File::size('fileSize'),
		Field\Link::custom('add', _('Ny').'...', '/MediaUpload.php', 'page_edit', _('Undersök').'...', '/MediaEdit.php', array('mediaID'))
		/*Field::('mediaID', _('Redigera') . '...', 'page_edit', '/ArticleEdit.php', array('articleID', 'localeID'))*/
	);