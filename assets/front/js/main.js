import './toggle';
import { Map } from './map-main';
import { Survey }  from './survey';
import { Collections } from './collections';


(function() {
    $('[data-toggle="tooltip"]').tooltip(); 

    const mapInstance = new Map();
    mapInstance.init();

    const pathVoteSurvey = new Survey(mapInstance);
    mapInstance.setSurvey(pathVoteSurvey);

    const collctions = new Collections(mapInstance);

    window.addEventListener('hashchange', (e) => {
        switch(window.location.hash) {
            case '#collection':
                mapInstance.setModeCollection();
            break;
            default: 
                mapInstance.setModeView();
        }
    });

    
 })();

