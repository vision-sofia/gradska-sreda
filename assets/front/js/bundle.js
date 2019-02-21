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
eval("\n\nvar _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };\n\nvar _mapConfig = __webpack_require__(0);\n\n(function () {\n    if (!document.getElementById('mapMain')) {\n        return;\n    }\n    var mapCenter = [42.697664, 23.3166103];\n    var defaultObjectStyle = {\n        color: \"#ff9710\",\n        opacity: 0.5,\n        width: 5\n    };\n    var objectsSettings = {};\n\n    var map = new L.map('mapMain', {\n        updateWhenZooming: false\n    });\n\n    var mapStyle = L.tileLayer(_mapConfig.mapBoxUrl, {\n        attribution: _mapConfig.mapBoxAttribution,\n        maxNativeZoom: 19,\n        maxZoom: 20,\n        minZoom: 11,\n        updateWhenZooming: false\n    });\n    mapStyle.addTo(map);\n\n    var updateMapThrottle = void 0;\n    map.on('dragend zoomend', function () {\n        clearTimeout(updateMapThrottle);\n        updateMapThrottle = setTimeout(updateMap, 200);\n    });\n    map.on('load', function () {\n        clearTimeout(updateMapThrottle);\n        updateMapThrottle = setTimeout(function () {\n            updateMap(function () {\n                locate();\n            });\n        }, 200);\n    });\n\n    map.setView(mapCenter, 17);\n    map.on('locationfound', onLocationFound);\n    map.on('locationerror', onLocationError);\n\n    var geoJsonLayer = L.geoJSON([], {\n        style: function style(feature) {\n            return objectsSettings.styles[feature.properties._s1] ? _extends({}, objectsSettings.styles[feature.properties._s1]) : _extends({}, defaultObjectStyle);\n        },\n        onEachFeature: function onEachFeature(feature, layer) {\n            if (feature.properties._behavior === 'info' || feature.properties._behavior === 'survey') {\n                var popupContent = '<p class=\"text-center\">' + feature.properties.type + '<br />' + feature.properties.name + '</p>';\n                layer.bindPopup(popupContent, {\n                    closeButton: true,\n                    offset: L.point(0, -20)\n                });\n                layer.on('popupclose', function () {\n                    confirmPopup.addClass('d-none');\n                    setLayerDefaultStyle(layer);\n                });\n                layer.on('popupopen', function () {\n                    setLayerActiveStyle(layer);\n                });\n            }\n            layer.on('click', function (ev) {\n                takeAction(layer, ev);\n            });\n            layer.on('mouseover', function () {\n                if (layer._popup && layer._popup.isOpen()) {\n                    return;\n                }\n                if (objectsSettings.styles[feature.properties._s2]) {\n                    setLayerHoverStyle(layer);\n                }\n            });\n            layer.on('mouseout', function () {\n                if (layer._popup && layer._popup.isOpen()) {\n                    return;\n                }\n                if (objectsSettings.styles[feature.properties._s1]) {\n                    setLayerDefaultStyle(layer);\n                }\n            });\n        },\n        pointToLayer: function pointToLayer(feature, latlng) {\n            return L.circleMarker(latlng, objectsSettings.styles[feature.properties._s1]);\n        }\n    }).addTo(map);\n\n    var confirmPopup = $('.confirm');\n    $(document).on('click', '[data-confirm-cancel]', function () {\n        map.closePopup();\n    });\n    var loading = $('.loading');\n\n    function updateMap() {\n        var fn = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : function () {};\n\n        var zoom = map.getZoom();\n        var coords = map.getBounds();\n\n        $.ajax({\n            data: {\n                in: coords._southWest.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._northEast.lat,\n                zoom: zoom\n            },\n            url: \"/front-end/map?\",\n            success: function success(results) {\n                objectsSettings = results.settings;\n                console.log(objectsSettings);\n                geoJsonLayer.clearLayers();\n                geoJsonLayer.addData(results.objects);\n                fn();\n            }\n        });\n    }\n\n    function takeAction(layer, ev) {\n        switch (layer.feature.properties._behavior) {\n            case 'info':\n                layer.openPopup();\n                break;\n            case 'navigation':\n                if (layer.feature.properties._zoom) {\n                    var coords = map.mouseEventToLatLng(ev.originalEvent);\n                    map.setView([coords.lat, coords.lng], layer.feature.properties._zoom);\n                } else {\n                    map.fitBounds(layer.getBounds(), { maxZoom: [0, 0] });\n                }\n                break;\n            case 'survey':\n                layer.openPopup();\n                var dialogTitle = objectsSettings.dialog[layer.feature.properties._dtext] || 'Искате ли да оцените';\n                var dialogLink = '/geo/' + layer.feature.properties.id;\n                confirmPopup.removeClass('d-none');\n                confirmPopup.find('[data-confirm-title]').html(dialogTitle + '?');\n                confirmPopup.find('[data-confirm-link]').attr('href', dialogLink);\n                break;\n        }\n    }\n\n    function setLayerDefaultStyle(layer) {\n        layer.setStyle(objectsSettings.styles[layer.feature.properties._s1] || defaultObjectStyle);\n    }\n\n    function setLayerHoverStyle(layer) {\n        layer.setStyle(objectsSettings.styles[layer.feature.properties._s2]);\n    }\n\n    function setLayerActiveStyle(layer) {\n        switch (layer.feature.geometry.type) {\n            case \"Point\":\n                layer.setStyle(objectsSettings.styles['on_dialog_point']);\n                break;\n            case \"MultiLineString\":\n                layer.setStyle(objectsSettings.styles['on_dialog_line']);\n                break;\n            case \"Polygon\":\n                layer.setStyle(objectsSettings.styles['on_dialog_polygon']);\n                break;\n        }\n    }\n\n    function locate() {\n        loading.removeClass('d-none');\n        map.locate({ setView: true });\n    }\n\n    function onLocationFound(e) {\n        loading.addClass('d-none');\n        var radius = e.accuracy / 2;\n\n        var center = L.circle(e.latlng, {\n            color: '#fff',\n            fillColor: '#2A93EE',\n            fillOpacity: 1,\n            weight: 4,\n            opacity: 1,\n            radius: 5\n        }).addTo(map);\n\n        L.circle(e.latlng, {\n            radius: e.accuracy / 2,\n            color: '#136AEC',\n            fillColor: '#136AEC',\n            fillOpacity: 0.15,\n            weight: 0\n        }).addTo(map);\n\n        center.bindPopup(\"Намирате се в радиус от \" + radius + \" метра от тази локация\", {\n            offset: L.point(0, -10)\n        });\n        center.openPopup();\n\n        map.setZoom(objectsSettings.default_zoom);\n    }\n\n    function onLocationError() {\n        loading.addClass('d-none');\n        map.setView(mapCenter, 17);\n    }\n})();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMi5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21hcC1tYWluLmpzPzVhZWIiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHsgbWFwQm94QXR0cmlidXRpb24sIG1hcEJveFVybCB9IGZyb20gJy4vbWFwLWNvbmZpZyc7XG5cbigoKSA9PiB7XG4gICAgaWYgKCFkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbWFwTWFpbicpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG4gICAgY29uc3QgbWFwQ2VudGVyID0gWzQyLjY5NzY2NCwgMjMuMzE2NjEwM107XG4gICAgY29uc3QgZGVmYXVsdE9iamVjdFN0eWxlID0ge1xuICAgICAgICBjb2xvcjogXCIjZmY5NzEwXCIsXG4gICAgICAgIG9wYWNpdHk6IDAuNSxcbiAgICAgICAgd2lkdGg6IDVcbiAgICB9O1xuICAgIGxldCBvYmplY3RzU2V0dGluZ3MgPSB7fTtcblxuICAgIGxldCBtYXAgPSBuZXcgTC5tYXAoJ21hcE1haW4nLCB7XG4gICAgICAgIHVwZGF0ZVdoZW5ab29taW5nOiBmYWxzZVxuICAgIH0pO1xuXG4gICAgbGV0IG1hcFN0eWxlID0gTC50aWxlTGF5ZXIobWFwQm94VXJsLCB7XG4gICAgICAgIGF0dHJpYnV0aW9uOiBtYXBCb3hBdHRyaWJ1dGlvbixcbiAgICAgICAgbWF4TmF0aXZlWm9vbTogMTksXG4gICAgICAgIG1heFpvb206IDIwLFxuICAgICAgICBtaW5ab29tOiAxMSxcbiAgICAgICAgdXBkYXRlV2hlblpvb21pbmc6IGZhbHNlXG4gICAgfSk7XG4gICAgbWFwU3R5bGUuYWRkVG8obWFwKTtcblxuICAgIGxldCB1cGRhdGVNYXBUaHJvdHRsZTtcbiAgICBtYXAub24oJ2RyYWdlbmQgem9vbWVuZCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgY2xlYXJUaW1lb3V0KHVwZGF0ZU1hcFRocm90dGxlKTtcbiAgICAgICAgdXBkYXRlTWFwVGhyb3R0bGUgPSBzZXRUaW1lb3V0KHVwZGF0ZU1hcCwgMjAwKTtcbiAgICB9KTtcbiAgICBtYXAub24oJ2xvYWQnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgIGNsZWFyVGltZW91dCh1cGRhdGVNYXBUaHJvdHRsZSk7XG4gICAgICAgIHVwZGF0ZU1hcFRocm90dGxlID0gc2V0VGltZW91dCgoKSA9PiB7XG4gICAgICAgICAgICB1cGRhdGVNYXAoKCkgPT4ge1xuICAgICAgICAgICAgICAgIGxvY2F0ZSgpO1xuICAgICAgICAgICAgfSlcbiAgICAgICAgfSwgMjAwKTtcbiAgICB9KTtcblxuICAgIG1hcC5zZXRWaWV3KG1hcENlbnRlciwgMTcpO1xuICAgIG1hcC5vbignbG9jYXRpb25mb3VuZCcsIG9uTG9jYXRpb25Gb3VuZCk7XG4gICAgbWFwLm9uKCdsb2NhdGlvbmVycm9yJywgb25Mb2NhdGlvbkVycm9yKTtcblxuICAgIGxldCBnZW9Kc29uTGF5ZXIgPSBMLmdlb0pTT04oW10sIHtcbiAgICAgICAgc3R5bGU6IGZ1bmN0aW9uIChmZWF0dXJlKSB7XG4gICAgICAgICAgICByZXR1cm4gb2JqZWN0c1NldHRpbmdzLnN0eWxlc1tmZWF0dXJlLnByb3BlcnRpZXMuX3MxXSA/IHsuLi5vYmplY3RzU2V0dGluZ3Muc3R5bGVzW2ZlYXR1cmUucHJvcGVydGllcy5fczFdfSA6IHsuLi5kZWZhdWx0T2JqZWN0U3R5bGV9XG4gICAgICAgIH0sXG4gICAgICAgIG9uRWFjaEZlYXR1cmU6IGZ1bmN0aW9uIChmZWF0dXJlLCBsYXllcikge1xuICAgICAgICAgICAgaWYgKGZlYXR1cmUucHJvcGVydGllcy5fYmVoYXZpb3IgPT09ICdpbmZvJyB8fCBmZWF0dXJlLnByb3BlcnRpZXMuX2JlaGF2aW9yID09PSAnc3VydmV5Jykge1xuICAgICAgICAgICAgICAgIGxldCBwb3B1cENvbnRlbnQgPSBgPHAgY2xhc3M9XCJ0ZXh0LWNlbnRlclwiPiR7ZmVhdHVyZS5wcm9wZXJ0aWVzLnR5cGV9PGJyIC8+JHtmZWF0dXJlLnByb3BlcnRpZXMubmFtZX08L3A+YDtcbiAgICAgICAgICAgICAgICBsYXllci5iaW5kUG9wdXAocG9wdXBDb250ZW50LCB7XG4gICAgICAgICAgICAgICAgICAgIGNsb3NlQnV0dG9uOiB0cnVlLFxuICAgICAgICAgICAgICAgICAgICBvZmZzZXQ6IEwucG9pbnQoMCwgLTIwKVxuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIGxheWVyLm9uKCdwb3B1cGNsb3NlJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgICAgICBjb25maXJtUG9wdXAuYWRkQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgICAgICAgICAgICAgICBzZXRMYXllckRlZmF1bHRTdHlsZShsYXllcik7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgbGF5ZXIub24oJ3BvcHVwb3BlbicsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgc2V0TGF5ZXJBY3RpdmVTdHlsZShsYXllcik7XG4gICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICBsYXllci5vbignY2xpY2snLCBmdW5jdGlvbiAoZXYpIHtcbiAgICAgICAgICAgICAgICB0YWtlQWN0aW9uKGxheWVyLCBldik7XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIGxheWVyLm9uKCdtb3VzZW92ZXInLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgaWYgKGxheWVyLl9wb3B1cCAmJiBsYXllci5fcG9wdXAuaXNPcGVuKCkpIHtcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICBpZiAob2JqZWN0c1NldHRpbmdzLnN0eWxlc1tmZWF0dXJlLnByb3BlcnRpZXMuX3MyXSkge1xuICAgICAgICAgICAgICAgICAgICBzZXRMYXllckhvdmVyU3R5bGUobGF5ZXIpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgbGF5ZXIub24oJ21vdXNlb3V0JywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIGlmIChsYXllci5fcG9wdXAgJiYgbGF5ZXIuX3BvcHVwLmlzT3BlbigpKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgaWYgKG9iamVjdHNTZXR0aW5ncy5zdHlsZXNbZmVhdHVyZS5wcm9wZXJ0aWVzLl9zMV0pIHtcbiAgICAgICAgICAgICAgICAgICAgc2V0TGF5ZXJEZWZhdWx0U3R5bGUobGF5ZXIpO1xuICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9LFxuICAgICAgICBwb2ludFRvTGF5ZXI6IGZ1bmN0aW9uIChmZWF0dXJlLCBsYXRsbmcpIHtcbiAgICAgICAgICAgIHJldHVybiBMLmNpcmNsZU1hcmtlcihsYXRsbmcsIG9iamVjdHNTZXR0aW5ncy5zdHlsZXNbZmVhdHVyZS5wcm9wZXJ0aWVzLl9zMV0pO1xuICAgICAgICB9XG4gICAgfSkuYWRkVG8obWFwKTtcblxuICAgIGNvbnN0IGNvbmZpcm1Qb3B1cCA9ICQoJy5jb25maXJtJyk7XG4gICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgJ1tkYXRhLWNvbmZpcm0tY2FuY2VsXScsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgbWFwLmNsb3NlUG9wdXAoKTtcbiAgICB9KTtcbiAgICBjb25zdCBsb2FkaW5nID0gJCgnLmxvYWRpbmcnKTtcblxuICAgIGZ1bmN0aW9uIHVwZGF0ZU1hcChmbiA9ICgpID0+IHt9KSB7XG4gICAgICAgIGxldCB6b29tID0gbWFwLmdldFpvb20oKTtcbiAgICAgICAgbGV0IGNvb3JkcyA9IG1hcC5nZXRCb3VuZHMoKTtcblxuICAgICAgICAkLmFqYXgoe1xuICAgICAgICAgICAgZGF0YToge1xuICAgICAgICAgICAgICAgIGluOiBjb29yZHMuX3NvdXRoV2VzdC5sbmcgKyAnLCcgK1xuICAgICAgICAgICAgICAgICAgICBjb29yZHMuX25vcnRoRWFzdC5sYXQgKyAnLCcgK1xuICAgICAgICAgICAgICAgICAgICBjb29yZHMuX3NvdXRoV2VzdC5sbmcgKyAnLCcgK1xuICAgICAgICAgICAgICAgICAgICBjb29yZHMuX3NvdXRoV2VzdC5sYXQgKyAnLCcgK1xuICAgICAgICAgICAgICAgICAgICBjb29yZHMuX25vcnRoRWFzdC5sbmcgKyAnLCcgK1xuICAgICAgICAgICAgICAgICAgICBjb29yZHMuX3NvdXRoV2VzdC5sYXQgKyAnLCcgK1xuICAgICAgICAgICAgICAgICAgICBjb29yZHMuX25vcnRoRWFzdC5sbmcgKyAnLCcgK1xuICAgICAgICAgICAgICAgICAgICBjb29yZHMuX25vcnRoRWFzdC5sYXQgKyAnLCcgK1xuICAgICAgICAgICAgICAgICAgICBjb29yZHMuX3NvdXRoV2VzdC5sbmcgKyAnLCcgK1xuICAgICAgICAgICAgICAgICAgICBjb29yZHMuX25vcnRoRWFzdC5sYXQsXG4gICAgICAgICAgICAgICAgem9vbTogem9vbVxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIHVybDogXCIvZnJvbnQtZW5kL21hcD9cIixcbiAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uIChyZXN1bHRzKSB7XG4gICAgICAgICAgICAgICAgb2JqZWN0c1NldHRpbmdzID0gcmVzdWx0cy5zZXR0aW5ncztcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhvYmplY3RzU2V0dGluZ3MpO1xuICAgICAgICAgICAgICAgIGdlb0pzb25MYXllci5jbGVhckxheWVycygpO1xuICAgICAgICAgICAgICAgIGdlb0pzb25MYXllci5hZGREYXRhKHJlc3VsdHMub2JqZWN0cyk7XG4gICAgICAgICAgICAgICAgZm4oKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gdGFrZUFjdGlvbihsYXllciwgZXYpIHtcbiAgICAgICAgc3dpdGNoIChsYXllci5mZWF0dXJlLnByb3BlcnRpZXMuX2JlaGF2aW9yKSB7XG4gICAgICAgICAgICBjYXNlICdpbmZvJzpcbiAgICAgICAgICAgICAgICBsYXllci5vcGVuUG9wdXAoKTtcbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgJ25hdmlnYXRpb24nOlxuICAgICAgICAgICAgICAgIGlmIChsYXllci5mZWF0dXJlLnByb3BlcnRpZXMuX3pvb20pIHtcbiAgICAgICAgICAgICAgICAgICAgbGV0IGNvb3JkcyA9IG1hcC5tb3VzZUV2ZW50VG9MYXRMbmcoZXYub3JpZ2luYWxFdmVudCk7XG4gICAgICAgICAgICAgICAgICAgIG1hcC5zZXRWaWV3KFtjb29yZHMubGF0LCBjb29yZHMubG5nXSwgbGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLl96b29tKTtcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgICAgICBtYXAuZml0Qm91bmRzKGxheWVyLmdldEJvdW5kcygpLCB7bWF4Wm9vbTogWzAsIDBdfSk7XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgY2FzZSAnc3VydmV5JzpcbiAgICAgICAgICAgICAgICBsYXllci5vcGVuUG9wdXAoKTtcbiAgICAgICAgICAgICAgICBsZXQgZGlhbG9nVGl0bGUgPSBvYmplY3RzU2V0dGluZ3MuZGlhbG9nW2xheWVyLmZlYXR1cmUucHJvcGVydGllcy5fZHRleHRdIHx8ICfQmNGB0LrQsNGC0LUg0LvQuCDQtNCwINC+0YbQtdC90LjRgtC1JztcbiAgICAgICAgICAgICAgICBsZXQgZGlhbG9nTGluayA9ICcvZ2VvLycgKyBsYXllci5mZWF0dXJlLnByb3BlcnRpZXMuaWQ7XG4gICAgICAgICAgICAgICAgY29uZmlybVBvcHVwLnJlbW92ZUNsYXNzKCdkLW5vbmUnKTtcbiAgICAgICAgICAgICAgICBjb25maXJtUG9wdXAuZmluZCgnW2RhdGEtY29uZmlybS10aXRsZV0nKS5odG1sKGAke2RpYWxvZ1RpdGxlfT9gKTtcbiAgICAgICAgICAgICAgICBjb25maXJtUG9wdXAuZmluZCgnW2RhdGEtY29uZmlybS1saW5rXScpLmF0dHIoJ2hyZWYnLCBkaWFsb2dMaW5rKTtcbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIHNldExheWVyRGVmYXVsdFN0eWxlKGxheWVyKSB7XG4gICAgICAgIGxheWVyLnNldFN0eWxlKG9iamVjdHNTZXR0aW5ncy5zdHlsZXNbbGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLl9zMV0gfHwgZGVmYXVsdE9iamVjdFN0eWxlKVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIHNldExheWVySG92ZXJTdHlsZShsYXllcikge1xuICAgICAgICBsYXllci5zZXRTdHlsZShvYmplY3RzU2V0dGluZ3Muc3R5bGVzW2xheWVyLmZlYXR1cmUucHJvcGVydGllcy5fczJdKVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIHNldExheWVyQWN0aXZlU3R5bGUobGF5ZXIpIHtcbiAgICAgICAgc3dpdGNoIChsYXllci5mZWF0dXJlLmdlb21ldHJ5LnR5cGUpIHtcbiAgICAgICAgICAgIGNhc2UgXCJQb2ludFwiOlxuICAgICAgICAgICAgICAgIGxheWVyLnNldFN0eWxlKG9iamVjdHNTZXR0aW5ncy5zdHlsZXNbJ29uX2RpYWxvZ19wb2ludCddKTtcbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgXCJNdWx0aUxpbmVTdHJpbmdcIjpcbiAgICAgICAgICAgICAgICBsYXllci5zZXRTdHlsZShvYmplY3RzU2V0dGluZ3Muc3R5bGVzWydvbl9kaWFsb2dfbGluZSddKTtcbiAgICAgICAgICAgICAgICBicmVhaztcbiAgICAgICAgICAgIGNhc2UgXCJQb2x5Z29uXCI6XG4gICAgICAgICAgICAgICAgbGF5ZXIuc2V0U3R5bGUob2JqZWN0c1NldHRpbmdzLnN0eWxlc1snb25fZGlhbG9nX3BvbHlnb24nXSk7XG4gICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBsb2NhdGUoKSB7XG4gICAgICAgIGxvYWRpbmcucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgICBtYXAubG9jYXRlKHtzZXRWaWV3OiB0cnVlfSk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gb25Mb2NhdGlvbkZvdW5kKGUpIHtcbiAgICAgICAgbG9hZGluZy5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgICAgIGxldCByYWRpdXMgPSBlLmFjY3VyYWN5IC8gMjtcblxuICAgICAgICBsZXQgY2VudGVyID0gTC5jaXJjbGUoZS5sYXRsbmcsIHtcbiAgICAgICAgICAgIGNvbG9yOiAgICAgICAnI2ZmZicsXG4gICAgICAgICAgICBmaWxsQ29sb3I6ICAgJyMyQTkzRUUnLFxuICAgICAgICAgICAgZmlsbE9wYWNpdHk6IDEsXG4gICAgICAgICAgICB3ZWlnaHQ6ICAgICAgNCxcbiAgICAgICAgICAgIG9wYWNpdHk6ICAgICAxLFxuICAgICAgICAgICAgcmFkaXVzOiAgICAgIDVcbiAgICAgICAgfSkuYWRkVG8obWFwKTtcblxuICAgICAgICBMLmNpcmNsZShlLmxhdGxuZywge1xuICAgICAgICAgICAgcmFkaXVzOiAgICAgIGUuYWNjdXJhY3kgLyAyLFxuICAgICAgICAgICAgY29sb3I6ICAgICAgICcjMTM2QUVDJyxcbiAgICAgICAgICAgIGZpbGxDb2xvcjogICAnIzEzNkFFQycsXG4gICAgICAgICAgICBmaWxsT3BhY2l0eTogMC4xNSxcbiAgICAgICAgICAgIHdlaWdodDogICAgICAwXG4gICAgICAgIH0pLmFkZFRvKG1hcCk7XG5cbiAgICAgICAgY2VudGVyLmJpbmRQb3B1cChcItCd0LDQvNC40YDQsNGC0LUg0YHQtSDQsiDRgNCw0LTQuNGD0YEg0L7RgiBcIiArIHJhZGl1cyArIFwiINC80LXRgtGA0LAg0L7RgiDRgtCw0LfQuCDQu9C+0LrQsNGG0LjRj1wiLCB7XG4gICAgICAgICAgICAgICAgb2Zmc2V0OiBMLnBvaW50KDAsIC0xMClcbiAgICAgICAgICAgIH0pO1xuICAgICAgICBjZW50ZXIub3BlblBvcHVwKCk7XG5cbiAgICAgICAgbWFwLnNldFpvb20ob2JqZWN0c1NldHRpbmdzLmRlZmF1bHRfem9vbSk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gb25Mb2NhdGlvbkVycm9yKCkge1xuICAgICAgICBsb2FkaW5nLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgICAgbWFwLnNldFZpZXcobWFwQ2VudGVyLCAxNylcbiAgICB9XG59KSgpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIGFzc2V0cy1zcmMvZnJvbnQvanMvbWFwLW1haW4uanMiXSwibWFwcGluZ3MiOiI7Ozs7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBSEE7QUFLQTtBQUNBO0FBQ0E7QUFDQTtBQURBO0FBQ0E7QUFHQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFMQTtBQU9BO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUZBO0FBSUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUF6Q0E7QUFDQTtBQTJDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUFBO0FBQ0E7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFVQTtBQVhBO0FBYUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQXJCQTtBQXVCQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBbkJBO0FBcUJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQVRBO0FBV0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBTkE7QUFDQTtBQVFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUxBO0FBQ0E7QUFPQTtBQUNBO0FBREE7QUFHQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///2\n");

