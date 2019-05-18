import { Map } from './map-main';
import { Survey }  from './survey';


(function() {
    const mapInstance = new Map();
    console.log(mapInstance);
    mapInstance.init();
    const pathVoteSurvey = new Survey(mapInstance);
    mapInstance.setSurvey(pathVoteSurvey);
    
 })();




