<?
class TextEditorIO extends AjaxIO {

	private function interpretType($type, $arg = null) {

		switch($type) {

			case 'blogPostURL':
				$this->importArgs('blogPostID');
				return sprintf('<a href="%%URL_BLOGPOST|%u%%">%s</a>', $this->blogPostID, $this->innerText);

			case 'htmlEncode':
				$this->importArgs('text');
				return htmlspecialchars($this->text);

			case 'productURL':
				$this->importArgs('productID');
				return sprintf('<a href="%%URL_PRODUCT|%u%%">%s</a>', $this->productID, $this->innerText);

			case 'stubLimit':
				return sprintf('<flipshop:text type="stubStart" />'."\n".'%s'."\n".'<flipshop:text type="stubEnd" />', $this->innerText);

			case 'image':
				$this->importArgs('mediaID');
				$query = DB::prepareQuery("SELECT fileHash FROM Media WHERE ID = %u", $this->mediaID);
				if ( $result = DB::queryAndFetchOne($query) ) {
					return sprintf('<flipshop:media type="image" hash="%s" text="" href="" mediaID="%u" />', $result, $this->mediaID);
				}
				else {
					return '';
				}

			case 'URL':
				$this->importArgs('url', 'target', 'rel');
				$html = sprintf('<a href="%s"', $this->url);
				if( $this->target ) $html.= ' target="' . $this->target . '"';
				if( $this->rel ) $html.= ' rel="' . $this->rel . '"';
				$html.= sprintf('>%s</a>', $this->innerText);
				return $html;

			 case 'youtube':
				$this->importArgs('pointer');
				preg_match('/[A-Za-z0-9\-]{11}/', $this->pointer, $matches);
				return sprintf('<flipshop:embed type="youtube" id="%s" text="" />', $matches[0]);

			case 'b':
			case 'u':
			case 'i':
				return sprintf('<%1$s>%2$s</%1$s>', $type, $this->innerText);

			case 'p':
				return sprintf('<%1$s>' . "\n" . '%2$s' . "\n" . '</%1$s>', $type, $this->innerText);
		
			case 'ol':
			case 'ul':
				$html = sprintf("<%s>\n", $type);
				foreach(explode("\n", $this->innerText) as $line) {
					$html.= "\t<li>" . $line . "</li>\n";
				}
				$html.= sprintf("</%s>", $type);
				return $html;
		}
	}
		
	public function receive() {
	
		$text = $this->interpretType($this->type);
		echo $text;
		exit();
	}
}

$AjaxIO = new TextEditorIO('receive', array('type', 'innerText'));
