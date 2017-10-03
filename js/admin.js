var config = custom_field_geometry_options;

var defaults = {
	"lat": 0,
	"lng": 0,
	"zoom": 1,
	"tiles": [
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

var div = document.createElement( 'div' );
div.style.width = '100%';
div.style.height = '100%';

var map_root = document.querySelector( '#map-' + custom_field_geometry_id );
map_root.appendChild( div );

if ( ! config.zoom ) {
	config.zoom = 14
}

if ( isNaN( parseInt( config.zoom ) ) ) {
	config.zoom = 0
}

if ( isNaN( parseFloat( config.lat ) ) ) {
	config.lat = 0
}

if ( isNaN( parseFloat( config.lng ) ) ) {
	config.lng = 0
}

var map = L.map( div, {
	scrollWheelZoom: false,
	dragging: !L.Browser.mobile,
	tap: false
} )
.setView( new L.LatLng( config.lat, config.lng ), config.zoom )

var basemaps = {}
for ( var i = 0; i < config.tiles.length; i++ ) {
	var layer = L.tileLayer( config.tiles[ i ].tile, {
		id: i,
		attribution: '<a href="' + config.tiles[ i ].attribution_url + '" target="_blank">' + config.tiles[ i ].attribution + '</a>'
	} )
	basemaps[ config.tiles[ i ].name ] = layer
	if ( 0 === i ) {
		map.addLayer( layer )
	}
}

if ( config.tiles.length > 1 ) {
	L.control.layers( basemaps, {}, { position: 'bottomright' } ).addTo( map )
}

var featureGroup = L.featureGroup().addTo(map);

var drawControl = new L.Control.Draw( {
	draw: config.controls,
	edit: {
		featureGroup: featureGroup,
		edit: false
	}
} ).addTo( map );

map.on( 'draw:created', function( e ) {
	var type = e.layerType,
		layer = e.layer;
	featureGroup.addLayer(layer);
} );

var geojson = document.querySelector( '#' + custom_field_geometry_id + ' .geojson' ).value;
if ( geojson ) {
	var geojsonLayer = L.geoJson( JSON.parse( geojson ) );
	geojsonLayer.eachLayer( function( l ) {
		featureGroup.addLayer( l );
	} );
}

map.on( 'moveend', function( e ) {
	var zoom = e.target._zoom
	var center = map.getCenter()
	var lat = center.lat
	var lng = center.lng
	if ( lng > 180 ) {
		while( lng > 180 ) {
			lng = lng - 360
		}
	} else if ( lng < -180 ) {
		while( lng < -180 ) {
			lng = lng + 360
		}
	}
	document.querySelector( '#' + custom_field_geometry_id + ' .lat' ).value = lat;
	document.querySelector( '#' + custom_field_geometry_id + ' .lng' ).value = lng;
	document.querySelector( '#' + custom_field_geometry_id + ' .zoom' ).value = zoom;
	window.localStorage.setItem( 'location', zoom + ',' + lat + ',' + lng )
} )

document.querySelector( '#post' ).addEventListener( 'submit', function() {
	if ( ! document.querySelector( '#' + custom_field_geometry_id + ' .lat' ).value ) {
		document.querySelector( '#' + custom_field_geometry_id + ' .lat' ).value = config.lat;
	}
	if ( ! document.querySelector( '#' + custom_field_geometry_id + ' .lng' ).value ) {
		document.querySelector( '#' + custom_field_geometry_id + ' .lng' ).value = config.lng;
	}
	if ( ! document.querySelector( '#' + custom_field_geometry_id + ' .zoom' ).value ) {
		document.querySelector( '#' + custom_field_geometry_id + ' .zoom' ).value = config.zoom;
	}

	var geojson = JSON.stringify( featureGroup.toGeoJSON() );
	document.querySelector( '#' + custom_field_geometry_id + ' .geojson' ).value = geojson;
} );
