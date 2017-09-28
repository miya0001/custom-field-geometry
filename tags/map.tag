<map class="map" style="width: 100%; height: 100%;">
	<script>
		window.icon_num = 0;

		var div = document.createElement( 'div' )
		this.root.appendChild( div )
		div.style.width = '100%'
		div.style.height = '100%'

		if ( ! opts.zoom ) {
			opts.zoom = 14
		}

		if ( isNaN( parseInt( opts.zoom ) ) ) {
			opts.zoom = 0
		}

		if ( isNaN( parseFloat( opts.lat ) ) ) {
			opts.lat = 0
		}

		if ( isNaN( parseFloat( opts.lng ) ) ) {
			opts.lng = 0
		}

		var map = L.map( div, { scrollWheelZoom: false } )
			.setView( new L.LatLng( opts.lat, opts.lng ), opts.zoom )

		var layers = opts.layers

		var basemaps = {}
		for ( var i = 0; i < layers.length; i++ ) {
			var layer = L.tileLayer( layers[ i ].tile, {
			id: i,
				attribution: '<a href="' + layers[ i ].attribution_url + '" target="_blank">' + layers[ i ].attribution + '</a>'
			} )
			basemaps[ layers[ i ].name ] = layer
			if ( 0 === i ) {
				map.addLayer( layer )
			}
		}

		var featureGroup = L.featureGroup().addTo(map);

		var drawControl = new L.Control.Draw( {
			draw: {
				circle: false,
				circlemarker: false
			},
			edit: {
				featureGroup: featureGroup
			}
		} ).addTo( map );

		var icon_images = [
			'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
			'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
			'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
			'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png',
			'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-yellow.png',
			'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-violet.png',
			'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png',
			'https://cdn.rawgit.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-black.png'
		]

		var icons = []
		for ( var i = 0; i < icon_images.length; i++ ) {
			icons.push( new L.Icon( {
				iconUrl: icon_images[i],
				shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
				iconSize: [25, 41],
				iconAnchor: [12, 41],
				popupAnchor: [1, -34],
				shadowSize: [41, 41]
			} ) )
		}

        map.on( L.Draw.Event.DRAWSTART, function( e ) {
			drawControl.setDrawingOptions( {
				marker: {
					icon: icons[ icon_num % icons.length ]
				}
			} );
		} );

        map.on( L.Draw.Event.CREATED, function( e ) {
            var type = e.layerType,
                layer = e.layer,
				feature = layer.feature = layer.feature || {};
			feature.type = feature.type || "Feature";
			var props = feature.properties = feature.properties || {};

            if ( type === 'marker' ) {
				props.icon = icon_num % icons.length;
				layer.setIcon( icons[ props.icon ] );
				layer.on( 'click', function( e ) {
					icon_num += 1;
					var n = icon_num % icons.length;
					e.target.setIcon( icons[ n ] );
					props.icon = n;
				} )
            }
            featureGroup.addLayer(layer);
        } );

		var geojson = jQuery( '#' + custom_field_geometry_id + ' .geojson' ).val();
		if ( geojson ) {
			var geojsonLayer = L.geoJson( JSON.parse( geojson ) );
			geojsonLayer.eachLayer( function( l ) {
				if ( 'Point' === l.feature.geometry.type ) {
					l.setIcon( icons[ l.feature.properties.icon ] )
				}
				featureGroup.addLayer( l );
			} );
		}

		map.on( 'draw:created', function( e ) {
			featureGroup.addLayer( e.layer );
		} );

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
			jQuery( '#' + custom_field_geometry_id + ' .lat' ).val( lat );
			jQuery( '#' + custom_field_geometry_id + ' .lng' ).val( lng );
			jQuery( '#' + custom_field_geometry_id + ' .zoom' ).val( zoom );
			window.localStorage.setItem( 'location', zoom + ',' + lat + ',' + lng )
		} )

		jQuery( '#post' ).on( 'submit', function() {
			if ( ! jQuery( '#' + custom_field_geometry_id + ' .lat' ).val() ) {
				jQuery( '#' + custom_field_geometry_id + ' .lat' ).val( opts.lat );
			}
			if ( ! jQuery( '#' + custom_field_geometry_id + ' .lng' ).val() ) {
				jQuery( '#' + custom_field_geometry_id + ' .lng' ).val( opts.lng );
			}
			if ( ! jQuery( '#' + custom_field_geometry_id + ' .zoom' ).val() ) {
				jQuery( '#' + custom_field_geometry_id + ' .zoom' ).val( opts.zoom );
			}

			var geojson = JSON.stringify( featureGroup.toGeoJSON() );
			jQuery( '#' + custom_field_geometry_id + ' .geojson' ).val( geojson );
		} );
	</script>
</map>