/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nvar _mapConfig = __webpack_require__(0);\n\n(function () {\n    if (!document.getElementById('mapMini')) {\n        return;\n    }\n    var mapCenter = [42.697664, 23.3166103];\n    //TODO draw the object and fit to its bounds\n\n    var map = new L.map('mapMini', {\n        center: mapCenter,\n        zoom: 15,\n        updateWhenZooming: false\n    });\n\n    var mapStyle = L.tileLayer(_mapConfig.mapBoxUrl, {\n        attribution: _mapConfig.mapBoxAttribution,\n        maxNativeZoom: 19,\n        maxZoom: 20,\n        minZoom: 11,\n        updateWhenZooming: false\n    });\n    mapStyle.addTo(map);\n})();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMy5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21hcC1taW5pLmpzPzhhY2EiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHsgbWFwQm94QXR0cmlidXRpb24sIG1hcEJveFVybCB9IGZyb20gJy4vbWFwLWNvbmZpZyc7XG5cbigoKSA9PiB7XG4gICAgaWYgKCFkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbWFwTWluaScpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG4gICAgY29uc3QgbWFwQ2VudGVyID0gWzQyLjY5NzY2NCwyMy4zMTY2MTAzXTtcbiAgICAvL1RPRE8gZHJhdyB0aGUgb2JqZWN0IGFuZCBmaXQgdG8gaXRzIGJvdW5kc1xuXG4gICAgbGV0IG1hcCA9IG5ldyBMLm1hcCgnbWFwTWluaScsIHtcbiAgICAgICAgY2VudGVyOiBtYXBDZW50ZXIsXG4gICAgICAgIHpvb206IDE1LFxuICAgICAgICB1cGRhdGVXaGVuWm9vbWluZzogZmFsc2VcbiAgICB9KTtcblxuICAgIGxldCBtYXBTdHlsZSA9IEwudGlsZUxheWVyKG1hcEJveFVybCwge1xuICAgICAgICBhdHRyaWJ1dGlvbjogbWFwQm94QXR0cmlidXRpb24sXG4gICAgICAgIG1heE5hdGl2ZVpvb206IDE5LFxuICAgICAgICBtYXhab29tOiAyMCxcbiAgICAgICAgbWluWm9vbTogMTEsXG4gICAgICAgIHVwZGF0ZVdoZW5ab29taW5nOiBmYWxzZVxuICAgIH0pO1xuICAgIG1hcFN0eWxlLmFkZFRvKG1hcCk7XG59KSgpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIGFzc2V0cy1zcmMvZnJvbnQvanMvbWFwLW1pbmkuanMiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFIQTtBQUNBO0FBS0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBTEE7QUFPQTtBQUNBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///3\n");

/***/ })
/******/ ]);