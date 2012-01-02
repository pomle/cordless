<?
namespace Element;

## global $css;
## $css[] = '/css/QuickTable.css';

class Table
{
	public static function inputs()
	{
		$T = new self();
		return $T;
	}

	public static function texts()
	{
		return new self();
	}


	public function __construct()
	{
		$this->rows = array();
	}

	public function __toString()
	{
		ob_start();
		?>
		<table>
			<?
			$i = 0;
			foreach($this->rows as $cols)
			{
				$th = array_shift($cols);
				?>
				<tr class="row <? echo (++$i % 2) ? 'odd' : 'even'; ?>">
					<th><? echo $th; ?></th>
					<?
					foreach($cols as $col)
					{
						?><td><? echo $col; ?></td><?
					}
					?>
				</tr>
				<?
			}
			?>
		</table>
		<?
		return ob_get_clean();
	}


	public function addRow()
	{
		$this->rows[] = func_get_args();
		return $this;
	}
}