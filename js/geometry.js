function Geometry( selector, opts ) {}

Geometry.prototype.init = function() {
	var el = document.querySelectorAll( '.cf-geometry' );
	var maps = [];
	for ( var i = 0; i < el.length; i++ ) {
		var id = el[i].getAttribute( 'data-post-id' );
		var map = this.display( el[i] );
		map.postId = id;
		maps.push( map )
	}
	return maps;
}

Geometry.prototype.display = function( map_container ) {
	var lat = map_container.getAttribute( 'data-lat' );
	var lng = map_container.getAttribute( 'data-lng' );
	var zoom = map_container.getAttribute( 'data-zoom' );
	var geojson = map_container.getAttribute( 'data-geojson' );

	// Initialize the map.
	var map = L.map( map_container );
	map.setView( [ lat, lng ], zoom );
	L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
		attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(map);

	// Place geometries from GeoJson to the map.
	if ( geojson ) {
		var geojsonLayer = L.geoJson( JSON.parse( geojson ) );
		geojsonLayer.addTo( map );
	}

	return map;
}

var g = new Geometry();
window.geometries = g.init();
