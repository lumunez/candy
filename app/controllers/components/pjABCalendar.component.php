<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
require_once PJ_COMPONENTS_PATH . 'pjCalendar.component.php';
class pjABCalendar extends pjCalendar
{
	protected $priceBasedOn = 1; //1 - days, 2 - nights
	
	public static function factory($opts=array())
	{
		return new pjABCalendar($opts);
	}
	
	public function getMonthView($month, $year, $reservation_arr = array(), $price_arr = array())
    {
        return $this->getMonthHTML($month, $year, 1, $reservation_arr, $price_arr);
    }
    
    public function setPriceBasedOn($content)
    {
    	if (in_array($content, array(1,2)))
    	{
    		$this->priceBasedOn = $content;
    	}
    	return $this;
    }
}
?>