<?
namespace Element\Form;

class Upload extends \Element\_Common
{
	public function __construct(\Element\IOCall $IOCall)
	{
		$this->IOCall = $IOCall;

		$this->replaceMediaID = null;

		$this->showBrowseFields = true;
		$this->countBrowseFields = 1;

		$this->uploadAction = 'upload';
		$this->fetchAction = 'url';
	}

	public function __toString()
	{
		ob_start();

		$UploadIO = clone $this->IOCall;
		$UploadIO->setParam('action', $this->uploadAction);

		$FetchIO = clone $this->IOCall;


		echo $UploadIO->getHead();
		?>
		<fieldset>
			<legend><? echo \Element\Tag::legend('mouse_add', 'Drag & Drop'); ?></legend>
			<?
			echo \Element\Table::inputs()
				->addRow(_('Typ'), new \Element\Module('SelectBox.MediaTypes', 'preferredMediaType', true))
				;



			echo new \Element\FileUpload($UploadIO);
			?>
		</fieldset>
		<?
		echo $UploadIO->getFoot();


		echo $FetchIO->getHead();
		?>
		<fieldset>
			<legend><? echo \Element\Tag::legend('world_link', 'Fetch Resource'); ?></legend>
			<?
			echo \Element\Table::inputs()
				->addRow(_('URL'), \Asenine\Element\Input::text('url')->size(100))
				;

			$IOControl = new \Element\IOControl($this->IOCall);
			echo $IOControl->setButtons(\Element\Button::IO($this->fetchAction, 'world_add', 'Download'));
			?>
		</fieldset>
		<?
		echo $FetchIO->getFoot();


		if( $this->showBrowseFields )
		{
			$Table = new \Element\Table();

			$i = 0;
			while($i++ < $this->countBrowseFields)
				$Table->addRow(
					sprintf('File #%u', $i),
					\Asenine\Element\Input::file("media[$i]")->size(64),
					new \Element\Module('SelectBox.MediaTypes', "mediaType[$i]", true));

			?>

			<form action="?upload=1" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend><? echo \Element\Tag::legend('application_form_edit', 'File List'); ?></legend>

				<?
				echo
					$Table;

				echo \Element\Button::submit('arrow_divide', 'Upload');
				?>
			</fieldset>
			</form>
			<?
		}

		return ob_get_clean();
	}
}