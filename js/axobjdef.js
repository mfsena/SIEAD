//ActiveX Object Defines
var AXOBJECT_ID = "AxMediaControl";
var AXOBJECT_PATH = "AxViewer/";
var AXOBJECT_NAME = "AxMediaControl.cab";
var AXOBJECT_VER = "2,0,68,3213";
var user = SendCGICMD("/cgi-bin/view/hello");
var TEMP_USER_NAME = GetQueryVariableEx2("UserName", user);
var TEMP_PASSWORD = GetQueryVariableEx2("Password", user);
// do NOT change these two lines
//var GET_USER_NAME = "";
//var GET_PASSWORD = "";
// do NOT change these two lines

//Video Stream Defines
var CHANNEL = "0";

var MAXWIDTH = 1280;
var MINWIDTH = 320;

var PROTOCOL_TYPE = "3"; //(set as cookie on client PC) 1->TCP, 2->UDP, 3->HTTP 4->Multicast  ,default connect order  TCP->UDP->HTTP
var g_szProtocolType = "2";
var MPEG4_ACCESS_NAME = "video.mp4";
var MJPEG_ACCESS_NAME = "video.mjpg";
var MULTICAST_ACCESS_NAME = "multicast.mp4";
var RECORDER_SUPPORT = "1";
var VIDEO_FMT=1;
var PLUGIN_LANG=0;
var INITMODE = "none";
var CAPTEXT = "Live view";
var STATUSBAR = 1;
var TOOLBAR = 1;
var TOOLTIP = 1;
var CONTEXTMENU = 0;
var TOOLBARCONF = "stream+rec+mic+zoom+time";
var AUTOSTART=1;
var VIEW_SIZE = "320x240";
var vdoWidth = 605;
var vdoHeight = 440;
var Buffer_Enable = getBufferEn();
var _platform = navigator.platform;
var HOST_NAME=location.hostname;
var HOST_PORT = location.port;
var HOST_SSL_PORT = 0;
var HOST_PROTOCOL = location.protocol;
var PUPOPS = 0;
var ADVANCED = 0;
var RecordSize = 50;
    
if(HOST_PROTOCOL=="https:")
{
  if(HOST_PORT==0 || HOST_PORT=="")
    HOST_SSL_PORT = 443;
  else
    HOST_SSL_PORT = HOST_PORT;
}

if(HOST_PORT==0 || HOST_PORT=="")
    HOST_PORT = 80; //default port

var mydate = new Date();
var newimg = new Image();
var imgURL;
var imgSrc = new Image();

GetDeviceInfo_A('view', 'Properties.Firmware.ActiveXID&group=Properties.PTZ.PTZ&group=General.Network.RTSP.Port');
var CLASS_ID=GetQueryVariable('Properties.Firmware.ActiveXID');
//var CLASS_ID="B4CB8358-ABDB-47EE-BC2D-437B5DEBABCB";
var PTZSupport=GetQueryVariable('Properties.PTZ.PTZ');
var PTZMouseCtl = 0; 
if(PTZSupport=="yes")
  PTZMouseCtl = 1; 
var RTSP_PORT = GetQueryVariable('General.Network.RTSP.Port');

function RGB(r,g,b){
    return (b*65536+g*256+r);
}

function refreshImgInFirefox() {

  var imgObj = document.getElementById('jpeg');
  var newURL;
  if (imgObj)
  {
    newURL = "/jpg/image.jpg?" + (new Date()).getTime();
    newimg.src = newURL;
    newimg.onload=refreshImgInFirefox;
    newimg.onerror=refreshImgInFirefox;
    imgObj.src = newURL;
  }
}

function DispImage()
{
	var imgObj = document.getElementById('jpeg');
	imgObj.src=imgURL;
}

function JPGStart()
{
	imgURL = 'jpg/image.jpg'+"?"+(new Date()).getTime();
	imgSrc.src = imgURL;
	imgSrc.onload=DispImage;
	imgSrc.onerror=DispImage;
}

var imgURL1 = "jpg/image.jpg";
var imgURL2 = "jpg/image.jpg";
var errtimer1 = null;
var errtimer2 = null;

