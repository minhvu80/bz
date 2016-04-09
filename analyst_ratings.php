<?
    $token = file_get_contents("token.txt");
    $actions=file("actions.txt");
    foreach($actions as $l) $arrAction[]=strtolower(trim($l,"\r\n"));
    print_r($arrAction);
		
    while(1==1){
	$arrAction = array();
        $actions=file("actions.txt");
        foreach($actions as $l) $arrAction[]=  strtolower(trim($l,"\r\n"));
        
        $analysts=file("analysts.txt");
        foreach($analysts as $l) $arrAnalyst[]=strtolower(trim($l,"\r\n"));
        
        $date = date("Y-m-d");
	$date = "2016-04-08";	        
	$url = "http://api.benzinga.com/api/v2/calendar/ratings?token=$token&parameters[date]={$date}&pagesize=1000";
		
	$data = file_get_contents($url);
		
		
		
        $arrData = json_decode($data);
        
		
             
        
            
            foreach($arrData->ratings as $a){
                
                
                if(in_array(strtolower($a->analyst),$arrAnalyst) && in_array(strtolower($a->action_company),$arrAction)){
                //if(in_array(strtolower($a->action_company),$arrAction)){
                    $arrDisplay[$a->updated]=$a;                 
                }
                
                
                
            }
        
            
            krsort($arrDisplay);
            $totalCount = count($arrDisplay);
            if($currentCount<$totalCount){
                echo "\n\n".date("H:i:s",time()+10800)."\n\n";
                exec('powershell -c (New-Object Media.SoundPlayer "C:\Windows\Media\Alarm08.wav").PlaySync()');
                $currentCount=$totalCount;
				$maxDisplay = $totalCount>=20 ? 20 : $totalCount;
				
                foreach($arrDisplay as $a){
                    $c++;
                    echo date("H:i:s",$a->updated+10800)."\t".$a->time."\t".$a->ticker."\t".substr($a->name,0,5)."\t".substr($a->action_company,0,1)."\t".substr($a->rating_current,0,3)."\t".substr($a->analyst,0,10)."\n";
                    //if($c>=$maxDisplay) break;
                }
                
                
            }
            
            
            sleep(10);
    }                
?>