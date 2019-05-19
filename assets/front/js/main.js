import { Map } from './map-main';
import { Survey }  from './survey';


(function() {
    const mapInstance = new Map();
    mapInstance.init();
    const pathVoteSurvey = new Survey(mapInstance);
    mapInstance.setSurvey(pathVoteSurvey);
    
 })();

