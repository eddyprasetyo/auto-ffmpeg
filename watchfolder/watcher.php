#!/usr/bin/php
<?php
require "setting.php";
function write2log($message)
{
	$log="/var/log/php.script/ott.log"; // put your absoulte path log here
	$jam=date("H:i:s");
	file_put_contents($log,"$jam - $message\n",FILE_APPEND | LOCK_EX);
}
write2log("Status: starting up");
$ukuran=array();
$ukuransebelumnya=array();
while(42)
{
	$session_ftp_check=ftp_connect($ftp_server_watchfolder);
	$login_result=ftp_login($session_ftp_check,$ftp_user_watchfolder,$ftp_password_watchfolder);
	if((!$session_ftp_check) || (!$login_result))
	{
		write2log("Gagal Masuk ke watch folder");
		goto end1;
	}
	$dirlist=ftp_nlist($session_ftp_check,$watch_folder);
	if(count($dirlist)<1)
	{
		write2log("Tidak ada file");
		goto end1;
	}
	$konek=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
	if(!$konek)
	{
		write2log("gagal konek db server");
		goto end2;
	}
	if(!mysql_select_db($mysql_db,$konek))
	{
		write2log("gagal select db");
		goto end2;
	}
	for($i=0;$i<count($dirlist);$i++)
	{
	    preg_match("^/ifs/data/ott/(.+)\.[MXF|mxf]^",$dirlist[$i],$idprogram);
		if($idprogram)
		{
			$kueri=mysql_query("SELECT * FROM TB_QUEUE WHERE ID_PROGRAM = '$idprogram[1]' AND ID_JOB = '4';");
			if(mysql_num_rows($kueri))goto end3;
			$ukuran[$idprogram[1]]=ftp_size($session_ftp_check,"$watch_folder/$idprogram[1].MXF");
			if(!array_key_exists($idprogram[1],$ukuransebelumnya))$ukuransebelumnya[$idprogram[1]]=0;
			if($ukuran[$idprogram[1]] ==  $ukuransebelumnya[$idprogram[1]])
			{
				mysql_query("CALL INSERT_JOB('$idprogram[1]','4',@A);");
				$ambil=mysql_fetch_array(mysql_query("SELECT @A AS MESSAGE;"));
				if($ambil['MESSAGE'] == "JOB BERHASIL DIMASUKKAN")write2log("$idprogram[1] masuk antrian");
			}
			$ukuransebelumnya[$idprogram[1]]=$ukuran[$idprogram[1]];
			end3:
		}
	}
	end2:
	mysql_close($konek);
	end1:
	ftp_close($session_ftp_check);
	sleep(15);
}//end while
?>
