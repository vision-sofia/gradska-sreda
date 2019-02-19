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
eval("\n\nvar _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };\n\nvar mapBoxUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';\nvar mapBoxAttribution = '&copy <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors';\nvar mapCenter = [42.697664, 23.3166103];\nvar defaultStyle = {\n    color: \"#ff9710\",\n    opacity: 0.5,\n    width: 5\n};\nvar layerStyles = {};\n\nvar map = new L.map('mapMain', {\n    center: mapCenter,\n    zoom: 17,\n    updateWhenZooming: false\n});\n\nvar mapStyle = L.tileLayer(mapBoxUrl, {\n    attribution: mapBoxAttribution,\n    maxNativeZoom: 19,\n    maxZoom: 20,\n    minZoom: 12,\n    updateWhenZooming: false\n});\n\nmapStyle.addTo(map);\n// updateMap();\n\nvar updateMapThrottle = void 0;\n\nmap.on('dragend', updateMap).on('zoomend', function () {\n    clearTimeout(updateMapThrottle);\n    updateMapThrottle = setTimeout(updateMap, 500);\n});\n\nfunction updateMap() {\n    var zoom = map.getZoom();\n    var coords = map.getBounds();\n\n    $.ajax({\n        data: {\n            in: coords._southWest.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._northEast.lat,\n            zoom: zoom\n        },\n        url: \"/front-end/map?\",\n        success: function success(results) {\n            layerStyles = results.settings.styles;\n            // console.log(layerStyles);\n            drawLayers(results.objects);\n        }\n    });\n}\n\nfunction drawLayers(objects) {\n\n    objects.forEach(function (el) {\n        // Get object styles if any or set default styles\n        var options = layerStyles[el._s1] ? _extends({}, layerStyles[el._s1]) : _extends({}, defaultStyle);\n        options.label = el.name || '';\n        options.id = el.id;\n        options.behavior = el.attributes._behavior;\n        options._s1 = el._s1;\n        options._s2 = el._s2;\n\n        switch (el.geometry.type) {\n            case \"MultiLineString\":\n                L.polyline(el.geometry.coordinates[0], options).on('click', clickObject).on('mouseover', mouseEnter).on('mouseout', mouseLeave).addTo(map);\n                break;\n            case \"Polygon\":\n                L.polygon(el.geometry.coordinates[0], options).on('click', clickObject).addTo(map);\n                break;\n        }\n    });\n}\n\nfunction clickObject(event) {\n    console.log(event.target.options.behavior);\n}\n\nfunction mouseEnter(event) {\n    if (layerStyles[event.target.options._s2]) {\n        event.sourceTarget.setStyle(layerStyles[event.target.options._s2]);\n    }\n}\n\nfunction mouseLeave(event) {\n    if (layerStyles[event.target.options._s2]) {\n        event.sourceTarget.setStyle(layerStyles[event.target.options._s1] || defaultStyle);\n    }\n}//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMS5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21hcC1tYWluLmpzPzVhZWIiXSwic291cmNlc0NvbnRlbnQiOlsiY29uc3QgbWFwQm94VXJsID0gJ2h0dHBzOi8ve3N9LnRpbGUub3BlbnN0cmVldG1hcC5vcmcve3p9L3t4fS97eX0ucG5nJztcbmNvbnN0IG1hcEJveEF0dHJpYnV0aW9uID0gYCZjb3B5IDxhIGhyZWY9XCJodHRwczovL3d3dy5vcGVuc3RyZWV0bWFwLm9yZy9jb3B5cmlnaHRcIj5PcGVuU3RyZWV0TWFwPC9hPiBjb250cmlidXRvcnNgO1xuY29uc3QgbWFwQ2VudGVyID0gWzQyLjY5NzY2NCwyMy4zMTY2MTAzXTtcbmNvbnN0IGRlZmF1bHRTdHlsZSA9IHtcbiAgICBjb2xvcjogXCIjZmY5NzEwXCIsXG4gICAgb3BhY2l0eTogMC41LFxuICAgIHdpZHRoOiA1XG59O1xubGV0IGxheWVyU3R5bGVzID0ge307XG5cbmxldCBtYXAgPSBuZXcgTC5tYXAoJ21hcE1haW4nLCB7XG4gICAgY2VudGVyOiBtYXBDZW50ZXIsXG4gICAgem9vbTogMTcsXG4gICAgdXBkYXRlV2hlblpvb21pbmc6IGZhbHNlXG59KTtcblxubGV0IG1hcFN0eWxlID0gTC50aWxlTGF5ZXIobWFwQm94VXJsLCB7XG4gICAgYXR0cmlidXRpb246IG1hcEJveEF0dHJpYnV0aW9uLFxuICAgIG1heE5hdGl2ZVpvb206IDE5LFxuICAgIG1heFpvb206IDIwLFxuICAgIG1pblpvb206IDEyLFxuICAgIHVwZGF0ZVdoZW5ab29taW5nOiBmYWxzZVxufSk7XG5cbm1hcFN0eWxlLmFkZFRvKG1hcCk7XG4vLyB1cGRhdGVNYXAoKTtcblxubGV0IHVwZGF0ZU1hcFRocm90dGxlO1xuXG5tYXAub24oJ2RyYWdlbmQnLCB1cGRhdGVNYXApLm9uKCd6b29tZW5kJywgZnVuY3Rpb24oKSB7XG4gICAgY2xlYXJUaW1lb3V0KHVwZGF0ZU1hcFRocm90dGxlKTtcbiAgICB1cGRhdGVNYXBUaHJvdHRsZSA9IHNldFRpbWVvdXQodXBkYXRlTWFwLCA1MDApO1xufSk7XG5cbmZ1bmN0aW9uIHVwZGF0ZU1hcCgpIHtcbiAgICBsZXQgem9vbSA9IG1hcC5nZXRab29tKCk7XG4gICAgbGV0IGNvb3JkcyA9IG1hcC5nZXRCb3VuZHMoKTtcbiAgICBcbiAgICAkLmFqYXgoe1xuICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgICBpbjogY29vcmRzLl9zb3V0aFdlc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICBjb29yZHMuX25vcnRoRWFzdC5sYXQgKyAnLCcgK1xuICAgICAgICAgICAgICAgIGNvb3Jkcy5fc291dGhXZXN0LmxuZyArICcsJyArXG4gICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubGF0ICsgJywnICtcbiAgICAgICAgICAgICAgICBjb29yZHMuX25vcnRoRWFzdC5sbmcgKyAnLCcgK1xuICAgICAgICAgICAgICAgIGNvb3Jkcy5fc291dGhXZXN0LmxhdCArICcsJyArXG4gICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICBjb29yZHMuX25vcnRoRWFzdC5sYXQgKyAnLCcgK1xuICAgICAgICAgICAgICAgIGNvb3Jkcy5fc291dGhXZXN0LmxuZyArICcsJyArXG4gICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubGF0LFxuICAgICAgICAgICAgem9vbTogem9vbVxuICAgICAgICB9LFxuICAgICAgICB1cmw6IFwiL2Zyb250LWVuZC9tYXA/XCIsXG4gICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uIChyZXN1bHRzKSB7XG4gICAgICAgICAgICBsYXllclN0eWxlcyA9IHJlc3VsdHMuc2V0dGluZ3Muc3R5bGVzO1xuICAgICAgICAgICAgLy8gY29uc29sZS5sb2cobGF5ZXJTdHlsZXMpO1xuICAgICAgICAgICAgZHJhd0xheWVycyhyZXN1bHRzLm9iamVjdHMpO1xuICAgICAgICB9XG4gICAgfSk7XG59XG5cbmZ1bmN0aW9uIGRyYXdMYXllcnMob2JqZWN0cykge1xuXG4gICAgb2JqZWN0cy5mb3JFYWNoKChlbCkgPT4ge1xuICAgICAgICAvLyBHZXQgb2JqZWN0IHN0eWxlcyBpZiBhbnkgb3Igc2V0IGRlZmF1bHQgc3R5bGVzXG4gICAgICAgIGxldCBvcHRpb25zID0gbGF5ZXJTdHlsZXNbZWwuX3MxXSA/IHsuLi5sYXllclN0eWxlc1tlbC5fczFdfSA6IHsuLi5kZWZhdWx0U3R5bGV9O1xuICAgICAgICBvcHRpb25zLmxhYmVsID0gZWwubmFtZSB8fCAnJztcbiAgICAgICAgb3B0aW9ucy5pZCA9IGVsLmlkO1xuICAgICAgICBvcHRpb25zLmJlaGF2aW9yID0gZWwuYXR0cmlidXRlcy5fYmVoYXZpb3I7XG4gICAgICAgIG9wdGlvbnMuX3MxID0gZWwuX3MxO1xuICAgICAgICBvcHRpb25zLl9zMiA9IGVsLl9zMjtcblxuICAgICAgICBzd2l0Y2goZWwuZ2VvbWV0cnkudHlwZSkge1xuICAgICAgICAgICAgY2FzZSBcIk11bHRpTGluZVN0cmluZ1wiOlxuICAgICAgICAgICAgICAgIEwucG9seWxpbmUoZWwuZ2VvbWV0cnkuY29vcmRpbmF0ZXNbMF0sIG9wdGlvbnMpXG4gICAgICAgICAgICAgICAgICAgIC5vbignY2xpY2snLCBjbGlja09iamVjdClcbiAgICAgICAgICAgICAgICAgICAgLm9uKCdtb3VzZW92ZXInLCBtb3VzZUVudGVyKVxuICAgICAgICAgICAgICAgICAgICAub24oJ21vdXNlb3V0JywgbW91c2VMZWF2ZSlcbiAgICAgICAgICAgICAgICAgICAgLmFkZFRvKG1hcCk7XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgXCJQb2x5Z29uXCI6XG4gICAgICAgICAgICAgICAgTC5wb2x5Z29uKGVsLmdlb21ldHJ5LmNvb3JkaW5hdGVzWzBdLCBvcHRpb25zKVxuICAgICAgICAgICAgICAgICAgICAub24oJ2NsaWNrJywgY2xpY2tPYmplY3QpXG4gICAgICAgICAgICAgICAgICAgIC5hZGRUbyhtYXApO1xuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgIH1cbiAgICB9KTtcbn1cblxuZnVuY3Rpb24gY2xpY2tPYmplY3QoZXZlbnQpIHtcbiAgICBjb25zb2xlLmxvZyhldmVudC50YXJnZXQub3B0aW9ucy5iZWhhdmlvcik7XG59XG5cbmZ1bmN0aW9uIG1vdXNlRW50ZXIoZXZlbnQpIHtcbiAgICBpZiAobGF5ZXJTdHlsZXNbZXZlbnQudGFyZ2V0Lm9wdGlvbnMuX3MyXSkge1xuICAgICAgICBldmVudC5zb3VyY2VUYXJnZXQuc2V0U3R5bGUobGF5ZXJTdHlsZXNbZXZlbnQudGFyZ2V0Lm9wdGlvbnMuX3MyXSk7XG4gICAgfVxufVxuXG5mdW5jdGlvbiBtb3VzZUxlYXZlKGV2ZW50KSB7XG4gICAgaWYgKGxheWVyU3R5bGVzW2V2ZW50LnRhcmdldC5vcHRpb25zLl9zMl0pIHtcbiAgICAgICAgZXZlbnQuc291cmNlVGFyZ2V0LnNldFN0eWxlKGxheWVyU3R5bGVzW2V2ZW50LnRhcmdldC5vcHRpb25zLl9zMV0gfHwgZGVmYXVsdFN0eWxlKTtcbiAgICB9XG59XG5cblxuXG4vLyBXRUJQQUNLIEZPT1RFUiAvL1xuLy8gYXNzZXRzLXNyYy9mcm9udC9qcy9tYXAtbWFpbi5qcyJdLCJtYXBwaW5ncyI6Ijs7OztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBSEE7QUFLQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFIQTtBQUNBO0FBS0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBTEE7QUFDQTtBQU9BO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFVQTtBQVhBO0FBYUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBbkJBO0FBcUJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBS0E7QUFDQTtBQUNBO0FBR0E7QUFaQTtBQWNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///1\n");

/***/ })
/******/ ]);