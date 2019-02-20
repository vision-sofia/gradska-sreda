import { mapBoxAttribution, mapBoxUrl } from './map-config';

(() => {
    if (!document.getElementById('mapMini')) {
        return;
    }
    const mapCenter = [42.697664,23.3166103];
    //TODO draw the object and fit to its bounds

    let map = new L.map('mapMini', {
        center: mapCenter,
        zoom: 15,
        updateWhenZooming: false
    });

    let mapStyle = L.tileLayer(mapBoxUrl, {
        attribution: mapBoxAttribution,
        maxNativeZoom: 19,
        maxZoom: 20,
        minZoom: 11,
        updateWhenZooming: false
    });
    mapStyle.addTo(map);
})();
