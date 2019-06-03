import { Map } from './map-main';
import { Survey }  from './survey';


(function() {
    $('[data-toggle="tooltip"]').tooltip(); 

    const mapInstance = new Map();
    mapInstance.init();
    const pathVoteSurvey = new Survey(mapInstance);
    mapInstance.setSurvey(pathVoteSurvey);
    
 })();

