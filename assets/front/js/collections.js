export class Collections {
    mapInstance;

    constructor(mapInstance) {
        this.mapInstance = mapInstance;

        this.init();
    }

    init() {
        this.getGeoCollectionsList();
    }

    getGeoCollectionsList() {
        $.ajax({
            url: '/geo-collection/info',
            success: function (result) {
                console.log(result);
                
                // let html = `<ul class="mt-4 pl-4">`;

                // Object.keys(result).forEach(function (item) {
                //     html += `<li class="mb-2">
				// 			<a href="/geo-collection/${result[item].collectionUuid}" class="font-weight-bold">Маршрут</a>`;
                //     if (typeof gcOpen !== 'undefined') {
                //         if (gcOpen === result[item].collectionUuid) {
                //             html += ` [<span class="text-success">активен</span>]
				// 			<form method="post" class="float-right">
				// 			    <input type="hidden" name="_method" value="delete">
				// 				<button type="submit" class="btn btn-sm btn-danger" style="font-size: 11px; padding: 4px 5px 0"><i class="fa fa-trash"></i> </button>
				// 			</form>`;

                //         }
                //     }

                //     html += `<div>
				// 			дължина: ${result[item].length} м<br />
				// 			оценен: ${result[item].completion.percentage} %<br />
				// 				<div class="progress" style="height: 3px;">
				// 					<div class="progress-bar" role="progressbar" style="width: ${result[item].completion.percentage}%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
				// 				</div>`;

                //     if(result[item].interconnectedClustersCount > 1) {
                //         html += `<span class="text-danger">грешка: в маршрута има прекъсване</span>`;
                //     }

                //     html +=	`</div>
				// 		</li>`;
                // });

                // html += `</ul>`;

                // $('#div3').html(html);
            }
        });
    }
}