function BBJPGStart1()
{
  EShow("jpeg2","block");
	EShow("jpeg1","none");
	
	clearTimeout(errtimer2);

	imgURL1 = 'jpg/image.jpg?'+(new Date()).getTime();
  EID("jpeg1").onload = BBJPGStart2;
	EID("jpeg1").src = imgURL1;
  errtimer1 = setTimeout("doChkImgErr()",10000);
}

function BBJPGStart2()
{
  EShow("jpeg1","block");
	EShow("jpeg2","none");
  
	clearTimeout(errtimer1);

	imgURL2 = 'jpg/image.jpg?'+(new Date()).getTime();
  EID("jpeg2").onload = BBJPGStart1;
  EID("jpeg2").src = imgURL2;
  
  errtimer2 = setTimeout("doChkImgErr()",10000);
}

function doChkImgErr()
{
  window.top.mainFrame.location.reload(true);
}

function Viewer()
{
  var os = getOs();
  var normalSize = 0, resolutionW = 0, resolutionH = 0, scale, scale2;

    if(INITMODE == "md")
    {
      GetDeviceInfo('Motion.SetupStreamIndex&group=ImageSource.I0.Config.MaxResolution');
      CHANNEL = parseInt(GetQueryVariable('Motion.SetupStreamIndex'));
      normalSize = GetQueryVariable('ImageSource.I0.Config.MaxResolution');
      MAXWIDTH = 640;
      MINWIDTH = 480;
    }
    else if(INITMODE == "ad" || INITMODE == "mask" || (INITMODE == "none" && ADVANCED == 1) )
    {
      GetDeviceInfo('Image.SetupStreamIndex&group=ImageSource.I0.Config.MaxResolution');
      CHANNEL = parseInt(GetQueryVariable('Image.SetupStreamIndex'));
      normalSize = GetQueryVariable('ImageSource.I0.Config.MaxResolution');
      MAXWIDTH = 640;
      MINWIDTH = 480;
    }
    else
    {
      GetDeviceInfo_A('view', 'Image.I' + CHANNEL + '.Resolution');
    	normalSize = GetQueryVariable('Image.I' + CHANNEL + '.Resolution');
    }

  if(VIEW_SIZE.indexOf("X")>=0)
  {
    scale = VIEW_SIZE.slice(0,VIEW_SIZE.indexOf("X"));
  }

  if(normalSize.indexOf("x")>=0)
  {
    resolutionW=normalSize.slice(0,normalSize.indexOf("x"));
    resolutionH=normalSize.slice(normalSize.indexOf("x")+1,normalSize.length);
  }


  vdoWidth = resolutionW*scale2;
  vdoHeight = resolutionH*scale2;
  //if(_platform.toLowerCase().indexOf("mac") >= 0)
  if(0)
  {
    if(INITMODE == "MotionDetect")
    {return;/*AppletWidth=640;AppletHeight=360;vdoWidth=480;vdoHeight=360;*/}
    else if(INITMODE == "PrivateMask")
    {return;/*AppletWidth=480;AppletHeight=360;vdoWidth=480;vdoHeight=360;*/}
    else
    {
      if(VIEW_SIZE == "Large")
        {AppletWidth=640;AppletHeight=(480+58);vdoWidth=640;vdoHeight=480;}
      else if(VIEW_SIZE == "Medium")
        {AppletWidth=320;AppletHeight=(240+58);vdoWidth=320;vdoHeight=240;}
      else
        {AppletWidth=320;AppletHeight=315;}
    }	
  	objectID = "ObjJavaCam";
    document.writeln('<applet  NAME="ObjJavaCam" CODE = "javacam.QTStreamingApplet.class" JAVA_CODEBASE = "./java/" WIDTH = '+AppletWidth+' HEIGHT = '+AppletHeight+' MAYSCRIPT></xmp>');
    document.writeln('    <PARAM NAME = CODE VALUE = "javacam.QTStreamingApplet.class" >');
    document.writeln('    <PARAM NAME = CODEBASE VALUE = "./java/" >');
    document.writeln('    <PARAM NAME = ARCHIVE VALUE = "qtcam_007.jar" >');
    document.writeln('    <param name="type" value="application/x-java-applet;version=1.5.0">');
    document.writeln('    <param name="scriptable" value="false">');
    document.write("    <PARAM name=\"Protocol\" VALUE=\"" + getProtocol() + "\">");
    document.write("	<PARAM name='InitMode' VALUE=\"" + INITMODE + "\">");
    document.write("    <PARAM name=\"CompressType\" VALUE=\"" + getVideoFmt() + "\">");
    document.write("    <PARAM name=\"Language\" VALUE=\"" + PLUGIN_LANG + "\">");
    document.write("    <PARAM name=\"RecorderEn\" VALUE=\"" + RECORDER_SUPPORT + "\">");
    document.write("    <PARAM name=\"UserName\" VALUE=\"" + TEMP_USER_NAME + "\">");
    document.write("    <PARAM name=\"Password\" VALUE=\"" + TEMP_PASSWORD + "\">");
    document.write("    <PARAM name=\"BufferEn\" VALUE=\"" + Buffer_Enable + "\">");
    document.write("    <PARAM name=\"vdoWidth\" VALUE=\"" + vdoWidth + "\">");
    document.write("    <PARAM name=\"vdoHeight\" VALUE=\"" + vdoHeight + "\">");
    document.writeln('</applet>');
    document.close();
  }
  else if(os == "IE" && CHANNEL!="3"){
	document.open();
	if(INITMODE == "md" || INITMODE == "ad"|| INITMODE == "mask" || (INITMODE == "none" && ADVANCED == 1))
  {
    vdoWidth = 640;
    vdoHeight = 480;
  }
  else
  {
    if(getViewSize()=="Large")
    {
      vdoWidth = 640;
      vdoHeight = 480;
    }
    else
    {
      vdoWidth = 320;
      vdoHeight = 240;
    }
  }
  if(STATUSBAR==1)
	  vdoHeight+=25;
	if(TOOLBAR==1)
	  vdoHeight+=30;
	document.write("<OBJECT NAME='" + AXOBJECT_ID + "'");
	 document.write(" width="+vdoWidth+" height="+vdoHeight);
    document.write(" CLASSID='CLSID:" + CLASS_ID + "' data='data:application/x-oleobject'");
    document.write(" CODEBASE='" + AXOBJECT_PATH + "" + AXOBJECT_NAME + "#version=" + AXOBJECT_VER + "'>");
    
    document.write(" <PARAM name='BkColor' VALUE=" + RGB(0,0,0) + ">");
    document.write(" <PARAM name='TextColor' VALUE=" + RGB(200,200,200) + ">");
    document.write(" <PARAM name='ButtonColor' VALUE=" + RGB(100,180,200) + ">");
    document.write(" <PARAM name='HoverColor' VALUE=" + RGB(18,204,214) + ">");
    
    document.write("	<PARAM name='UIMode' VALUE='" + INITMODE + "'>");
    document.write("	<PARAM name='ShowStatusBar' VALUE='" + STATUSBAR + "'>");
    document.write("	<PARAM name='ShowToolBar' VALUE='" + TOOLBAR + "'>");
    document.write("	<PARAM name='EnableContextMenu' VALUE='" + CONTEXTMENU + "'>");

    document.write("	<PARAM name='CaptionText' VALUE='" + CAPTEXT + "'>");
    document.write("	<PARAM name='ToolBarConfiguration' VALUE='" + TOOLBARCONF + "'>");
    document.write("	<PARAM name='HostIP' VALUE='" + HOST_NAME + "'>");
    document.write("	<PARAM name='HttpPort' VALUE='" + HOST_PORT + "'>");
    document.write("	<PARAM name='SSLPort' VALUE='" + HOST_SSL_PORT + "'>");
    
    document.write("	<PARAM name='MediaProtocol' VALUE='" + PROTOCOL_TYPE + "'>");
    document.write("	<PARAM name='MediaChannel' VALUE='" + CHANNEL + "'>");
    document.write("	<PARAM name='MediaUsername' VALUE='" + TEMP_USER_NAME + "'>");
    document.write("	<PARAM name='MediaPassword' VALUE='" + TEMP_PASSWORD + "'>");
    document.write("  <PARAM name='AutoStart' VALUE='" + AUTOSTART + "'>");
    document.write("	<PARAM name='MediaDelay' VALUE='" + Buffer_Enable + "'>");
    document.write("  <PARAM name='ShowToolTip' VALUE='" + TOOLTIP + "'>");
    document.write("  <PARAM name='PTZMouseCtl' VALUE='" + PTZMouseCtl + "'>");
    document.write("  <PARAM name='Popups' VALUE='" + PUPOPS + "'>");
    document.write("  <PARAM name='RecordSize' VALUE='10'>");
	document.write("</OBJECT>");
	document.close();
	}else{
      if(INITMODE == "md" || INITMODE == "ad" || INITMODE == "mask")
      {
        return;
      }else{
        if(getViewSize()=="Large")
        {
          vdoWidth = 640;
          vdoHeight = 480;
        }
        else
        {
          vdoWidth = 320;
          vdoHeight = 240;
        }
        
        var browserInfo = navigator.userAgent;
        if(browserInfo.search("BlackBerry")==0)
        {
          document.open();
          document.write("<img id=\"jpeg1\" src=\"/jpg/image.jpg\" width="+vdoWidth+" height="+vdoHeight+" onclick=\"refreshImg();\" />");
          document.write("<img id=\"jpeg2\" src=\"\" width="+vdoWidth+" height="+vdoHeight+" onclick=\"refreshImg();\" />");
          document.close();
          BBJPGStart2();        
        }else{
          document.open();
          document.write("<img id=\"jpeg\" src=\"/jpg/image.jpg\" onload=\"JPGStart()\" width="+vdoWidth+" height="+vdoHeight+"onclick=\"refreshImg();\" />");
          document.close();
          //setTimeout("refreshImgInFirefox();",200);   //after 200ms run refresh image.
        }
      }
      objectID = "jpeg";
  }
}

