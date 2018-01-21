      function initialize() {
        
        var places=["30.04442,31.235712","51.500335,-0.130083","48.856614,2.352222","35.689488,139.691706","41.901514,12.460774","40.416691,-3.700345","38.725299,-9.150036","42.358431,-71.059773","40.714353,-74.005973","41.964486,-73.440803","41.412433,-73.864614","35.011636,135.768029","34.693738,135.502165","50.62925,3.057256","34.033333,-5","52.519171,13.406091","34.052234,-118.243685","37.77493,-122.419415","45.509874,6.659294","26.142036,-81.79481","40.748132,14.4898","45.440847,12.315515","43.771033,11.248001","47.80949,13.05501","-20.348404,57.552152","37.508039,15.082851","51.527382,-0.087232","51.053018,-4.204163","53.962291,-1.081899","46.191353,-1.374361"];
var numb = Math.floor((Math.random()*13)+0);
var lat = places[numb];
var bits = lat.split(',');

        var mapOptions = {
          center: new google.maps.LatLng(bits[0], bits[1]),
          zoom: 13,
scrollwheel: false,
disableDoubleClickZoom:true,
          disableDefaultUI: true,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          
        };

        var styles = [
  {
    "stylers": [
      { "saturation": -100 }
    ]
  },{
    "featureType": "road",
    "stylers": [
      { "visibility": "simplified" }
    ]
  },{
    "featureType": "transit",
    "stylers": [
      { "visibility": "off" }
    ]
  },{
    "featureType": "administrative.locality",
    "stylers": [
      { "visibility": "off" }
    ]
  },{
    "featureType": "administrative.neighborhood",
    "stylers": [
      { "visibility": "off" }
    ]
  },{
    "elementType": "labels.text",
    "stylers": [
      { "visibility": "off" }
    ]
  },{
    "elementType": "labels",
    "stylers": [
      { "visibility": "off" }
    ]
  }
]

        var styledMap = new google.maps.StyledMapType(styles,
    {name: "styles"});
        var map = new google.maps.Map(document.getElementById("header"),
            mapOptions);
        map.mapTypes.set('map_style', styledMap);
  map.setMapTypeId('map_style');
      }