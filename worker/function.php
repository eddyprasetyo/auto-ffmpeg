<?php

/* Function to Change timecode format from HH:MM:SS:FF to HH:MM:SS.MS
accept string as parameter, and return string too
doesn't validate the input
*/
function FrameToMS($timecode)
{
	$strframe=substr($timecode,-2);
	$intms=(intval($strframe)*40);
	$strms=strval($intms);
	$temptimecode=substr($timecode,0,-3);
	$newtimecode="$temptimecode.$strms";
	return $newtimecode;
	
}

/* Function to Change timecode format from HH:MM:SS:FF(string) to total frame (integer)
accept string as parameter, and return integer
doesn't validate the input
*/

function FrameToTotalFrame($timecode)
{
	$inthour=intval(substr($timecode,0,2));
	$intminute=intval(substr($timecode,3,2));
	$intsecond=intval(substr($timecode,6,2));
	$intframe=intval(substr($timecode,9,2));
	$total=(90000*$inthour)+(1500*$intminute)+(25*$intsecond)+$intframe;
	return $total;
}
/* Function to Change timecode format from HH:MM:SS:MS(string) to total frame (integer)
accept string as parameter, and return integer
doesn't validate the input
*/
function MSToTotalFrame($timecode)
{
    $inthour=intval(substr($timecode,0,2));
	$intminute=intval(substr($timecode,3,2));
	$intsecond=intval(substr($timecode,6,2));
	$intframe=intval(substr($timecode,9,2))/4;
	$total=(90000*$inthour)+(1500*$intminute)+(25*$intsecond)+$intframe;
	return $total;
}
//for loging and display
function Write2Log($message)
{
    $logfile="worker.log";
    $jam=date("H:i:s");
	file_put_contents($logfile,"$jam - $message\r\n",FILE_APPEND | LOCK_EX);
	echo"$jam - $message\n";
}
function Write2LogSql($WorkerID,$MySqlResource,$message)
{
    $logfile="worker.log";
    $jam=date("Y-m-d H:i:s");
	file_put_contents($logfile,"$jam - $message\r\n",FILE_APPEND | LOCK_EX);
	mysql_query("CALL INSERT_WORKER_LOG('$WorkerID','$message');",$MySqlResource);
	echo"$jam - $message\n";
}

/* get last line from text
accept string as parameter, and return an array
doesn't validate the input
*/
function ReadLastLine($file,$linecount)
{
//how many lines?
//$linecount=11;

//what's a typical line length?
$length=25;

//which file?
//$file="progress.txt";

//we double the offset factor on each iteration
//if our first guess at the file offset doesn't
//yield $linecount lines
$offset_factor=1;


$bytes=filesize($file);

$fp = fopen($file, "r");
if(!$fp)
{
	return false;
	goto endFunction;
}

$complete=false;
while (!$complete)
{
    //seek to a position close to end of file
    $offset = $linecount * $length * $offset_factor;
    fseek($fp, -$offset, SEEK_END);


    //we might seek mid-line, so read partial line
    //if our offset means we're reading the whole file, 
    //we don't skip...
    if ($offset<$bytes)
        fgets($fp);

    //read all following lines, store last x
    $lines=array();
    while(!feof($fp))
    {
        $line = fgets($fp);
        array_push($lines, $line);
        if (count($lines)>$linecount)
        {
            array_shift($lines);
            $complete=true;
        }
    }

    //if we read the whole file, we're done, even if we
    //don't have enough lines
    if ($offset>=$bytes)
        $complete=true;
    else
        $offset_factor*=2; //otherwise let's seek even further back

}
return $lines;
endFunction:
fclose($fp);



}


function formatBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');   

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}
?>