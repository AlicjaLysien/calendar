<?php

function isToday($current_day, $month, $year){
    if(($year == date("Y") && $month == date("m")) && $current_day == date('d')) return 'today';
}

function isEvent($current_date, $allEvents, $data){
    foreach($allEvents as $event){
        if($current_date == $event->date && $data == "style") return 'event';
        if($current_date == $event->date && $data == "event") return $event->name;
    }
}

function currentDate($current_day, $month, $year){
    if(strlen($current_day) == 1) $current_day = '0'.$current_day;
    if(strlen($month) == 1) $month = '0'.$month;
    $current_date = $current_day.'-'.$month.'-'.$year;
    return $current_date;
}

function createCalendar() {

    include 'events.php';
    include 'czech_months.php';

    $month = 0;
    $year = 0;
     
    if(!$month && isset($_POST['month'])) {
        if($_POST['action'] == "previous") {
            $month = $_POST['month'] - 1;
            if($month == 0) $month = 12;
        }
        if($_POST['action'] == "next") {
            $month = $_POST['month'] + 1;
            if($month == 13) $month = 1;
        }   
    }else if(!$month){
        $month = date("m",time());  
    }    

    $month = ltrim($month, '0');

    if(!$year && isset($_POST['year'])) {
        $year = $_POST['year'];
        if($_POST['action'] == "previous") {
            if($month == 12) $year--;
        }
        if($_POST['action'] == "next") {
            if($month == 1) $year++;
        }
    } else if (!$year) {
        $year = date("Y",time());  
    }
     
     
    $number_of_days_in_month = cal_days_in_month(CAL_GREGORIAN,$month,$year);  
    $first_day_of_week_string = strftime("%A",strtotime($year."-".$month."-1"));
    $first_day_of_week = date('N', strtotime($first_day_of_week_string));


    $calendar='<div class="month"><div class="header">
        <form action="index.php" method="post">
            <input type="hidden" name="month" value='.$month.' />
            <input type="hidden" name="year" value='.$year.' />
            <input type="hidden" name="action" value="previous" />
            <input type="submit" value="<" class="form_previous" />
        </form>';

    $current_month = $czech_months[$month - 1];
    $calendar= $calendar.'<div class="current_month">'.$current_month. ' '. $year .'</div>
        <form action="index.php" method="post">
            <input type="hidden" name="month" value='.$month.' />
            <input type="hidden" name="year" value='.$year.' />
            <input type="hidden" name="action" value="next" />
            <input type="submit" value=">" class="form_next" />
        </form>';
    
    $calendar= $calendar.'
        </div>
        <div class="week week_names">
            <div class="day">po</div>
            <div class="day">út</div>
            <div class="day">st</div>
            <div class="day">čt</div>
            <div class="day">pá</div>
            <div class="day">so</div>
            <div class="day">ne</div>
        </div>
        <div class="week week_first">';


    $is_first_week = true;

    $first_week_calendar = "";

    $current_day = 1;
    for ($current_day; $current_day <= 8 - $first_day_of_week; $current_day++) {
        $current_date = currentDate($current_day, $month, $year);
        $first_week_calendar = $first_week_calendar.'<div class="day '.
        isToday($current_day, $month, $year).' '.isEvent($current_date, $allEvents, 'style').
        '" data-event="'.isEvent($current_date, $allEvents, 'event').'"
        data-date="'.$current_date.'"
        >'.$current_day.'</div>';
    };

    $calendar = $calendar.$first_week_calendar.'</div>';

    $is_first_week = false;
    $number_of_days_without_first_week = $number_of_days_in_month - (8 - $first_day_of_week);
        
    $number_of_rows = $number_of_days_without_first_week / 7 + 1;

    $calendar_weeks = "";
    for ($row = 1; $row <= $number_of_rows; $row++) {
        $calendar_weeks = $calendar_weeks.'<div class="week week_others">';
        for($i = 1; $i < 8; $i++){
            $current_date = currentDate($current_day, $month, $year);
            $calendar_weeks = $calendar_weeks.'<div class="day '.
            isToday($current_day, $month, $year).' '.isEvent($current_date, $allEvents, 'style').
                '" data-event="'.isEvent($current_date, $allEvents, 'event').'"
                data-date="'.$current_date.'"
                >'.$current_day.'</div>';
            if($current_day === $number_of_days_in_month) break;
            $current_day = $current_day + 1;
        }
        $calendar_weeks = $calendar_weeks.'</div>';
        if($current_day === $number_of_days_in_month) break;
    }

    $calendar = $calendar.$calendar_weeks.'</div>';

    echo $calendar;  
}   
