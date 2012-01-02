<?
namespace Element\Antiloop;

include DIR_ANTILOOP_LISTS . 'Users.inc.php';

$Antiloop->addField(Field::enabled('isAdministrator', _('Administrator'), 'user_suit'), null, 'isEnabled');
