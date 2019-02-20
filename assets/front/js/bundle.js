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
eval("\n\nvar _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };\n\nvar _mapConfig = __webpack_require__(0);\n\n(function () {\n    if (!document.getElementById('mapMain')) {\n        return;\n    }\n    var mapCenter = [42.697664, 23.3166103];\n    var defaultObjectStyle = {\n        color: \"#ff9710\",\n        opacity: 0.5,\n        width: 5\n    };\n    var objectStyles = {};\n    var dialogTitles = {};\n\n    var map = new L.map('mapMain', {\n        updateWhenZooming: false\n    });\n\n    var mapStyle = L.tileLayer(_mapConfig.mapBoxUrl, {\n        attribution: _mapConfig.mapBoxAttribution,\n        maxNativeZoom: 19,\n        maxZoom: 20,\n        minZoom: 11,\n        // detectRetina: true,\n        updateWhenZooming: false\n    });\n    mapStyle.addTo(map);\n\n    var updateMapThrottle = void 0;\n    map.on('load dragend zoomend', function () {\n        clearTimeout(updateMapThrottle);\n        updateMapThrottle = setTimeout(updateMap, 200);\n    });\n\n    map.setView(mapCenter, 17);\n\n    var geoJsonLayer = L.geoJSON([], {\n        style: function style(feature) {\n            return objectStyles[feature.properties._s1] ? _extends({}, objectStyles[feature.properties._s1]) : _extends({}, defaultObjectStyle);\n        },\n        onEachFeature: function onEachFeature(feature, layer) {\n            if (feature.properties._behavior === 'info' || feature.properties._behavior === 'survey') {\n                var popupContent = '<p class=\"text-center\">' + feature.properties.type + '<br />' + feature.properties.name + '</p>';\n                layer.bindPopup(popupContent, {\n                    closeButton: true,\n                    offset: L.point(0, -20)\n                });\n                layer.getPopup().on('remove', function () {\n                    confirmPopup.addClass('d-none');\n                });\n            }\n            layer.on('click', function (ev) {\n                takeAction(layer, ev);\n            });\n            if (feature.geometry.type !== 'Point') {\n                layer.on('mouseover', function () {\n                    mouseEnter(this);\n                });\n                layer.on('mouseout', function () {\n                    mouseLeave(this);\n                });\n            }\n        },\n        pointToLayer: function pointToLayer(feature, latlng) {\n            return L.circleMarker(latlng, {\n                radius: 8,\n                fillColor: \"#ff7800\",\n                color: \"#000\",\n                weight: 1,\n                opacity: 1,\n                fillOpacity: 0.8\n            });\n        }\n    }).addTo(map);\n\n    var confirmPopup = $('.confirm');\n    $(document).on('click', '[data-confirm-cancel]', function () {\n        map.closePopup();\n    });\n\n    function updateMap() {\n        var zoom = map.getZoom();\n        var coords = map.getBounds();\n\n        $.ajax({\n            data: {\n                in: coords._southWest.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._northEast.lat,\n                zoom: zoom\n            },\n            url: \"/front-end/map?\",\n            success: function success(results) {\n                objectStyles = results.settings.styles;\n                dialogTitles = results.settings.dialog;\n                geoJsonLayer.clearLayers();\n                geoJsonLayer.addData(results.objects);\n            }\n        });\n    }\n\n    function takeAction(layer, ev) {\n        switch (layer.feature.properties._behavior) {\n            case 'info':\n                layer.openPopup();\n                break;\n            case 'navigation':\n                if (layer.feature.properties._zoom) {\n                    var coords = map.mouseEventToLatLng(ev.originalEvent);\n                    map.setView([coords.lat, coords.lng], layer.feature.properties._zoom);\n                } else {\n                    map.fitBounds(layer.getBounds(), { maxZoom: [0, 0] });\n                }\n                break;\n            case 'survey':\n                layer.openPopup();\n                var dialogTitle = dialogTitles[layer.feature.properties._dtext] || 'Искате ли да оцените';\n                var dialogLink = '/geo/' + layer.feature.properties.id;\n                confirmPopup.removeClass('d-none');\n                confirmPopup.find('[data-confirm-title]').html(dialogTitle + '?');\n                confirmPopup.find('[data-confirm-link]').attr('href', dialogLink);\n                break;\n        }\n    }\n\n    function mouseEnter(layer) {\n        if (objectStyles[layer.feature.properties._s2]) {\n            layer.setStyle(objectStyles[layer.feature.properties._s2]);\n        }\n    }\n\n    function mouseLeave(layer) {\n        if (objectStyles[layer.feature.properties._s2]) {\n            layer.setStyle(objectStyles[layer.feature.properties._s1] || defaultObjectStyle);\n        }\n    }\n})();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMi5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21hcC1tYWluLmpzPzVhZWIiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHsgbWFwQm94QXR0cmlidXRpb24sIG1hcEJveFVybCB9IGZyb20gJy4vbWFwLWNvbmZpZyc7XG5cbigoKSA9PiB7XG4gICAgaWYgKCFkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbWFwTWFpbicpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG4gICAgY29uc3QgbWFwQ2VudGVyID0gWzQyLjY5NzY2NCwyMy4zMTY2MTAzXTtcbiAgICBjb25zdCBkZWZhdWx0T2JqZWN0U3R5bGUgPSB7XG4gICAgICAgIGNvbG9yOiBcIiNmZjk3MTBcIixcbiAgICAgICAgb3BhY2l0eTogMC41LFxuICAgICAgICB3aWR0aDogNVxuICAgIH07XG4gICAgbGV0IG9iamVjdFN0eWxlcyA9IHt9O1xuICAgIGxldCBkaWFsb2dUaXRsZXMgPSB7fTtcblxuICAgIGxldCBtYXAgPSBuZXcgTC5tYXAoJ21hcE1haW4nLCB7XG4gICAgICAgIHVwZGF0ZVdoZW5ab29taW5nOiBmYWxzZVxuICAgIH0pO1xuXG4gICAgbGV0IG1hcFN0eWxlID0gTC50aWxlTGF5ZXIobWFwQm94VXJsLCB7XG4gICAgICAgIGF0dHJpYnV0aW9uOiBtYXBCb3hBdHRyaWJ1dGlvbixcbiAgICAgICAgbWF4TmF0aXZlWm9vbTogMTksXG4gICAgICAgIG1heFpvb206IDIwLFxuICAgICAgICBtaW5ab29tOiAxMSxcbiAgICAgICAgLy8gZGV0ZWN0UmV0aW5hOiB0cnVlLFxuICAgICAgICB1cGRhdGVXaGVuWm9vbWluZzogZmFsc2VcbiAgICB9KTtcbiAgICBtYXBTdHlsZS5hZGRUbyhtYXApO1xuXG4gICAgbGV0IHVwZGF0ZU1hcFRocm90dGxlO1xuICAgIG1hcC5vbignbG9hZCBkcmFnZW5kIHpvb21lbmQnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgY2xlYXJUaW1lb3V0KHVwZGF0ZU1hcFRocm90dGxlKTtcbiAgICAgICAgdXBkYXRlTWFwVGhyb3R0bGUgPSBzZXRUaW1lb3V0KHVwZGF0ZU1hcCwgMjAwKTtcbiAgICB9KTtcblxuICAgIG1hcC5zZXRWaWV3KG1hcENlbnRlciwgMTcpO1xuXG4gICAgbGV0IGdlb0pzb25MYXllciA9IEwuZ2VvSlNPTihbXSwge1xuICAgICAgICBzdHlsZTogZnVuY3Rpb24oZmVhdHVyZSkge1xuICAgICAgICAgICAgcmV0dXJuIG9iamVjdFN0eWxlc1tmZWF0dXJlLnByb3BlcnRpZXMuX3MxXSA/IHsuLi5vYmplY3RTdHlsZXNbZmVhdHVyZS5wcm9wZXJ0aWVzLl9zMV19IDogey4uLmRlZmF1bHRPYmplY3RTdHlsZX1cbiAgICAgICAgfSxcbiAgICAgICAgb25FYWNoRmVhdHVyZTogZnVuY3Rpb24oZmVhdHVyZSwgbGF5ZXIpIHtcbiAgICAgICAgICAgIGlmIChmZWF0dXJlLnByb3BlcnRpZXMuX2JlaGF2aW9yID09PSAnaW5mbycgfHwgZmVhdHVyZS5wcm9wZXJ0aWVzLl9iZWhhdmlvciA9PT0gJ3N1cnZleScpIHtcbiAgICAgICAgICAgICAgICBsZXQgcG9wdXBDb250ZW50ID0gYDxwIGNsYXNzPVwidGV4dC1jZW50ZXJcIj4ke2ZlYXR1cmUucHJvcGVydGllcy50eXBlfTxiciAvPiR7ZmVhdHVyZS5wcm9wZXJ0aWVzLm5hbWV9PC9wPmA7XG4gICAgICAgICAgICAgICAgbGF5ZXIuYmluZFBvcHVwKHBvcHVwQ29udGVudCwge1xuICAgICAgICAgICAgICAgICAgICBjbG9zZUJ1dHRvbjogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgb2Zmc2V0OiBMLnBvaW50KDAsIC0yMClcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBsYXllci5nZXRQb3B1cCgpLm9uKCdyZW1vdmUnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICAgICAgY29uZmlybVBvcHVwLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgICAgIGxheWVyLm9uKCdjbGljaycsIGZ1bmN0aW9uIChldikge1xuICAgICAgICAgICAgICAgIHRha2VBY3Rpb24obGF5ZXIsIGV2KTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgaWYgKGZlYXR1cmUuZ2VvbWV0cnkudHlwZSAhPT0gJ1BvaW50Jykge1xuICAgICAgICAgICAgICAgIGxheWVyLm9uKCdtb3VzZW92ZXInLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIG1vdXNlRW50ZXIodGhpcylcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBsYXllci5vbignbW91c2VvdXQnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIG1vdXNlTGVhdmUodGhpcylcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSxcbiAgICAgICAgcG9pbnRUb0xheWVyOiBmdW5jdGlvbiAoZmVhdHVyZSwgbGF0bG5nKSB7XG4gICAgICAgICAgICByZXR1cm4gTC5jaXJjbGVNYXJrZXIobGF0bG5nLCB7XG4gICAgICAgICAgICAgICAgcmFkaXVzIDogOCxcbiAgICAgICAgICAgICAgICBmaWxsQ29sb3IgOiBcIiNmZjc4MDBcIixcbiAgICAgICAgICAgICAgICBjb2xvciA6IFwiIzAwMFwiLFxuICAgICAgICAgICAgICAgIHdlaWdodCA6IDEsXG4gICAgICAgICAgICAgICAgb3BhY2l0eSA6IDEsXG4gICAgICAgICAgICAgICAgZmlsbE9wYWNpdHkgOiAwLjhcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9XG4gICAgfSkuYWRkVG8obWFwKTtcblxuICAgIGNvbnN0IGNvbmZpcm1Qb3B1cCA9ICQoJy5jb25maXJtJyk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJ1tkYXRhLWNvbmZpcm0tY2FuY2VsXScsIGZ1bmN0aW9uKCkge1xuICAgICAgICBtYXAuY2xvc2VQb3B1cCgpO1xuICAgIH0pO1xuXG4gICAgZnVuY3Rpb24gdXBkYXRlTWFwKCkge1xuICAgICAgICBsZXQgem9vbSA9IG1hcC5nZXRab29tKCk7XG4gICAgICAgIGxldCBjb29yZHMgPSBtYXAuZ2V0Qm91bmRzKCk7XG5cbiAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgIGRhdGE6IHtcbiAgICAgICAgICAgICAgICBpbjogY29vcmRzLl9zb3V0aFdlc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubGF0ICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubGF0ICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubGF0ICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubGF0ICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9zb3V0aFdlc3QubG5nICsgJywnICtcbiAgICAgICAgICAgICAgICAgICAgY29vcmRzLl9ub3J0aEVhc3QubGF0LFxuICAgICAgICAgICAgICAgIHpvb206IHpvb21cbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB1cmw6IFwiL2Zyb250LWVuZC9tYXA/XCIsXG4gICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAocmVzdWx0cykge1xuICAgICAgICAgICAgICAgIG9iamVjdFN0eWxlcyA9IHJlc3VsdHMuc2V0dGluZ3Muc3R5bGVzO1xuICAgICAgICAgICAgICAgIGRpYWxvZ1RpdGxlcyA9IHJlc3VsdHMuc2V0dGluZ3MuZGlhbG9nO1xuICAgICAgICAgICAgICAgIGdlb0pzb25MYXllci5jbGVhckxheWVycygpO1xuICAgICAgICAgICAgICAgIGdlb0pzb25MYXllci5hZGREYXRhKHJlc3VsdHMub2JqZWN0cyk7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIHRha2VBY3Rpb24obGF5ZXIsIGV2KSB7XG4gICAgICAgIHN3aXRjaCAobGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLl9iZWhhdmlvcikge1xuICAgICAgICAgICAgY2FzZSAnaW5mbyc6XG4gICAgICAgICAgICAgICAgbGF5ZXIub3BlblBvcHVwKCk7XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgJ25hdmlnYXRpb24nOlxuICAgICAgICAgICAgICAgIGlmIChsYXllci5mZWF0dXJlLnByb3BlcnRpZXMuX3pvb20pIHtcbiAgICAgICAgICAgICAgICAgICAgbGV0IGNvb3JkcyA9IG1hcC5tb3VzZUV2ZW50VG9MYXRMbmcoZXYub3JpZ2luYWxFdmVudCk7XG4gICAgICAgICAgICAgICAgICAgIG1hcC5zZXRWaWV3KFtjb29yZHMubGF0LCBjb29yZHMubG5nXSwgbGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLl96b29tKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBtYXAuZml0Qm91bmRzKGxheWVyLmdldEJvdW5kcygpLCB7bWF4Wm9vbTogWzAsIDBdfSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICBjYXNlICdzdXJ2ZXknOlxuICAgICAgICAgICAgICAgIGxheWVyLm9wZW5Qb3B1cCgpO1xuICAgICAgICAgICAgICAgIGxldCBkaWFsb2dUaXRsZSA9IGRpYWxvZ1RpdGxlc1tsYXllci5mZWF0dXJlLnByb3BlcnRpZXMuX2R0ZXh0XSB8fCAn0JjRgdC60LDRgtC1INC70Lgg0LTQsCDQvtGG0LXQvdC40YLQtSc7XG4gICAgICAgICAgICAgICAgbGV0IGRpYWxvZ0xpbmsgPSAnL2dlby8nICsgbGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLmlkO1xuICAgICAgICAgICAgICAgIGNvbmZpcm1Qb3B1cC5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICAgICAgICAgICAgY29uZmlybVBvcHVwLmZpbmQoJ1tkYXRhLWNvbmZpcm0tdGl0bGVdJykuaHRtbChgJHtkaWFsb2dUaXRsZX0/YCk7XG4gICAgICAgICAgICAgICAgY29uZmlybVBvcHVwLmZpbmQoJ1tkYXRhLWNvbmZpcm0tbGlua10nKS5hdHRyKCdocmVmJywgZGlhbG9nTGluayk7XG4gICAgICAgICAgICBicmVhaztcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIG1vdXNlRW50ZXIobGF5ZXIpIHtcbiAgICAgICAgaWYgKG9iamVjdFN0eWxlc1tsYXllci5mZWF0dXJlLnByb3BlcnRpZXMuX3MyXSkge1xuICAgICAgICAgICAgbGF5ZXIuc2V0U3R5bGUob2JqZWN0U3R5bGVzW2xheWVyLmZlYXR1cmUucHJvcGVydGllcy5fczJdKTtcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIG1vdXNlTGVhdmUobGF5ZXIpIHtcbiAgICAgICAgaWYgKG9iamVjdFN0eWxlc1tsYXllci5mZWF0dXJlLnByb3BlcnRpZXMuX3MyXSkge1xuICAgICAgICAgICAgbGF5ZXIuc2V0U3R5bGUob2JqZWN0U3R5bGVzW2xheWVyLmZlYXR1cmUucHJvcGVydGllcy5fczFdIHx8IGRlZmF1bHRPYmplY3RTdHlsZSk7XG4gICAgICAgIH1cbiAgICB9XG59KSgpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIGFzc2V0cy1zcmMvZnJvbnQvanMvbWFwLW1haW4uanMiXSwibWFwcGluZ3MiOiI7Ozs7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBSEE7QUFLQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBREE7QUFDQTtBQUdBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBTkE7QUFRQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRkE7QUFJQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFOQTtBQVFBO0FBcENBO0FBQ0E7QUFzQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBVUE7QUFYQTtBQWFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBcEJBO0FBc0JBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFuQkE7QUFxQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///2\n");

