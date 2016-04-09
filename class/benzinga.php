<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of benzinga
 *
 * @author PCUser
 */
class Benzinga
{
    var $token;
    
    function __construct(){
        $this->token = file_get_contents("token.txt");  
    }
    
    function getRating(){
        $arrAction  = $this->getAction();
        $arrAnalyst = $this->getAnalyst();
        while (1 == 1) {
            //$date    = date("Y-m-d");
            $date    = "2016-04-08";
            $url     = "http://api.benzinga.com/api/v2/calendar/ratings?token={$this->token}&parameters[date]={$date}&pagesize=1000";
            $data    = file_get_contents($url);
            $arrData = json_decode($data);
            if(count($arrData)>0){
                foreach ($arrData->ratings as $a) {
                
                
                    if (in_array(strtolower($a->analyst), $arrAnalyst) && in_array(strtolower($a->action_company), $arrAction)) {
                        //if(in_array(strtolower($a->action_company),$arrAction)){
                        $arrDisplay[$a->updated] = $a;
                    }
                 }
            
                krsort($arrDisplay);
                $totalCount = count($arrDisplay);
                if ($currentCount < $totalCount) {
                    echo "\n\n" . date("H:i:s", time() + 10800) . "\n\n";
                    exec('powershell -c (New-Object Media.SoundPlayer "C:\Windows\Media\Alarm08.wav").PlaySync()');
                    $currentCount = $totalCount;
                    $maxDisplay   = $totalCount >= 20 ? 20 : $totalCount;

                    foreach ($arrDisplay as $a) {
                        $c++;
                        echo date("H:i:s", $a->updated + 10800) . "\t" . $a->time . "\t" . $a->ticker . "\t" . substr($a->name, 0, 5) . "\t" . substr($a->action_company, 0, 1) . "\t" . substr($a->rating_current, 0, 3) . "\t" . substr($a->analyst, 0, 10) . "\n";
                        //if($c>=$maxDisplay) break;
                    }


                }
            }
            else{
                echo "No data for today $date";
                break;
            }
            
            sleep(10);
        }
    }
    
    function getAction()
    {
        $actions = file("actions.txt");
        foreach ($actions as $l)
            $arrAction[] = strtolower(trim($l, "\r\n"));
        return $arrAction;
    }
    
    function getAnalyst()
    {
        $analysts = file("analysts.txt");
        foreach ($analysts as $l)
            $arrAnalyst[] = strtolower(trim($l, "\r\n"));
        return $arrAnalyst;
    }
}
?>