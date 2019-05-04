import {mapBoxAttribution, mapBoxUrl} from './map-config';

(() => {

    if (!document.getElementById('mapMini')) {
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

    let map = new L.map('mapMini', {
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
                    if ($('#mapMini').data('locate-on-load') === true) {
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
    geoCollection();


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
            c: center.lat + ',' + center.lng,
            g: mapOption.collection
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
                /*
                                map.fitBounds(results.bbox, {

                                });
                */
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
            success: function () {



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

        if (typeof mapOption.bbox == "object") {
            map.fitBounds(mapOption.bbox, {});
        } else {
            $.ajax({
                url: "/map/p",
                success: function (results) {
                    zoom = results.zoom;
                    lat = results.lat;
                    lng = results.lng;

                    setRealInitialMapView(lat, lng, zoom);
                }
            });
        }
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
        setLayerActiveStyle(layer);
        removeAllPopups();
        layer.feature.properties.activePopup = true;

        let coordinates;


        if (layer.feature.properties._behavior === 'survey') {
            coordinates = map.mouseEventToLatLng(ev.originalEvent);
            if (mapOption.survey === true) {
                openConfirmModal(layer);
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

        let popupContent = `<p class="text-center"><!--<form method="post" class="m-form" action="/front-end/geo-collection/add"><input type="hidden" name="geo-object" value="${layer.feature.properties.id}"><button type="submit">${layer.feature.properties.id}</button></form>-->${layer.feature.properties.type}<br />${layer.feature.properties.name}</p>`;

        popupLayer.bindPopup(popupContent, {
            offset: L.point(0, -20)
        }).on('popupclose', function () {
            confirmModal.addClass('d-none');
            layer.feature.properties.activePopup = false;
            setLayerDefaultStyle(layer);
            removeAllPopups();
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
                    let center = map.getCenter();
                    updateMap(center);
                }
            });

            geoCollection();
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

    function geoCollection() {
        if(typeof gcOpen !== 'undefined') {
        $.ajax({
            url: "http://gradska-sreda.localhost/geo-collection/"+ gcOpen + "/info",
            success: function (result) {
                let html = `<ul class="mt-4 pl-4">`;

                Object.keys(result).forEach(function (item) {
                    html += `<li class="mb-2">
							<a href="/geo-collection/${result[item].collection_uuid}" class="font-weight-bold">Маршрут</a>`;

                    if(gcOpen === result[item].collection_uuid) {
                        html += ` [<span class="text-success">активен</span>]
							<form method="post" class="float-right">
								<button type="submit" class="btn btn-sm btn-danger" style="font-size: 11px; padding: 4px 5px 0"><i class="fa fa-trash"></i> </button>
							</form>`;

                    }

                    html += `<div>
							дължина: ${result[item].length} м<br />
							оценен: ${result[item].completion.percentage} %<br />
								<div class="progress" style="height: 3px;">
									<div class="progress-bar" role="progressbar" style="width: ${result[item].completion.percentage}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>
						</li>`;
                });

                html += `</ul>`;

                $("#div3").html(html);
            }
        });

        }
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