/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nvar _mapConfig = __webpack_require__(0);\n\n(function () {\n    if (!document.getElementById('mapMini')) {\n        return;\n    }\n    var mapCenter = [42.697664, 23.3166103];\n    //TODO draw the object and fit to its bounds\n\n    var map = new L.map('mapMini', {\n        center: mapCenter,\n        zoom: 15,\n        updateWhenZooming: false\n    });\n\n    var mapStyle = L.tileLayer(_mapConfig.mapBoxUrl, {\n        attribution: _mapConfig.mapBoxAttribution,\n        maxNativeZoom: 19,\n        maxZoom: 20,\n        minZoom: 11,\n        updateWhenZooming: false\n    });\n    mapStyle.addTo(map);\n})();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMy5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21hcC1taW5pLmpzPzhhY2EiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHsgbWFwQm94QXR0cmlidXRpb24sIG1hcEJveFVybCB9IGZyb20gJy4vbWFwLWNvbmZpZyc7XG5cbigoKSA9PiB7XG4gICAgaWYgKCFkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbWFwTWluaScpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG4gICAgY29uc3QgbWFwQ2VudGVyID0gWzQyLjY5NzY2NCwyMy4zMTY2MTAzXTtcbiAgICAvL1RPRE8gZHJhdyB0aGUgb2JqZWN0IGFuZCBmaXQgdG8gaXRzIGJvdW5kc1xuXG4gICAgbGV0IG1hcCA9IG5ldyBMLm1hcCgnbWFwTWluaScsIHtcbiAgICAgICAgY2VudGVyOiBtYXBDZW50ZXIsXG4gICAgICAgIHpvb206IDE1LFxuICAgICAgICB1cGRhdGVXaGVuWm9vbWluZzogZmFsc2VcbiAgICB9KTtcblxuICAgIGxldCBtYXBTdHlsZSA9IEwudGlsZUxheWVyKG1hcEJveFVybCwge1xuICAgICAgICBhdHRyaWJ1dGlvbjogbWFwQm94QXR0cmlidXRpb24sXG4gICAgICAgIG1heE5hdGl2ZVpvb206IDE5LFxuICAgICAgICBtYXhab29tOiAyMCxcbiAgICAgICAgbWluWm9vbTogMTEsXG4gICAgICAgIHVwZGF0ZVdoZW5ab29taW5nOiBmYWxzZVxuICAgIH0pO1xuICAgIG1hcFN0eWxlLmFkZFRvKG1hcCk7XG59KSgpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIGFzc2V0cy1zcmMvZnJvbnQvanMvbWFwLW1pbmkuanMiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFIQTtBQUNBO0FBS0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBTEE7QUFPQTtBQUNBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///3\n");

/***/ })
/******/ ]);