
var request = null;
var response="undefined";
var devCfg="undefined";
var error="Error";
var g_szCompressType = "1";
var g_szRotate = "None";
var requestTimeout=60000;

function CreateRequest(){  
	request = null;
    try {
      request = new XMLHttpRequest();
    } catch (trymicrosoft) {
      try {
        request = new ActiveXObject("Msxml2.XMLHTTP");
      } catch (othermicrosoft) {
        try {
          request = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (failed) {
          request = false;
        }
      }
    }

    if (!request)
        alert("Error initializing XMLHttpRequest!");
    return request;
}
function updatePage(){
        if (request.readyState == 4)
        {
           if (request.status == 200){       
               response = request.responseText
               alert(response); 
               //response = request.responseText.split("\n");
               //alert(request.responseText);
               //alert(response[0]);
           }
           else if (request.status == 404)
               alert("Request URL does not exist");
           else
               alert("Error: status code is " + request.status);
        }
}

function requestAbort(){
  alert(loadLangString("L_ServerNoResponse",false));
	request.abort();
}

function HttpRequest(method, cgistr, cb, async, timeout){ 
    var tempstring = eval("'"+cgistr+"'");
    var timer;
    //alert( method+' '+tempstring );
    request=CreateRequest();
    if ( request == null) { // Sorry, no Ajax
        alert("Ajax not available");
        return null;
    }
    request.open(method, tempstring, async); //synchronous
    //request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    if(timeout > 0)
    {//alert(timeout);
    	timer = setTimeout("requestAbort()",timeout);
    }
    
    if(async)
    {
        request.onreadystatechange = function(){  
            if (request.readyState == 4)
            {
            	 if(timeout > 0)
            		clearTimeout(timer);
            	
               if (request.status == 200){       
                   response = request.responseText
                   //alert(response); 
                   //devCfg=response;
                   if(cb) cb();
                   //response = request.responseText.split("\n");
                   //alert(request.responseText);
                   //alert(response[0]);
               }
               else if (request.status == 404)
                   alert("Request URL does not exist");
//Qmik add start, Scott Chang, 2007/5/20
	             else if(request.status == 0){
	                 //alert("Request abort");
	             }
//Qmik add end, Scott Chang, 2007/5/20
               else {
                   //alert("Error: status code is " + request.status);
               }
            }
        }
    }
    if(method=="POST")
    {
    	//request.setRequestHeader("Content-Type", "multipart/form-data");
        request.send(cgistr);
    }
    else
    {
        request.setRequestHeader( "If-Modified-Since", "Sat, 1 Jan 2000 00:00:00 GMT" );
        request.send(null);
    }
    if(async) return;
    //response = request.responseText;
    //response.setHeader( "Pragma", "no-cache" );
    //response.addHeader( "Cache-Control", "must-revalidate" );
    //response.addHeader( "Cache-Control", "no-cache" );
    //response.addHeader( "Cache-Control", "no-store" );
    //response.setDateHeader("Expires", 0); 
    //alert(request.responseText);
    if (request.status != 200)
    {
    	  if(timeout > 0){
    	  	//alert("clear timeout error");
          clearTimeout(timer);}
        return error;
    }else{
    	  if(timeout > 0){
    	  	//alert("clear timeout");    	  	
          clearTimeout(timer);}
        return request.responseText;
    }
}
function SendCGICMD(){  
    return HttpRequest("GET", arguments[0], arguments[1]?arguments[1]:null, arguments[2]?arguments[2]:false, arguments[3]?arguments[3]:requestTimeout);
}

function GetDeviceInfo(catalog){  
    var cgi = eval("'/cgi-bin/operator/param?action=list&group="+catalog+"'");
    devCfg=HttpRequest("GET", cgi, null, false, requestTimeout);   
}
function GetDeviceInfo_A(level,catalog){  
    var cgi = eval("'/cgi-bin/"+level+"/param?action=list&group="+catalog+"'");
    devCfg=HttpRequest("GET", cgi, null, false, requestTimeout);   
}
function GetDeviceInfoEx2(catalog){  
    var cgi = eval("'/cgi-bin/operator/param?action=list&group="+catalog+"'");
    devCfg=HttpRequest("GET", cgi, null, false, requestTimeout);
    return devCfg;   
}
function GetDeviceInfoEx(catalog, cb){  
    var cgi = eval("'/cgi-bin/operator/param?action=list&group="+catalog+"'");
    //HttpRequest("GET", cgi, cb, true);  
    devCfg=HttpRequest("GET", cgi, null, false, requestTimeout); 
    if(cb) cb();
}
function GetDeviceInfoEx3(catalog, cb){  
    var cgi = eval("'/cgi-bin/admin/param?action=list&group="+catalog+"'");
    //HttpRequest("GET", cgi, cb, true);  
    devCfg=HttpRequest("GET", cgi, null, false, requestTimeout); 
    if(cb) cb();
}
function UpdateDeviceAllInfo(){
	//GetDeviceInfo("root");
}
function GetQueryVariable(variable){
	//GetDeviceInfo("root", false);
    //alert(devCfg);
    //alert(variable);
    if(devCfg==undefined)
    {
    	vars="";
        return vars
    }
    var vars = devCfg.split(variable+"="); 
    if(vars[1]==undefined)
    {
        //alert("Error: Undefined!");
        vars="";
        return vars
    }
    var pair = vars[1].split("\n"); 
    //alert(pair[0]);

    return pair[0];
}

function GetQueryVariableEx(variable){
	GetDeviceInfo(variable);
    //alert(devCfg);
    //alert(variable);
    if(devCfg==undefined)
    {
    	vars="";
        return vars
    }
    var vars = devCfg.split(variable+"="); 
    if(vars[1]==undefined)
    {
        //alert("Error: Undefined!");
        vars="";
        return vars
    }
    var pair = vars[1].split("\n"); 
    //alert(pair[0]);

    return pair[0];
}
function GetQueryVariableEx2(variable,cfg){
    //alert(devCfg);
    //alert(variable);
    if(cfg==undefined)
    {
    	vars="";
        return vars
    }
    var vars = cfg.split(variable+"="); 
    if(vars[1]==undefined)
    {
        //alert("Error: Undefined!");
        vars="";
        return vars
    }
    var pair = vars[1].split("\n"); 
    //alert(pair[0]);

    return pair[0];
}
function doCheckEmpty(strTxt)
{
	if(get_Length(strTxt) <= 0){
		return false;
	}
	if(chk_Empty(strTxt)){
		return false;
	}
	
	return true;
	
}

function pausecomp(millis) 
{
    var date = new Date();
    var curDate = null;

    do { curDate = new Date(); } 
    while(curDate-date < millis);
} 

function GetFWUpgradeStatus(){
	var rsp=HttpRequest("GET", "/cgi-bin/admin/firmwareupgrade?action=progress", null, false, requestTimeout);
	var progress = rsp.split("Progress="); 
	var vars = progress[1].split("%"); 
	//alert(vars[0]);
    return (vars[0]);
}
//Qmik added start, Scott Chang, 2007/4/10
function replaceEscapes(strTxt){
    return(escape(strTxt));
}

function saveData(strPath, strData) {
   if(strData=="") strData="None";
   var cgi=eval("'/cgi-bin/admin/filewrite?SAVE.filePath="+strPath+"'");
   cgi+=eval("'&SAVE.fileData="+strData+"'");
   SendCGICMD(cgi);
}

function readData(strPath){         
    var cgi=eval("'/cgi-bin/admin/fileread?READ.filePath="+strPath+"'");
    var data=HttpRequest("GET", cgi, null, false, requestTimeout);
    if(data=="None") data="";
    return (data);
}
//Qmik added end, Scott Chang, 2007/4/10
//Qmik added start, Scott Chang, 2007/4/20
function Clear_Seq(strType, strValue)
{
    var cgi=eval("'/cgi-bin/admin/seqclear?Clear."+strType+"="+strValue+"'");
    SendCGICMD(cgi);
	alert(loadLangString("L_ApplySuccess",false));
}
//Qmik added end, Scott Chang, 2007/4/20
function setCookies(name,value)
{
  var Days = 30; //此 cookie ?被保存 30 天
  var exp  = new Date();    //new Date("December 31, 9998");
  exp.setTime(exp.getTime() + Days*24*60*60*1000);
  document.cookie = name + "="+ escape(value) +";expires="+ exp.toGMTString();
}
function getCookies(name)
{
  var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
  if(arr != null) return unescape(arr[2]); return null;
}
function delCookies(name)
{
  var exp = new Date();
  exp.setTime(exp.getTime() - 1);
  var cval=getCookie(name);
  if(cval!=null) document.cookie=name +"="+cval+";expires="+exp.toGMTString();
} 
function getVideoFmt()
{ 
	fmt=getCookies("VideoFmt");
	if(fmt==null)
	{
	    setCookies("VideoFmt","1");
	    return "1";
	}
	if(fmt!="1" && fmt!="2" && fmt!="3")
	{
	    setCookies("VideoFmt","1");
	    return "1";
	}
	return fmt;
}
function setVideoFmt(fmt)
{ 
	if(fmt!="1" && fmt!="2" && fmt!="3")
	{
	  return;
	}
	setCookies("VideoFmt",fmt);
	g_szCompressType=fmt;
}

function getImageRotate()
{ 
	rotate=getCookies(g_szRotate);
	
	if(rotate==null)
	{
	    setCookies(g_szRotate,"None");
	    return "None";
	}
	if(rotate!="None" && rotate!="Mirror" && rotate!="Flip" && rotate!="MirrorFlip")
	{
	    setCookies(g_szRotate,"None");
	    return "None";
	}
	return rotate;
}
function setImageRotate(rotate)
{ 
	if(rotate!="None" && rotate!="Mirror" && rotate!="Flip" && rotate!="MirrorFlip")
	    return;
	setCookies(g_szRotate,rotate);
	g_szRotate=rotate;
}
function setProtocol(protocol)
{ 
	setCookies("ProtocolType",protocol);
	g_szProtocolType=protocol;
}

function getProtocol()
{ 
	//return g_szLangType;
	var protocol=getCookies("ProtocolType");
	
	if(protocol==null ||
	(protocol != "1" && protocol != "2" && protocol!= "3" && protocol!= "4")
	)
  {
    protocol="3"; //set as HTTP
  }

	return protocol;
}
function setBufferEn(En)
{ 
	setCookies("Buffering",En);
	Buffer_Enable=En;
}
function getBufferEn()
{ 
	var buff=getCookies("Buffering");
	if(buff==null)
      buff="0"; //set as disable
	return buff;
}
function setViewSize(size)
{ 
	setCookies("ViewSize",size);
	VIEW_SIZE=size;
}
function getViewSize()
{ 
	var size=getCookies("ViewSize");
	if(size==null ||
	( size!="Medium" && size!="Large" )
	)
  {
    size="Medium"; //set as disable
  }

	return size;
}
function GetDeviceInfoByAdmin(catalog, cb){  
    var cgi = eval("'/cgi-bin/admin/param?action=list&group="+catalog+"'");
    //HttpRequest("GET", cgi, cb, true);  
    devCfg=HttpRequest("GET", cgi, null, false, requestTimeout); 
    if(cb) cb();
}
function ExtractString(str, start, end)
{
	var re1,re2;
	re1 = str.split(start);
	if(re1[1]==undefined)
		return re1;
	re2 = re1[1].split(end);
	return re2[0];

}

function ReplaceAll(strSource, strFind, strRepl) {
    var str5 = new String(strSource);
    while (str5.indexOf(strFind) != -1) {
          str5=str5.replace(strFind, strRepl);
    }
   return str5;
} 
var portCfg="";
function checkPortAssignment(type, curPort)
{
	if(portCfg=="")
	    portCfg = SendCGICMD("/cgi-bin/operator/param?action=list&group=General.Network.RTSP&group=General.Network.RTP&group=General.System&group=Syslog.ServerPort")
    if(type!="General.Network.RTSP.Port")
       if(GetQueryVariableEx2("General.Network.RTSP.Port", portCfg)==curPort)
	       return loadLangString("L_RtspPort",false);
	if(type!="General.System.HTTPPort")
	   if(GetQueryVariableEx2("General.System.HTTPPort", portCfg)==curPort)
	       return loadLangString("L_HTTPPort",false);
	if(type!="Syslog.ServerPort")
	   if(GetQueryVariableEx2("Syslog.ServerPort", portCfg)==curPort)
	       return loadLangString("L_SysLog",false) + " - " + loadLangString("L_ServerPort",false);
	if(type!="General.Network.RTP.R0.VideoPort")
	{
	    var R0VideoPort = GetQueryVariableEx2("General.Network.RTP.R0.VideoPort", portCfg);
	    var re = loadLangString("L_ComputerView",false) + " - " + loadLangString("L_MulticastStreaming",false)+ " - " + loadLangString("L_VideoPortNum",false);
	    if(R0VideoPort==curPort)
	    {
	        re += (" ( " + curPort + " ~ " + (parseInt(curPort)+1) + " ) ");
	        return re;
	    }
	    else if(R0VideoPort==(parseInt(curPort)-1))
	    {  
	        re += (" ( " + (parseInt(curPort)-1) + " ~ " + curPort + " ) ");
	        return re;
	    }
     }
     if(type!="General.Network.RTP.R0.AudioPort")
	{
	   var R0AudioPort = GetQueryVariableEx2("General.Network.RTP.R0.AudioPort", portCfg);
	   var re = loadLangString("L_ComputerView",false) + " - " + loadLangString("L_MulticastStreaming",false)+ " - " + loadLangString("L_AudioPortNum",false);
	   if(R0AudioPort==curPort)
	   {
	       re += (" ( " + curPort + " ~ " + (parseInt(curPort)+1) + " ) ");
	       return re;
	   }
	   else if(R0AudioPort==(parseInt(curPort)+1))
	   {
	       re += (" ( " + (parseInt(curPort)-1) + " ~ " + curPort + " ) ");
	       return re;
	   }
	}
	if(type!="General.Network.RTP.R1.VideoPort")
	{
	   var R1VideoPort = GetQueryVariableEx2("General.Network.RTP.R1.VideoPort", portCfg);
	   var re = loadLangString("L_MobileView",false) + " - " + loadLangString("L_MulticastStreaming",false)+ " - " + loadLangString("L_VideoPortNum",false);
	   if(R1VideoPort==curPort)
	   {
	       re += (" ( " + curPort + " ~ " + (parseInt(curPort)+1) + " ) ");
	       return re;
	   }
	   else if(R1VideoPort==(parseInt(curPort)-1))
	   {
	       re += (" ( " + (parseInt(curPort)-1) + " ~ " + curPort + " ) ");
           return re;
        }
     }
     if(type!="General.Network.RTP.R1.AudioPort")
	{
	   var R1AudioPort = GetQueryVariableEx2("General.Network.RTP.R1.AudioPort", portCfg);
	   var re = loadLangString("L_MobileView",false) + " - " + loadLangString("L_MulticastStreaming",false)+ " - " + loadLangString("L_AudioPortNum",false);
	   if(R1AudioPort==curPort)
	   {
	       re += (" ( " + curPort + " ~ " + (parseInt(curPort)+1) + " ) ");
	       return re;
	   }  
 	   else if(R1AudioPort==(parseInt(curPort)+1))
	   {
	       re += (" ( " + (parseInt(curPort)-1) + " ~ " + curPort + " ) ");
	       return re;
        }
     }
     return "OK";
}
var addressCfg = "";
function checkMCAddressAssignment(type, curIP)
{
	if(addressCfg=="")
	    addressCfg = SendCGICMD("/cgi-bin/operator/param?action=list&group=General.Network.RTP.R0.IPAddress&group=General.Network.RTP.R1.IPAddress");
    if(type!="General.Network.RTP.R0.IPAddress")
       if(GetQueryVariableEx2("General.Network.RTP.R0.IPAddress", addressCfg)==curIP)
           return loadLangString("L_ComputerView",false) + " - " + loadLangString("L_MulticastStreaming",false)+ " - " + loadLangString("L_MulticastAddress",false);
    if(type!="General.Network.RTP.R1.IPAddress")
       if(GetQueryVariableEx2("General.Network.RTP.R1.IPAddress", addressCfg)==curIP)
           return loadLangString("L_MobileView",false) + " - " + loadLangString("L_MulticastStreaming",false)+ " - " + loadLangString("L_MulticastAddress",false);
    return "OK";
}
function StyleCustomize(curframe)
{
	var style = SendCGICMD("/cgi-bin/view/param?action=list&group=Layout");
	var BGColorEnabled;
	var BGColor;
	var TextColorEnabled;
	var TextColor;
	if(curframe == "menu") {
	    BGColorEnabled = GetQueryVariableEx2('Layout.OwnMenuBGColorEnabled',style);
	    BGColor = GetQueryVariableEx2('Layout.OwnMenuBGColor',style);
	    TextColorEnabled = GetQueryVariableEx2('Layout.OwnMenuTextColorEnabled',style);
	    TextColor = GetQueryVariableEx2('Layout.OwnMenuTextColor',style);
/*	} else if(curframe == "banner") {
		BGColorEnabled = GetQueryVariableEx2('Layout.Banner.OwnBGColorEnabled',style);
	    BGColor = GetQueryVariableEx2('Layout.Banner.OwnBGColor',style);
	    TextColorEnabled = GetQueryVariableEx2('Layout.OwnTextColorEnabled',style);
	    TextColor = GetQueryVariableEx2('Layout.OwnTextColor',style);
*/	} else {
		BGColorEnabled = GetQueryVariableEx2('Layout.OwnBGColorEnabled',style);
	    BGColor = GetQueryVariableEx2('Layout.OwnBGColor',style);
	    TextColorEnabled = GetQueryVariableEx2('Layout.OwnTextColorEnabled',style);
	    TextColor = GetQueryVariableEx2('Layout.OwnTextColor',style);
	}
	var BGPictureAddr = GetQueryVariableEx2('Layout.BGPicture.Address',style);
	var BGPicturePath = GetQueryVariableEx2('Layout.BGPicture.Path',style);
    var BGPictureSource = GetQueryVariableEx2('Layout.BGPicture.Source',style);
	if(BGColorEnabled=="yes")
	    document.body.style.backgroundColor=BGColor;
	if(TextColorEnabled=="yes")
	    document.body.style.color=TextColor;
	var url;
	if(BGPictureSource=="own") 
	{
	    url=eval('"url(style/user/image/'+BGPicturePath+')"');
		if(BGPicturePath!="none")
		    document.body.style.backgroundImage=url;
	}
    else if(BGPictureSource=="external")
	{
	    url=eval('"url('+BGPictureAddr+')"');
	    document.body.style.backgroundImage=url;
	}
}

function UpdateParam(){  
    var re = HttpRequest("GET", arguments[0], null, false, requestTimeout);
    if((re.search(/OK/g) >= 0)||arguments[1]==false)
    {
    	alert(loadLangString("L_ApplySuccess",false));
    	return true;
    }
    else
    {
    	alert(re);
    	return false;
    }
}

function EID(strId){return document.getElementById(strId)};
function EName(strName){return document.getElementsByName(strName)};
function EShow(strId,show){EID(strId).style.display=show};
function EVisible(strId,visible){EID(strId).style.visibility=visible};