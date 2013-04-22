var g_szOutputChar = "";
var g_szLangType = "en-US";
var defaultTmpDoc = null;

var oXmlDoc = null;
var curlang = "";
var Langver = "";

function loadLanguage()
{
    oXmlDoc = new ActiveXObject("Microsoft.XMLDOM");
	  oXmlDoc.async = false;
	  var bRet = oXmlDoc.load("lang\\language.xml");
	  if (bRet == false)
	  {
		   oXmlDoc.load("lang\\default.xml");
	  }
}

function ReadLangVer()
{
  var url = "/lang/lang_version?timeStamp=" + new Date().getTime();
  var val = "";
  xmlHttp = new XMLHttpRequest(); 
  xmlHttp.open("Get", url, false); 
  xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
  xmlHttp.send(null);
  
  if(xmlHttp.status == 200)
  {
    val = xmlHttp.responseText.split("\"");
    curlang = val[1];
    Langver = val[3];
  }
}

function makeRequest(url) {
  http_request = false;

  if (window.XMLHttpRequest) // Mozilla, Safari,...
  {
    http_request = new XMLHttpRequest();
    if (http_request.overrideMimeType) {
      http_request.overrideMimeType('text/xml');
    }
  }
  if (!http_request) {
    alert('Giving up :( Cannot create an XMLHTTP instance');
    return false;
  }
  http_request.open('GET', url, false);
  http_request.send(null); 
}

function loadLanguageNoneIE()
{
  ReadLangVer();
  makeRequest("lang/language.xml?" + curlang + "&" + Langver );
  oXmlDoc = http_request.responseXML;
}

function style_display_on() { 
   var ie = getOs();
   if (getOs() == "IE"){
      return "block"; 
   } else { // Mozilla, Safari,... 
      return "table-row"; 
   } 
}

function hex2rgb(hex)
{
var r = (0xFF0000 & hex) >> 16; var r = (0xFF0000 & hex) >> 16;
var g = (0x00FF00 & hex) >> 8; var g = (0x00FF00 & hex) >> 8;
var b = (0x0000FF & hex); var b = (0x0000FF & hex);
return "rgb(" + r + "," + g + "," + b + ")"; return "rgb(" + r + "," + g + "," + b + ")";
}

function show_blank_on(){
  var os = getOs();
  if(os != "IE"){
    if( document.getElementById('table1')  != undefined )
    {
      document.getElementById('table1').style.display="none";
    }
  }
}
function show_blank_off(){
  var os = getOs();
  if(os != "IE"){
    if( document.getElementById('table1') != undefined )
    {
      document.getElementById('table1').style.display="block";
    }
  }
}

function getOs() 
{ 
   if(navigator.userAgent.indexOf("MSIE")>0) { 
        return "IE"; 
   }
   if(isFirefox=navigator.userAgent.indexOf("Firefox")>0){ 
        return "Firefox"; 
   } 
   if(isSafari=navigator.userAgent.indexOf("Safari")>0) { 
        return "Safari"; 
   }   
   if(isOpera=navigator.userAgent.indexOf("Opera")>0) {
        return "Opera"; 
   }
} 
// PDL:
// This function is used to read the string of each localization 
// (according to the location of OS)
//
// get a XML parser
// use this XML parser to get the string 
function loadLangString(strtag,display)
{
  var os = getOs();
	if (os.indexOf("IE") >= 0)
	  {

    // If banner isn't exist, load again.
	  if(top.topFrame == null)
	  {
	    if(oXmlDoc == null )
      {
	       loadLanguage();
	    }
    // Banner is exist but not load language.
		}else if(top.topFrame.oXmlDoc == null){
	  		loadLanguage();
	  		top.topFrame.oXmlDoc = oXmlDoc;

    // Banner is exist and load language.
  	}else if(oXmlDoc == null){
  	    oXmlDoc = top.topFrame.oXmlDoc;
  	}
  		g_szOutputChar = getData(oXmlDoc, "STRING/"+strtag);
  		if(g_szOutputChar=="")
  		{		
        if(defaultTmpDoc == null)
        {
          defaultTmpDoc = new ActiveXObject("Microsoft.XMLDOM");
        }
        defaultTmpDoc.async = false;
        defaultTmpDoc.load("lang\\default.xml");
        g_szOutputChar = getData(defaultTmpDoc, "STRING/"+strtag);
        if(g_szOutputChar=="")
          g_szOutputChar="Error";
  		}
  	}else{
        // If banner isn't exist, load again.
        if(top.topFrame == null)
        {
          if(oXmlDoc == null )
          {
            loadLanguageNoneIE();
          }
        // Banner is exist but not load language.
        }else if(top.topFrame.oXmlDoc == null){
          loadLanguageNoneIE();
          top.topFrame.oXmlDoc = http_request.responseXML;
        // Banner is exist and load language.
        }else if(oXmlDoc == null){
          oXmlDoc = top.topFrame.oXmlDoc;
        }

      try{
        g_szOutputChar=oXmlDoc.getElementsByTagName(strtag)[0].firstChild.nodeValue; //getElementsByTagName(strtag)[0].childNodes[0].nodeValue;
      }
      catch(e){		
      				try{
      				makeRequest("lang/default.xml");
      				oXmlDoc = http_request.responseXML;	
    					g_szOutputChar=oXmlDoc.getElementsByTagName(strtag)[0].firstChild.nodeValue; //getElementsByTagName(strtag)[0].childNodes[0].nodeValue;
                        oXmlDoc = top.topFrame.oXmlDoc;
      				}
      				catch(e){																	
      				g_szOutputChar="Error";			//jack add for strtag is not in XML's Tag 
      				}
      }
    	//if(g_szOutputChar=="")
       		//g_szOutputChar="Error";
      //loadLanguageNoneIE();
  	}
	if(display==true)
	    document.write(g_szOutputChar);
	return g_szOutputChar;
}

// PDL:
// This function is used to get the date of a XML element
function getData(oDoc, szXmlPath)
{
    var szRetval = "";
	var NodeObj=oDoc.selectSingleNode(szXmlPath);
	if (NodeObj)
		szRetval = NodeObj.text;
	return szRetval;
}

function setLanguage(langType)
{ 
	setCookies(g_szLangType,langType);
	g_szLangType=langType;
}

function getLanguage()
{ 
	lang=loadLangString("L_LanguagePack",false);
	return lang;
}
function setCookies(name,value)
{
  var Days = 30; //  cookie will keep 30 days.
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

function getLangUicode()
{
  return loadLangString("L_LocalLanguage",false);
}
