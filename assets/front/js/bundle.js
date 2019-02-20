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
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\n__webpack_require__(1);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMC5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21haW4uanM/ODBmNyJdLCJzb3VyY2VzQ29udGVudCI6WyJpbXBvcnQgJ21hcC1tYWluJ1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIGFzc2V0cy1zcmMvZnJvbnQvanMvbWFpbi5qcyJdLCJtYXBwaW5ncyI6Ijs7QUFBQSIsInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///0\n");

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nvar _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };\n\nvar mapBoxUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';\nvar mapBoxAttribution = '&copy <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors';\nvar mapCenter = [42.697664, 23.3166103];\nvar defaultObjectStyle = {\n    color: \"#ff9710\",\n    opacity: 0.5,\n    width: 5\n};\nvar objectStyles = {};\n\nvar map = new L.map('mapMain', {\n    updateWhenZooming: false\n});\n\nvar mapStyle = L.tileLayer(mapBoxUrl, {\n    attribution: mapBoxAttribution,\n    maxNativeZoom: 19,\n    maxZoom: 20,\n    minZoom: 11,\n    // detectRetina: true,\n    updateWhenZooming: false\n});\nmapStyle.addTo(map);\n\nvar updateMapThrottle = void 0;\nmap.on('load dragend zoomend', function () {\n    clearTimeout(updateMapThrottle);\n    updateMapThrottle = setTimeout(updateMap, 200);\n});\n\nmap.setView(mapCenter, 17);\n\nvar geoJsonLayer = L.geoJSON([], {\n    style: function style(feature) {\n        return objectStyles[feature.properties._s1] ? _extends({}, objectStyles[feature.properties._s1]) : _extends({}, defaultObjectStyle);\n    },\n    onEachFeature: function onEachFeature(feature, layer) {\n        if (feature.properties._behavior === 'info') {\n            var popupContent = '<p class=\"text-center\">' + feature.properties.type + '<br />' + feature.properties.name + '</p>';\n            layer.bindPopup(popupContent, {\n                closeButton: true,\n                offset: L.point(0, -20)\n            });\n        }\n        layer.on('click', function () {\n            takeAction(layer);\n        });\n        if (feature.geometry.type !== 'Point') {\n            layer.on('mouseover', function () {\n                mouseEnter(this);\n            });\n            layer.on('mouseout', function () {\n                mouseLeave(this);\n            });\n        }\n    },\n    pointToLayer: function pointToLayer(feature, latlng) {\n        return L.circleMarker(latlng, {\n            radius: 8,\n            fillColor: \"#ff7800\",\n            color: \"#000\",\n            weight: 1,\n            opacity: 1,\n            fillOpacity: 0.8\n        });\n    }\n}).addTo(map);\n\nfunction updateMap() {\n    var zoom = map.getZoom();\n    var coords = map.getBounds();\n\n    $.ajax({\n        data: {\n            in: coords._southWest.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._northEast.lat,\n            zoom: zoom\n        },\n        url: \"/front-end/map?\",\n        success: function success(results) {\n            objectStyles = results.settings.styles;\n            geoJsonLayer.clearLayers();\n            geoJsonLayer.addData(results.objects);\n        }\n    });\n}\n\nfunction takeAction(layer) {\n    switch (layer.feature.properties._behavior) {\n        case 'info':\n            layer.openPopup();break;\n        case 'navigation':\n            map.fitBounds(layer.getBounds(), { padding: [0, 0] });break;\n        case 'survey':\n            console.log('go to inner page');break;\n    }\n}\n\nfunction mouseEnter(layer) {\n    if (objectStyles[layer.feature.properties._s2]) {\n        layer.setStyle(objectStyles[layer.feature.properties._s2]);\n    }\n}\n\nfunction mouseLeave(layer) {\n    if (objectStyles[layer.feature.properties._s2]) {\n        layer.setStyle(objectStyles[layer.feature.properties._s1] || defaultObjectStyle);\n    }\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMS5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21hcC1tYWluLmpzPzVhZWIiXSwic291cmNlc0NvbnRlbnQiOlsiY29uc3QgbWFwQm94VXJsID0gJ2h0dHBzOi8ve3N9LnRpbGUub3BlbnN0cmVldG1hcC5vcmcve3p9L3t4fS97eX0ucG5nJztcbmNvbnN0IG1hcEJveEF0dHJpYnV0aW9uID0gYCZjb3B5IDxhIGhyZWY9XCJodHRwczovL3d3dy5vcGVuc3RyZWV0bWFwLm9yZy9jb3B5cmlnaHRcIj5PcGVuU3RyZWV0TWFwPC9hPiBjb250cmlidXRvcnNgO1xuY29uc3QgbWFwQ2VudGVyID0gWzQyLjY5NzY2NCwyMy4zMTY2MTAzXTtcbmNvbnN0IGRlZmF1bHRPYmplY3RTdHlsZSA9IHtcbiAgICBjb2xvcjogXCIjZmY5NzEwXCIsXG4gICAgb3BhY2l0eTogMC41LFxuICAgIHdpZHRoOiA1XG59O1xubGV0IG9iamVjdFN0eWxlcyA9IHt9O1xuXG5sZXQgbWFwID0gbmV3IEwubWFwKCdtYXBNYWluJywge1xuICAgIHVwZGF0ZVdoZW5ab29taW5nOiBmYWxzZVxufSk7XG5cbmxldCBtYXBTdHlsZSA9IEwudGlsZUxheWVyKG1hcEJveFVybCwge1xuICAgIGF0dHJpYnV0aW9uOiBtYXBCb3hBdHRyaWJ1dGlvbixcbiAgICBtYXhOYXRpdmVab29tOiAxOSxcbiAgICBtYXhab29tOiAyMCxcbiAgICBtaW5ab29tOiAxMSxcbiAgICAvLyBkZXRlY3RSZXRpbmE6IHRydWUsXG4gICAgdXBkYXRlV2hlblpvb21pbmc6IGZhbHNlXG59KTtcbm1hcFN0eWxlLmFkZFRvKG1hcCk7XG5cbmxldCB1cGRhdGVNYXBUaHJvdHRsZTtcbm1hcC5vbignbG9hZCBkcmFnZW5kIHpvb21lbmQnLCBmdW5jdGlvbigpIHtcbiAgICBjbGVhclRpbWVvdXQodXBkYXRlTWFwVGhyb3R0bGUpO1xuICAgIHVwZGF0ZU1hcFRocm90dGxlID0gc2V0VGltZW91dCh1cGRhdGVNYXAsIDIwMCk7XG59KTtcblxubWFwLnNldFZpZXcobWFwQ2VudGVyLCAxNyk7XG5cbmxldCBnZW9Kc29uTGF5ZXIgPSBMLmdlb0pTT04oW10sIHtcbiAgICBzdHlsZTogZnVuY3Rpb24oZmVhdHVyZSkge1xuICAgICAgICByZXR1cm4gb2JqZWN0U3R5bGVzW2ZlYXR1cmUucHJvcGVydGllcy5fczFdID8gey4uLm9iamVjdFN0eWxlc1tmZWF0dXJlLnByb3BlcnRpZXMuX3MxXX0gOiB7Li4uZGVmYXVsdE9iamVjdFN0eWxlfVxuICAgIH0sXG4gICAgb25FYWNoRmVhdHVyZTogZnVuY3Rpb24oZmVhdHVyZSwgbGF5ZXIpIHtcbiAgICAgICAgaWYgKGZlYXR1cmUucHJvcGVydGllcy5fYmVoYXZpb3IgPT09ICdpbmZvJykge1xuICAgICAgICAgICAgbGV0IHBvcHVwQ29udGVudCA9IGA8cCBjbGFzcz1cInRleHQtY2VudGVyXCI+JHtmZWF0dXJlLnByb3BlcnRpZXMudHlwZX08YnIgLz4ke2ZlYXR1cmUucHJvcGVydGllcy5uYW1lfTwvcD5gO1xuICAgICAgICAgICAgbGF5ZXIuYmluZFBvcHVwKHBvcHVwQ29udGVudCwge1xuICAgICAgICAgICAgICAgIGNsb3NlQnV0dG9uOiB0cnVlLFxuICAgICAgICAgICAgICAgIG9mZnNldDogTC5wb2ludCgwLCAtMjApXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgICAgICBsYXllci5vbignY2xpY2snLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICB0YWtlQWN0aW9uKGxheWVyKTtcbiAgICAgICAgfSk7XG4gICAgICAgIGlmIChmZWF0dXJlLmdlb21ldHJ5LnR5cGUgIT09ICdQb2ludCcpIHtcbiAgICAgICAgICAgIGxheWVyLm9uKCdtb3VzZW92ZXInLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgbW91c2VFbnRlcih0aGlzKVxuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBsYXllci5vbignbW91c2VvdXQnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgbW91c2VMZWF2ZSh0aGlzKVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH1cbiAgICB9LFxuICAgIHBvaW50VG9MYXllcjogZnVuY3Rpb24gKGZlYXR1cmUsIGxhdGxuZykge1xuICAgICAgICByZXR1cm4gTC5jaXJjbGVNYXJrZXIobGF0bG5nLCB7XG4gICAgICAgICAgICByYWRpdXMgOiA4LFxuICAgICAgICAgICAgZmlsbENvbG9yIDogXCIjZmY3ODAwXCIsXG4gICAgICAgICAgICBjb2xvciA6IFwiIzAwMFwiLFxuICAgICAgICAgICAgd2VpZ2h0IDogMSxcbiAgICAgICAgICAgIG9wYWNpdHkgOiAxLFxuICAgICAgICAgICAgZmlsbE9wYWNpdHkgOiAwLjhcbiAgICAgICAgfSk7XG4gICAgfVxufSkuYWRkVG8obWFwKTtcblxuZnVuY3Rpb24gdXBkYXRlTWFwKCkge1xuICAgIGxldCB6b29tID0gbWFwLmdldFpvb20oKTtcbiAgICBsZXQgY29vcmRzID0gbWFwLmdldEJvdW5kcygpO1xuICAgIFxuICAgICQuYWpheCh7XG4gICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgIGluOiBjb29yZHMuX3NvdXRoV2VzdC5sbmcgKyAnLCcgK1xuICAgICAgICAgICAgICAgIGNvb3Jkcy5fbm9ydGhFYXN0LmxhdCArICcsJyArXG4gICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICBjb29yZHMuX3NvdXRoV2VzdC5sYXQgKyAnLCcgK1xuICAgICAgICAgICAgICAgIGNvb3Jkcy5fbm9ydGhFYXN0LmxuZyArICcsJyArXG4gICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubGF0ICsgJywnICtcbiAgICAgICAgICAgICAgICBjb29yZHMuX25vcnRoRWFzdC5sbmcgKyAnLCcgK1xuICAgICAgICAgICAgICAgIGNvb3Jkcy5fbm9ydGhFYXN0LmxhdCArICcsJyArXG4gICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICBjb29yZHMuX25vcnRoRWFzdC5sYXQsXG4gICAgICAgICAgICB6b29tOiB6b29tXG4gICAgICAgIH0sXG4gICAgICAgIHVybDogXCIvZnJvbnQtZW5kL21hcD9cIixcbiAgICAgICAgc3VjY2VzczogZnVuY3Rpb24gKHJlc3VsdHMpIHtcbiAgICAgICAgICAgIG9iamVjdFN0eWxlcyA9IHJlc3VsdHMuc2V0dGluZ3Muc3R5bGVzO1xuICAgICAgICAgICAgZ2VvSnNvbkxheWVyLmNsZWFyTGF5ZXJzKCk7XG4gICAgICAgICAgICBnZW9Kc29uTGF5ZXIuYWRkRGF0YShyZXN1bHRzLm9iamVjdHMpO1xuICAgICAgICB9XG4gICAgfSk7XG59XG5cbmZ1bmN0aW9uIHRha2VBY3Rpb24obGF5ZXIpIHtcbiAgICBzd2l0Y2ggKGxheWVyLmZlYXR1cmUucHJvcGVydGllcy5fYmVoYXZpb3IpIHtcbiAgICAgICAgY2FzZSAnaW5mbyc6IGxheWVyLm9wZW5Qb3B1cCgpOyBicmVhaztcbiAgICAgICAgY2FzZSAnbmF2aWdhdGlvbic6IG1hcC5maXRCb3VuZHMobGF5ZXIuZ2V0Qm91bmRzKCksIHsgcGFkZGluZzogWzAsIDBdIH0pOyBicmVhaztcbiAgICAgICAgY2FzZSAnc3VydmV5JzogY29uc29sZS5sb2coJ2dvIHRvIGlubmVyIHBhZ2UnKTsgYnJlYWs7XG4gICAgfVxufVxuXG5mdW5jdGlvbiBtb3VzZUVudGVyKGxheWVyKSB7XG4gICAgaWYgKG9iamVjdFN0eWxlc1tsYXllci5mZWF0dXJlLnByb3BlcnRpZXMuX3MyXSkge1xuICAgICAgICBsYXllci5zZXRTdHlsZShvYmplY3RTdHlsZXNbbGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLl9zMl0pO1xuICAgIH1cbn1cblxuZnVuY3Rpb24gbW91c2VMZWF2ZShsYXllcikge1xuICAgIGlmIChvYmplY3RTdHlsZXNbbGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLl9zMl0pIHtcbiAgICAgICAgbGF5ZXIuc2V0U3R5bGUob2JqZWN0U3R5bGVzW2xheWVyLmZlYXR1cmUucHJvcGVydGllcy5fczFdIHx8IGRlZmF1bHRPYmplY3RTdHlsZSk7XG4gICAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIGFzc2V0cy1zcmMvZnJvbnQvanMvbWFwLW1haW4uanMiXSwibWFwcGluZ3MiOiI7Ozs7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUhBO0FBS0E7QUFDQTtBQUNBO0FBQ0E7QUFEQTtBQUNBO0FBR0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFOQTtBQVFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFGQTtBQUlBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQU5BO0FBUUE7QUFqQ0E7QUFDQTtBQW1DQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQVVBO0FBWEE7QUFhQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFuQkE7QUFxQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUFBO0FBQ0E7QUFBQTtBQUNBO0FBQUE7QUFIQTtBQUtBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///1\n");

/***/ })
/******/ ]);