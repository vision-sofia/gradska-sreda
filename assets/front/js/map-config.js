export const mapBoxUrl = 'https://gradska-sreda.dreamradio.org/map/styles/vs/{z}/{x}/{y}.png';
export const mapBoxAttribution = `&copy <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors`;

export const defaultMapSize = {
    areaHeight: 100, // Precents  // TODO take form MapInstance 
    areaWidth: 100, // Precents  // TODO take form MapInstance 
};

export const defaultElConfig = {
    headerId: '#app-header',
    elHeader: null,
};

export const defaultObjectStyle = {
    color: "#ff9710",
    opacity: 0.5,
    width: 5,
    mapActiveArea: {
        position: 'absolute',
        width: '100%',
        height: '100%',
        top: '0',
        left: '0',
        callculatedWidth: 0,    
        callculatedHeight: 0,
        callculatedTop: 0,
        callculatedBottom: 0,
        callculatedLeft: 0,
        callculatedRight: 0,
    }
};

export const apiEndpoints = {
    geo: '/geo/'
};


