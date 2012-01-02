<?
class Message {

	protected static $elements = array('error', 'alert', 'notice');
	protected static $messages = array();
	private static $redirect = array();
	private static $calls = array();
	private static $custom = array();

	public static function asJSON($action, $result = null, $call = null) {
		header('Content-type: text/x-json;');

		if( isset( self::$redirect['url'] ) ) {
			$call.= sprintf("setTimeout('window.location = %s', %d);", self::$redirect['url'], self::$redirect['timeout']);
		}

		if( count(self::$calls) > 0 ) {
			$call.= join('', self::$calls);
		}

		echo json_encode(array('action' => $action, 'message' => self::$messages, 'data' => $result, 'call' => $call) + self::$custom);

		exit();
	}

	/*public static function flash() {
		echo '<div class="static_messages">';
		foreach(self::$messages as $class => $messages) {
			self::displayElement($class, join('<br />', $messages));
		}
		echo '</div>';
	}*/

	private static function status($status) {
		//var_dump($status);
		if($status === 0	|| $status === 'alert'	|| $status === false) return 'alert';
		if($status > 0		|| $status === 'notice' || $status === true ) return 'notice';
		if($status < 0		|| $status === 'error') return 'error';
		return strval($status);
	}

	public static function redirect($url, $timeout = 500) {
		self::$redirect = array(
			'url' => '"'.$url.'"',
			'timeout' => (int)$timeout
		);
		self::addNotice(_('Vidarebefodrar...') . ' <a href="' . $url . '">' . _('Gå vidare nu') . '</a>');
	}

	public static function refresh($timeout = 500) {
		self::$redirect = array(
			'url' => 'window.location',
			'timeout' => (int)$timeout
		);
		self::addNotice(_('Uppdaterar vy...'));
	}

	public static function addNotice($message) {
		return self::addMessage('notice', $message);
	}
	public static function addAlert($message) {
		return self::addMessage('alert', $message);
	}
	public static function addError($message) {
		return self::addMessage('error', $message);
	}

	public static function displayNotice($message) {
		echo '<div class="messages static">';
		self::displayElement('notice', $message);
		echo '</div>';
	}

	public static function displayError($message) {
		echo '<div class="messages static">';
		self::displayElement('error', $message);
		echo '</div>';
	}

	protected static function addMessage($type, $message) {
		if(strlen($message) == 0) return false;

		if(in_array($type, self::$elements)) {
			self::$messages[$type][] = (string)$message;
		}else{
			self::$messages['alert'][] = sprintf(_('Invalid message type: "%s" for message "%s"'), $type, (string)$message);
		}
	}

	public static function addCall($javascript) {
		self::$calls[] = $javascript;
	}

	public static function flushAll() {
		foreach(self::$elements as $type) {
			self::$messages[$type] = array();
		}
	}

	public static function flushMessages($type) {
		if(in_array($type, self::$elements)) {
			self::$messages[$type] = array();
		}
	}

	public static function displayElements() {
		?>
		<div class="messages">
			<?
			foreach(self::$elements as $element) {
				$messages = null;
				if( isset(self::$messages[$element]) ) {
					$messages = nl2br(join("\n", self::$messages[$element]));
					self::flushMessages($element);
				}
				self::displayElement($element, $messages);
			}
			?>
		</div>
		<?
	}

	public static function displayElement($class, $message = null) {
		if( !in_array($class, self::$elements) ) return false;
		?>
		<ul class="message <? echo $class; ?>"<? if($message) echo ' style="display: block;"'; ?>><? if($message) echo $message; ?></ul>
		<?
	}

	public static function addCustom($key, $data = null) {
		self::$custom[$key] = $data;
	}

	public static function clearCustom($key) {
		unset(self::$custom[$key]);
	}
}