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
eval("\n\nvar _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };\n\nvar mapBoxUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';\nvar mapBoxAttribution = '&copy <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors';\nvar mapCenter = [42.697664, 23.3166103];\nvar defaultObjectStyle = {\n    color: \"#ff9710\",\n    opacity: 0.5,\n    width: 5\n};\nvar objectStyles = {};\n\nvar map = new L.map('mapMain', {\n    updateWhenZooming: false\n});\n\nvar mapStyle = L.tileLayer(mapBoxUrl, {\n    attribution: mapBoxAttribution,\n    maxNativeZoom: 19,\n    maxZoom: 20,\n    minZoom: 4,\n    // detectRetina: true,\n    updateWhenZooming: false\n});\nmapStyle.addTo(map);\n\nvar updateMapThrottle = void 0;\nmap.on('load dragend zoomend', function () {\n    clearTimeout(updateMapThrottle);\n    updateMapThrottle = setTimeout(updateMap, 200);\n});\n\nmap.setView(mapCenter, 15);\n\nvar geoJsonLayer = L.geoJSON([], {\n    style: function style(feature) {\n        return objectStyles[feature.properties._s1] ? _extends({}, objectStyles[feature.properties._s1]) : _extends({}, defaultObjectStyle);\n    },\n    onEachFeature: function onEachFeature(feature, layer) {\n        if (feature.properties._behavior === 'info') {\n            var popupContent = '<p class=\"text-center\">' + feature.properties.type + '<br />' + feature.properties.name + '</p>';\n            layer.bindPopup(popupContent, {\n                closeButton: true,\n                offset: L.point(0, -20)\n            });\n        }\n        layer.on('click', function () {\n            takeAction(layer);\n        });\n        if (feature.geometry.type !== 'Point') {\n            layer.on('mouseover', function () {\n                mouseEnter(this);\n            });\n            layer.on('mouseout', function () {\n                mouseLeave(this);\n            });\n        }\n    },\n    pointToLayer: function pointToLayer(feature, latlng) {\n        return L.circleMarker(latlng, {\n            radius: 8,\n            fillColor: \"#ff7800\",\n            color: \"#000\",\n            weight: 1,\n            opacity: 1,\n            fillOpacity: 0.8\n        });\n    }\n}).addTo(map);\n\nfunction updateMap() {\n    var zoom = map.getZoom();\n    var coords = map.getBounds();\n\n    $.ajax({\n        data: {\n            in: coords._southWest.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._northEast.lat,\n            zoom: zoom\n        },\n        url: \"/front-end/map?\",\n        success: function success(results) {\n            objectStyles = results.settings.styles;\n            geoJsonLayer.clearLayers();\n            geoJsonLayer.addData(results.objects);\n        }\n    });\n}\n\nfunction takeAction(layer) {\n    switch (layer.feature.properties._behavior) {\n        case 'info':\n            layer.openPopup();break;\n        case 'navigation':\n            map.fitBounds(layer.getBounds(), { padding: [0, 0] });break;\n        case 'survey':\n            console.log('go to inner page');break;\n    }\n}\n\nfunction mouseEnter(layer) {\n    if (objectStyles[layer.feature.properties._s2]) {\n        layer.setStyle(objectStyles[layer.feature.properties._s2]);\n    }\n}\n\nfunction mouseLeave(layer) {\n    if (objectStyles[layer.feature.properties._s2]) {\n        layer.setStyle(objectStyles[layer.feature.properties._s1] || defaultObjectStyle);\n    }\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMS5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21hcC1tYWluLmpzPzVhZWIiXSwic291cmNlc0NvbnRlbnQiOlsiY29uc3QgbWFwQm94VXJsID0gJ2h0dHBzOi8ve3N9LnRpbGUub3BlbnN0cmVldG1hcC5vcmcve3p9L3t4fS97eX0ucG5nJztcbmNvbnN0IG1hcEJveEF0dHJpYnV0aW9uID0gYCZjb3B5IDxhIGhyZWY9XCJodHRwczovL3d3dy5vcGVuc3RyZWV0bWFwLm9yZy9jb3B5cmlnaHRcIj5PcGVuU3RyZWV0TWFwPC9hPiBjb250cmlidXRvcnNgO1xuY29uc3QgbWFwQ2VudGVyID0gWzQyLjY5NzY2NCwyMy4zMTY2MTAzXTtcbmNvbnN0IGRlZmF1bHRPYmplY3RTdHlsZSA9IHtcbiAgICBjb2xvcjogXCIjZmY5NzEwXCIsXG4gICAgb3BhY2l0eTogMC41LFxuICAgIHdpZHRoOiA1XG59O1xubGV0IG9iamVjdFN0eWxlcyA9IHt9O1xuXG5sZXQgbWFwID0gbmV3IEwubWFwKCdtYXBNYWluJywge1xuICAgIHVwZGF0ZVdoZW5ab29taW5nOiBmYWxzZVxufSk7XG5cbmxldCBtYXBTdHlsZSA9IEwudGlsZUxheWVyKG1hcEJveFVybCwge1xuICAgIGF0dHJpYnV0aW9uOiBtYXBCb3hBdHRyaWJ1dGlvbixcbiAgICBtYXhOYXRpdmVab29tOiAxOSxcbiAgICBtYXhab29tOiAyMCxcbiAgICBtaW5ab29tOiA0LFxuICAgIC8vIGRldGVjdFJldGluYTogdHJ1ZSxcbiAgICB1cGRhdGVXaGVuWm9vbWluZzogZmFsc2Vcbn0pO1xubWFwU3R5bGUuYWRkVG8obWFwKTtcblxubGV0IHVwZGF0ZU1hcFRocm90dGxlO1xubWFwLm9uKCdsb2FkIGRyYWdlbmQgem9vbWVuZCcsIGZ1bmN0aW9uKCkge1xuICAgIGNsZWFyVGltZW91dCh1cGRhdGVNYXBUaHJvdHRsZSk7XG4gICAgdXBkYXRlTWFwVGhyb3R0bGUgPSBzZXRUaW1lb3V0KHVwZGF0ZU1hcCwgMjAwKTtcbn0pO1xuXG5tYXAuc2V0VmlldyhtYXBDZW50ZXIsIDE1KTtcblxubGV0IGdlb0pzb25MYXllciA9IEwuZ2VvSlNPTihbXSwge1xuICAgIHN0eWxlOiBmdW5jdGlvbihmZWF0dXJlKSB7XG4gICAgICAgIHJldHVybiBvYmplY3RTdHlsZXNbZmVhdHVyZS5wcm9wZXJ0aWVzLl9zMV0gPyB7Li4ub2JqZWN0U3R5bGVzW2ZlYXR1cmUucHJvcGVydGllcy5fczFdfSA6IHsuLi5kZWZhdWx0T2JqZWN0U3R5bGV9XG4gICAgfSxcbiAgICBvbkVhY2hGZWF0dXJlOiBmdW5jdGlvbihmZWF0dXJlLCBsYXllcikge1xuICAgICAgICBpZiAoZmVhdHVyZS5wcm9wZXJ0aWVzLl9iZWhhdmlvciA9PT0gJ2luZm8nKSB7XG4gICAgICAgICAgICBsZXQgcG9wdXBDb250ZW50ID0gYDxwIGNsYXNzPVwidGV4dC1jZW50ZXJcIj4ke2ZlYXR1cmUucHJvcGVydGllcy50eXBlfTxiciAvPiR7ZmVhdHVyZS5wcm9wZXJ0aWVzLm5hbWV9PC9wPmA7XG4gICAgICAgICAgICBsYXllci5iaW5kUG9wdXAocG9wdXBDb250ZW50LCB7XG4gICAgICAgICAgICAgICAgY2xvc2VCdXR0b246IHRydWUsXG4gICAgICAgICAgICAgICAgb2Zmc2V0OiBMLnBvaW50KDAsIC0yMClcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgICAgIGxheWVyLm9uKCdjbGljaycsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgIHRha2VBY3Rpb24obGF5ZXIpO1xuICAgICAgICB9KTtcbiAgICAgICAgaWYgKGZlYXR1cmUuZ2VvbWV0cnkudHlwZSAhPT0gJ1BvaW50Jykge1xuICAgICAgICAgICAgbGF5ZXIub24oJ21vdXNlb3ZlcicsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBtb3VzZUVudGVyKHRoaXMpXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIGxheWVyLm9uKCdtb3VzZW91dCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBtb3VzZUxlYXZlKHRoaXMpXG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfVxuICAgIH0sXG4gICAgcG9pbnRUb0xheWVyOiBmdW5jdGlvbiAoZmVhdHVyZSwgbGF0bG5nKSB7XG4gICAgICAgIHJldHVybiBMLmNpcmNsZU1hcmtlcihsYXRsbmcsIHtcbiAgICAgICAgICAgIHJhZGl1cyA6IDgsXG4gICAgICAgICAgICBmaWxsQ29sb3IgOiBcIiNmZjc4MDBcIixcbiAgICAgICAgICAgIGNvbG9yIDogXCIjMDAwXCIsXG4gICAgICAgICAgICB3ZWlnaHQgOiAxLFxuICAgICAgICAgICAgb3BhY2l0eSA6IDEsXG4gICAgICAgICAgICBmaWxsT3BhY2l0eSA6IDAuOFxuICAgICAgICB9KTtcbiAgICB9XG59KS5hZGRUbyhtYXApO1xuXG5mdW5jdGlvbiB1cGRhdGVNYXAoKSB7XG4gICAgbGV0IHpvb20gPSBtYXAuZ2V0Wm9vbSgpO1xuICAgIGxldCBjb29yZHMgPSBtYXAuZ2V0Qm91bmRzKCk7XG4gICAgXG4gICAgJC5hamF4KHtcbiAgICAgICAgZGF0YToge1xuICAgICAgICAgICAgaW46IGNvb3Jkcy5fc291dGhXZXN0LmxuZyArICcsJyArXG4gICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubGF0ICsgJywnICtcbiAgICAgICAgICAgICAgICBjb29yZHMuX3NvdXRoV2VzdC5sbmcgKyAnLCcgK1xuICAgICAgICAgICAgICAgIGNvb3Jkcy5fc291dGhXZXN0LmxhdCArICcsJyArXG4gICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICBjb29yZHMuX3NvdXRoV2VzdC5sYXQgKyAnLCcgK1xuICAgICAgICAgICAgICAgIGNvb3Jkcy5fbm9ydGhFYXN0LmxuZyArICcsJyArXG4gICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubGF0ICsgJywnICtcbiAgICAgICAgICAgICAgICBjb29yZHMuX3NvdXRoV2VzdC5sbmcgKyAnLCcgK1xuICAgICAgICAgICAgICAgIGNvb3Jkcy5fbm9ydGhFYXN0LmxhdCxcbiAgICAgICAgICAgIHpvb206IHpvb21cbiAgICAgICAgfSxcbiAgICAgICAgdXJsOiBcIi9mcm9udC1lbmQvbWFwP1wiLFxuICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAocmVzdWx0cykge1xuICAgICAgICAgICAgb2JqZWN0U3R5bGVzID0gcmVzdWx0cy5zZXR0aW5ncy5zdHlsZXM7XG4gICAgICAgICAgICBnZW9Kc29uTGF5ZXIuY2xlYXJMYXllcnMoKTtcbiAgICAgICAgICAgIGdlb0pzb25MYXllci5hZGREYXRhKHJlc3VsdHMub2JqZWN0cyk7XG4gICAgICAgIH1cbiAgICB9KTtcbn1cblxuZnVuY3Rpb24gdGFrZUFjdGlvbihsYXllcikge1xuICAgIHN3aXRjaCAobGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLl9iZWhhdmlvcikge1xuICAgICAgICBjYXNlICdpbmZvJzogbGF5ZXIub3BlblBvcHVwKCk7IGJyZWFrO1xuICAgICAgICBjYXNlICduYXZpZ2F0aW9uJzogbWFwLmZpdEJvdW5kcyhsYXllci5nZXRCb3VuZHMoKSwgeyBwYWRkaW5nOiBbMCwgMF0gfSk7IGJyZWFrO1xuICAgICAgICBjYXNlICdzdXJ2ZXknOiBjb25zb2xlLmxvZygnZ28gdG8gaW5uZXIgcGFnZScpOyBicmVhaztcbiAgICB9XG59XG5cbmZ1bmN0aW9uIG1vdXNlRW50ZXIobGF5ZXIpIHtcbiAgICBpZiAob2JqZWN0U3R5bGVzW2xheWVyLmZlYXR1cmUucHJvcGVydGllcy5fczJdKSB7XG4gICAgICAgIGxheWVyLnNldFN0eWxlKG9iamVjdFN0eWxlc1tsYXllci5mZWF0dXJlLnByb3BlcnRpZXMuX3MyXSk7XG4gICAgfVxufVxuXG5mdW5jdGlvbiBtb3VzZUxlYXZlKGxheWVyKSB7XG4gICAgaWYgKG9iamVjdFN0eWxlc1tsYXllci5mZWF0dXJlLnByb3BlcnRpZXMuX3MyXSkge1xuICAgICAgICBsYXllci5zZXRTdHlsZShvYmplY3RTdHlsZXNbbGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLl9zMV0gfHwgZGVmYXVsdE9iamVjdFN0eWxlKTtcbiAgICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gYXNzZXRzLXNyYy9mcm9udC9qcy9tYXAtbWFpbi5qcyJdLCJtYXBwaW5ncyI6Ijs7OztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBSEE7QUFLQTtBQUNBO0FBQ0E7QUFDQTtBQURBO0FBQ0E7QUFHQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQU5BO0FBUUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUZBO0FBSUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBTkE7QUFRQTtBQWpDQTtBQUNBO0FBbUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBVUE7QUFYQTtBQWFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQW5CQTtBQXFCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQUE7QUFDQTtBQUFBO0FBQ0E7QUFBQTtBQUhBO0FBS0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///1\n");

/***/ })
/******/ ]);