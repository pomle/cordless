<?
namespace Element;

global $css, $js;
$css[] = '/css/FileUpload.css';
$js[] = '/js/jquery/jquery.serializeJSON.js';
$js[] = '/js/jquery/jquery.dropUpload.js';
$js[] = '/js/FileUpload.js';

class FileUpload extends IOControl
{
	public function __construct($IOCall, $action = 'upload', $paramsVars = null)
	{
		$this->action = $action;
		$this->paramsVars = $paramsVars;
		parent::__construct($IOCall);
	}

	public function __toString()
	{
		$AjaxCall = clone $this->IOCall->AjaxCall;
		$AjaxCall->setParam('action', $this->action);
		if(is_array($this->paramsVars)) {
			foreach($this->paramsVars as $name => $value) {
				$AjaxCall->setParam($name, $value);
			}
		}
		$url = (string)$AjaxCall;

		ob_start();
		?>
		<div class="fileUpload">

			<div class="dropbox"></div>

			<div class="fileList">

			</div>

			<? echo parent::__toString(); ?>

		</div>
		<?
		$string = ob_get_clean();

		return $string;
	}
}
