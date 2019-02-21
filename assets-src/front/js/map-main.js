import { mapBoxAttribution, mapBoxUrl } from './map-config';

(() => {
    if (!document.getElementById('mapMain')) {
        return;
    }
    const loading = $('.loading');
    const confirmPopup = $('.confirm');
    $(document).on('click', '[data-confirm-cancel]', function () {
        map.closePopup();
    });
    const mapCenter = [42.697664, 23.3166103];
    const defaultObjectStyle = {
        color: "#ff9710",
        opacity: 0.5,
        width: 5
    };
    let objectsSettings = {};
    let selectedObject = {};
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

    let updateMapThrottle;
    map.on('dragend zoomend', function () {
        clearTimeout(updateMapThrottle);
        updateMapThrottle = setTimeout(() => {
            updateMap(() => {
                if (!initialLoad) {
                    initialLoad = true;
                    // locate();
                }
            })
        }, 200);
    });

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

    map.on('locationfound', setMapViewToMyLocation);
    map.on('locationerror', setInitialMapView);

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
                    selectedObject = {};
                });
                layer.on('popupopen', function () {
                    setLayerActiveStyle(layer);
                });
            }
            layer.on('click', function (ev) {
                map.closePopup();
                zoomToLayer(layer, ev);
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

    setInitialMapView();

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
                geoJsonLayer.clearLayers();
                geoJsonLayer.addData(results.objects);
                if (Object.keys(selectedObject).length) {
                    setLayerActiveStyle(selectedObject.layer);
                    openLayerPopup(selectedObject.layer, selectedObject.event);
                }
                fn();
            }
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

    function zoomToLayer(layer, ev) {
        selectedObject.layer = layer;
        selectedObject.event = ev;

        if (layer.feature.properties._zoom && layer.feature.properties._zoom !== map.getZoom()) {
            map.setView(ev.latlng, layer.feature.properties._zoom);
        } else {
            map.setView(ev.latlng);
            openLayerPopup(layer, ev);
        }
    }

    function openLayerPopup(layer, ev) {
        if (!layer.getPopup()) {
            return;
        }
        layer.getPopup().setLatLng(ev.latlng).openOn(map);
        if (layer.feature.properties._behavior === 'survey') {
            openConfirmPopup(layer);
        }
    }

    function openConfirmPopup(layer) {
        let dialogTitle = objectsSettings.dialog[layer.feature.properties._dtext] || 'Искате ли да оцените';
        let dialogLink = '/geo/' + layer.feature.properties.id;
        confirmPopup.removeClass('d-none');
        confirmPopup.find('[data-confirm-title]').html(`${dialogTitle}?`);
        confirmPopup.find('[data-confirm-link]').attr('href', dialogLink);
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
})();
