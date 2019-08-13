import { defaultMapSize } from './map-config';

export class Collection {

    constructor(mapInstance, settings) {
        this.mapInstance = mapInstance;
        this.settings = settings;
        
        this.layer = L.geoJSON([], { 
            style: (feature) => {
                let styles = settings.styles[feature.properties._s1] ? {...settings.styles[feature.properties._s1]} : {...defaultObjectStyle};
                return styles;
            },
            onEachFeature: (feature, layer) => {
                layer.on('click', (ev) => {
                    console.log('Collections - LayerGeoJson - CLICK');
                    this.onLayerClick(layer, ev);
                    this.mapInstance.zoomToLayer(layer, ev);
                });
                layer.on('mouseover', () => {
                    if (layer.feature.properties.activePopup) {
                        return; 
                    }
                    if (settings.styles[feature.properties._s2]) {
                        this.setLayerHoverStyle(layer);
                    }
                });
                layer.on('mouseout', () => {
                    if (layer.feature.properties.activePopup || this.activeLayer === layer) {
                        return;
                    }
                    if (settings.styles[feature.properties._s1]) {
                        this.setLayerDefaultStyle(layer);
                    }
                });
            },
            pointToLayer: (feature, latlng) => {
                return L.circleMarker(latlng, settings.styles[feature.properties._s1]);
            }
        });
    }
    
    setLayerDefaultStyle(layer) {
        layer.setStyle(this.settings.styles[layer.feature.properties._s1] || defaultObjectStyle)
    }

    setLayerHoverStyle(layer) {
        layer.setStyle(this.settings.styles[layer.feature.properties._s2])
    }

    onLayerClick(layer, ev) {
        this.mapInstance.onLayerClick(layer, ev);
    // TODO: Remove if not needed
    //     layer.feature.properties.activePopup = true;
    //     this.mapInstance.setLayerActiveStyle(layer);
    //     this.mapInstance.removeAllPopups();
    //     console.log('s------');
        
    //    console.log(layer.feature.properties._behavior);
       
    //     if (layer.feature.properties._behavior === 'survey') {
    //         if (this.isCollectionsActive) {
    //             this.add(layer, ev);
    //         }
    //     }
    }
}

export class Collections {
    mapInstance;
    activeCollectionId;
    elCollectionsContainer;
    elCollectionsList;
    isCollectionShown = false;
    collectionsResponse;
    get isCollectionsActive() {
        return this.mapInstance.map.hasLayer(this.mapInstance.mapResponse.CollectionsLayerControl);
    }

    constructor(mapInstance) {
        this.mapInstance = mapInstance;
        this.init();
        this.events();
    }

    init() {
        this.elCollectionsContainer = document.getElementById('collections');
        this.elCollectionsList = this.elCollectionsContainer.querySelector('#collections-list');
        this.getGeoCollectionsList();
    }

    events() {
        // document.getElementById('collections-toggle').checked = this.isCollectionsActive;
        
        $(document).on('click', '#collections-toggle', () => {
            this.toggleCollectionLayer();
        });

        $(document).on('click', '[data-collection-id]', (e) => {
            e.preventDefault();
            $('[data-collection-id]').removeClass('active');
            e.currentTarget.classList.add('active');
            const collectionId = e.currentTarget.getAttribute('data-collection-id');
            this.setActiveCollection(collectionId);
        })

        $(document).on('click', '[data-toggle-for="collections"][data-toggle-open]', () => {
            this.open();
        });
        
        $(document).on('click', '[data-toggle-for="collections"][data-toggle-close]', () => {
            this.close();
        });

        $(document).on('click', '#collections .remove', (e) => {
            const uuid = e.currentTarget.getAttribute('data-uuid');
            this.delete(uuid);
        });

        $(document).on('click', '#collections .add', (e) => {
            this.new();
        });

        this.mapInstance.toggleHeaderEl(true);
    }

