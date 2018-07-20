var TimeIn = [];
var TimeOut = [];
var TimecodeIn =[];
var Dur =[];
var Duration = [];
var Pot = 0;
var TableRow = [];
var TableDataTimeIn=[];
var TableDataTimeOut=[];
var TableDataDuration=[];
var TableDataPot=[];
window.onload = function(){
	document.getElementById("buttontcin").disabled = false;
}
function onTimeUpdate(event){
	document.getElementById("currentTimeCode").innerHTML = MstoTime(event.currentTime);
}
function tostart(){
	var myVideo = document.getElementById("video1");
	myVideo.pause();
	myVideo.currentTime = 0;
}
function toend(){
	var myVideo = document.getElementById("video1");
	myVideo.pause();
	myVideo.currentTime = myVideo.duration;
}
function reserve1s(){
	var myVideo = document.getElementById("video1");
	myVideo.pause();
	var currtime = myVideo.currentTime;
	myVideo.currentTime = currtime - 1;
}
function reserve10s(){
	var myVideo = document.getElementById("video1");
	myVideo.pause();
	var currtime = myVideo.currentTime;
	myVideo.currentTime = currtime - 10;
}
function reserve60s(){
	var myVideo = document.getElementById("video1");
	myVideo.pause();
	var currtime = myVideo.currentTime;
	myVideo.currentTime = currtime - 60;
}
function pause(){
	var myVideo = document.getElementById("video1");
	if (myVideo.paused){}
	else myVideo.pause();
}
function play(){
	var myVideo = document.getElementById("video1");
	if (myVideo.paused) myVideo.play();
}
function forward1s(){
	var myVideo = document.getElementById("video1");
	myVideo.pause();
	var currtime = myVideo.currentTime;
	myVideo.currentTime = currtime + 1;	
}
function forward10s(){
	var myVideo = document.getElementById("video1");
	myVideo.pause();
	var currtime = myVideo.currentTime;
	myVideo.currentTime = currtime + 10;	
}
function forward60s(){
	var myVideo = document.getElementById("video1");
	myVideo.pause();
	var currtime = myVideo.currentTime;
	myVideo.currentTime = currtime + 60;	
}
function tcin(){	
	if(Pot==0){
		//cek apa ada in apa enggak
		if(TimeIn[Pot]){
			//cek TimeOut kalo gak ada create TR and new TimeIn
			if(TimeOut[Pot]){
				Pot++;
				var myTable = document.getElementById("segtab");
				var myVideo = document.getElementById("video1");
				TableRow[Pot] = myTable.insertRow();
				TableDataTimeIn[Pot] = TableRow[Pot].insertCell();
				TimeIn[Pot] = myVideo.currentTime;
				TableDataTimeIn[Pot].innerHTML = secondsToTime(TimeIn[Pot]);
			}
			else {
				//update TimeIn
				var myVideo = document.getElementById("video1");
				TimeIn[Pot] = myVideo.currentTime;
				TableDataTimeIn[Pot].innerHTML = secondsToTime(TimeIn[Pot]);			
			}
		}
		else{
			//Create TR and new TimeIn
			var myTable = document.getElementById("segtab");
			var myVideo = document.getElementById("video1");
			TableRow[Pot] = myTable.insertRow();
			TableDataTimeIn[Pot] = TableRow[Pot].insertCell(0);
			TimeIn[Pot] = myVideo.currentTime;
			TableDataTimeIn[Pot].innerHTML = secondsToTime(TimeIn[Pot]);
			document.getElementById("buttontcout").disabled = false;
		}
	}
	else{
		if (TimeOut[Pot]){
			//update Pot create TR and new TimeIn
			Pot++;
			var myTable = document.getElementById("segtab");
			var myVideo = document.getElementById("video1");
			TableRow[Pot] = myTable.insertRow();
			TableDataTimeIn[Pot] = TableRow[Pot].insertCell();
			TimeIn[Pot] = myVideo.currentTime;
			TableDataTimeIn[Pot].innerHTML = secondsToTime(TimeIn[Pot]);
		}
		else {
			//update TimeIn
			var myVideo = document.getElementById("video1");
			TimeIn[Pot] = myVideo.currentTime;
			TableDataTimeIn[Pot].innerHTML = secondsToTime(TimeIn[Pot]);
		}	
	}
}
function tcout(){
	if (TimeOut[Pot]){
		//update TD TimeOut, Durasi
		var myTable = document.getElementById("segtab");
		var myVideo = document.getElementById("video1");
		TimeOut[Pot] = myVideo.currentTime;
		if (TimeOut[Pot] < TimeIn[Pot]){
			TableDataTimeOut[Pot].innerHTML = "error";
			TableDataDuration[Pot].innerHTML = "error";
			TableDataPot[Pot].innerHTML = "error";
			alert("Timecode out lebih awal dari Timecode in!!!\nSilahkan pilih Timecode yang sesuai");
			document.getElementById("buttonsegmentasimanual").disabled = true;
			document.getElementById("buttontcin").disabled = true;
		}
		else {
			TimecodeIn[Pot]=secondsToTime(TimeIn[Pot]);
			TableDataTimeOut[Pot].innerHTML = secondsToTime(TimeOut[Pot]);
			Duration[Pot] = TimeOut[Pot] - TimeIn[Pot];
			Dur[Pot]= secondsToTime(Duration[Pot]);
			TableDataDuration[Pot].innerHTML = Dur[Pot];
			TableDataPot[Pot].innerHTML = Pot + 1 ;
			document.getElementById("buttontcin").disabled = false;
			document.getElementById("buttonsegmentasimanual").disabled = false;
		}
	}
	else {
		//create TD TimeOut, Durasi, and Potongan Ke
		var myTable = document.getElementById("segtab");
		var myVideo = document.getElementById("video1");
		TableDataTimeOut[Pot] = TableRow[Pot].insertCell(1);
		TableDataDuration[Pot] = TableRow[Pot].insertCell(2);
		TableDataPot[Pot] = TableRow[Pot].insertCell(3);
		TimeOut[Pot] = myVideo.currentTime;
		if (TimeOut[Pot] < TimeIn[Pot]){
			TableDataTimeOut[Pot].innerHTML = "error";
			TableDataDuration[Pot].innerHTML = "error";
			TableDataPot[Pot].innerHTML = "error";
			alert("Timecode out lebih awal dari Timecode in!!!\nSilahkan pilih Timecode yang sesuai");
			document.getElementById("buttonsegmentasimanual").disabled = true;
			document.getElementById("buttontcin").disabled = true;
		}
		else {
			TimecodeIn[Pot]=secondsToTime(TimeIn[Pot]);
			TableDataTimeOut[Pot].innerHTML = secondsToTime(TimeOut[Pot]);
			Duration[Pot] = TimeOut[Pot] - TimeIn[Pot];
			Dur[Pot]=secondsToTime(Duration[Pot]);
			TableDataDuration[Pot].innerHTML = Dur[Pot];
			TableDataPot[Pot].innerHTML = Pot + 1 ;
			document.getElementById("buttonsegmentasimanual").disabled = false;
		}		
	}
}
function secondsToTime(secs){
    secs = Math.round(secs);
    var hours = Math.floor(secs / (60 * 60));
    var divisor_for_minutes = secs % (60 * 60);
    var minutes = Math.floor(divisor_for_minutes / 60);
    var divisor_for_seconds = divisor_for_minutes % 60;
    var seconds = Math.ceil(divisor_for_seconds);
	if (hours <10) hours = "0"+hours;
	if (minutes <10) minutes = "0"+minutes;
	if (seconds <10) seconds = "0"+seconds;	
    return hours+":"+minutes+":"+seconds;
}
function MstoTime(currtime){
    ms = Math.round(currtime*1000);
    var hours = Math.floor(ms / (60 * 60 * 1000));
    var divisor_for_minutes = ms % (60 * 60 * 1000);
    var minutes = Math.floor(divisor_for_minutes / (60 * 1000));
    var divisor_for_seconds = divisor_for_minutes % (60 * 1000);
	var seconds = Math.floor(divisor_for_seconds / 1000);
	var miliseconds = divisor_for_seconds % 1000;
	if (hours <10) hours = "0"+hours;
	if (minutes <10) minutes = "0"+minutes;
	if (seconds <10) seconds = "0"+seconds;
	if (miliseconds <100){
		if (miliseconds < 10) miliseconds = "00"+miliseconds;
		else miliseconds = "0"+miliseconds;
	}	
    return hours+":"+minutes+":"+seconds+"."+miliseconds;
}
function TimetoTimeinFrame(currtime){
    ms = Math.round(currtime*25);
    var hours = Math.floor(ms / (60 * 60 * 25));
    var divisor_for_minutes = ms % (60 * 60 * 25);
    var minutes = Math.floor(divisor_for_minutes / (60 * 25));
    var divisor_for_seconds = divisor_for_minutes % (60 * 25);
	var seconds = Math.floor(divisor_for_seconds / 25);
	var frame = divisor_for_seconds % 25;
	if (hours <10) hours = "0"+hours;
	if (minutes <10) minutes = "0"+minutes;
	if (seconds <10) seconds = "0"+seconds;
	if (frame < 10) frame = "0"+frame;
    return hours+":"+minutes+":"+seconds+"."+frame;
}
function kirim(){
	document.form1.insegmentasimanual.value=TimecodeIn.toString();
	document.form1.dursegmentasimanual.value=Dur.toString();
}