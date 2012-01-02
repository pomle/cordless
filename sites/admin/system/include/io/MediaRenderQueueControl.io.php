<?
class MediaRenderIO extends AjaxIO 
{
	public function start() 
	{
		DB::autocommit(true);
		MediaRenderQueue::processItem($this->mediaRenderQueueID);
		Message::addNotice(_('Process Klar'));
	}
}

// $action contains the initial function that is called, for example 'save'
$AjaxIO = new MediaRenderIO($action, array('mediaRenderQueueID'));
