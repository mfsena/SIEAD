var VLC_Classid = "clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921";
var VLC_Codebase = "http://downloads.videolan.org/pub/videolan/vlc/latest/win32/axvlc.cab";
var VLC_Embed_Pluginspage = "http://www.videolan.org";
var VLC_Embed_Type = "application/x-vlc-plugin";
var VLC_Embed_Version = "VideoLAN.VLCPlugin.2";
var VLC_Embed_Width = 640;
var VLC_Embed_Height = 480;
var VLC_Embed_toolbar = true;
var VLC_Embed_Name = "vlc";
var VLC_Pause_Flag = false;

function VLC_Viewer(vlc_UMedia)
{
    var Codec;
    switch(CHANNEL)
    {
      default:
      case 1:
        Codec = "MPEG4";
        break;
      case 2:
        Codec = "MJPEG";
        break;
    }
    
    var normalSize = getViewSize();
    
    if(normalSize == null)
    {
        normalSize = "Medium";
        setViewSize(normalSize);
    }
    
    if(normalSize == "Medium")
    {
        normalSize = "320x240";
    }else if(normalSize == "Large"){
        normalSize = "640x480";
    }
    
    if(normalSize.indexOf("x")>=0)
    {
        VLC_Embed_Width = normalSize.slice(0,normalSize.indexOf("x"));
        VLC_Embed_Height = normalSize.slice(normalSize.indexOf("x")+1,normalSize.length);
    }    
    
  	document.open();
  	document.write("<Object id='vlc'");
  	document.write("  classid='" + VLC_Classid + "'");
  	document.write("  codebase='" + VLC_Codebase + "'");
  	document.write("  width='" + VLC_Embed_Width + "'");
  	document.write("  height='" + VLC_Embed_Height + "'");
    document.write("  events='True' ");
  	document.write(">");
  	document.write("  <param name='AutoLoop' value='no' ></param>");
  	document.write("  <param name='AutoPlay' value='no' ></param>");
  	document.write("  <param name='fullscreen' value='true' ></param>");
  	
    document.write("<EMBED id='vlc_embed'");
    document.write("  pluginspace='" + VLC_Embed_Pluginspage + "'");
    document.write("  type='" + VLC_Embed_Type + "'");
    document.write("  version='" + VLC_Embed_Version + "'");
    document.write("  width='" + VLC_Embed_Width + "'");
    document.write("  height='" + VLC_Embed_Height + "'");
    document.write("  toolbar='" + VLC_Embed_toolbar + "'");
    document.write("  name='" + VLC_Embed_Name + "'");
    document.write("  autoplay='no'");
    document.write(">");
    document.write("</EMBED> ");
    
    document.write("</Object> ");
  	document.close();
    
    var vlc = getVLC("vlc");
    var vlc_url = eval("'rtsp://" + HOST_NAME + ":" + RTSP_PORT + "/" + vlc_UMedia + "'"); 
    var options;
    
    switch(PROTOCOL_TYPE)
    {
        case "1":
            options = new Array(":rtsp-tcp",":autoscale");
            break;
        case "3":
            options = new Array(":rtsp-http",":rtsp-http-port=" + HOST_PORT,":autoscale");
            break;
        default:
            options = new Array(":autoscale");
            break;
    }
    vlc.playlist.items.clear();
    while( vlc.playlist.items.count > 0 )
    {
        // clear() may return before the playlist has actually been cleared
        // just wait for it to finish its job
    }
            
    var itemId = vlc.playlist.add(vlc_url,"Live_View",options);
    vlc.playlist.playItem(itemId);
}

function getVLC(name)
{
    if(window.document[name])
    {
        return window.document[name];
    }
    else if (navigator.appName.indexOf("Microsoft Internet")==-1)
    {
        if (document.embeds && document.embeds[name])
            return document.embeds[name];
    }
    else
    {
        return document.getElementById(name);
    }
}

function VLC_DoAct(index)
{
    var vlc = getVLC("vlc");
    switch(index)
    {
        case 'Play':
            VLC_DoPlayOrPause();
            break;
        case 'Pause':
            VLC_DoPlayOrPause();
            break;
        case 'Stop':
            VLC_DoStop();
            break;
        case 'Record':
            break;
        case 'Recording':
            break;
        case 'Snapshot':
            var image_url = "jpg/image.jpg?"+(new Date()).getTime();
            window.open( image_url ,"Snapshot", "resizable=1,scrollbars=1,status=0" );
            break;
        case 'Sound':
            vlc.audio.toggleMute();
            break;
        case 'SoundMute':
            vlc.audio.toggleMute();
            break;
        case 'Talk':
            break;
        case 'TalkMute':
            break;
        case 'Fullscreen':
            vlc.video.toggleFullscreen();
            break;
    }
}

/* Actions */

function VLC_DoPlayOrPause()
{
    var vlc = getVLC("vlc");
    if( vlc )
    {
        if( vlc.playlist.isPlaying )
        {
            vlc.playlist.togglePause();
            VLC_Pause_Flag = true;
        }
        else if( vlc.playlist.items.count > 0 )
        {
            vlc.playlist.play();
            VLC_Pause_Flag = false;
        }
        else
        {
            alert('nothing to play !');
        }
    }
}

function VLC_DoStop()
{
    var vlc = getVLC("vlc");

    if( vlc )
    {    
        vlc.playlist.stop();
    }
}

function VLC_DoUpdateVolume(deltaVol)
{
    var vlc = getVLC("vlc");
    if( vlc )
    {
        vlc.audio.volume = (deltaVol * 2);
    }
}