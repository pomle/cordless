<?
class Post extends \Manager\Common\DB
{
	public static function addToDB()
	{
		$query = "INSERT INTO Posts (timeCreated, isPublished) VALUES(UNIX_TIMESTAMP(), timeCreated)";
		$postID = \DB::queryAndGetID($query);
		return static::loadOneFromDB($postID);
	}

	public static function loadFromDB($postIDs)
	{
		$posts = array();

		$query = \DB::prepareQuery("SELECT
				p.ID AS postID,
				p.isPublished,
				p.timeCreated,
				p.timeModified,
				p.timePublished,
				p.title,
				p.uri
			FROM
				Posts p
			WHERE
				p.ID IN %a", $postIDs);

		$result = \DB::queryAndFetchResult($query);

		while($post = \DB::assoc($result))
		{
			$Post = new static();

			$Post->postID = (int)$post['postID'];

			$Post->isPublished = (bool)$post['isPublished'];

			$Post->timeCreated = (int)$post['timeCreated'] ?: null;
			$Post->timeModified = (int)$post['timeModified'] ?: null;
			$Post->timePublished = (int)$post['timePublished'] ?: null;

			$Post->title = $post['title'];
			$Post->uri = $post['uri'];

			$posts[$Post->postID] = $Post;
		}

		return $posts;
	}


	public function __construct()
	{
	}

	public function __get($key)
	{
		return $this->$key;
	}
}