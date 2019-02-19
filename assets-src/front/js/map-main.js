const mapBoxUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
const mapBoxAttribution = `&copy <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors`;
const mapCenter = [42.697664,23.3166103];
const defaultStyle = {
    color: "#ff9710",
    opacity: 0.5,
    width: 5
};
let layerStyles = {};

let map = new L.map('mapMain', {
    center: mapCenter,
    zoom: 17,
    updateWhenZooming: false
});

let mapStyle = L.tileLayer(mapBoxUrl, {
    attribution: mapBoxAttribution,
    maxNativeZoom: 19,
    maxZoom: 20,
    minZoom: 12,
    updateWhenZooming: false
});

mapStyle.addTo(map);
// updateMap();

let updateMapThrottle;

map.on('dragend', updateMap).on('zoomend', function() {
    clearTimeout(updateMapThrottle);
    updateMapThrottle = setTimeout(updateMap, 500);
});

function updateMap() {
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
            layerStyles = results.settings.styles;
            // console.log(layerStyles);
            drawLayers(results.objects);
        }
    });
}

function drawLayers(objects) {

    objects.forEach((el) => {
        // Get object styles if any or set default styles
        let options = layerStyles[el._s1] ? {...layerStyles[el._s1]} : {...defaultStyle};
        options.label = el.name || '';
        options.id = el.id;
        options.behavior = el.attributes._behavior;
        options._s1 = el._s1;
        options._s2 = el._s2;

        switch(el.geometry.type) {
            case "MultiLineString":
                L.polyline(el.geometry.coordinates[0], options)
                    .on('click', clickObject)
                    .on('mouseover', mouseEnter)
                    .on('mouseout', mouseLeave)
                    .addTo(map);
            break;
            case "Polygon":
                L.polygon(el.geometry.coordinates[0], options)
                    .on('click', clickObject)
                    .addTo(map);
            break;
        }
    });
}

function clickObject(event) {
    console.log(event.target.options.behavior);
}

function mouseEnter(event) {
    if (layerStyles[event.target.options._s2]) {
        event.sourceTarget.setStyle(layerStyles[event.target.options._s2]);
    }
}

function mouseLeave(event) {
    if (layerStyles[event.target.options._s2]) {
        event.sourceTarget.setStyle(layerStyles[event.target.options._s1] || defaultStyle);
    }
}
