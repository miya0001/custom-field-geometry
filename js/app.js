var config = custom_field_geometry_options;

var defaults = {
	"lat": 0,
	"lng": 0,
	"zoom": 1,
	"layers": [
		{
			"name": "Open Street Map",
			"tile": "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
			"attribution": "OpenStreetMap Contributers",
			"attribution_url": "http://osm.org/copyright"
		}
	],
	"controls": {
		circle: false,
		circlemarker: false
	}
}

for ( var prop in defaults ) {
    if ( prop in config ) { continue; }
    config[prop] = defaults[prop];
}

const latlng = localStorage.getItem( 'location' )
if ( latlng ) {
  [ config.zoom, config.lat, config.lng ] = latlng.split( ',' )
}

// Override the lat and lng from post_meta
if ( document.querySelector( '#' + custom_field_geometry_id + ' .lat' ).value && document.querySelector( '#' + custom_field_geometry_id + ' .lng' ).value ) {
	config.lat  = document.querySelector( '#' + custom_field_geometry_id + ' .lat' ).value;
	config.lng  = document.querySelector( '#' + custom_field_geometry_id + ' .lng' ).value;
	config.zoom = document.querySelector( '#' + custom_field_geometry_id + ' .zoom' ).value;
}

riot.mount( "map", config )