    toggleCollectionLayer(toggle) {
        if (!this.isCollectionShown) {
            // TODO: Remove this if not needed
            // this.mapInstance.map.removeLayer(this.mapInstance.mapResponse.CollectionsLayerGeoJson);
            this.open();
        } else {
            // this.mapInstance.mapResponse.CollectionsLayerGeoJson.addTo(this.mapInstance.map);
            this.close();
        }
    }

    add(layer) {
        if (typeof this.activeCollectionId !== 'undefined') {
            this.getGeoCollectionsList();

            $.ajax({
                type: 'POST',
                url: '/api/geo-collection/add',
                data: {
                    'geo-object': layer.feature.properties.id,
                    'collection': this.activeCollectionId
                },
                success: () => {
                    const center = this.mapInstance.map.getCenter();
                    this.mapInstance.updateMap(center);
                }
            });
        }
    }

    delete(layerUUID) {
        const layer = this.mapInstance.mapResponse.CollectionsLayerControl.getLayer(layerUUID);
        
        $.ajax({
            type: 'DELETE',
            url: `/api/geo-collection/${layerUUID}`,
            success: () => {
                this.getGeoCollectionsList();
                this.mapInstance.mapResponse.CollectionsLayerControl.removeLayer(layer);
            }
        });
    }

    new() {
        $.ajax({
            type: 'POST',
            url: '/api/geo-collection/new',
            success: (response) => {
                this.activeCollectionId = response.id;
                this.getGeoCollectionsList();
            }
        });
    }

    setActiveCollection(activeCollectionId) {
        this.activeCollectionId = activeCollectionId;
        const activeCollection = this.collectionsResponse.find(geoLocation => geoLocation.id === this.activeCollectionId);
        const layer = this.mapInstance.mapResponse.CollectionsLayerControl.getLayer(activeCollectionId);
      
        if (layer) {
            if (!this.mapInstance.map.getBounds().contains(layer.getBounds())) {
                this.mapInstance.map.fitBounds(layer.getBounds());
            }
    
            this.mapInstance.zoomToLayer(null, null, layer.getBounds().getCenter());
        }
    }

    unsetActiveCollection() {
        $('[data-collection-id]').removeClass('active;')
        this.activeCollectionId = null;
    }

    getGeoCollectionsList() {
        $.ajax({
            url: '/api/geo-collection/info',
            success: result => {
                this.collectionsResponse = result;
                let html = `<ul class="collections-list mt-4 pl-4">`;

                this.collectionsResponse.forEach(geoLocation => {
                    html += `<li class="collections-list-item mb-2">`;
                    html += `
                        <div class="d-flex">
                            <a class="collections-list-item-link font-weight-bold ${this.activeCollectionId === geoLocation.id ? 'active' : null}" data-collection-id="${geoLocation.id}" href="#${geoLocation.id}">
                                ${geoLocation.name ? geoLocation.name : 'Маршрут ' +  geoLocation.identify} <span class="is-active">[<span class="text-success">активен</span>]</span>
                            </a>
                            <span class="d-flex justify-content-end flex-grow-1">
                                <button data-uuid="${geoLocation.id}" class="remove btn btn-sm btn-danger">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </span>
                        </div>
                    `;

                    html += `
                        <div>
							дължина: ${geoLocation.length} м<br />
							оценен: ${geoLocation.completion.percentage} %<br />
								<div class="progress" style="height: 3px;">
									<div class="progress-bar" role="progressbar" style="width: ${geoLocation.completion.percentage}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                        </div>
                    `;

                    html +=	`</li>`;
                });

                html += `</ul>`;

                this.elCollectionsList.innerHTML = html;
            }
        });
    }

    open() {
        this.mapInstance.map.closePopup();
        this.mapInstance.toggleHeaderEl(false);

        this.isCollectionShown = true;
        this.elCollectionsContainer.classList.add('active');

        this.mapInstance.addToActiveAreaList(this.elCollectionsContainer);
    }

    close() {
        this.mapInstance.toggleHeaderEl(true);
        this.isCollectionShown = false;
        this.elCollectionsContainer.classList.remove('active');

        this.mapInstance.removeFromActiveAreaList(this.elCollectionsContainer);
    }
}