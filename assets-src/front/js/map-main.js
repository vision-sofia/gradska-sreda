import { mapBoxAttribution, mapBoxUrl } from './map-config';

(() => {
    if (!document.getElementById('mapMain')) {
        return;
    }
    const loading = $('.loading');
    const confirmModal = $('.confirm');
    $(document).on('click', '[data-confirm-cancel]', function () {
        removeAllPopups();
    });
    const mapCenter = [42.697664, 23.3166103];
    const defaultObjectStyle = {
        color: "#ff9710",
        opacity: 0.5,
        width: 5
    };
    let objectsSettings = {};
    let initialLoad = false;

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

    let myLocationButton = L.Control.extend({
        options: {
            position: 'topleft'
        },
        onAdd: () => {
            let container = L.DomUtil.create('button', 'leaflet-bar leaflet-control-custom far fa-dot-circle');
            container.type = "button";
            container.onclick = function () {
                locate();
            };
            return container;
        }
    });
    map.addControl(new myLocationButton());

    let myLocationLayerGroup = L.layerGroup();
    myLocationLayerGroup.addTo(map);

    let popusLayerGroup = L.layerGroup();
    popusLayerGroup.addTo(map);

    let updateMapThrottle;
    map.on('dragend zoomend', function () {
        clearTimeout(updateMapThrottle);
        updateMapThrottle = setTimeout(() => {
            updateMap(() => {
                if (!initialLoad) {
                    initialLoad = true;
                    if ($('#mapMain').data('locate-on-load') === true) {
                        locate();
                    }
                }
            })
        }, 200);
    });

    map.on('locationfound', setMapViewToMyLocation);
    map.on('locationerror', setInitialMapView);

    let geoJsonLayer = L.geoJSON([], {
        style: function (feature) {
            return objectsSettings.styles[feature.properties._s1] ? {...objectsSettings.styles[feature.properties._s1]} : {...defaultObjectStyle}
        },
        onEachFeature: function (feature, layer) {
            layer.on('click', function (ev) {
                switch (feature.properties._behavior) {
                    case "navigation": zoomToLayer(layer, ev);
                    break;
                    default:
                        openLayerPopup(layer, ev);
                        zoomToLayer(layer, ev);
                    break;
                }
            });
            layer.on('mouseover', function () {
                if (layer.feature.properties.activePopup) {
                    return;
                }
                if (objectsSettings.styles[feature.properties._s2]) {
                    setLayerHoverStyle(layer);
                }
            });
            layer.on('mouseout', function () {
                if (layer.feature.properties.activePopup) {
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

    setInitialMapView();

    function updateMap(fn = () => {}) {
        let zoom = map.getZoom();
        let coords = map.getBounds();

        $.ajax({
            data: {
                in: coords._southWest.lng + ',' +
                    coords._northEast.lat + ',' +
                    coords._northEast.lng + ',' +
                    coords._southWest.lat + ',',
                zoom: zoom
            },
            url: "/front-end/map?",
            success: function (results) {
                objectsSettings = results.settings;
                geoJsonLayer.clearLayers();
                geoJsonLayer.addData(results.objects);
                fn();
            }
        });
    }

    function locate() {
        loading.removeClass('d-none');
        map.locate({
            setView: true,
            maxZoom: objectsSettings.default_zoom
        });
    }

    function setMapViewToMyLocation(e) {
        loading.addClass('d-none');
        let radius = e.accuracy / 2;

        myLocationLayerGroup.eachLayer((layer) => {
            myLocationLayerGroup.removeLayer(layer);
        });

        let center = L.circle(e.latlng, {
            color:       '#fff',
            fillColor:   '#2A93EE',
            fillOpacity: 1,
            weight:      4,
            opacity:     1,
            radius:      5
        }).addTo(myLocationLayerGroup);

        L.circle(e.latlng, {
            radius:      e.accuracy / 2,
            color:       '#136AEC',
            fillColor:   '#136AEC',
            fillOpacity: 0.15,
            weight:      0
        }).addTo(myLocationLayerGroup);

        center.bindPopup("Намирате се в радиус от " + radius + " метра от тази локация", {
            offset: L.point(0, -10)
        });
        center.openPopup();
    }

    function setInitialMapView() {
        loading.addClass('d-none');
        map.setView(mapCenter, 17)
    }

    function zoomToLayer(layer, ev) {
        let clickCoordinates = map.mouseEventToLatLng(ev.originalEvent);
        if (layer.feature.properties._zoom && layer.feature.properties._zoom !== map.getZoom()) {
            map.setView(clickCoordinates, layer.feature.properties._zoom);
        } else {
            map.setView(clickCoordinates);
        }
    }

    function openLayerPopup(layer, ev) {
        setLayerActiveStyle(layer);
        removeAllPopups();
        layer.feature.properties.activePopup = true;

        let coordinates;
        if (layer.feature.properties._behavior === 'survey') {
            coordinates = map.mouseEventToLatLng(ev.originalEvent);
            openConfirmModal(layer);
        } else {
            coordinates = ev.latlng;
        }

        let popupLayer = L.circle(coordinates, {
            fillOpacity: 0,
            weight:      0,
            opacity:     0,
            radius:      1
        }).addTo(popusLayerGroup);

        let popupContent = `<p class="text-center">${layer.feature.properties.type}<br />${layer.feature.properties.name}</p>`;

        popupLayer.bindPopup(popupContent, {
            offset: L.point(0, -20)
        }).on('popupclose', function () {
            confirmModal.addClass('d-none');
            layer.feature.properties.activePopup = false;
            setLayerDefaultStyle(layer);
            removeAllPopups();
        }).openPopup();
    }

    function openConfirmModal(layer) {
        let dialogTitle = objectsSettings.dialog[layer.feature.properties._dtext] || 'Искате ли да оцените';
        let dialogLink = '/geo/' + layer.feature.properties.id;
        confirmModal.removeClass('d-none');
        confirmModal.find('[data-confirm-title]').html(`${dialogTitle}?`);
        confirmModal.find('[data-confirm-link]').attr('href', dialogLink);
    }

    function removeAllPopups() {
        map.closePopup();
        popusLayerGroup.eachLayer((layer) => {
            popusLayerGroup.removeLayer(layer);
        });
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
})();
