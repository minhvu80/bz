<?php

/**
 * Description of benzinga
 *
 * @author PCUser
 */
class Benzinga
{
    public $token;
    public $sleep_time;
    
    function __construct($sleep_time=10){
        $this->token = file_get_contents("token.txt");  
        $this->sleep_time = $sleep_time;
    }
    
    function getRating($date=""){
        $arrAction  = $this->getAction();
        $arrAnalyst = $this->getAnalyst();
        $foreverloop = true;
        $date = $date=="" ? date("Y-m-d") : $date;
        while ($foreverloop) {            
            $url     = "http://api.benzinga.com/api/v2/calendar/ratings?token={$this->token}&parameters[date]={$date}&pagesize=1000";
            $data    = file_get_contents($url);
            $arrData = json_decode($data);
            if(count($arrData)>0){
                foreach ($arrData->ratings as $a) {
                    // put ratings into array with timestamp            
                    if (in_array(strtolower($a->analyst), $arrAnalyst) && in_array(strtolower($a->action_company), $arrAction)) {
                        $arrDisplay[$a->updated] = $a;
                    }
                 }
            
                krsort($arrDisplay);
                $totalCount = count($arrDisplay);
                // track difference and alert user of new changes
                if ($currentCount < $totalCount) {                    
                    echo "\n\n" . date("H:i:s", time() + 10800) . "\n\n";
                    exec('powershell -c (New-Object Media.SoundPlayer "C:\Windows\Media\Alarm08.wav").PlaySync()');
                    $currentCount = $totalCount;
                    $maxDisplay   = $totalCount >= 20 ? 20 : $totalCount;

                    foreach ($arrDisplay as $a) {
                        $c++;
                        echo date("H:i:s", $a->updated + 10800) . "\t" . $a->time . "\t" . $a->ticker . "\t" . substr($a->name, 0, 5) . "\t" . substr($a->action_company, 0, 1) . "\t" . substr($a->rating_current, 0, 3) . "\t" . substr($a->analyst, 0, 10) . "\n";                        
                    }
                }
                // pause before polling the next call
                sleep($this->sleep_time);
            }
            else{
                echo "No data for today $date";
                $foreverloop = false;
            }
            
            
        }
    }
    
    function getAction()
    {
        $actions = file("actions.txt");
        foreach ($actions as $l){
            $arrAction[] = strtolower(trim($l, "\r\n"));
        }
        return $arrAction;
    }
    
    function getAnalyst()
    {
        $analysts = file("analysts.txt");
        foreach ($analysts as $l){
            $arrAnalyst[] = strtolower(trim($l, "\r\n"));
        }
        return $arrAnalyst;
    }
}
?>