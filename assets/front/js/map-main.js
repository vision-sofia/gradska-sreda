import { mapBoxAttribution, mapBoxUrl, apiEndpoints, defaultObjectStyle } from './map-config';

export class Map {
    map;
    objectsSettings = {};
    geoJsonLayer = {};
    activeLayer;
    myLocationLayerGroup = L.layerGroup();
    popusLayerGroup = L.layerGroup();
    voteSurvay;

    constructor() {
    }

    init() {
        if (!document.getElementById('mapMain')) {
            return;
        }
        this.loading = $('.loading');
        this.confirmModal = $('.confirm');

        $(document).on('click', '[data-confirm-cancel]', () => {
            this.removeAllPopups();
        });
        //const mapCenter = mapOption.center;
        //const mapZoom = mapOption.zoom;

    
        let initialLoad = false;

        this.map = new L.map('mapMain', {
            updateWhenZooming: false,
            attributionControl: false
        });

        this.map.setActiveArea(defaultObjectStyle.mapActiveArea);

        let mapStyle = L.tileLayer(mapBoxUrl, {
            attribution: mapBoxAttribution,
            // * If difference between "maxNativeZoom" and "maxZoom" === 2
            // and "leafLet-active-area" is included  "Maximum call stack size exceeded" is thrown on max zoom reached.
            // https://github.com/Mappy/Leaflet-active-area/issues/32
            maxNativeZoom: 20, 
            maxZoom: 21,
            minZoom: 11,
            updateWhenZooming: false
        });
        mapStyle.addTo(this.map);

        let myLocationButton = L.Control.extend({
            options: {
                position: 'topleft'
            },
            onAdd: () => {
                let container = L.DomUtil.create('button', 'leaflet-bar leaflet-control-custom far fa-dot-circle');
                container.type = "button";
                container.onclick = () => {
                    this.locate();
                };
                return container;
            }
        });
        this.map.addControl(new myLocationButton());

        
        this.myLocationLayerGroup.addTo(this.map);

        this.popusLayerGroup.addTo(this.map);

        let updateMapThrottle;
        this.map.on('moveend', () => {
            clearTimeout(updateMapThrottle);
            
            updateMapThrottle = setTimeout(() => {
                let center = this.map.getCenter();

                this.updateMap(center, () => {
                    if (!initialLoad) {
                        initialLoad = true;
                        if ($('#mapMain').data('locate-on-load') === true) {
                            this.locate();
                        }
                    }
                })
            }, 200);
        });


        this.map.on('locationfound', this.setMapViewToMyLocation.bind(this));
        this.map.on('locationerror', this.setInitialMapView.bind(this));

        this.geoJsonLayer = L.geoJSON([], { 
            style: (feature) => {
                let styles = this.objectsSettings.styles[feature.properties._s1] ? {...this.objectsSettings.styles[feature.properties._s1]} : {...defaultObjectStyle};
                return styles;
            },
            onEachFeature: (feature, layer) => {
                layer.on('click', (ev) => {
                    switch (feature.properties._behavior) {
                        case 'navigation':
                            this.zoomToLayer(layer, ev);
                            break;
                        default:
                            this.openLayerPopup(layer, ev);
                            this.zoomToLayer(layer, ev);
                            break;
                    }
                });
                layer.on('mouseover', () => {
                    if (layer.feature.properties.activePopup) {
                        return; 
                    }
                    if (this.objectsSettings.styles[feature.properties._s2]) {
                        this.setLayerHoverStyle(layer);
                    }
                });
                layer.on('mouseout', () => {
                    if (layer.feature.properties.activePopup || this.activeLayer === layer) {
                        return;
                    }
                    if (this.objectsSettings.styles[feature.properties._s1]) {
                        this.setLayerDefaultStyle(layer);
                    }
                });
            },
            pointToLayer: (feature, latlng) => {
                return L.circleMarker(latlng, this.objectsSettings.styles[feature.properties._s1]);
            }
        }).addTo(this.map);

        this.setInitialMapView();
    }

