import { mapBoxAttribution, mapBoxUrl, apiEndpoints, defaultObjectStyle, defaultElConfig } from './map-config';
import { debounce } from './helpers';
import { Collection } from './collections';
// import { defaultMapSize } from './map-config';

export class Map {
    map;
    activeLayer;
    myLocationLayerGroup = L.layerGroup();
    popusLayerGroup = L.layerGroup();
    voteSurvay;
    mapResponse = {
        settings: {},
        ObjectsLayerGeoJson: {},
        CollectionsLayerControl: {},
        SurveyResponses: {},
    };
    activeAreaList = [];
    isMapLoaded = false;


    constructor() {
    }

    init() {
        this.initMap();
        this.selectInitialElements();
        this.events();
        this.loading = $('.loading');
        this.toggleHeaderEl(true);
        //const mapCenter = mapOption.center;
        //const mapZoom = mapOption.zoom;

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
                container.type = 'button';
                container.onclick = () => {
                    this.locate();
                };
                return container;
            }
        });
        this.map.addControl(new myLocationButton());

        this.myLocationLayerGroup.addTo(this.map);

        this.popusLayerGroup.addTo(this.map);

        this.mapResponse.ObjectsLayerGeoJson = L.geoJSON([], {
            style: (feature) => {
                let styles = this.mapResponse.settings.styles[feature.properties._s1] ? {...this.mapResponse.settings.styles[feature.properties._s1]} : {...defaultObjectStyle};
                return styles;
            },
            onEachFeature: (feature, layer) => {
                layer.on('click', (ev) => {
                    console.log('Object LayerGeoJson CLICK');
                    switch (feature.properties._behavior) {
                        case 'navigation':
                            this.zoomToLayer(layer, ev);
                            break;
                        default:
                            this.onLayerClick(layer, ev);
                            this.zoomToLayer(layer, ev);
                            break;
                    }
                });
                layer.on('mouseover', () => {
                    if (layer.feature.properties.activePopup) {
                        return;
                    }
                    if (this.mapResponse.settings.styles[feature.properties._s2]) {
                        this.setLayerHoverStyle(layer);
                    }
                });
                layer.on('mouseout', () => {
                    if (layer.feature.properties.activePopup || this.activeLayer === layer) {
                        return;
                    }
                    if (this.mapResponse.settings.styles[feature.properties._s1]) {
                        this.setLayerDefaultStyle(layer);
                    }
                });
            },
            pointToLayer: (feature, latlng) => {
                return L.circleMarker(latlng, this.mapResponse.settings.styles[feature.properties._s1]);
            }
        }).addTo(this.map);


        this.mapResponse.CollectionsLayerControl = L.layerGroup().addTo(this.map);

        this.mapResponse.SurveyResponsesLayerGeoJson = L.geoJSON([], {
            style: (feature) => {
                let styles = this.mapResponse.settings.styles[feature.properties._s1] ? {...this.mapResponse.settings.styles[feature.properties._s1]} : {...defaultObjectStyle};
                return styles;
            },
            onEachFeature: (feature, layer) => {
                layer.on('click', (ev) => {
                    console.log('Survey - ResponsesLayerGeoJson - CLICK');
                    switch (feature.properties._behavior) {
                        case 'navigation':
                            this.zoomToLayer(layer, ev);
                            break;
                        default:
                            this.onLayerClick(layer, ev);
                            this.zoomToLayer(layer, ev);
                            break;
                    }
                });
                layer.on('mouseover', () => {
                    if (layer.feature.properties.activePopup) {
                        return;
                    }
                    if (this.mapResponse.settings.styles[feature.properties._s2]) {
                        this.setLayerHoverStyle(layer);
                    }
                });
                layer.on('mouseout', () => {
                    if (layer.feature.properties.activePopup || this.activeLayer === layer) {
                        return;
                    }
                    if (this.mapResponse.settings.styles[feature.properties._s1]) {
                        this.setLayerDefaultStyle(layer);
                    }
                });
            },
            pointToLayer: (feature, latlng) => {
                return L.circleMarker(latlng, this.mapResponse.settings.styles[feature.properties._s1]);
            }
        }).addTo(this.map);

        this.setInitialMapView();
    }

    initMap() {
        const elMap = document.getElementById('mapMain');
        if (!elMap) {
            return;
        }

        this.map = new L.map('mapMain', {
            updateWhenZooming: false,
            attributionControl: false
        });

        this.map.setActiveArea('map-active-area');
        this.map.setActiveArea(defaultObjectStyle.mapActiveArea);
    }

    events() {
        $(document).on('click', '[data-confirm-cancel]', () => {
            this.removeAllPopups();
        });

        $(document).on('click', '[data-confirm-cancel]', () => {
            this.removeAllPopups();
        });

        $(document).on('click', '[href="collections"]', (e) => {
            $(e.currentTarget).toggleClass('.active')
        });


        this.map.on('moveend', debounce(() => {
            const center = this.map.getCenter();
            this.updateMap(center);
        }, 200));

        this.map.on('locationfound', this.setMapViewToMyLocation.bind(this));
        this.map.on('locationerror', this.setInitialMapView.bind(this));

        window.addEventListener('resize', debounce(() => {
            this.setActiveArea();
        }, 200, false), false);
    }

    selectInitialElements() {
        defaultElConfig.elHeader = document.querySelector(defaultElConfig.headerId);
    }

    toggleHeaderEl(isActive) {
        if (window.innerWidth > 768 && !isActive) {
            return;
        }

        if (isActive || !defaultElConfig.elHeader.classList.contains('active') && isActive) {
            defaultElConfig.elHeader.classList.add('active');
            this.addToActiveAreaList(defaultElConfig.elHeader);
        } else {
            defaultElConfig.elHeader.classList.remove('active');
            this.removeFromActiveAreaList(defaultElConfig.elHeader);
        }
    }

    updateMap(center, fn = () => {
    }) {
        const zoom = this.map.getZoom();
        const bounds = this.map.getBounds();
        let returnedTarget = {};

        const a = {
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
            url: '/front-end/map?',
            success: (results) => {
                this.isMapLoaded = true;
                this.mapResponse.settings = results.settings;
                this.mapResponse.ObjectsLayerGeoJson.clearLayers();

                this.mapResponse.ObjectsLayerGeoJson.addData(results.objects);
                this.mapResponse.CollectionsLayerControl.clearLayers();

                const geoCollectinResult = results.geoCollections;

                for (const removeThisObj in geoCollectinResult) {
                    if (geoCollectinResult.hasOwnProperty(removeThisObj)) {
                        const collection = new Collection(this, this.mapResponse.settings);

                        collection.layer._leaflet_id = removeThisObj;
                        geoCollectinResult[removeThisObj].forEach(item => {
                            collection.layer.addData(item);
                        })

                        this.mapResponse.CollectionsLayerControl.addLayer(collection.layer);
                    }
                }

                this.mapResponse.SurveyResponsesLayerGeoJson.clearLayers();
                this.mapResponse.SurveyResponsesLayerGeoJson.addData(results.surveyResponses);

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
            maxZoom: this.mapResponse.settings.default_zoom
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
        if (layer && layer.feature.properties._zoom && layer.feature.properties._zoom !== this.map.getZoom()) {
            this.map.setView(clickCoordinates, layer.feature.properties._zoom);
        } else {
            this.map.setView(clickCoordinates);

            this.saveViewport(clickCoordinates);
        }
    }

    onLayerClick(layer, ev) {
        console.log('CLICK');

        layer.feature.properties.activePopup = true;
        this.setLayerActiveStyle(layer);
        this.removeAllPopups();

        switch (layer.feature.properties._behavior) {
            case 'info':
                this.openInfoPopup(layer, ev);
                break;
            case 'survey':
                if (this.collections.isCollectionsActive && this.collections.isCollectionShown) {
                    this.collections.add(layer, ev);
                } else {
                    this.openSuerveyPopup(layer, ev);
                }
                break;
        }
    }

    removeAllPopups() {
        this.map.closePopup();
        this.popusLayerGroup.eachLayer((layer) => {
            this.popusLayerGroup.removeLayer(layer);
        });
    }

    onPopupClose(layer) {
        layer.feature.properties.activePopup = false;
        this.activeLayer = null;
        this.setLayerDefaultStyle(layer);
        this.removeAllPopups();
    }

    openInfoPopup(layer, ev) {
        const coordinates = this.map.mouseEventToLatLng(ev.originalEvent);

        let popupLayer = L.circle(coordinates, {
            fillOpacity: 0,
            weight: 0,
            opacity: 0,
            radius: 1
        }).addTo(this.popusLayerGroup);

        const infoTemplate = `
            <p class="text-center">
                ${layer.feature.properties.type}
                <br />
                ${layer.feature.properties.name}
            </p>
        `;

        const popupContent = infoTemplate;

        popupLayer.bindPopup(popupContent, {
            offset: L.point(0, -20)
        }).on('popupclose', () => {
            this.onPopupClose(layer);
        }).openPopup();
    }

    openSuerveyPopup(layer, ev) {
        const coordinates = this.map.mouseEventToLatLng(ev.originalEvent);
        this.setSurveyData(layer, ev);

        let popupLayer = L.circle(coordinates, {
            fillOpacity: 0,
            weight: 0,
            opacity: 0,
            radius: 1
        }).addTo(this.popusLayerGroup);

        const surveyTemplate = `
            <p class="text-center">
                ${layer.feature.properties.type}
                <br />
                ${layer.feature.properties.name}
            </p>
            <div class="survey-modal text-center">
                <h5 class="font-weight-bold mb-2 h6" data-confirm-title>
                    Искате ли да оцените
                </h5>
                <button data-toggle-open class="btn btn-success mr-3 py-0 px-2" data-toggle-for="path-vote-survey"  data-url="${ apiEndpoints.geo + layer.feature.properties.id }">ДА</button>
                <button data-confirm-cancel class="btn btn-danger cursor-pointer py-0 px-2">НЕ</button>
            </div>
        `;

        const popupContent = surveyTemplate;

        popupLayer.bindPopup(popupContent, {
            // offset: L.point(0, -20)
        }).on('popupclose', () => {
            this.onPopupClose(layer);
        }).openPopup();
    }

    setLayerDefaultStyle(layer) {
        layer.setStyle(this.mapResponse.settings.styles[layer.feature.properties._s1] || defaultObjectStyle)
    }

    setLayerHoverStyle(layer) {
        layer.setStyle(this.mapResponse.settings.styles[layer.feature.properties._s2])
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
                this.activeLayer.setStyle(this.mapResponse.settings.styles['on_dialog_point']);
                break;
            case 'MultiLineString':
                this.activeLayer.setStyle(this.mapResponse.settings.styles['on_dialog_line']);
                break;
            case 'Polygon':
                this.activeLayer.setStyle(this.mapResponse.settings.styles['on_dialog_polygon']);
                break;
        }
    }

    setCollection(collctions) {
        this.collections = collctions;
    }

    setSurvey(voteSurvay) {
        this.voteSurvay = voteSurvay;
    }

    setSurveyData(layer, ev) {
        this.voteSurvay.setLayerData(layer, ev);
    }

    setActiveArea() {
        if (!this.map._loaded) {
            return;
        }

        let top = 0,
        bottom = 0,
        left = 0,
        right = 0;

        let callculatedWidth = 0,
        callculatedWidthLeft = 0,
        callculatedWidthRight = 0,
        callculatedHeight = 0,
        callculatedHeightTop = 0,
        callculatedHeightBot = 0;

        this.activeAreaList.forEach(el => {
            const elWidth = parseFloat(getComputedStyle(el).getPropertyValue('width')),
            elHeight = parseFloat(getComputedStyle(el).getPropertyValue('height')),
            elTop = parseFloat(getComputedStyle(el).getPropertyValue('top')),
            elBottom = parseFloat(getComputedStyle(el).getPropertyValue('bottom')),
            elLeft = parseFloat(getComputedStyle(el).getPropertyValue('left')),
            elRight = parseFloat(getComputedStyle(el).getPropertyValue('right'));

            // Optimise this code repetition
            if (el.clientWidth === window.innerWidth) {
                if (Math.round(elBottom) <= 0) {
                    // Bottom
                    if (elHeight > callculatedHeightBot) {
                        callculatedHeightBot = elHeight;
                    }
                } else if (Math.round(elTop) === 0) {
                    // TOP
                    if (elHeight > callculatedHeightTop) {
                        top = elHeight;
                        callculatedHeightTop = elHeight;
                    }
                }

                callculatedHeight = callculatedHeightTop + callculatedHeightBot;
            } else if (el.clientHeight === window.innerHeight) {
                if (Math.round(elRight) <= 0) {
                    // Right
                    if (elWidth > callculatedWidthRight) {
                        callculatedWidthRight = elWidth;
                    }
                } else if (Math.round(elLeft) === 0) {
                    // Left
                    if (elWidth > callculatedWidthLeft) {
                        left = elWidth;
                        callculatedWidthLeft = elWidth;
                    }
                }

                callculatedWidth = callculatedWidthLeft + callculatedWidthRight;
            }
        });

        const width = window.innerWidth - callculatedWidth;
        const height = window.innerHeight - callculatedHeight;

        const lastCenterPoint = this.map.getCenter();

        setTimeout(() => {
            this.map.panTo(lastCenterPoint);
        }, 200);

        this.map.setActiveArea({
            ...defaultObjectStyle.mapActiveArea,
            width: width + 'px',
            height: height + 'px',
            top: top + 'px',
            bottom: bottom + 'px',
            left: left + 'px',
            right: right + 'px',
        });
    }

    addToActiveAreaList(activeAreaConfig) {
        if (!this.activeAreaList.includes(activeAreaConfig)) {
            this.activeAreaList.push(activeAreaConfig);
            this.setActiveArea();
        }
    }

    removeFromActiveAreaList(activeAreaConfig) {
        const index = this.activeAreaList.indexOf(activeAreaConfig);

        if (index > -1) {
            this.activeAreaList.splice(index, 1);
            this.setActiveArea();
        }
    }
};

