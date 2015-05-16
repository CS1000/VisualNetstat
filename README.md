# VisualNetstat  
Visual Netstat via IP2Location DB5.LITE  
  
![](https://raw.githubusercontent.com/CS1000/VisualNetstat/master/img/demo.png)
  
Visualize your machine's ESTABLISHED connections on a World Map.  
  
Assets:  
  
 - D3.js (+topojson)  
 - DataMaps  
 - IP2Location's PHP Module for binary DB queries  
  
Requires:  
  
 - PHP `exec()` permissions  
 - `netstat` (Network Utility) access  
 - IP2Location DB5 or higher (geolocation)  
 - enough RAM for script execution (binary DB alone is ~50MB)  

Tested on Linux, but Windows code should also run.  
  
Fast build for the IP2Location contest in the last hour.  
Nightly addon: Trace Route for *NIX users on netstat listing.  
