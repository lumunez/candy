<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjCalendar
{
    private $startDay = 0;
    
    private $startMonth = 1;
    
    private $dayNames = array("S", "M", "T", "W", "T", "F", "S");
    
    private $monthNames = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    
    private $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    
    public function __construct()
    {
    	
    }

    public function getDayNames()
    {
        return $this->dayNames;
    }

    public function setDayNames($names)
    {
        $this->dayNames = $names;
        return $this;
    }

    public function getMonthNames()
    {
        return $this->monthNames;
    }

    public function setMonthNames($names)
    {
        $this->monthNames = $names;
        return $this;
    }

    public function getStartDay()
    {
        return $this->startDay;
    }

    public function setStartDay($day)
    {
        $this->startDay = $day;
        return $this;
    }

    public function getStartMonth()
    {
        return $this->startMonth;
    }

    public function setStartMonth($month)
    {
        $this->startMonth = $month;
        return $this;
    }

    public function getCalendarLink($month, $year)
    {
        return "";
    }

    public function getDateLink($day, $month, $year)
    {
        return "";
    }

    public function getCurrentMonthView()
    {
        $date = getdate(time());
        return $this->getMonthView($date["mon"], $date["year"]);
    }

    public function getCurrentYearView()
    {
        $date = getdate(time());
        return $this->getYearView($date["year"]);
    }

    public function getMonthView($month, $year)
    {
        return $this->getMonthHTML($month, $year);
    }

    public function getYearView($year)
    {
        return $this->getYearHTML($year);
    }

    public function getDaysInMonth($month, $year)
    {
        if ($month < 1 || $month > 12)
        {
            return 0;
        }
   
        $days = $this->daysInMonth[$month - 1];
   
        if ($month == 2)
        {
            // Check for leap year
            // Forget the 4000 rule, I doubt I'll be around then...
        
            if ($year%4 == 0)
            {
                if ($year%100 == 0)
                {
                    if ($year%400 == 0)
                    {
                        $days = 29;
                    }
                }
                else
                {
                    $days = 29;
                }
            }
        }
    
        return $days;
    }

    public function getMonthHTML($month, $year, $showYear = 1, $reservation_arr = array(), $price_arr = array())
    {
    	$showTooltip = true;
    	$booking_arr = array_key_exists('bookings', $reservation_arr) ? $reservation_arr['bookings'] : array();
    	$map_arr = array_key_exists('map', $reservation_arr) ? $reservation_arr['map'] : array();
        $string = "";
        
        $adjustedDate = $this->adjustDate($month, $year);
        $month = $adjustedDate[0];
        $year = $adjustedDate[1];
        
    	$daysInMonth = $this->getDaysInMonth($month, $year);
    	$date = getdate(mktime(12, 0, 0, $month, 1, $year));
    	
    	$first = $date["wday"];
    	$monthNames = $this->getMonthNames();
    	$monthName = $monthNames[$month - 1];
    	
    	$prev = $this->adjustDate($month - 1, $year);
    	$next = $this->adjustDate($month + 1, $year);
    	
    	if ($showYear == 1)
    	{
    	    $prevMonth = $this->getCalendarLink($prev[0], $prev[1]);
    	    $nextMonth = $this->getCalendarLink($next[0], $next[1]);
    	} else {
    	    $prevMonth = "";
    	    $nextMonth = "";
    	}
    	
    	$header = $monthName . (($showYear > 0) ? " " . $year : "");
    	
    	$string .= "<table class=\"calendarTable\" cellspacing=\"1\" cellpadding=\"2\">\n";
    	$string .= "<tr>\n";
    	$string .= "<td>" . (($prevMonth == "") ? "&nbsp;" : "<a href=\"$prevMonth\">&lt;&lt;</a>")  . "</td>\n";
    	$string .= "<td class=\"calendarMonth\" colspan=\"5\">$header</td>\n";
    	$string .= "<td>" . (($nextMonth == "") ? "&nbsp;" : "<a href=\"$nextMonth\">&gt;&gt;</a>")  . "</td>\n";
    	$string .= "</tr>\n";
    	
    	$dayNames = $this->getDayNames();
    	
    	$string .= "<tr>\n";
    	$string .= "<td class=\"calendarWeekDay\">" . $dayNames[($this->getStartDay()+0)%7] . "</td>\n";
    	$string .= "<td class=\"calendarWeekDay\">" . $dayNames[($this->getStartDay()+1)%7] . "</td>\n";
    	$string .= "<td class=\"calendarWeekDay\">" . $dayNames[($this->getStartDay()+2)%7] . "</td>\n";
    	$string .= "<td class=\"calendarWeekDay\">" . $dayNames[($this->getStartDay()+3)%7] . "</td>\n";
    	$string .= "<td class=\"calendarWeekDay\">" . $dayNames[($this->getStartDay()+4)%7] . "</td>\n";
    	$string .= "<td class=\"calendarWeekDay\">" . $dayNames[($this->getStartDay()+5)%7] . "</td>\n";
    	$string .= "<td class=\"calendarWeekDay\">" . $dayNames[($this->getStartDay()+6)%7] . "</td>\n";
    	$string .= "</tr>\n";
    	
    	// We need to work out what date to start at so that the first appears in the correct column
    	$dayIndex = $this->getStartDay() + 1 - $first;
    	while ($dayIndex > 1)
    	{
    	    $dayIndex -= 7;
    	}

        // Make sure we know when today is, so that we can use a different CSS style
        $today = getdate(time());
    	
    	while ($dayIndex <= $daysInMonth)
    	{
    	    $string .= "<tr>\n";
    	    
    	    for ($i = 0; $i < 7; $i++)
    	    {
        	    $class = ($year == $today["year"] && $month == $today["mon"] && $dayIndex == $today["mday"]) ? "calendarToday" : "calendar";
        	    $timestamp = mktime(0, 0, 0, $month, $dayIndex, $year);
        	    if ($dayIndex > 0 && $dayIndex <= $daysInMonth)
        	    {
	        	    foreach ($booking_arr as $booking)
	        	    {
	        	    	if ($this->priceBasedOn == 1)
	        	    	{
	        	    		# Price based on days
		        	    	if ($booking['date_from'] <= $timestamp && $booking['date_to'] >= $timestamp)
		        	    	{
		        	    		$class = 'calendarReserved';
		        	    		break;
		        	    	}
	        	    	}
	        	    }
	        	    
	        	    # Price based on nights
	        	    if ($this->priceBasedOn == 2)
	        	    {
		        	    if (isset($map_arr[$timestamp]))
		        	    {
		        	    	if ($map_arr[$timestamp]['in'] > 0 || ($map_arr[$timestamp]['start'] > 0 && $map_arr[$timestamp]['end'] > 0))
		        	    	{
		        	    		$class = 'calendarReserved';
		        	    	} elseif ($map_arr[$timestamp]['in'] == 0 && $map_arr[$timestamp]['start'] > 0 && $map_arr[$timestamp]['end'] == 0) {
		        	    		$class = 'calendarReservedLeft';
		        	    	} elseif ($map_arr[$timestamp]['in'] == 0 && $map_arr[$timestamp]['start'] == 0 && $map_arr[$timestamp]['end'] > 0) {
		        	    		$class = 'calendarReservedRight';
		        	    	}
		        	    }
	        	    }
        	    } else {
        	    	$class = 'calendarPast';
        	    }
    	        $string .= "<td class=\"$class\" id=\"td_".$timestamp."\" align=\"right\" valign=\"top\">";
    	        if ($dayIndex > 0 && $dayIndex <= $daysInMonth)
    	        {
    	            $link = $this->getDateLink($dayIndex, $month, $year);
    	            $string .= (($link == "") ? $dayIndex : "<a href=\"$link\">$dayIndex</a>");
    	        	if ($showTooltip)
            		{
            			$string .= '<div class="calendarTooltip" id="t_td_'.$timestamp.'">' . (isset($price_arr[$timestamp]) && !empty($price_arr[$timestamp]) ? $price_arr[$timestamp] : 'N/A') . '</div>';
            		}
    	        } else {
    	            $string .= "&nbsp;";
    	        }
      	        $string .= "</td>\n";
        	    $dayIndex++;
    	    }
    	    $string .= "</tr>\n";
    	}
    	$string .= "</table>\n";
    	
    	return $string;
    }

    public function getYearHTML($year)
    {
        $string = "";
    	$prev = $this->getCalendarLink(0, $year - 1);
    	$next = $this->getCalendarLink(0, $year + 1);
        
        $string .= "<table class=\"calendarTable\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n";
        $string .= "<tr>";
    	$string .= "<td align=\"center\" valign=\"top\" align=\"left\">" . (($prev == "") ? "&nbsp;" : "<a href=\"$prev\">&lt;&lt;</a>")  . "</td>\n";
        $string .= "<td class=\"calendarHeader\" valign=\"top\" align=\"center\">" . (($this->getStartMonth() > 1) ? $year . " - " . ($year + 1) : $year) ."</td>\n";
    	$string .= "<td align=\"center\" valign=\"top\" align=\"right\">" . (($next == "") ? "&nbsp;" : "<a href=\"$next\">&gt;&gt;</a>")  . "</td>\n";
        $string .= "</tr>\n";
        $string .= "<tr>";
        $string .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(0 + $this->getStartMonth(), $year, 0) ."</td>\n";
        $string .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(1 + $this->getStartMonth(), $year, 0) ."</td>\n";
        $string .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(2 + $this->getStartMonth(), $year, 0) ."</td>\n";
        $string .= "</tr>\n";
        $string .= "<tr>\n";
        $string .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(3 + $this->getStartMonth(), $year, 0) ."</td>\n";
        $string .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(4 + $this->getStartMonth(), $year, 0) ."</td>\n";
        $string .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(5 + $this->getStartMonth(), $year, 0) ."</td>\n";
        $string .= "</tr>\n";
        $string .= "<tr>\n";
        $string .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(6 + $this->getStartMonth(), $year, 0) ."</td>\n";
        $string .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(7 + $this->getStartMonth(), $year, 0) ."</td>\n";
        $string .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(8 + $this->getStartMonth(), $year, 0) ."</td>\n";
        $string .= "</tr>\n";
        $string .= "<tr>\n";
        $string .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(9 + $this->getStartMonth(), $year, 0) ."</td>\n";
        $string .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(10 + $this->getStartMonth(), $year, 0) ."</td>\n";
        $string .= "<td class=\"calendar\" valign=\"top\">" . $this->getMonthHTML(11 + $this->getStartMonth(), $year, 0) ."</td>\n";
        $string .= "</tr>\n";
        $string .= "</table>\n";
        
        return $string;
    }

    public function adjustDate($month, $year)
    {
        $adjustedDate = array();
        $adjustedDate[0] = $month;
        $adjustedDate[1] = $year;
        
        while ($adjustedDate[0] > 12)
        {
            $adjustedDate[0] -= 12;
            $adjustedDate[1]++;
        }
        
        while ($adjustedDate[0] <= 0)
        {
            $adjustedDate[0] += 12;
            $adjustedDate[1]--;
        }
        
        return $adjustedDate;
    }
}
?>