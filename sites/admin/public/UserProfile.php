<?
require '../Init.inc.php';

if( !class_exists('\\Element\\IOCall') ) exit();
if( !class_exists('\\Element\\Tabs') ) exit();
if( !class_exists('\\Element\\MessageBox') ) exit();

if( !defined('USER_ID') || USER_ID == 0 ) exit();

use \Element\IOCall as IOCall;

$user = \Manager\Dataset\User::getProperties(USER_ID);

$pageTitle = _('User Profile');

$IO = new IOCall('User');
$IOControl = new \Element\IOControl($IO);

require HEADER;
?>
<div class="tabs">
	<?
	$Tabs = new \Element\Tabs();
	echo $Tabs
		->addTab('overview', _('Account'), 'user_orange')
		->addTab('personals', _(''), 'vcard')
		->addTab('password', _('Password'), 'key')
		->addTab('policy', _('Policies'), 'shield')
		->addTab('userGroup', _('Groups'), 'group')
		->addTab('setting', _('Settings'), 'wrench_orange');
	?>

	<fieldset class="tab" id="overview">
		<legend><? echo \Element\Tag::legend('user_orange', _('Kontoöversikt')); ?></legend>
		<?
		echo \Element\Table::texts()
			->addRow(_('Kontot skapat'), \Format::timestamp($user['timeCreated']))
			->addRow(_('Användarnamn'), $user['username'])
			->addRow(_('Användar-ID'), USER_ID)
			->addRow(
				_('Lösenord ålder'),
				\Format::elapsedTime($passWordAge = (time() - $user['timePasswordLastChange'])) .
				( \User::PASSWORD_MAX_AGE ? ' ('. sprintf(_('Byte begärs om %u dagar'), (\User::PASSWORD_MAX_AGE - $passwordAge) / (60*60*24)) . ')' : '' )
			)
			->addRow(_('Senaste inloggning'), \Format::timestamp($user['timeLastLogin']))
			->addRow(_('Antal inloggningar'), $user['countLoginsSuccessful'])
			->addRow(_('Misslyckade inloggningsförsök'), $user['countLoginsFailed']);
		?>
	</fieldset>
	<?
	echo $IO->getHead();
	?>
	<fieldset class="tab" id="personals">
		<legend><? echo \Element\Tag::legend('vcard', _('Personuppgifter')); ?></legend>
		<?
		$size = 64;
		echo \Element\Table::inputs()
			->addRow(_('Fullständigt namn'), \Element\Input::text('fullname', $user['fullname'])->size($size))
			->addRow(_('E-postadress'), \Element\Input::text('email', $user['email'])->size($size))
			->addRow(_('Telefonnummer'), \Element\Input::text('phone', $user['phone'])->size($size));

		echo $IOControl->setButtons(new \Element\Button\Save('saveProfile'));
		?>
	</fieldset>
	<?
	echo $IO->getFoot();


	$IO = new IOCall('User');
	echo $IO->getHead();
	?>
	<fieldset class="tab" id="password">
		<legend><? echo \Element\Tag::legend('key', _('Lösenord')); ?></legend>

		<?
		echo \Element\Input::hidden('userID', USER_ID);

		echo \Element\Table::inputs()
			->addRow(_('Nuvarande lösenord'), \Element\Input::password('passwordCurrent'))
			->addRow(_('Nytt lösenord'), \Element\Input::password('passwordNew'))
			->addRow(_('Repetera nytt lösenord'), \Element\Input::password('passwordNewVerify'));

		echo $IOControl->setButtons(new \Element\Button\Save('setPassword'));
		?>
	</fieldset>
	<?
	echo $IO->getFoot();
	?>

	<div class="tab" id="policy">
		<fieldset>
			<legend><? echo \Element\Tag::legend('shield', _('Särskilda Rättigheter')); ?></legend>
			<?
			if( count($policyIDs = \Manager\Dataset\User::getPolicies(USER_ID)) > 0 )
			{
				$policies = \Manager\Policy::loadFromDB($policyIDs);

				$Table = new \Element\Table();
				foreach($policies as $Policy)
					$Table->addRow($Policy->name, $Policy->description);

				echo $Table;
			}
			else
			{
				echo \Element\MessageBox::notice(_('Du har för närvarande inga särskilda rättigheter'));
			}


			?>
		</fieldset>

		<fieldset>
			<legend><? echo \Element\Tag::legend('key_go', _('Ärvda Rättigheter')); ?></legend>
			<?
			if( count($policies = $User->getPolicies()) )
			{
				$Table = new \Element\Table();
				foreach($policies as $policy => $state)
					$Table->addRow($state, $policy);

				echo $Table;
			}
			else
			{
				echo \Element\MessageBox::notice(_('Du har för närvarande inga ärvda rättigheter'));
			}
			?>
		</fieldset>
	</div>

	<fieldset class="tab" id="userGroup">
		<legend><? echo \Element\Tag::legend('group', _('Grupper')); ?></legend>
		<?
		if( count($userGroupIDs = \Manager\Dataset\User::getGroups(USER_ID)) > 0 )
		{
			$userGroups = \Manager\UserGroup::loadFromDB($userGroupIDs);

			$Table = new \Element\Table();
			foreach($userGroups as $UserGroup)
				$Table->addRow($UserGroup->name, $UserGroup->description);

			echo $Table;
		}
		else
		{
			echo \Element\MessageBox::notice(_('Du är för närvarande inte medlem i någon grupp'));
		}
		?>
	</fieldset>

	<?
	$IO = new IOCall('UserSettings');
	echo $IO->getHead();
	?>
	<fieldset class="tab" id="setting">
		<legend><? echo \Element\Tag::legend('wrench_orange', _('Inställningar')); ?></legend>
		<?
		$userSettings = \Manager\Dataset\UserSetting::getAvailable();

		if( $User->isAdministrator() )
		{
			$adminSettings = array
			(
				'DebugMode' => array
				(
					'description' => _('Aktivera avbuggningsläge'),
					'type' => 'boolean',
					'default' => false
				)
			);

			$userSettings = array_merge($adminSettings, $userSettings);
		}

		$Table = \Element\Table::inputs();
		foreach($userSettings as $key => $properties)
		{
			$curVal = $User->getSetting($key);

			$inputName = 'settings[' . $key . ']';

			switch($properties['type'])
			{
				case 'boolean':
					$Input = \Element\Input::checkbox($inputName, 1, $curVal);
					break;

				case 'selector':
					$Input = \Element\SelectBox::keyPair($inputName, $curVal, $properties['values']);
					break;

				default:
					$Input = \Element\Input::text($inputName, $curVal);
					break;
			}

			$Table->addRow($properties['description'], $Input);
		}

		echo $Table;

		$Control = new \Element\IOControl($IO);
		echo $Control
			->addButton(new \Element\Button\Save())
			->createButton('flush', 'delete', _('Nollställ inställningar'), _('Detta återställer alla dina inställningar till standardvärden. Är du säker på att du vill fortsätta?'))
			->createButton('forget', 'page_white', _('Nollställ visningsinställningar'), _('Detta återställer alla dina visningsinställningar. Är du säker på att du vill fortsätta?'));

		?>
	</fieldset>
	<?
	$IO->getFoot();
	?>
</div>
<?
require FOOTER;