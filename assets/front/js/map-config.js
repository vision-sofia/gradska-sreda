/*const mapBoxUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';*/
export const mapBoxUrl = 'https://gradska-sreda.dreamradio.org/map/styles/vs/{z}/{x}/{y}.png';
export const mapBoxAttribution = `&copy <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors`;

export const defaultObjectStyle = {
    color: "#ff9710",
    opacity: 0.5,
    width: 5,
    mapActiveArea: {
        position: 'absolute',
        top: '0',
        left: '0',
        width: '100%',
        height: '100%',
    }
};

export const apiEndpoints = {
    geo: '/geo/'
};


