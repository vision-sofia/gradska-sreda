import { mapBoxAttribution, mapBoxUrl, apiEndpoints } from './map-config';
import { Survey }  from './survey';

const pathVoteSurvey = new Survey();

(() => {
    if (!document.getElementById('mapMain')) {
        return;
    }
    const loading = $('.loading');
    const confirmModal = $('.confirm');

    $(document).on('click', '[data-confirm-cancel]', function () {
        removeAllPopups();
    });

    //const mapCenter = mapOption.center;
    //const mapZoom = mapOption.zoom;

    const defaultObjectStyle = {
        color: "#ff9710",
        opacity: 0.5,
        width: 5
    };
    let objectsSettings = {};
    let initialLoad = false;

    let map = new L.map('mapMain', {
        updateWhenZooming: false,
        attributionControl: false
    });

    let mapStyle = L.tileLayer(mapBoxUrl, {
        attribution: mapBoxAttribution,
        maxNativeZoom: 19,
        maxZoom: 21,
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
            let center = map.getCenter();

            updateMap(center, () => {
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
            let styles = objectsSettings.styles[feature.properties._s1] ? {...objectsSettings.styles[feature.properties._s1]} : {...defaultObjectStyle};
            return styles;
        },
        onEachFeature: function (feature, layer) {
            layer.on('click', function (ev) {
                switch (feature.properties._behavior) {
                    case "navigation":
                        zoomToLayer(layer, ev);
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

    function updateMap(center, fn = () => {
    }) {
        let zoom = map.getZoom();
        let bounds = map.getBounds();
        let returnedTarget = {};

        let a = {
            in: bounds._southWest.lng + ',' +
            bounds._northEast.lat + ',' +
            bounds._northEast.lng + ',' +
            bounds._southWest.lat + ',',
            zoom: zoom,
            c: center.lat + ',' + center.lng
        };

        if (typeof b !== 'undefined') {
            returnedTarget = Object.assign(a, b);
        } else {
            returnedTarget = a;
        }

        $.ajax({
            data: returnedTarget,
            url: "/front-end/map?",
            success: function (results) {
                objectsSettings = results.settings;
                geoJsonLayer.clearLayers();
                geoJsonLayer.addData(results.objects);
                fn();
            }
        });
    }

    function saveViewport(center) {
        let zoom = map.getZoom();
        let a = {
            zoom: zoom,
            c: center.lat + ',' + center.lng
        };

        $.ajax({
            data: a,
            url: "/map/z",
            success: function (results) {

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
            color: '#fff',
            fillColor: '#2A93EE',
            fillOpacity: 1,
            weight: 4,
            opacity: 1,
            radius: 5
        }).addTo(myLocationLayerGroup);

        L.circle(e.latlng, {
            radius: e.accuracy / 2,
            color: '#136AEC',
            fillColor: '#136AEC',
            fillOpacity: 0.15,
            weight: 0
        }).addTo(myLocationLayerGroup);

        center.bindPopup("Намирате се в радиус от " + radius + " метра от тази локация", {
            offset: L.point(0, -10)
        });
        center.openPopup();
    }

    function setInitialMapView() {
        loading.addClass('d-none');

        let zoom;
        let lat;
        let lng;

        $.ajax({
            url: "/map/p",
            success: function (results) {
                zoom = results.zoom;
                lat = results.lat;
                lng = results.lng;
                setRealInitialMapView(lat, lng, zoom)
            }
        });
    }

    function setRealInitialMapView(lat, lng, zoom) {
        map.setView([lat, lng], zoom)
    }

    function zoomToLayer(layer, ev) {
        let clickCoordinates = map.mouseEventToLatLng(ev.originalEvent);
        if (layer.feature.properties._zoom && layer.feature.properties._zoom !== map.getZoom()) {
            map.setView(clickCoordinates, layer.feature.properties._zoom);
        } else {
            map.setView(clickCoordinates);

            saveViewport(clickCoordinates);
        }
    }

    function openLayerPopup(layer, ev) {
        pathVoteSurvey.geoObjectUUID = layer.feature.properties.id;
        pathVoteSurvey.layer = layer;
        pathVoteSurvey.getQuestions();

        setLayerActiveStyle(layer);
        removeAllPopups();
        layer.feature.properties.activePopup = true;

        let coordinates;


        if (layer.feature.properties._behavior === 'survey') {
            coordinates = map.mouseEventToLatLng(ev.originalEvent);
            if (mapOption.survey === true) {
                // openConfirmModal(layer);
            }
        } else {
            coordinates = ev.latlng;
        }


        let popupLayer = L.circle(coordinates, {
            fillOpacity: 0,
            weight: 0,
            opacity: 0,
            radius: 1
        }).addTo(popusLayerGroup);

        const surveyTemplate = `
            <p class="text-center">
                <!-- <form method="post" class="m-form" action="/front-end/geo-collection/add">
                    <input type="hidden" name="geo-object" value="${layer.feature.properties.id}">
                    <button type="submit">${layer.feature.properties.id}</button>
                </form> -->
                ${layer.feature.properties.type}<br />${layer.feature.properties.name}
            </p>
            <div class="">
                <div class="container py-4">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h5 class="font-weight-bold mb-3" data-confirm-title>
                                ${ objectsSettings.dialog[layer.feature.properties._dtext] || 'Искате ли да оцените' }
                            </h5>
                        </div>
                        <div class="col-12 text-center">
                            <button data-survey-request data-url="${ apiEndpoints.geo + layer.feature.properties.id }" class="btn btn-success mr-3 px-4">ДА</button>
                            <button data-confirm-cancel class="btn btn-danger cursor-pointer px-4">НЕ</button>
                        </div>
                    </div>
                </div>
            </div>`;

        const popupContent = surveyTemplate;

        popupLayer.bindPopup(popupContent, {
            offset: L.point(0, -20)
        }).on('popupclose', function () {
            confirmModal.addClass('d-none');
            layer.feature.properties.activePopup = false;
            setLayerDefaultStyle(layer);
            removeAllPopups();
            pathVoteSurvey.close();
        }).openPopup();

        let collection = mapOption.collection;

        if (typeof collection !== 'undefined') {
            $.ajax({
                type: "POST",
                url: '/front-end/geo-collection/add',
                data: {
                    'geo-object': layer.feature.properties.id,
                    'collection': collection
                },
                success: function () {
                    updateMap();
                }
            });
        }
        /*
                $(".m-form").submit(function(e) {
                    var form = $(this);
                    var url = form.attr('action');

                    $.ajax({
                        type: "POST",
                        url: '/front-end/geo-collection/add',
                        //data: form.serialize(),
                        data: {
                            'geo-object': 'cd538bf5-3220-4259-b26d-3488d71ca7d7'
                        },
                        success: function()
                        {
                            updateMap();
                        }
                    });

                    e.preventDefault();
                });
        */
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