function NormalViewer(width,height)
{
  var os = _platform.toLowerCase();

  if(os.indexOf("mac") >= 0)
  {
    if(INITMODE == "md")
    {return;/*AppletWidth=640;AppletHeight=360;vdoWidth=480;vdoHeight=360;*/}
    else if(INITMODE == "mask")
    {return;/*AppletWidth=480;AppletHeight=360;vdoWidth=480;vdoHeight=360;*/}
    else
    {
      if(VIEW_SIZE == "Large")
        {AppletWidth=640;AppletHeight=(480+58);vdoWidth=640;vdoHeight=480;}
      else if(VIEW_SIZE == "Medium")
        {AppletWidth=320;AppletHeight=(240+58);vdoWidth=320;vdoHeight=240;}
      else
        {AppletWidth=320;AppletHeight=315;}
    }	
    document.writeln('<object NAME="ObjJavaCam" classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93" WIDTH = '+AppletWidth+' HEIGHT = '+AppletHeight+'  codebase="http://java.sun.com/update/1.5.0/jinstall-1_5_0_11-windows-i586.cab#Version=1,5,0,11"><xmp>');
    document.writeln('<applet  NAME="ObjJavaCam" CODE = "IPCam.CamMain2.class" JAVA_CODEBASE = "./java/" WIDTH = '+AppletWidth+' HEIGHT = '+AppletHeight+' MAYSCRIPT></xmp>');
    document.writeln('    <PARAM NAME = CODE VALUE = "IPCam.CamMain2.class" >');
    document.writeln('    <PARAM NAME = CODEBASE VALUE = "./java/" >');
    document.writeln('    <PARAM NAME = ARCHIVE VALUE = "custom_211a_01.jar, JavaCam.jar" >');
    document.writeln('    <param name="type" value="application/x-java-applet;version=1.5.0">');
    document.writeln('    <param name="scriptable" value="false">');
    document.write("	<PARAM name='StreamChannel'  VALUE=\"" + CHANNEL + "\">");
    document.write("	<PARAM name='InitMode' VALUE=\"" + INITMODE + "\">");
    document.write("    <PARAM name=\"CompressType\" VALUE=\"" + getVideoFmt() + "\">");
    document.write("    <PARAM name=\"Language\" VALUE=\"" + PLUGIN_LANG + "\">");
    document.write("    <PARAM name=\"RecorderEn\" VALUE=\"" + RECORDER_SUPPORT + "\">");
    document.write("    <PARAM name=\"UserName\" VALUE=\"" + TEMP_USER_NAME + "\">");
    document.write("    <PARAM name=\"Password\" VALUE=\"" + TEMP_PASSWORD + "\">");
    document.write("    <PARAM name=\"BufferEn\" VALUE=\"" + Buffer_Enable + "\">");
    document.write("    <PARAM name=\"vdoWidth\" VALUE=\"" + vdoWidth + "\">");
    document.write("    <PARAM name=\"vdoHeight\" VALUE=\"" + vdoHeight + "\">");
    document.writeln('</applet>');
    document.close();
  }
  else if(navigator.appName.indexOf("Microsoft") >= 0)
	{
    if(STATUSBAR==1)
      height+=25;
    if(TOOLBAR==1)
      height+=28;
	document.open();
  document.write("<OBJECT NAME='" + AXOBJECT_ID + "'");
	document.write(" width="+width+" height="+height);
  document.write(" CLASSID='CLSID:" + CLASS_ID + "' data='data:application/x-oleobject'");
  document.write(" CODEBASE='" + AXOBJECT_PATH + "" + AXOBJECT_NAME + "#version=" + AXOBJECT_VER + "'>");
  
  document.write(" <PARAM name='BkColor' VALUE=" + RGB(0,0,0) + ">");
    document.write(" <PARAM name='TextColor' VALUE=" + RGB(200,200,200) + ">");
    document.write(" <PARAM name='ButtonColor' VALUE=" + RGB(100,180,200) + ">");
    document.write(" <PARAM name='HoverColor' VALUE=" + RGB(18,204,214) + ">");
    
    document.write("	<PARAM name='UIMode' VALUE='" + INITMODE + "'>");
    document.write("	<PARAM name='ShowStatusBar' VALUE='" + STATUSBAR + "'>");
    document.write("	<PARAM name='ShowToolBar' VALUE='" + TOOLBAR + "'>");
    document.write("	<PARAM name='EnableContextMenu' VALUE='" + CONTEXTMENU + "'>");

    document.write("	<PARAM name='CaptionText' VALUE='" + CAPTEXT + "'>");
    document.write("	<PARAM name='ToolBarConfiguration' VALUE='" + TOOLBARCONF + "'>");
    document.write("	<PARAM name='HostIP' VALUE='" + HOST_NAME + "'>");
    document.write("	<PARAM name='HttpPort' VALUE='" + HOST_PORT + "'>");
    document.write("	<PARAM name='SSLPort' VALUE='" + HOST_SSL_PORT + "'>");
    document.write("	<PARAM name='MediaProtocol' VALUE='" + PROTOCOL_TYPE + "'>");
    document.write("	<PARAM name='MediaChannel' VALUE='" + CHANNEL + "'>");
    document.write("	<PARAM name='MediaUsername' VALUE='" + TEMP_USER_NAME + "'>");
    document.write("	<PARAM name='MediaPassword' VALUE='" + TEMP_PASSWORD + "'>");
    document.write("  <PARAM name='AutoStart' VALUE='" + AUTOSTART + "'>");
    document.write("	<PARAM name='MediaDelay' VALUE='" + Buffer_Enable + "'>");
    document.write("  <PARAM name='ShowToolTip' VALUE='" + TOOLTIP + "'>");
    document.write("  <PARAM name='PTZMouseCtl' VALUE='" + PTZMouseCtl + "'>");
    document.write("  <PARAM name='Popups' VALUE='" + PUPOPS + "'>");
	document.write("</OBJECT>");
	document.close();
  }else{
      if(INITMODE == "md" || INITMODE == "ad" || INITMODE == "mask")
      {
        return;
      }else{
        vdoWidth=640;
        var browserInfo = navigator.userAgent;
        if(browserInfo.search("BlackBerry")==0)
        {
          document.open();
          document.write("<img id=\"jpeg1\" src=\"/jpg/image.jpg\" width="+vdoWidth+" height="+vdoHeight+" onclick=\"refreshImg();\" />");
          document.write("<img id=\"jpeg2\" src=\"\" width="+vdoWidth+" height="+vdoHeight+" onclick=\"refreshImg();\" />");
          document.close();
          BBJPGStart2();   
        }else{
          document.open();
          document.write("<img id=\"jpeg\" src=\"/jpg/image.jpg\" onload=\"JPGStart()\" width="+vdoWidth+" onclick=\"refreshImg();\" />");
          document.close();
        }
      }
      objectID = "jpeg";
  }
}