    updateMap(center, fn = () => {
    }) {
        let zoom = this.map.getZoom();
        let bounds = this.map.getBounds();
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
            success: (results) => {
                this.objectsSettings = results.settings;
                this.geoJsonLayer.clearLayers();
                this.geoJsonLayer.addData(results.objects);
                this.setLayerActiveStyle();
                fn();
            }
        });
    }

    saveViewport(center) {
        let zoom = this.map.getZoom();
        let a = {
            zoom: zoom,
            c: center.lat + ',' + center.lng
        };

        $.ajax({
            data: a,
            url: "/map/z",
            success: (results) => {

            }
        });
    }

    locate() {
        this.loading.removeClass('d-none');
        this.map.locate({
            setView: true,
            maxZoom: this.objectsSettings.default_zoom
        });
    }

    setMapViewToMyLocation(e) {
        this.loading.addClass('d-none');
        let radius = e.accuracy / 2;

        this.myLocationLayerGroup.eachLayer((layer) => {
            this.myLocationLayerGroup.removeLayer(layer);
        });

        let center = L.circle(e.latlng, {
            color: '#fff',
            fillColor: '#2A93EE',
            fillOpacity: 1,
            weight: 4,
            opacity: 1,
            radius: 5
        }).addTo(this.myLocationLayerGroup);

        L.circle(e.latlng, {
            radius: e.accuracy / 2,
            color: '#136AEC',
            fillColor: '#136AEC',
            fillOpacity: 0.15,
            weight: 0
        }).addTo(this.myLocationLayerGroup);

        center.bindPopup("Намирате се в радиус от " + radius + " метра от тази локация", {
            offset: L.point(0, -10)
        });
        center.openPopup();
    }

    setInitialMapView() {
        this.loading.addClass('d-none');

        let zoom;
        let lat;
        let lng;

        $.ajax({
            url: "/map/p",
            success: (results) => {
                zoom = results.zoom;
                lat = results.lat;
                lng = results.lng;
                this.setRealInitialMapView(lat, lng, zoom)
            }
        });
    }

    setRealInitialMapView(lat, lng, zoom) {
        this.map.setView([lat, lng], zoom)
    }

    zoomToLayer(layer, ev, coordinates) {
        let clickCoordinates = coordinates || ev.latlng;
        if (layer.feature.properties._zoom && layer.feature.properties._zoom !== this.map.getZoom()) {
            this.map.setView(clickCoordinates, layer.feature.properties._zoom);
        } else {
            this.map.setView(clickCoordinates);

            this.saveViewport(clickCoordinates);
        }
    }

    openLayerPopup(layer, ev) {
        layer.feature.properties.activePopup = true;
        this.setLayerActiveStyle(layer);
        this.removeAllPopups();

        let coordinates;


        if (layer.feature.properties._behavior === 'survey') {
            coordinates = this.map.mouseEventToLatLng(ev.originalEvent);
            if (mapOption.survey === true) {
                // openConfirmModal(layer);
                this.setSurveyData(layer, ev);
            }
        } else {
            coordinates = ev.latlng;
        }


        let popupLayer = L.circle(coordinates, {
            fillOpacity: 0,
            weight: 0,
            opacity: 0,
            radius: 1
        }).addTo(this.popusLayerGroup);


        const surveyTemplate = `
            <p class="text-center">
                <!-- <form method="post" class="m-form" action="/front-end/geo-collection/add">
                    <input type="hidden" name="geo-object" value="${layer.feature.properties.id}">
                    <button type="submit">${layer.feature.properties.id}</button>
                </form> -->
                ${layer.feature.properties.type}<br />${layer.feature.properties.name}
            </p>
            <div class="survey-modal">
                <div class="container py-4">
                    <div class="row">
                        <div class="col-12 text-center">
                            <h5 class="font-weight-bold mb-3" data-confirm-title>
                              Искате ли да оцените
                            </h5>
                        </div>
                        <div class="col-12 text-center">
                            <button data-survey-open data-url="${ apiEndpoints.geo + layer.feature.properties.id }" class="btn btn-success mr-3 px-4">ДА</button>
                            <button data-confirm-cancel class="btn btn-danger cursor-pointer px-4">НЕ</button>
                        </div>
                    </div>
                </div>
            </div>`;

        const popupContent = surveyTemplate;

        popupLayer.bindPopup(popupContent, {
            offset: L.point(0, -20)
        }).on('popupclose', () => {
            this.confirmModal.addClass('d-none');
            layer.feature.properties.activePopup = false;
            this.activeLayer = null;
            this.setLayerDefaultStyle(layer);
            this.removeAllPopups();
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
                success: () => {
                    this.updateMap();
                }
            });
        }
    }

    removeAllPopups() {
        this.map.closePopup();
        this.popusLayerGroup.eachLayer((layer) => {
            this.popusLayerGroup.removeLayer(layer);
        });
    }

    setLayerDefaultStyle(layer) {
        layer.setStyle(this.objectsSettings.styles[layer.feature.properties._s1] || defaultObjectStyle)
    }

    setLayerHoverStyle(layer) {
        layer.setStyle(this.objectsSettings.styles[layer.feature.properties._s2])
    }

    setLayerActiveStyle(layer) {
        if (layer) {
            this.activeLayer = layer;
        } else if (!this.activeLayer) {
            return;
        } else {
            this.activeLayer.addTo(this.map);
        }

        this.activeLayer.bringToFront();

        switch (this.activeLayer.feature.geometry.type) {
            case 'Point':
                this.activeLayer.setStyle(this.objectsSettings.styles['on_dialog_point']);
                break;
            case 'MultiLineString':
                this.activeLayer.setStyle(this.objectsSettings.styles['on_dialog_line']);
                break;
            case 'Polygon':
                this.activeLayer.setStyle(this.objectsSettings.styles['on_dialog_polygon']);
                break;
        }
    }

    setSurvey(voteSurvay) {
        this.voteSurvay = voteSurvay;
    }

    setSurveyData(layer, ev) {
        this.voteSurvay.setLayerData(layer, ev);
    }

    // closeSurvey() => {
        // pathVoteSurvey.close();
    // }
};

