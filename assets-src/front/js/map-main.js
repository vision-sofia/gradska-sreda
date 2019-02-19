const mapBoxUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
const mapBoxAttribution = `&copy <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors`;
const mapCenter = [42.697664,23.3166103];
const defaultObjectStyle = {
    color: "#ff9710",
    opacity: 0.5,
    width: 5
};
let objectStyles = {};

let map = new L.map('mapMain', {
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

let allObjectsLayer = L.layerGroup([]);
allObjectsLayer.addTo(map);

let updateMapThrottle;
map.on('load dragend zoomend', function() {
    clearTimeout(updateMapThrottle);
    updateMapThrottle = setTimeout(updateMap, 200);
});

map.setView(mapCenter, 15);

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
            objectStyles = results.settings.styles;
            console.log(results.settings.styles);
            allObjectsLayer.clearLayers();
            drawLayers(results.objects);
        }
    });
}

function drawLayers(objects) {
    objects.forEach(el => {
        let options = objectStyles[el._s1] ? {...objectStyles[el._s1]} : {...defaultObjectStyle};
        options.label = el.name || '';
        options.id = el.id;
        options.behavior = el.attributes._behavior;
        options._s1 = el._s1;
        options._s2 = el._s2;

        switch(el.geometry.type) {
            case "MultiLineString":
                allObjectsLayer.addLayer(new L.polyline(el.geometry.coordinates[0], options)
                    .on('click', clickObject)
                    .on('mouseover', mouseEnter)
                    .on('mouseout', mouseLeave));
            break;
            case "Polygon":
                allObjectsLayer.addLayer(new L.polygon(el.geometry.coordinates[0], options)
                    .on('click', clickObject)
                    .on('mouseover', mouseEnter)
                    .on('mouseout', mouseLeave));
            break;
        }
    });
}

function clickObject(event) {
    console.log(event.target.options.behavior);
}

function mouseEnter(event) {
    objectStyles[event.target.options._s2].fill = '#000000';
    if (objectStyles[event.target.options._s2]) {
        event.sourceTarget.setStyle(objectStyles[event.target.options._s2]);
    }
}

function mouseLeave(event) {
    if (objectStyles[event.target.options._s2]) {
        event.sourceTarget.setStyle(objectStyles[event.target.options._s1] || defaultObjectStyle);
    }
}
