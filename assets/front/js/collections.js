export class Collections {
    mapInstance;
    collectionId;
    elCollectionsList;
    get isCollectionsActive() {
        return this.mapInstance.map.hasLayer(this.mapInstance.mapResponse.CollectionsGeoJsonLayer);
    }

    constructor(mapInstance) {
        this.mapInstance = mapInstance;

        this.init();
        this.events();
    }

    init() {
        this.elCollectionsList = document.getElementById('collections-list');
        this.getGeoCollectionsList();
    }

    events() {
        document.getElementById('collections-toggle').checked = this.isCollectionsActive;
        
        $(document).on('change', '#collections-toggle', () => {
            this.toggleCollectionLayer();
        });

        $(document).on('click', '[data-collection-id]', (e) => {
            e.preventDefault();
            this.setActiveCollection(e.currentTarget.getAttribute('data-collection-id'));
        })
    }

    toggleCollectionLayer(toggle) {
        if (this.isCollectionsActive) {
            this.mapInstance.map.removeLayer(this.mapInstance.mapResponse.CollectionsGeoJsonLayer);
        } else {
            this.mapInstance.mapResponse.CollectionsGeoJsonLayer.addTo(this.mapInstance.map);
        }
    }

    add(layer) {
        if (typeof this.collectionId !== 'undefined') {
            $.ajax({
                type: 'POST',
                url: '/front-end/geo-collection/add',
                data: {
                    'geo-object': layer.feature.properties.id,
                    'collection': this.collectionId
                },
                success: () => {
                    const center = this.mapInstance.map.getCenter();
                    this.mapInstance.updateMap(center);
                }
            });
        }
    }

    setActiveCollection(collectionId) {
        this.collectionId = collectionId;
    }

    getGeoCollectionsList() {
        $.ajax({
            url: '/geo-collection/info',
            success: result => {
                let html = `<ul class="mt-4 pl-4">`;

                result.forEach(geoLocation => {
                    html += `<li class="mb-2">
							<a data-collection-id="${geoLocation.collectionUuid}" href="#${geoLocation.collectionUuid}" class="font-weight-bold">Маршрут</a>`;
                    if (typeof gcOpen !== 'undefined') {
                        if (gcOpen === geoLocation.collectionUuid) {
                            html += ` [<span class="text-success">активен</span>]
							<form method="post" class="float-right">
							    <input type="hidden" name="_method" value="delete">
								<button type="submit" class="btn btn-sm btn-danger" style="font-size: 11px; padding: 4px 5px 0"><i class="fa fa-trash"></i> </button>
							</form>`;

                        }
                    }

                    html += `<div>
							дължина: ${geoLocation.length} м<br />
							оценен: ${geoLocation.completion.percentage} %<br />
								<div class="progress" style="height: 3px;">
									<div class="progress-bar" role="progressbar" style="width: ${geoLocation.completion.percentage}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
								</div>`;

                    if(geoLocation.interconnectedClustersCount > 1) {
                        html += `<span class="text-danger">грешка: в маршрута има прекъсване</span>`;
                    }

                    html +=	`</div>
						</li>`;
                });

                html += `</ul>`;

                this.elCollectionsList.innerHTML = html;
            }
        });
    }
}