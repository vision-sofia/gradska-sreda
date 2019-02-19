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
eval("\n\nvar _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };\n\nvar mapBoxUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';\nvar mapBoxAttribution = '&copy <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors';\nvar mapCenter = [42.697664, 23.3166103];\nvar defaultObjectStyle = {\n    color: \"#ff9710\",\n    opacity: 0.5,\n    width: 5\n};\nvar objectStyles = {};\n\nvar map = new L.map('mapMain', {\n    updateWhenZooming: false\n});\n\nvar mapStyle = L.tileLayer(mapBoxUrl, {\n    attribution: mapBoxAttribution,\n    maxNativeZoom: 19,\n    maxZoom: 20,\n    minZoom: 12,\n    updateWhenZooming: false\n});\nmapStyle.addTo(map);\n\nvar allObjectsLayer = L.layerGroup([]);\nallObjectsLayer.addTo(map);\n\nvar updateMapThrottle = void 0;\nmap.on('load dragend zoomend', function () {\n    clearTimeout(updateMapThrottle);\n    updateMapThrottle = setTimeout(updateMap, 200);\n});\n\nmap.setView(mapCenter, 15);\n\nfunction updateMap() {\n    var zoom = map.getZoom();\n    var coords = map.getBounds();\n\n    $.ajax({\n        data: {\n            in: coords._southWest.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._northEast.lat,\n            zoom: zoom\n        },\n        url: \"/front-end/map?\",\n        success: function success(results) {\n            objectStyles = results.settings.styles;\n            console.log(results.settings.styles);\n            allObjectsLayer.clearLayers();\n            drawLayers(results.objects);\n        }\n    });\n}\n\nfunction drawLayers(objects) {\n    objects.forEach(function (el) {\n        var options = objectStyles[el._s1] ? _extends({}, objectStyles[el._s1]) : _extends({}, defaultObjectStyle);\n        options.label = el.name || '';\n        options.id = el.id;\n        options.behavior = el.attributes._behavior;\n        options._s1 = el._s1;\n        options._s2 = el._s2;\n\n        switch (el.geometry.type) {\n            case \"MultiLineString\":\n                allObjectsLayer.addLayer(new L.polyline(el.geometry.coordinates[0], options).on('click', clickObject).on('mouseover', mouseEnter).on('mouseout', mouseLeave));\n                break;\n            case \"Polygon\":\n                allObjectsLayer.addLayer(new L.polygon(el.geometry.coordinates[0], options).on('click', clickObject).on('mouseover', mouseEnter).on('mouseout', mouseLeave));\n                break;\n        }\n    });\n}\n\nfunction clickObject(event) {\n    console.log(event.target.options.behavior);\n}\n\nfunction mouseEnter(event) {\n    objectStyles[event.target.options._s2].fill = '#000000';\n    if (objectStyles[event.target.options._s2]) {\n        event.sourceTarget.setStyle(objectStyles[event.target.options._s2]);\n    }\n}\n\nfunction mouseLeave(event) {\n    if (objectStyles[event.target.options._s2]) {\n        event.sourceTarget.setStyle(objectStyles[event.target.options._s1] || defaultObjectStyle);\n    }\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMS5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21hcC1tYWluLmpzPzVhZWIiXSwic291cmNlc0NvbnRlbnQiOlsiY29uc3QgbWFwQm94VXJsID0gJ2h0dHBzOi8ve3N9LnRpbGUub3BlbnN0cmVldG1hcC5vcmcve3p9L3t4fS97eX0ucG5nJztcbmNvbnN0IG1hcEJveEF0dHJpYnV0aW9uID0gYCZjb3B5IDxhIGhyZWY9XCJodHRwczovL3d3dy5vcGVuc3RyZWV0bWFwLm9yZy9jb3B5cmlnaHRcIj5PcGVuU3RyZWV0TWFwPC9hPiBjb250cmlidXRvcnNgO1xuY29uc3QgbWFwQ2VudGVyID0gWzQyLjY5NzY2NCwyMy4zMTY2MTAzXTtcbmNvbnN0IGRlZmF1bHRPYmplY3RTdHlsZSA9IHtcbiAgICBjb2xvcjogXCIjZmY5NzEwXCIsXG4gICAgb3BhY2l0eTogMC41LFxuICAgIHdpZHRoOiA1XG59O1xubGV0IG9iamVjdFN0eWxlcyA9IHt9O1xuXG5sZXQgbWFwID0gbmV3IEwubWFwKCdtYXBNYWluJywge1xuICAgIHVwZGF0ZVdoZW5ab29taW5nOiBmYWxzZVxufSk7XG5cbmxldCBtYXBTdHlsZSA9IEwudGlsZUxheWVyKG1hcEJveFVybCwge1xuICAgIGF0dHJpYnV0aW9uOiBtYXBCb3hBdHRyaWJ1dGlvbixcbiAgICBtYXhOYXRpdmVab29tOiAxOSxcbiAgICBtYXhab29tOiAyMCxcbiAgICBtaW5ab29tOiAxMixcbiAgICB1cGRhdGVXaGVuWm9vbWluZzogZmFsc2Vcbn0pO1xubWFwU3R5bGUuYWRkVG8obWFwKTtcblxubGV0IGFsbE9iamVjdHNMYXllciA9IEwubGF5ZXJHcm91cChbXSk7XG5hbGxPYmplY3RzTGF5ZXIuYWRkVG8obWFwKTtcblxubGV0IHVwZGF0ZU1hcFRocm90dGxlO1xubWFwLm9uKCdsb2FkIGRyYWdlbmQgem9vbWVuZCcsIGZ1bmN0aW9uKCkge1xuICAgIGNsZWFyVGltZW91dCh1cGRhdGVNYXBUaHJvdHRsZSk7XG4gICAgdXBkYXRlTWFwVGhyb3R0bGUgPSBzZXRUaW1lb3V0KHVwZGF0ZU1hcCwgMjAwKTtcbn0pO1xuXG5tYXAuc2V0VmlldyhtYXBDZW50ZXIsIDE1KTtcblxuZnVuY3Rpb24gdXBkYXRlTWFwKCkge1xuICAgIGxldCB6b29tID0gbWFwLmdldFpvb20oKTtcbiAgICBsZXQgY29vcmRzID0gbWFwLmdldEJvdW5kcygpO1xuICAgIFxuICAgICQuYWpheCh7XG4gICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgIGluOiBjb29yZHMuX3NvdXRoV2VzdC5sbmcgKyAnLCcgK1xuICAgICAgICAgICAgICAgIGNvb3Jkcy5fbm9ydGhFYXN0LmxhdCArICcsJyArXG4gICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICBjb29yZHMuX3NvdXRoV2VzdC5sYXQgKyAnLCcgK1xuICAgICAgICAgICAgICAgIGNvb3Jkcy5fbm9ydGhFYXN0LmxuZyArICcsJyArXG4gICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubGF0ICsgJywnICtcbiAgICAgICAgICAgICAgICBjb29yZHMuX25vcnRoRWFzdC5sbmcgKyAnLCcgK1xuICAgICAgICAgICAgICAgIGNvb3Jkcy5fbm9ydGhFYXN0LmxhdCArICcsJyArXG4gICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICBjb29yZHMuX25vcnRoRWFzdC5sYXQsXG4gICAgICAgICAgICB6b29tOiB6b29tXG4gICAgICAgIH0sXG4gICAgICAgIHVybDogXCIvZnJvbnQtZW5kL21hcD9cIixcbiAgICAgICAgc3VjY2VzczogZnVuY3Rpb24gKHJlc3VsdHMpIHtcbiAgICAgICAgICAgIG9iamVjdFN0eWxlcyA9IHJlc3VsdHMuc2V0dGluZ3Muc3R5bGVzO1xuICAgICAgICAgICAgY29uc29sZS5sb2cocmVzdWx0cy5zZXR0aW5ncy5zdHlsZXMpO1xuICAgICAgICAgICAgYWxsT2JqZWN0c0xheWVyLmNsZWFyTGF5ZXJzKCk7XG4gICAgICAgICAgICBkcmF3TGF5ZXJzKHJlc3VsdHMub2JqZWN0cyk7XG4gICAgICAgIH1cbiAgICB9KTtcbn1cblxuZnVuY3Rpb24gZHJhd0xheWVycyhvYmplY3RzKSB7XG4gICAgb2JqZWN0cy5mb3JFYWNoKGVsID0+IHtcbiAgICAgICAgbGV0IG9wdGlvbnMgPSBvYmplY3RTdHlsZXNbZWwuX3MxXSA/IHsuLi5vYmplY3RTdHlsZXNbZWwuX3MxXX0gOiB7Li4uZGVmYXVsdE9iamVjdFN0eWxlfTtcbiAgICAgICAgb3B0aW9ucy5sYWJlbCA9IGVsLm5hbWUgfHwgJyc7XG4gICAgICAgIG9wdGlvbnMuaWQgPSBlbC5pZDtcbiAgICAgICAgb3B0aW9ucy5iZWhhdmlvciA9IGVsLmF0dHJpYnV0ZXMuX2JlaGF2aW9yO1xuICAgICAgICBvcHRpb25zLl9zMSA9IGVsLl9zMTtcbiAgICAgICAgb3B0aW9ucy5fczIgPSBlbC5fczI7XG5cbiAgICAgICAgc3dpdGNoKGVsLmdlb21ldHJ5LnR5cGUpIHtcbiAgICAgICAgICAgIGNhc2UgXCJNdWx0aUxpbmVTdHJpbmdcIjpcbiAgICAgICAgICAgICAgICBhbGxPYmplY3RzTGF5ZXIuYWRkTGF5ZXIobmV3IEwucG9seWxpbmUoZWwuZ2VvbWV0cnkuY29vcmRpbmF0ZXNbMF0sIG9wdGlvbnMpXG4gICAgICAgICAgICAgICAgICAgIC5vbignY2xpY2snLCBjbGlja09iamVjdClcbiAgICAgICAgICAgICAgICAgICAgLm9uKCdtb3VzZW92ZXInLCBtb3VzZUVudGVyKVxuICAgICAgICAgICAgICAgICAgICAub24oJ21vdXNlb3V0JywgbW91c2VMZWF2ZSkpO1xuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICBjYXNlIFwiUG9seWdvblwiOlxuICAgICAgICAgICAgICAgIGFsbE9iamVjdHNMYXllci5hZGRMYXllcihuZXcgTC5wb2x5Z29uKGVsLmdlb21ldHJ5LmNvb3JkaW5hdGVzWzBdLCBvcHRpb25zKVxuICAgICAgICAgICAgICAgICAgICAub24oJ2NsaWNrJywgY2xpY2tPYmplY3QpXG4gICAgICAgICAgICAgICAgICAgIC5vbignbW91c2VvdmVyJywgbW91c2VFbnRlcilcbiAgICAgICAgICAgICAgICAgICAgLm9uKCdtb3VzZW91dCcsIG1vdXNlTGVhdmUpKTtcbiAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICB9XG4gICAgfSk7XG59XG5cbmZ1bmN0aW9uIGNsaWNrT2JqZWN0KGV2ZW50KSB7XG4gICAgY29uc29sZS5sb2coZXZlbnQudGFyZ2V0Lm9wdGlvbnMuYmVoYXZpb3IpO1xufVxuXG5mdW5jdGlvbiBtb3VzZUVudGVyKGV2ZW50KSB7XG4gICAgb2JqZWN0U3R5bGVzW2V2ZW50LnRhcmdldC5vcHRpb25zLl9zMl0uZmlsbCA9ICcjMDAwMDAwJztcbiAgICBpZiAob2JqZWN0U3R5bGVzW2V2ZW50LnRhcmdldC5vcHRpb25zLl9zMl0pIHtcbiAgICAgICAgZXZlbnQuc291cmNlVGFyZ2V0LnNldFN0eWxlKG9iamVjdFN0eWxlc1tldmVudC50YXJnZXQub3B0aW9ucy5fczJdKTtcbiAgICB9XG59XG5cbmZ1bmN0aW9uIG1vdXNlTGVhdmUoZXZlbnQpIHtcbiAgICBpZiAob2JqZWN0U3R5bGVzW2V2ZW50LnRhcmdldC5vcHRpb25zLl9zMl0pIHtcbiAgICAgICAgZXZlbnQuc291cmNlVGFyZ2V0LnNldFN0eWxlKG9iamVjdFN0eWxlc1tldmVudC50YXJnZXQub3B0aW9ucy5fczFdIHx8IGRlZmF1bHRPYmplY3RTdHlsZSk7XG4gICAgfVxufVxuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIGFzc2V0cy1zcmMvZnJvbnQvanMvbWFwLW1haW4uanMiXSwibWFwcGluZ3MiOiI7Ozs7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUhBO0FBS0E7QUFDQTtBQUNBO0FBQ0E7QUFEQTtBQUNBO0FBR0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBTEE7QUFPQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBVUE7QUFYQTtBQWFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBcEJBO0FBc0JBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFJQTtBQUNBO0FBQ0E7QUFJQTtBQVpBO0FBY0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///1\n");

/***/ })
/******/ ]);