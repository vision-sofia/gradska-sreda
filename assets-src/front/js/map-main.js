import { mapBoxAttribution, mapBoxUrl } from './map-config';

(() => {
    if (!document.getElementById('mapMain')) {
        return;
    }
    const mapCenter = [42.697664,23.3166103];
    const defaultObjectStyle = {
        color: "#ff9710",
        opacity: 0.5,
        width: 5
    };
    let objectStyles = {};

    let map = new L.map('mapMain', {
        updateWhenZooming: false
    });

    let mapStyle = L.tileLayer(mapBoxUrl, {
        attribution: mapBoxAttribution,
        maxNativeZoom: 19,
        maxZoom: 20,
        minZoom: 11,
        // detectRetina: true,
        updateWhenZooming: false
    });
    mapStyle.addTo(map);

    let updateMapThrottle;
    map.on('load dragend zoomend', function() {
        clearTimeout(updateMapThrottle);
        updateMapThrottle = setTimeout(updateMap, 200);
    });

    map.setView(mapCenter, 17);

    let geoJsonLayer = L.geoJSON([], {
        style: function(feature) {
            return objectStyles[feature.properties._s1] ? {...objectStyles[feature.properties._s1]} : {...defaultObjectStyle}
        },
        onEachFeature: function(feature, layer) {
            if (feature.properties._behavior === 'info') {
                let popupContent = `<p class="text-center">${feature.properties.type}<br />${feature.properties.name}</p>`;
                layer.bindPopup(popupContent, {
                    closeButton: true,
                    offset: L.point(0, -20)
                });
            }
            layer.on('click', function () {
                takeAction(layer);
            });
            if (feature.geometry.type !== 'Point') {
                layer.on('mouseover', function () {
                    mouseEnter(this)
                });
                layer.on('mouseout', function () {
                    mouseLeave(this)
                });
            }
        },
        pointToLayer: function (feature, latlng) {
            return L.circleMarker(latlng, {
                radius : 8,
                fillColor : "#ff7800",
                color : "#000",
                weight : 1,
                opacity : 1,
                fillOpacity : 0.8
            });
        }
    }).addTo(map);

    function updateMap() {
        let zoom = map.getZoom();
        let coords = map.getBounds();

        $.ajax({
            data: {
                in: coords._southWest.lng + ',' +
                    coords._northEast.lat + ',' +
                    coords._southWest.lng + ',' +
                    coords._southWest.lat + ',' +
                    coords._northEast.lng + ',' +
                    coords._southWest.lat + ',' +
                    coords._northEast.lng + ',' +
                    coords._northEast.lat + ',' +
                    coords._southWest.lng + ',' +
                    coords._northEast.lat,
                zoom: zoom
            },
            url: "/front-end/map?",
            success: function (results) {
                objectStyles = results.settings.styles;
                geoJsonLayer.clearLayers();
                geoJsonLayer.addData(results.objects);
            }
        });
    }

    function takeAction(layer) {
        console.log(layer.feature.properties._behavior);
        switch (layer.feature.properties._behavior) {
            case 'info':
                layer.openPopup();
                break;
            case 'navigation':
                map.fitBounds(layer.getBounds(), { padding: [0, 0] });
                break;
            case 'survey':
                window.location.href = '/geo/' + layer.feature.properties.id;
                break;
        }
    }

    function mouseEnter(layer) {
        if (objectStyles[layer.feature.properties._s2]) {
            layer.setStyle(objectStyles[layer.feature.properties._s2]);
        }
    }

    function mouseLeave(layer) {
        if (objectStyles[layer.feature.properties._s2]) {
            layer.setStyle(objectStyles[layer.feature.properties._s1] || defaultObjectStyle);
        }
    }
})();