function RemoteViewer(width,height,FILE_PATH)
{
     if(STATUSBAR==1)
         height+=25;
     if(TOOLBAR==1)
         height+=28;
	
    document.open();
    document.write("<OBJECT NAME='" + AXOBJECT_ID + "'");
    document.write(" width="+width+" height="+height);
    document.write(" CLASSID='CLSID:" + CLASS_ID + "' data='data:application/x-oleobject'");
    document.write(" CODEBASE='" + AXOBJECT_PATH + "" + AXOBJECT_NAME + "#version=" + AXOBJECT_VER + "'>");
    document.write(" <PARAM name='BkColor' VALUE=" + RGB(0,0,0) + ">");
    document.write(" <PARAM name='TextColor' VALUE=" + RGB(200,200,200) + ">");
    document.write(" <PARAM name='ButtonColor' VALUE=" + RGB(100,180,200) + ">");
    document.write(" <PARAM name='HoverColor' VALUE=" + RGB(18,204,214) + ">"); 
    document.write(" <PARAM name='UIMode' VALUE='" + INITMODE + "'>");
    document.write(" <PARAM name='ShowStatusBar' VALUE='" + STATUSBAR + "'>");
    document.write(" <PARAM name='ShowToolBar' VALUE='" + TOOLBAR + "'>");
    document.write(" <PARAM name='EnableContextMenu' VALUE='" + CONTEXTMENU + "'>");
    document.write(" <PARAM name='CaptionText' VALUE='" + CAPTEXT + "'>");
    document.write(" <PARAM name='ToolBarConfiguration' VALUE='" + TOOLBARCONF + "'>");
    document.write(" <PARAM name='HostIP' VALUE='" + HOST_NAME + "'>");
    document.write(" <PARAM name='HttpPort' VALUE='" + HOST_PORT + "'>");
    document.write(" <PARAM name='SSLPort' VALUE='" + HOST_SSL_PORT + "'>");
    document.write(" <PARAM name='MediaProtocol' VALUE='" + PROTOCOL_TYPE + "'>");
    document.write(" <PARAM name='MediaChannel' VALUE='" + CHANNEL + "'>");
    document.write(" <PARAM name='MediaURL' VALUE='rtsp://" + HOST_NAME + ":" + RTSP_PORT + "/file/mnt/"+FILE_PATH+"'>");
    document.write(" <PARAM name='MediaUsername' VALUE='" + TEMP_USER_NAME + "'>");
    document.write(" <PARAM name='MediaPassword' VALUE='" + TEMP_PASSWORD + "'>");
    document.write(" <PARAM name='AutoStart' VALUE='" + AUTOSTART + "'>");
    document.write(" <PARAM name='MediaDelay' VALUE='" + Buffer_Enable + "'>");
    document.write(" <PARAM name='ShowToolTip' VALUE='" + TOOLTIP + "'>");
    document.write(" <PARAM name='PTZMouseCtl' VALUE='" + PTZMouseCtl + "'>");
    document.write(" <PARAM name='Popups' VALUE='" + PUPOPS + "'>");
    document.write(" <PARAM name='RecordSize' VALUE='" + RecordSize + "'>");
    document.write("</OBJECT>");
    document.close();
}


function onAxobjUnload()
{
    if(AxMediaControl.UIMode)
    {
      AxMediaControl.StopRecord();
      AxMediaControl.Stop();
      setProtocol(AxMediaControl.MediaProtocol);
    }
}
