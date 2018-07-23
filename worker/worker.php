<?php
require "setting.php";
require "function.php";
touch($logfile);
Write2Log("Starting worker");
while(42)
{
	/*	Pulling job from the the queue in form of mysql query
		The queue push by the pusher
	*/
	$konek=mysql_connect($mysql_server,$mysql_user,$mysql_pass);
	if(!$konek)
	{
		Write2Log("gagal konek db server");
		goto selesai1;
	}
	if(!mysql_select_db($mysql_db,$konek))
	{
		Write2Log("gagal select db");
		goto selesai1;
	}
	mysql_query("CALL PROCESS_JOB(@B,@C);",$konek);
	$ambil=mysql_fetch_array(mysql_query("SELECT @B AS ID_PROGRAM,@C AS ID_JOB;",$konek));
	$id_program=$ambil['ID_PROGRAM'];
	$id_job=$ambil['ID_JOB'];
	if ($id_program && $id_job)
	{
		foreach( glob('*.[Mm][XxPp][4Ff]') as $file )
		{
			unlink($file);
		}
		$progress_name=array();
		$number_progress=array();
		$step=0;
		Write2LogSql($WorkerID,$konek,"$id_program Processing with job $id_job");
		
		//fetch the clip from mediabase,first step for job 1,2,3, 
		if($id_job==1 || $id_job==2 || $id_job==3)
		{
			$step++;
			$progress_name[$step]="Transfer dari Mediabase";
			$number_progress[$step]="0 %";
			mysql_query("CALL INSERT_STEP_PROCESS('$id_job','$id_program','$progress_name[$step]','$number_progress[$step]');",$konek);
			Write2LogSql($WorkerID,$konek,"$id_program start ambil program dari mediabase");
			//begin transfer progress
			$session_fxp_src = ftp_connect($ftp_server_mediabase, 2098);
			$login_result = ftp_login($session_fxp_src, $ftp_user_mediabase, $ftp_pass_mediabase);
			if ((!$session_fxp_src) || (!$login_result))
			{ 
				$status_transcode="error";
				$info="gagal konek ke Mediabase untuk clip untuk $id_program dengan job $id_job";
				mysql_query("CALL JOB_ERROR('$id_program','$id_job','$info');",$konek);
				goto selesai2; 
			}
			Write2LogSql($WorkerID,$konek,"$id_program sukses konek ke mediabase");
			$session_fxp_dst = ftp_connect($ftp_server_youtube);
			$login_result = ftp_login($session_fxp_dst, $ftp_user_youtube_rw, $ftp_pass_youtube_rw);
			if ((!$session_fxp_dst) || (!$login_result))
			{ 
				$status_transcode="error";
				$info="gagal konek ke Server untuk clip untuk $id_program dengan job $id_job";
				mysql_query("CALL JOB_ERROR('$id_program','$id_job','$info');",$konek);
				goto selesai2;
			}
			Write2LogSql($WorkerID,$konek,"$id_program sukses konek ke ftp youtube");
			
			//$size_src=ftp_size($session_fxp_src,"$id_program");
			$size_src=file_get_contents("http://toa.nettv.co.id/parsing/tes-size-newsan.php?id=$id_program");
			$human_size_src=formatBytes($size_src);
			Write2LogSql($WorkerID,$konek,"$id_program ukuran di newsan $human_size_src");
			//ftp_raw($session_fxp_dst,"CWD /$dest_folder");
			ftp_raw($session_fxp_dst,"TYPE I");
			ftp_raw($session_fxp_src,"TYPE A");
			ftp_raw($session_fxp_src,"PASV");
			ftp_raw($session_fxp_src,"TYPE I");
			$pasvmode=substr(ftp_raw($session_fxp_dst, "PASV")[0],27,-1);
			ftp_raw($session_fxp_src,"PORT $pasvmode");
			ftp_raw($session_fxp_src,"RETR $id_program.mxf");
			ftp_raw($session_fxp_dst,"STOR $id_program.MXF");
			
			sleep(1);
			$j=0;
			$size_before=0;
			$responmgx=substr(ftp_raw($session_fxp_src,"NOOP")[0],0,52);
			while($responmgx=="550 Requested action not taken. Transfer in progress")
			{
			    sleep(1);
				$session_fxp_dst_cek = ftp_connect($ftp_server_youtube);
				$login_result = ftp_login($session_fxp_dst_cek, $ftp_user_youtube_ro, $ftp_pass_youtube_ro);
				if ((!$session_fxp_dst_cek) || (!$login_result))
				{ 
					goto lanjut;
				}
				$responftp=ftp_raw($session_fxp_dst_cek,"SIZE $id_program.MXF");
				$size_dst=floatval(str_replace('213 ', '', $responftp[0]));
				//$size_dst=ftp_size($session_fxp_dst_cek,"$id_program.MXF");
				$human_size_dst=formatBytes($size_dst);
				$progress=round(100*($size_dst / $size_src));
				$progress_speed=$size_dst-$size_before;
				$human_progress_speed=formatBytes($progress_speed);
				$number_progress[$step]="$progress %";
				mysql_query("CALL INSERT_STEP_PROCESS('$id_job','$id_program','$progress_name[$step]','$number_progress[$step]');",$konek);
				Write2LogSql($WorkerID,$konek,"$id_program transfer dari mediabase $progress % $human_size_dst of $human_size_src with $human_progress_speed /s");
				$size_before=$size_dst;
				lanjut:
				ftp_close($session_fxp_dst_cek);
				$j++;
				if ($j>$ftp_transfer_timeout) goto outcheck;
				$responmgx=substr(ftp_raw($session_fxp_src,"NOOP")[0],0,52);		
			}
			ftp_close($session_fxp_src);
			ftp_close($session_fxp_dst);
			outcheck:
			if ($j<$ftp_transfer_timeout)
			{
				$number_progress[$step]="100 %";
				mysql_query("CALL INSERT_STEP_PROCESS('$id_job','$id_program','$progress_name[$step]','$number_progress[$step]');",$konek);
			}
			else
			{
				$info="Transfer timeout";
				mysql_query("CALL JOB_ERROR('$id_program','$id_job','$info');",$konek);
				goto selesai2;
			}
			Write2LogSql($WorkerID,$konek,"$id_program transfer dari mediabase sukses");
		}
		
		//get segmentation and transcode, first step for job 4, second step for job 1,2
		if($id_job==1 || $id_job==2 || $id_job==4)
		{
			//get segmentasi
			$step++;
			$progress_name[$step]="Ambil Segmentasi";
			$number_progress[$step]="0 %";
			mysql_query("CALL INSERT_STEP_PROCESS('$id_job','$id_program','$progress_name[$step]','$number_progress[$step]');",$konek);
			Write2LogSql($WorkerID,$konek,"$id_program ambil segment");

			$baris=file_get_contents("http://toa.nettv.co.id/parsing/tes-segmen2.php?id=$id_program");
			if(!$baris)
			{
			    $info="Segmentasi tidak ada";
			    mysql_query("CALL JOB_ERROR('$id_program','$id_job','$info');",$konek);
			    Write2LogSql($WorkerID,$konek,"$id_program $info");
				goto selesai2;
			}
			if(strlen($baris)<40)
			{
			    $info="Segmentasi salah";
			    mysql_query("CALL JOB_ERROR('$id_program','$id_job','$info');",$konek);
			    Write2LogSql($WorkerID,$konek,"$id_program $info");
				goto selesai2;
			}
			Write2LogSql($WorkerID,$konek,"$id_program ambil segment kelar");
			Write2LogSql($WorkerID,$konek,"$id_program $baris");
			$jumlahsegmen=round(strlen($baris)/40);
			Write2LogSql($WorkerID,$konek,"$id_program jumlah segment : $jumlahsegmen");
			if($jumlahsegmen<2)
			{
			    $info="Jumlah Segmen hanya 1";
			    mysql_query("CALL JOB_ERROR('$id_program','$id_job','$info');",$konek);
			    Write2LogSql($WorkerID,$konek,"$id_program $info");
				goto selesai2;
			}
			$number_progress[$step]="100 %";
			mysql_query("CALL INSERT_STEP_PROCESS('$id_job','$id_program','$progress_name[$step]','$number_progress[$step]');",$konek);
			$segmen=array();
			$ss=array();
			$dur=array();
			$durfr=array();
			if($id_job==2 || $id_job==4)
			{
				if(file_exists($ffmpeg_list_segment_file))unlink($ffmpeg_list_segment_file);
				touch($ffmpeg_list_segment_file);
			}
			for($i=0;$i<$jumlahsegmen;$i++)
			{
				$segmen[$i]=$i+1;
				$ss[$i]=FrameToMS(substr($baris,(($i*40)+15),11));
				$dur[$i]=FrameToMS(substr($baris,(($i*40)+28),11));
				$durfr[$i]=FrameToTotalFrame(substr($baris,(($i*40)+28),11));
				if(file_exists($ffmpeg_progress_file))unlink($ffmpeg_progress_file);
				$command="$binary_ffmpeg -progress $ffmpeg_progress_file -ss $ss[$i] -i $id_program.MXF -t $dur[$i] $ffmpeg_option_filter $ffmpeg_option_video $ffmpeg_option_audio -y $youtube_destination_folder\\$id_program-SEGMEN$segmen[$i].mp4";
				if($id_job==2 || $id_job==4)
				{
					file_put_contents($ffmpeg_list_segment_file,"file '$id_program-SEGMEN$segmen[$i].mp4'\r\n",FILE_APPEND | LOCK_EX);
					if($id_job==4)
					{
						if(!file_exists("$ott_queue_folder\\$id_program.MXF"))
						{
							$info="Source file hilang";
							mysql_query("CALL JOB_ERROR('$id_program','$id_job','$info');",$konek);
							Write2LogSql($WorkerID,$konek,"$id_program $info");
							goto selesai2;
						}
						$command="$binary_ffmpeg -progress $ffmpeg_progress_file -ss $ss[$i] -i $ott_queue_folder\\$id_program.MXF -t $dur[$i] $ffmpeg_option_filter $ffmpeg_option_video $ffmpeg_option_audio -y $id_program-SEGMEN$segmen[$i].mp4";
					}
					else $command="$binary_ffmpeg -progress $ffmpeg_progress_file -ss $ss[$i] -i $id_program.MXF -t $dur[$i] $ffmpeg_option_filter $ffmpeg_option_video $ffmpeg_option_audio -y $id_program-SEGMEN$segmen[$i].mp4";
				}
				Write2LogSql($WorkerID,$konek,"$id_program kirim command $command");
                pclose(popen("start /B $command >nul 2>&1", "r"));
                Write2LogSql($WorkerID,$konek,"$id_program command terkirim");
                $step++;
				$progress_name[$step]="Transcode Segmen $segmen[$i] dari $jumlahsegmen Segmen";
				Write2LogSql($WorkerID,$konek,"$id_program $progress_name[$step]");
				sleep(30);          
				$ffmpeg_progress=ReadLastLine($ffmpeg_progress_file,12);
				$cocok=preg_grep("/continue/",$ffmpeg_progress);
				while ($cocok)
				{
					preg_match("/frame=(\d+)\s/",$ffmpeg_progress[0],$frame);
					$progress=round(100*(intval($frame[1]) / $durfr[$i]));
					$number_progress[$step]="$progress %";
					Write2LogSql($WorkerID,$konek,"$id_program Transcode Segmen $segmen[$i] dari $jumlahsegmen $number_progress[$step]");
					mysql_query("CALL INSERT_STEP_PROCESS('$id_job','$id_program','$progress_name[$step]','$number_progress[$step]');",$konek);
					sleep(3);
					$ffmpeg_progress=ReadLastLine($ffmpeg_progress_file,12);
					preg_match("/continue/",$ffmpeg_progress[10],$cocok);
				}
				$cocok=preg_grep("/end/",$ffmpeg_progress);
				if($cocok)
				{
					$number_progress[$step]="100 %";
					mysql_query("CALL INSERT_STEP_PROCESS('$id_job','$id_program','$progress_name[$step]','$number_progress[$step]');",$konek);	
					Write2LogSql($WorkerID,$konek,"$id_program Sukses transcode Segmen$segmen[$i]");
				}
				else
				{
				    $info=implode(" ".$ffmpeg_progress);
				    mysql_query("CALL JOB_ERROR('$id_program','$id_job','Gagal Transcode Segmen$segmen[$i] - $info');",$konek);
				    Write2LogSql($WorkerID,$konek,"$id_program Gagal Transcode Segmen$segmen[$i] - $info");
				    goto selesai2;
				}
				unlink($ffmpeg_progress_file);
			}
			if($id_job==2 || $id_job==4)
			{
				$step++;
				$progress_name[$step]="Menggabungkan $jumlahsegmen Segmen";
				$totaldurfr=array_sum($durfr);
				$baris=file_get_contents("http://toa.nettv.co.id/parsing/tes-segmen2.php?id=$id_program");
				$jumlahsegmen=round(strlen($baris)/40);
				if(file_exists($ffmpeg_progress_file))unlink($ffmpeg_progress_file);
				if($id_job==2)$command="$binary_ffmpeg -progress $ffmpeg_progress_file -f concat -i $ffmpeg_list_segment_file -c copy -y $youtube_destination_folder\\$id_program.mp4";
				else $command="$binary_ffmpeg -progress $ffmpeg_progress_file -f concat -i $ffmpeg_list_segment_file -c copy -y $ott_destination_folder\\$id_program.mp4";
				Write2LogSql($WorkerID,$konek,"$id_program kirim command $command");
                pclose(popen("start /B $command >nul 2>&1", "r"));
                Write2LogSql($WorkerID,$konek,"$id_program command terkirim");
				sleep(30);
                $ffmpeg_progress=ReadLastLine($ffmpeg_progress_file,12);
				$cocok=preg_grep("/continue/",$ffmpeg_progress);
				while ($cocok)
				{
					preg_match("/frame=(\d+)\s/",$ffmpeg_progress[0],$frame);
					$progress=round(100*(intval($frame[1]) / $totaldurfr));
					$number_progress[$step]="$progress %";
					Write2LogSql($WorkerID,$konek,"$id_program Menggabungkan $jumlahsegmen Segmen $number_progress[$step]");
					mysql_query("CALL INSERT_STEP_PROCESS('$id_job','$id_program','$progress_name[$step]','$number_progress[$step]');",$konek);
					sleep(5);
					$ffmpeg_progress=ReadLastLine($ffmpeg_progress_file,12);
					preg_match("/continue/",$ffmpeg_progress[10],$cocok);				
				}
				$cocok=preg_grep("/end/",$ffmpeg_progress);
				if($cocok)
				{
					$number_progress[$step]="100 %";
					mysql_query("CALL INSERT_STEP_PROCESS('$id_job','$id_program','$progress_name[$step]','$number_progress[$step]');",$konek);
					Write2LogSql($WorkerID,$konek,"$id_program Sukses digabungkan");
					for($i=0;$i<$jumlahsegmen;$i++)
					{
						$seg=$i+1;
						unlink("$id_program-SEGMEN$seg.mp4");
						if(!file_exists("$id_program-SEGMEN$seg.mp4"))Write2LogSql($WorkerID,$konek,"$id_program Sukses menghapus $id_program-SEGMEN$seg.mp4");
						else Write2LogSql($WorkerID,$konek,"$id_program Gagal menghapus id_program-SEGMEN$seg.mp4");
					}
				}
				else
				{
				    $info=implode(" ",$ffmpeg_progress);
				    mysql_query("CALL JOB_ERROR('$id_program','$id_job','Gagal digabungkan - $info');",$konek);
					Write2LogSql($WorkerID,$konek,"$id_program Gagal digabungkan - $info");
				    goto selesai2;
				}
			}
			//job sukses
			if($id_job==1 || $id_job==2) unlink("$id_program.MXF");
			else unlink("$ott_queue_folder\\$id_program.MXF");
			mysql_query("CALL JOB_SUCCESS('$id_program','$id_job');",$konek);
			Write2LogSql($WorkerID,$konek,"$id_program $id_job Job selesai dengan sukses");
		}

		//second step for job 3, just transcode it (belum kelar)
		if($id_job==3)
		{
			//get duration of clip
			/* old method using ffprobe output and parse the duration
			$command="$binary_ffprobe c:\\lighttpd\\htdocs\\$id_program.MXF 2>&1";
			if(file_exists("command.bat"))unlink("command.bat");
			file_put_contents("command.bat",$command);
			$tes=shell_exec("start /b command.bat");
			unlink("command.bat");
			preg_match("/Duration:\s(.+),\ss/",$tes,$tes2);
			*/
			$durch=$baris=file_get_contents("http://toa.nettv.co.id/parsing/tes-durasi-newsan.php?id=$id_program");
			Write2LogSql($WorkerID,$konek,"$id_program durasi $durch");
			$totaldurfr=FrameToTotalFrame($durch);
			
			if(file_exists( $ffmpeg_progress_file))unlink( $ffmpeg_progress_file);
			$command="$binary_ffmpeg -progress $ffmpeg_progress_file -i $id_program.MXF $ffmpeg_option_filter $ffmpeg_option_video $ffmpeg_option_audio -y $youtube_destination_folder\\$id_program.mp4";
			Write2LogSql($WorkerID,$konek,"$id_program kirim command $command");
			pclose(popen("start /B $command >nul 2>&1", "r"));
			Write2LogSql($WorkerID,$konek,"$id_program command terkirim");
			$step++;
			$progress_name[$step]="Transcode";
			Write2LogSql($WorkerID,$konek,"$id_program $progress_name[$step]");
			sleep(10); 
			$ffmpeg_progress=ReadLastLine($ffmpeg_progress_file,12);
			$cocok=preg_grep("/continue/",$ffmpeg_progress);
			while ($cocok)
			{
				preg_match("/frame=(\d+)\s/",$ffmpeg_progress[0],$frame);
				$progress=round(100*(intval($frame[1]) / $totaldurfr));
				$number_progress[$step]="$progress %";
				Write2LogSql($WorkerID,$konek,"$id_program Transcode $number_progress[$step]");
				mysql_query("CALL INSERT_STEP_PROCESS('$id_job','$id_program','$progress_name[$step]','$number_progress[$step]');",$konek);
				sleep(3);
				$ffmpeg_progress=ReadLastLine($ffmpeg_progress_file,12);
				preg_match("/continue/",$ffmpeg_progress[10],$cocok);
			}
			$cocok=preg_grep("/end/",$ffmpeg_progress);
			if($cocok)
			{
				$number_progress[$step]="100 %";
				mysql_query("CALL INSERT_STEP_PROCESS('$id_job','$id_program','$progress_name[$step]','$number_progress[$step]');",$konek);	
				Write2LogSql($WorkerID,$konek,"$id_program Sukses transcode");
			}
			else
			{
				$info=implode(" ".$ffmpeg_progress);
				mysql_query("CALL JOB_ERROR('$id_program','$id_job','Gagal Transcode - $info');",$konek);
				Write2LogSql($WorkerID,$konek,"$id_program Gagal Transcode - $info");
				goto selesai2;
			}
			unlink($ffmpeg_progress_file);
			//joh sukses
			unlink("$id_program.MXF");
			mysql_query("CALL JOB_SUCCESS('$id_program','$id_job');",$konek);
			Write2LogSql($WorkerID,$konek,"$id_program $id_job Job selesai dengan sukses");
		}
		
		//pointer job error
		selesai2:
		if(file_exists($ffmpeg_list_segment_file))unlink($ffmpeg_list_segment_file);
		if(file_exists($ffmpeg_progress_file))unlink($ffmpeg_progress_file);
		mysql_query("DELETE FROM TB_PROCESS WHERE ID_PROGRAM = '$id_program' and ID_JOB = '$id_job';",$konek);
	}
	else
	{
		$jam=date("Y-m-d H:i:s");
		echo"$jam - Tidak ada job\n";
	}
	selesai:
	
	//Rotate Log
	if(filesize($logfile)>$Size2Rotate)
	{
	    for($i=$maxlogfile;$i>1;$i--)
	    {
	        $old=$i-1;
			if(file_exists("$logfile.$old"))rename("$logfile.$old","$logfile.$i");
	    }
	    rename($logfile,"$logfile.1");
	    touch($logfile);
	}
	mysql_query("DELETE FROM TB_WORKER_LOG WHERE LOG_TIME < DATE_SUB(NOW(), INTERVAL 72 HOUR);",$konek);
	selesai1:
	mysql_close($konek);
	sleep(15);
}
?>
