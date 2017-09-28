var config = {
	"lat": 0,
	"lng": 0,
	"zoom": 1,
	"layers": [ {
	"name": "Open Street Map",
	"tile": "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
		"attribution": "OpenStreetMap Contributers",
		"attribution_url": "http://osm.org/copyright"
	} ]
}

const latlng = localStorage.getItem( 'location' )
if ( latlng ) {
  [ config.zoom, config.lat, config.lng ] = latlng.split( ',' )
}
console.log(custom_field_geometry_id)
console.log( jQuery( '#' + custom_field_geometry_id ).length )
console.log( jQuery( '#' + custom_field_geometry_id + ' .lat' ).length )
// Override the lat and lng from post_meta
if ( jQuery( '#' + custom_field_geometry_id + ' .lat' ).val() && jQuery( '#' + custom_field_geometry_id + ' .lng' ).val() ) {
	config.lat  = jQuery( '#' + custom_field_geometry_id + ' .lat' ).val();
	config.lng  = jQuery( '#' + custom_field_geometry_id + ' .lng' ).val();
	config.zoom = jQuery( '#' + custom_field_geometry_id + ' .zoom' ).val();
}

riot.mount( "map", config )
