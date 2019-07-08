import { mapBoxAttribution, mapBoxUrl, apiEndpoints, defaultObjectStyle, defaultElConfig } from './map-config';
import { debounce } from './helpers';
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
        CollectionsLayerGeoJson: {},
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


        this.mapResponse.CollectionsLayerGeoJson = L.geoJSON([], { 
            style: (feature) => {
                let styles = this.mapResponse.settings.styles[feature.properties._s1] ? {...this.mapResponse.settings.styles[feature.properties._s1]} : {...defaultObjectStyle};
                return styles;
            },
            onEachFeature: (feature, layer) => {
                layer.on('click', (ev) => {
                    switch (feature.properties._behavior) {
                        case 'navigation':
                            this.zoomToLayer(layer, ev);
                            break;
                        default:
                            this.collections.onLayerClick(layer, ev);
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
        
        // window.addEventListener('resize', debounce(() => {
        // }, 200, false), false);
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
                this.mapResponse.CollectionsLayerGeoJson.clearLayers();
                this.mapResponse.CollectionsLayerGeoJson.addData(results.geoCollections);
                
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
                <button data-toggle-open class="btn btn-success mr-3 py-0 px-2" data-toggle-for="path-vote-suevey"  data-url="${ apiEndpoints.geo + layer.feature.properties.id }">ДА</button>
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

    setActiveArea(action) {

        let w = '100%',
        h = '100%',
        t = defaultObjectStyle.mapActiveArea.callculatedTop,
        b = defaultObjectStyle.mapActiveArea.callculatedBottom,
        l = defaultObjectStyle.mapActiveArea.callculatedLeft,
        r = defaultObjectStyle.mapActiveArea.callculatedRight;

        let callculatedWidth = 0,
        callculatedHeight = 0,
        callculatedHeightTop = 0,
        callculatedHeightBot = 0;


        let height2 = 0;
        let height2Top = 0;
        let width2 = 0;
        let width2Right = 0;

        console.log(this.activeAreaList);
        

        this.activeAreaList.forEach(actionConfig => {
            const surveyWidth = parseFloat(getComputedStyle(actionConfig).getPropertyValue('width'));
            const surveyHeight = parseFloat(getComputedStyle(actionConfig).getPropertyValue('height'));
            const surveyTop = parseFloat(getComputedStyle(actionConfig).getPropertyValue('top'));
            const surveyBottom = parseFloat(getComputedStyle(actionConfig).getPropertyValue('bottom'));
            const surveyLeft = parseFloat(getComputedStyle(actionConfig).getPropertyValue('left'));
            const surveyRight = parseFloat(getComputedStyle(actionConfig).getPropertyValue('right'));
       

            // const surveyLeft = parseFloat(getComputedStyle(actionConfig).getPropertyValue('left'));
            // const surveyRight = parseFloat(getComputedStyle(actionConfig).getPropertyValue('right'));
     
            // if (surveyHeight === defaultObjectStyle.mapActiveArea.callculatedHeight) {
            //     return;
            // }
           
            
            // if ( surveyHeight < window.innerHeight - defaultObjectStyle.mapActiveArea.callculatedHeight) {
            //     h = `calc(100% - ${surveyHeight}px)`;
            //     defaultObjectStyle.mapActiveArea.callculatedHeight = surveyHeight;
            // } else if (defaultObjectStyle.mapActiveArea.callculatedHeight > 0) {
            //     h = `calc(100% - ${defaultObjectStyle.mapActiveArea.callculatedHeight - surveyHeight}px)`;
            //     defaultObjectStyle.mapActiveArea.callculatedHeight = surveyHeight;
            // }
            // console.log(surveyWidth);
            // console.log('bottom', surveyBottom);
            // console.log('top', surveyTop);
            // console.log('top', window.innerHeight + surveyBottom);

            // console.log('bottom', surveyTop + actionConfig.clientHeight);
            // console.log('window.innerHeight', window.innerHeight);

            console.log(surveyLeft);
            console.log(surveyRight);
            console.log(surveyTop);
            console.log(surveyBottom);
            console.log(surveyTop + surveyHeight);
            console.log('A tova?', surveyHeight);
            
            console.log(window.innerHeight);
            
            // console.log(surveyBottom);
            
            if (actionConfig.clientWidth === window.innerWidth) {
                // defaultObjectStyle.mapActiveArea.callculatedHeight += surveyHeight;
debugger;

                // height2 += surveyHeight;
                // console.log(height2);

                // if (window.innerHeight === surveyTop) {
                //     t = 0;
                // } else if (surveyTop === 0) {
                //     t = surveyHeight;
                // }

                // console.log('----------HEIGHT--------------');
                // if (action === 'add') {
                //     if (surveyHeight > callculatedHeight) {
                //         height2 = surveyHeight;
                //     } else {
                //         // height2 = callculatedHeight;
                //     }

                //     callculatedHeight = surveyHeight;
                // } else {
                //     if (surveyHeight > callculatedHeight) {
                //         height2 = surveyHeight;
                //     } else {
                //         // height2 = callculatedHeight;
                //     }
                    
                //     callculatedHeight = surveyHeight;
                // }

                if (Math.round(surveyBottom) <= 0) {
                    // Bottom
                    if (surveyHeight > callculatedHeightBot) {
                        height2 = surveyHeight;
                        callculatedHeightBot = height2;
                    }
                } else if (Math.round(surveyTop) === 0) { 
                    // TOP
                    if (surveyHeight > callculatedHeightTop) {
                        height2 = surveyHeight;
                        t = height2;
                        callculatedHeightTop = height2;
                    }
                } else {
                    t = 0;
                }

                callculatedHeight = callculatedHeightTop + callculatedHeightBot;
            } else if (actionConfig.clientHeight === window.innerHeight) {
                console.log('----------WIDTH--------------');
                if (surveyWidth > defaultObjectStyle.mapActiveArea.callculatedWidth || width2 === 0) {
                    width2 = surveyWidth;
                    defaultObjectStyle.mapActiveArea.callculatedWidth += surveyWidth;
                } else {
                    defaultObjectStyle.mapActiveArea.callculatedWidth -= surveyWidth;
                }

                if (window.innerWidth === surveyLeft) {
                    l = 0;
                } else if (surveyLeft === 0) { 
                    l = width2;
                }
            }
        });

        width2 = window.innerWidth - width2;
        height2 = window.innerHeight - callculatedHeight;

        console.log('sdlaskaokaokaoakoka', height2);
        
        // width2 = width2 === 0 ? window.innerWidth : width2;

        this.map.setActiveArea({
            ...defaultObjectStyle.mapActiveArea,
            width: width2 + 'px',
            height: height2 + 'px',
            top: t + 'px',
            bottom: b,
            left: l + 'px',
            right: r,
        });

    }

    addToActiveAreaList(activeAreaConfig) {
        if (!this.activeAreaList.includes(activeAreaConfig)) {
            this.activeAreaList.push(activeAreaConfig);
            this.setActiveArea('add');
        }
    }

    removeFromActiveAreaList(activeAreaConfig) {
        const index = this.activeAreaList.indexOf(activeAreaConfig);

        if (index > -1) {
            this.activeAreaList.splice(index, 1);
            this.setActiveArea('remove');
        }
    }
};

