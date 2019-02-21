import { mapBoxAttribution, mapBoxUrl } from './map-config';

(() => {
    if (!document.getElementById('mapMain')) {
        return;
    }
    const mapCenter = [42.697664, 23.3166103];
    const defaultObjectStyle = {
        color: "#ff9710",
        opacity: 0.5,
        width: 5
    };
    let objectsSettings = {};

    let map = new L.map('mapMain', {
        updateWhenZooming: false
    });

    let mapStyle = L.tileLayer(mapBoxUrl, {
        attribution: mapBoxAttribution,
        maxNativeZoom: 19,
        maxZoom: 20,
        minZoom: 11,
        updateWhenZooming: false
    });
    mapStyle.addTo(map);

    let updateMapThrottle;
    map.on('dragend zoomend', function () {
        clearTimeout(updateMapThrottle);
        updateMapThrottle = setTimeout(updateMap, 200);
    });
    map.on('load', function () {
        clearTimeout(updateMapThrottle);
        updateMapThrottle = setTimeout(() => {
            updateMap(() => {
                locate();
            })
        }, 200);
    });

    map.setView(mapCenter, 17);
    map.on('locationfound', onLocationFound);
    map.on('locationerror', onLocationError);

    let geoJsonLayer = L.geoJSON([], {
        style: function (feature) {
            return objectsSettings.styles[feature.properties._s1] ? {...objectsSettings.styles[feature.properties._s1]} : {...defaultObjectStyle}
        },
        onEachFeature: function (feature, layer) {
            if (feature.properties._behavior === 'info' || feature.properties._behavior === 'survey') {
                let popupContent = `<p class="text-center">${feature.properties.type}<br />${feature.properties.name}</p>`;
                layer.bindPopup(popupContent, {
                    closeButton: true,
                    offset: L.point(0, -20)
                });
                layer.on('popupclose', function () {
                    confirmPopup.addClass('d-none');
                    setLayerDefaultStyle(layer);
                });
                layer.on('popupopen', function () {
                    setLayerActiveStyle(layer);
                });
            }
            layer.on('click', function (ev) {
                takeAction(layer, ev);
            });
            layer.on('mouseover', function () {
                if (layer._popup && layer._popup.isOpen()) {
                    return;
                }
                if (objectsSettings.styles[feature.properties._s2]) {
                    setLayerHoverStyle(layer);
                }
            });
            layer.on('mouseout', function () {
                if (layer._popup && layer._popup.isOpen()) {
                    return;
                }
                if (objectsSettings.styles[feature.properties._s1]) {
                    setLayerDefaultStyle(layer);
                }
            });
        },
        pointToLayer: function (feature, latlng) {
            return L.circleMarker(latlng, objectsSettings.styles[feature.properties._s1]);
        }
    }).addTo(map);

    const confirmPopup = $('.confirm');
    $(document).on('click', '[data-confirm-cancel]', function () {
        map.closePopup();
    });
    const loading = $('.loading');

    function updateMap(fn = () => {}) {
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
                objectsSettings = results.settings;
                console.log(objectsSettings);
                geoJsonLayer.clearLayers();
                geoJsonLayer.addData(results.objects);
                fn();
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
                let dialogTitle = objectsSettings.dialog[layer.feature.properties._dtext] || 'Искате ли да оцените';
                let dialogLink = '/geo/' + layer.feature.properties.id;
                confirmPopup.removeClass('d-none');
                confirmPopup.find('[data-confirm-title]').html(`${dialogTitle}?`);
                confirmPopup.find('[data-confirm-link]').attr('href', dialogLink);
                break;
        }
    }

    function setLayerDefaultStyle(layer) {
        layer.setStyle(objectsSettings.styles[layer.feature.properties._s1] || defaultObjectStyle)
    }

    function setLayerHoverStyle(layer) {
        layer.setStyle(objectsSettings.styles[layer.feature.properties._s2])
    }

    function setLayerActiveStyle(layer) {
        switch (layer.feature.geometry.type) {
            case "Point":
                layer.setStyle(objectsSettings.styles['on_dialog_point']);
                break;
            case "MultiLineString":
                layer.setStyle(objectsSettings.styles['on_dialog_line']);
                break;
            case "Polygon":
                layer.setStyle(objectsSettings.styles['on_dialog_polygon']);
                break;
        }
    }

    function locate() {
        loading.removeClass('d-none');
        map.locate({setView: true});
    }

    function onLocationFound(e) {
        loading.addClass('d-none');
        let radius = e.accuracy / 2;

        let center = L.circle(e.latlng, {
            color:       '#fff',
            fillColor:   '#2A93EE',
            fillOpacity: 1,
            weight:      4,
            opacity:     1,
            radius:      5
        }).addTo(map);

        L.circle(e.latlng, {
            radius:      e.accuracy / 2,
            color:       '#136AEC',
            fillColor:   '#136AEC',
            fillOpacity: 0.15,
            weight:      0
        }).addTo(map);

        center.bindPopup("Намирате се в радиус от " + radius + " метра от тази локация", {
                offset: L.point(0, -10)
            });
        center.openPopup();

        map.setZoom(objectsSettings.default_zoom);
    }

    function onLocationError() {
        loading.addClass('d-none');
        map.setView(mapCenter, 17)
    }
})();
