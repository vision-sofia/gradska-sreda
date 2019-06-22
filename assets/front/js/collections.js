
export class Collections {
    mapInstance;
    activeCollectionId;
    elComponent;
    elCollectionsList;
    isCollectionShown = false;
    get isCollectionsActive() {
        return this.mapInstance.map.hasLayer(this.mapInstance.mapResponse.CollectionsLayerGeoJson);
    }

    constructor(mapInstance) {
        this.mapInstance = mapInstance;
        this.init();
        this.events();
    }

    init() {
        this.elComponent = document.getElementById('collections');
        this.elCollectionsList = this.elComponent.querySelector('#collections-list');
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
            this.setActiveCollection(e.currentTarget.getAttribute('data-collection-id'));
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
        if (this.isCollectionsActive) {
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
                url: '/front-end/geo-collection/add',
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
        $.ajax({
            type: 'DELETE',
            url: `/front-end/geo-collection/${layerUUID}`,
            success: () => {
                this.getGeoCollectionsList();
                this.map
            }
        });
    }

    new() {
        $.ajax({
            type: 'POST',
            url: '/front-end/geo-collection/new',
            success: (response) => {
                this.activeCollectionId = response.id;
                this.getGeoCollectionsList();
            }
        });
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

    setActiveCollection(activeCollectionId) {
        this.activeCollectionId = activeCollectionId;

        // this.mapInstance.zoomToLayer(layer, ev);
    }

    getGeoCollectionsList() {
        $.ajax({
            url: '/front-end/geo-collection/info',
            success: result => {
                let html = `<ul class="collections-list mt-4 pl-4">`;

                result.forEach(geoLocation => {
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
        this.mapInstance.toggleHeaderEl(false);
        this.isCollectionShown = true;
        this.elComponent.classList.add('active');
    }

    close() {
        this.mapInstance.toggleHeaderEl(true);
        this.isCollectionShown = false;
        this.elComponent.classList.add('remove');
    }
}