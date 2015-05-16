var defaultOptions = {
    scope: 'world', //currently supports 'usa' and 'world', however with custom map data you can specify your own
    setProjection: setProjection, //returns a d3 path and projection functions
    projection: 'equirectangular', //style of projection to be used. try "mercator"
    height: null, //if not null, datamaps will grab the height of 'element'
    width: null, //if not null, datamaps will grab the width of 'element'
    done: function() {}, //callback when the map is done drawing
    fills: {
      defaultFill: '#ABDDA4' //the keys in this object map to the "fillKey" of [data] or [bubbles]
    },
    dataType: 'json', //for use with dataUrl, currently 'json' or 'csv'. CSV should have an `id` column
    dataUrl: null, //if not null, datamaps will attempt to fetch this based on dataType ( default: json )
    geographyConfig: {
        dataUrl: null, //if not null, datamaps will fetch the map JSON (currently only supports topojson)
        hideAntarctica: true,
        borderWidth: 1,
        borderColor: '#FDFDFD',
        popupTemplate: function(geography, data) { //this function should just return a string
          return '<div class="hoverinfo"><strong>' + geography.properties.name + '</strong></div>';
        },
        popupOnHover: true, //disable the popup while hovering
        highlightOnHover: true,
        highlightFillColor: '#FC8D59',
        highlightBorderColor: 'rgba(250, 15, 160, 0.2)',
        highlightBorderWidth: 2
    },
    bubblesConfig: {
        borderWidth: 2,
        borderColor: '#FFFFFF',
        popupOnHover: true,
        popupTemplate: function(geography, data) {
          return '<div class="hoverinfo"><strong>' + data.name + '</strong></div>';
        },
        fillOpacity: 0.75,
        highlightOnHover: true,
        highlightFillColor: '#FC8D59',
        highlightBorderColor: 'rgba(250, 15, 160, 0.2)',
        highlightBorderWidth: 2,
        highlightFillOpacity: 0.85
    },
    arcConfig: {
      strokeColor: '#DD1C77',
      strokeWidth: 1,
      arcSharpness: 1,
      animationSpeed: 600
    }
};


var arcs = new Datamap({
  element: document.getElementById("arcs"),
  scope: 'world',
  fills: {
    defaultFill: "#ABDDA4",
  }
});

arcs.arc([
  {
      origin: {
          latitude: 40.639722,
          longitude: -73.778889
      },
      destination: {
          latitude: 37.618889,
          longitude: -122.375
      }
  },
  {
      origin: {
          latitude: 30.194444,
          longitude: -97.67
      },
      destination: {
          latitude: 25.793333,
          longitude: -80.290556
      },
      options: {
        strokeWidth: 2,
        strokeColor: 'rgba(100, 10, 200, 0.4)',
greatArc: true
      }
  },
  {
      origin: {
          latitude: 39.861667,
          longitude: -104.673056
      },
      destination: {
          latitude: 35.877778,
          longitude: -78.7875
      }
  }
],  {strokeWidth: 1, arcSharpness: 1.4});