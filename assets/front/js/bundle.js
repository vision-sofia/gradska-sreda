/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nObject.defineProperty(exports, \"__esModule\", {\n  value: true\n});\nvar mapBoxUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';\nvar mapBoxAttribution = '&copy <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors';\n\nexports.mapBoxUrl = mapBoxUrl;\nexports.mapBoxAttribution = mapBoxAttribution;//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMC5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21hcC1jb25maWcuanM/MTRlMCJdLCJzb3VyY2VzQ29udGVudCI6WyJjb25zdCBtYXBCb3hVcmwgPSAnaHR0cHM6Ly97c30udGlsZS5vcGVuc3RyZWV0bWFwLm9yZy97en0ve3h9L3t5fS5wbmcnO1xuY29uc3QgbWFwQm94QXR0cmlidXRpb24gPSBgJmNvcHkgPGEgaHJlZj1cImh0dHBzOi8vd3d3Lm9wZW5zdHJlZXRtYXAub3JnL2NvcHlyaWdodFwiPk9wZW5TdHJlZXRNYXA8L2E+IGNvbnRyaWJ1dG9yc2A7XG5cbmV4cG9ydCB7bWFwQm94VXJsLCBtYXBCb3hBdHRyaWJ1dGlvbn1cblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyBhc3NldHMtc3JjL2Zyb250L2pzL21hcC1jb25maWcuanMiXSwibWFwcGluZ3MiOiI7Ozs7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFBQSIsInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///0\n");

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\n__webpack_require__(2);\n\n__webpack_require__(3);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMS5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21haW4uanM/ODBmNyJdLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgJ21hcC1tYWluJ1xuaW1wb3J0ICdtYXAtbWluaSdcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyBhc3NldHMtc3JjL2Zyb250L2pzL21haW4uanMiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTtBQUFBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///1\n");

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nvar _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };\n\nvar _mapConfig = __webpack_require__(0);\n\n(function () {\n    if (!document.getElementById('mapMain')) {\n        return;\n    }\n    var mapCenter = [42.697664, 23.3166103];\n    var defaultObjectStyle = {\n        color: \"#ff9710\",\n        opacity: 0.5,\n        width: 5\n    };\n    var objectStyles = {};\n\n    var map = new L.map('mapMain', {\n        updateWhenZooming: false\n    });\n\n    var mapStyle = L.tileLayer(_mapConfig.mapBoxUrl, {\n        attribution: _mapConfig.mapBoxAttribution,\n        maxNativeZoom: 19,\n        maxZoom: 20,\n        minZoom: 11,\n        // detectRetina: true,\n        updateWhenZooming: false\n    });\n    mapStyle.addTo(map);\n\n    var updateMapThrottle = void 0;\n    map.on('load dragend zoomend', function () {\n        clearTimeout(updateMapThrottle);\n        updateMapThrottle = setTimeout(updateMap, 200);\n    });\n\n    map.setView(mapCenter, 17);\n\n    var geoJsonLayer = L.geoJSON([], {\n        style: function style(feature) {\n            return objectStyles[feature.properties._s1] ? _extends({}, objectStyles[feature.properties._s1]) : _extends({}, defaultObjectStyle);\n        },\n        onEachFeature: function onEachFeature(feature, layer) {\n            if (feature.properties._behavior === 'info') {\n                var popupContent = '<p class=\"text-center\">' + feature.properties.type + '<br />' + feature.properties.name + '</p>';\n                layer.bindPopup(popupContent, {\n                    closeButton: true,\n                    offset: L.point(0, -20)\n                });\n            }\n            layer.on('click', function () {\n                takeAction(layer);\n            });\n            if (feature.geometry.type !== 'Point') {\n                layer.on('mouseover', function () {\n                    mouseEnter(this);\n                });\n                layer.on('mouseout', function () {\n                    mouseLeave(this);\n                });\n            }\n        },\n        pointToLayer: function pointToLayer(feature, latlng) {\n            return L.circleMarker(latlng, {\n                radius: 8,\n                fillColor: \"#ff7800\",\n                color: \"#000\",\n                weight: 1,\n                opacity: 1,\n                fillOpacity: 0.8\n            });\n        }\n    }).addTo(map);\n\n    function updateMap() {\n        var zoom = map.getZoom();\n        var coords = map.getBounds();\n\n        $.ajax({\n            data: {\n                in: coords._southWest.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._northEast.lat,\n                zoom: zoom\n            },\n            url: \"/front-end/map?\",\n            success: function success(results) {\n                objectStyles = results.settings.styles;\n                geoJsonLayer.clearLayers();\n                geoJsonLayer.addData(results.objects);\n            }\n        });\n    }\n\n    function takeAction(layer) {\n        console.log(layer.feature.properties._behavior);\n        switch (layer.feature.properties._behavior) {\n            case 'info':\n                layer.openPopup();\n                break;\n            case 'navigation':\n                map.fitBounds(layer.getBounds(), { padding: [0, 0] });\n                break;\n            case 'survey':\n                window.location.href = '/geo/' + layer.feature.properties.id;\n                break;\n        }\n    }\n\n    function mouseEnter(layer) {\n        if (objectStyles[layer.feature.properties._s2]) {\n            layer.setStyle(objectStyles[layer.feature.properties._s2]);\n        }\n    }\n\n    function mouseLeave(layer) {\n        if (objectStyles[layer.feature.properties._s2]) {\n            layer.setStyle(objectStyles[layer.feature.properties._s1] || defaultObjectStyle);\n        }\n    }\n})();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMi5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21hcC1tYWluLmpzPzVhZWIiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHsgbWFwQm94QXR0cmlidXRpb24sIG1hcEJveFVybCB9IGZyb20gJy4vbWFwLWNvbmZpZyc7XG5cbigoKSA9PiB7XG4gICAgaWYgKCFkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbWFwTWFpbicpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG4gICAgY29uc3QgbWFwQ2VudGVyID0gWzQyLjY5NzY2NCwyMy4zMTY2MTAzXTtcbiAgICBjb25zdCBkZWZhdWx0T2JqZWN0U3R5bGUgPSB7XG4gICAgICAgIGNvbG9yOiBcIiNmZjk3MTBcIixcbiAgICAgICAgb3BhY2l0eTogMC41LFxuICAgICAgICB3aWR0aDogNVxuICAgIH07XG4gICAgbGV0IG9iamVjdFN0eWxlcyA9IHt9O1xuXG4gICAgbGV0IG1hcCA9IG5ldyBMLm1hcCgnbWFwTWFpbicsIHtcbiAgICAgICAgdXBkYXRlV2hlblpvb21pbmc6IGZhbHNlXG4gICAgfSk7XG5cbiAgICBsZXQgbWFwU3R5bGUgPSBMLnRpbGVMYXllcihtYXBCb3hVcmwsIHtcbiAgICAgICAgYXR0cmlidXRpb246IG1hcEJveEF0dHJpYnV0aW9uLFxuICAgICAgICBtYXhOYXRpdmVab29tOiAxOSxcbiAgICAgICAgbWF4Wm9vbTogMjAsXG4gICAgICAgIG1pblpvb206IDExLFxuICAgICAgICAvLyBkZXRlY3RSZXRpbmE6IHRydWUsXG4gICAgICAgIHVwZGF0ZVdoZW5ab29taW5nOiBmYWxzZVxuICAgIH0pO1xuICAgIG1hcFN0eWxlLmFkZFRvKG1hcCk7XG5cbiAgICBsZXQgdXBkYXRlTWFwVGhyb3R0bGU7XG4gICAgbWFwLm9uKCdsb2FkIGRyYWdlbmQgem9vbWVuZCcsIGZ1bmN0aW9uKCkge1xuICAgICAgICBjbGVhclRpbWVvdXQodXBkYXRlTWFwVGhyb3R0bGUpO1xuICAgICAgICB1cGRhdGVNYXBUaHJvdHRsZSA9IHNldFRpbWVvdXQodXBkYXRlTWFwLCAyMDApO1xuICAgIH0pO1xuXG4gICAgbWFwLnNldFZpZXcobWFwQ2VudGVyLCAxNyk7XG5cbiAgICBsZXQgZ2VvSnNvbkxheWVyID0gTC5nZW9KU09OKFtdLCB7XG4gICAgICAgIHN0eWxlOiBmdW5jdGlvbihmZWF0dXJlKSB7XG4gICAgICAgICAgICByZXR1cm4gb2JqZWN0U3R5bGVzW2ZlYXR1cmUucHJvcGVydGllcy5fczFdID8gey4uLm9iamVjdFN0eWxlc1tmZWF0dXJlLnByb3BlcnRpZXMuX3MxXX0gOiB7Li4uZGVmYXVsdE9iamVjdFN0eWxlfVxuICAgICAgICB9LFxuICAgICAgICBvbkVhY2hGZWF0dXJlOiBmdW5jdGlvbihmZWF0dXJlLCBsYXllcikge1xuICAgICAgICAgICAgaWYgKGZlYXR1cmUucHJvcGVydGllcy5fYmVoYXZpb3IgPT09ICdpbmZvJykge1xuICAgICAgICAgICAgICAgIGxldCBwb3B1cENvbnRlbnQgPSBgPHAgY2xhc3M9XCJ0ZXh0LWNlbnRlclwiPiR7ZmVhdHVyZS5wcm9wZXJ0aWVzLnR5cGV9PGJyIC8+JHtmZWF0dXJlLnByb3BlcnRpZXMubmFtZX08L3A+YDtcbiAgICAgICAgICAgICAgICBsYXllci5iaW5kUG9wdXAocG9wdXBDb250ZW50LCB7XG4gICAgICAgICAgICAgICAgICAgIGNsb3NlQnV0dG9uOiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICBvZmZzZXQ6IEwucG9pbnQoMCwgLTIwKVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgbGF5ZXIub24oJ2NsaWNrJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIHRha2VBY3Rpb24obGF5ZXIpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBpZiAoZmVhdHVyZS5nZW9tZXRyeS50eXBlICE9PSAnUG9pbnQnKSB7XG4gICAgICAgICAgICAgICAgbGF5ZXIub24oJ21vdXNlb3ZlcicsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgbW91c2VFbnRlcih0aGlzKVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIGxheWVyLm9uKCdtb3VzZW91dCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgbW91c2VMZWF2ZSh0aGlzKVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICB9LFxuICAgICAgICBwb2ludFRvTGF5ZXI6IGZ1bmN0aW9uIChmZWF0dXJlLCBsYXRsbmcpIHtcbiAgICAgICAgICAgIHJldHVybiBMLmNpcmNsZU1hcmtlcihsYXRsbmcsIHtcbiAgICAgICAgICAgICAgICByYWRpdXMgOiA4LFxuICAgICAgICAgICAgICAgIGZpbGxDb2xvciA6IFwiI2ZmNzgwMFwiLFxuICAgICAgICAgICAgICAgIGNvbG9yIDogXCIjMDAwXCIsXG4gICAgICAgICAgICAgICAgd2VpZ2h0IDogMSxcbiAgICAgICAgICAgICAgICBvcGFjaXR5IDogMSxcbiAgICAgICAgICAgICAgICBmaWxsT3BhY2l0eSA6IDAuOFxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9KS5hZGRUbyhtYXApO1xuXG4gICAgZnVuY3Rpb24gdXBkYXRlTWFwKCkge1xuICAgICAgICBsZXQgem9vbSA9IG1hcC5nZXRab29tKCk7XG4gICAgICAgIGxldCBjb29yZHMgPSBtYXAuZ2V0Qm91bmRzKCk7XG5cbiAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgICBpbjogY29vcmRzLl9zb3V0aFdlc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubGF0ICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubGF0ICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubGF0ICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubGF0ICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubGF0LFxuICAgICAgICAgICAgICAgIHpvb206IHpvb21cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB1cmw6IFwiL2Zyb250LWVuZC9tYXA/XCIsXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAocmVzdWx0cykge1xuICAgICAgICAgICAgICAgIG9iamVjdFN0eWxlcyA9IHJlc3VsdHMuc2V0dGluZ3Muc3R5bGVzO1xuICAgICAgICAgICAgICAgIGdlb0pzb25MYXllci5jbGVhckxheWVycygpO1xuICAgICAgICAgICAgICAgIGdlb0pzb25MYXllci5hZGREYXRhKHJlc3VsdHMub2JqZWN0cyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIHRha2VBY3Rpb24obGF5ZXIpIHtcbiAgICAgICAgY29uc29sZS5sb2cobGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLl9iZWhhdmlvcik7XG4gICAgICAgIHN3aXRjaCAobGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLl9iZWhhdmlvcikge1xuICAgICAgICAgICAgY2FzZSAnaW5mbyc6XG4gICAgICAgICAgICAgICAgbGF5ZXIub3BlblBvcHVwKCk7XG4gICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICBjYXNlICduYXZpZ2F0aW9uJzpcbiAgICAgICAgICAgICAgICBtYXAuZml0Qm91bmRzKGxheWVyLmdldEJvdW5kcygpLCB7IHBhZGRpbmc6IFswLCAwXSB9KTtcbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgJ3N1cnZleSc6XG4gICAgICAgICAgICAgICAgd2luZG93LmxvY2F0aW9uLmhyZWYgPSAnL2dlby8nICsgbGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLmlkO1xuICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gbW91c2VFbnRlcihsYXllcikge1xuICAgICAgICBpZiAob2JqZWN0U3R5bGVzW2xheWVyLmZlYXR1cmUucHJvcGVydGllcy5fczJdKSB7XG4gICAgICAgICAgICBsYXllci5zZXRTdHlsZShvYmplY3RTdHlsZXNbbGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLl9zMl0pO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gbW91c2VMZWF2ZShsYXllcikge1xuICAgICAgICBpZiAob2JqZWN0U3R5bGVzW2xheWVyLmZlYXR1cmUucHJvcGVydGllcy5fczJdKSB7XG4gICAgICAgICAgICBsYXllci5zZXRTdHlsZShvYmplY3RTdHlsZXNbbGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLl9zMV0gfHwgZGVmYXVsdE9iamVjdFN0eWxlKTtcbiAgICAgICAgfVxuICAgIH1cbn0pKCk7XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gYXNzZXRzLXNyYy9mcm9udC9qcy9tYXAtbWFpbi5qcyJdLCJtYXBwaW5ncyI6Ijs7OztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFIQTtBQUtBO0FBQ0E7QUFDQTtBQUNBO0FBREE7QUFDQTtBQUdBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBTkE7QUFRQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRkE7QUFJQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFOQTtBQVFBO0FBakNBO0FBQ0E7QUFtQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFVQTtBQVhBO0FBYUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBbkJBO0FBcUJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFUQTtBQVdBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///2\n");

/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nvar _mapConfig = __webpack_require__(0);\n\n(function () {\n    if (!document.getElementById('mapMini')) {\n        return;\n    }\n    var mapCenter = [42.697664, 23.3166103];\n    //TODO draw the object and fit to its bounds\n\n    var map = new L.map('mapMini', {\n        center: mapCenter,\n        zoom: 15,\n        updateWhenZooming: false\n    });\n\n    var mapStyle = L.tileLayer(_mapConfig.mapBoxUrl, {\n        attribution: _mapConfig.mapBoxAttribution,\n        maxNativeZoom: 19,\n        maxZoom: 20,\n        minZoom: 11,\n        updateWhenZooming: false\n    });\n    mapStyle.addTo(map);\n})();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMy5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21hcC1taW5pLmpzPzhhY2EiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHsgbWFwQm94QXR0cmlidXRpb24sIG1hcEJveFVybCB9IGZyb20gJy4vbWFwLWNvbmZpZyc7XG5cbigoKSA9PiB7XG4gICAgaWYgKCFkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbWFwTWluaScpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG4gICAgY29uc3QgbWFwQ2VudGVyID0gWzQyLjY5NzY2NCwyMy4zMTY2MTAzXTtcbiAgICAvL1RPRE8gZHJhdyB0aGUgb2JqZWN0IGFuZCBmaXQgdG8gaXRzIGJvdW5kc1xuXG4gICAgbGV0IG1hcCA9IG5ldyBMLm1hcCgnbWFwTWluaScsIHtcbiAgICAgICAgY2VudGVyOiBtYXBDZW50ZXIsXG4gICAgICAgIHpvb206IDE1LFxuICAgICAgICB1cGRhdGVXaGVuWm9vbWluZzogZmFsc2VcbiAgICB9KTtcblxuICAgIGxldCBtYXBTdHlsZSA9IEwudGlsZUxheWVyKG1hcEJveFVybCwge1xuICAgICAgICBhdHRyaWJ1dGlvbjogbWFwQm94QXR0cmlidXRpb24sXG4gICAgICAgIG1heE5hdGl2ZVpvb206IDE5LFxuICAgICAgICBtYXhab29tOiAyMCxcbiAgICAgICAgbWluWm9vbTogMTEsXG4gICAgICAgIHVwZGF0ZVdoZW5ab29taW5nOiBmYWxzZVxuICAgIH0pO1xuICAgIG1hcFN0eWxlLmFkZFRvKG1hcCk7XG59KSgpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIGFzc2V0cy1zcmMvZnJvbnQvanMvbWFwLW1pbmkuanMiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFIQTtBQUNBO0FBS0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBTEE7QUFPQTtBQUNBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///3\n");

/***/ })
/******/ ]);