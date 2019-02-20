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
    let dialogTitles = {};

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
            if (feature.properties._behavior === 'info' || feature.properties._behavior === 'survey') {
                let popupContent = `<p class="text-center">${feature.properties.type}<br />${feature.properties.name}</p>`;
                layer.bindPopup(popupContent, {
                    closeButton: true,
                    offset: L.point(0, -20)
                });
                layer.getPopup().on('remove', function() {
                    confirmPopup.addClass('d-none');
                });
            }
            layer.on('click', function (ev) {
                takeAction(layer, ev);
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

    const confirmPopup = $('.confirm');
    $(document).on('click', '[data-confirm-cancel]', function() {
        map.closePopup();
    });

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
                dialogTitles = results.settings.dialog;
                geoJsonLayer.clearLayers();
                geoJsonLayer.addData(results.objects);
            }
        });
    }

    function takeAction(layer, ev) {
        switch (layer.feature.properties._behavior) {
            case 'info':
                layer.openPopup();
            break;
            case 'navigation':
                if (layer.feature.properties._zoom) {
                    let coords = map.mouseEventToLatLng(ev.originalEvent);
                    map.setView([coords.lat, coords.lng], layer.feature.properties._zoom);
                } else {
                    map.fitBounds(layer.getBounds(), {maxZoom: [0, 0]});
                }
            break;
            case 'survey':
                layer.openPopup();
                let dialogTitle = dialogTitles[layer.feature.properties._dtext] || 'Искате ли да оцените';
                let dialogLink = '/geo/' + layer.feature.properties.id;
                confirmPopup.removeClass('d-none');
                confirmPopup.find('[data-confirm-title]').html(`${dialogTitle}?`);
                confirmPopup.find('[data-confirm-link]').attr('href', dialogLink);
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
