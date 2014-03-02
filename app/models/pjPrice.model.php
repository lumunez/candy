<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjPriceModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'prices';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'listing_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'date_from', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'date_to', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjPriceModel($attr);
	}
	
	public function getPrice($from_ts, $to_ts, $listing_id, $days=1)
	{
		$arr = $this->where('t1.listing_id', $listing_id)->findAll()->getData();
		foreach ($arr as $key => $value)
		{
			$arr[$key]['from_ts'] = strtotime($value['date_from']);
			$arr[$key]['to_ts'] = strtotime($value['date_to']);
		}

		$offset = $days == 1 ? 86400 : 0;
		$amount = 0;
		for ($i = $from_ts; $i < $to_ts + $offset; $i += 86400)
		{
			foreach ($arr as $item)
			{
				if ($item['from_ts'] <= $i && $item['to_ts'] >= $i)
				{
					$amount += $item['price'];
					break;
				}
			}
		}
		return $amount;
	}
}
?>