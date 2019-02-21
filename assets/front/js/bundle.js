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
eval("\n\nvar _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };\n\nvar _mapConfig = __webpack_require__(0);\n\n(function () {\n    if (!document.getElementById('mapMain')) {\n        return;\n    }\n    var loading = $('.loading');\n    var confirmPopup = $('.confirm');\n    $(document).on('click', '[data-confirm-cancel]', function () {\n        map.closePopup();\n    });\n    var mapCenter = [42.697664, 23.3166103];\n    var defaultObjectStyle = {\n        color: \"#ff9710\",\n        opacity: 0.5,\n        width: 5\n    };\n    var objectsSettings = {};\n\n    var map = new L.map('mapMain', {\n        updateWhenZooming: false\n    });\n\n    var mapStyle = L.tileLayer(_mapConfig.mapBoxUrl, {\n        attribution: _mapConfig.mapBoxAttribution,\n        maxNativeZoom: 19,\n        maxZoom: 20,\n        minZoom: 11,\n        updateWhenZooming: false\n    });\n    mapStyle.addTo(map);\n\n    var updateMapThrottle = void 0;\n    map.on('dragend zoomend', function () {\n        clearTimeout(updateMapThrottle);\n        updateMapThrottle = setTimeout(updateMap, 200);\n    });\n\n    map.on('load', function () {\n        updateMap(function () {\n            // locate();\n        });\n    });\n\n    var myLocationButton = L.Control.extend({\n        options: {\n            position: 'topleft'\n        },\n        onAdd: function onAdd() {\n            var container = L.DomUtil.create('button', 'leaflet-bar leaflet-control-custom far fa-dot-circle');\n            container.type = \"button\";\n            container.onclick = function () {\n                locate();\n            };\n            return container;\n        }\n    });\n    map.addControl(new myLocationButton());\n\n    var myLocationLayerGroup = L.layerGroup();\n    myLocationLayerGroup.addTo(map);\n\n    map.on('locationfound', setMapViewToMyLocation);\n    map.on('locationerror', setInitialMapView);\n\n    var geoJsonLayer = L.geoJSON([], {\n        style: function style(feature) {\n            return objectsSettings.styles[feature.properties._s1] ? _extends({}, objectsSettings.styles[feature.properties._s1]) : _extends({}, defaultObjectStyle);\n        },\n        onEachFeature: function onEachFeature(feature, layer) {\n            if (feature.properties._behavior === 'info' || feature.properties._behavior === 'survey') {\n                var popupContent = '<p class=\"text-center\">' + feature.properties.type + '<br />' + feature.properties.name + '</p>';\n                layer.bindPopup(popupContent, {\n                    closeButton: true,\n                    offset: L.point(0, -20)\n                });\n                layer.on('popupclose', function () {\n                    confirmPopup.addClass('d-none');\n                    setLayerDefaultStyle(layer);\n                });\n                layer.on('popupopen', function () {\n                    setLayerActiveStyle(layer);\n                });\n            }\n            layer.on('click', function (ev) {\n                takeAction(layer, ev);\n            });\n            layer.on('mouseover', function () {\n                if (layer._popup && layer._popup.isOpen()) {\n                    return;\n                }\n                if (objectsSettings.styles[feature.properties._s2]) {\n                    setLayerHoverStyle(layer);\n                }\n            });\n            layer.on('mouseout', function () {\n                if (layer._popup && layer._popup.isOpen()) {\n                    return;\n                }\n                if (objectsSettings.styles[feature.properties._s1]) {\n                    setLayerDefaultStyle(layer);\n                }\n            });\n        },\n        pointToLayer: function pointToLayer(feature, latlng) {\n            return L.circleMarker(latlng, objectsSettings.styles[feature.properties._s1]);\n        }\n    }).addTo(map);\n\n    setInitialMapView();\n\n    function updateMap() {\n        var fn = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : function () {};\n\n        var zoom = map.getZoom();\n        var coords = map.getBounds();\n\n        $.ajax({\n            data: {\n                in: coords._southWest.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._southWest.lat + ',' + coords._northEast.lng + ',' + coords._northEast.lat + ',' + coords._southWest.lng + ',' + coords._northEast.lat,\n                zoom: zoom\n            },\n            url: \"/front-end/map?\",\n            success: function success(results) {\n                objectsSettings = results.settings;\n                geoJsonLayer.clearLayers();\n                geoJsonLayer.addData(results.objects);\n                fn();\n            }\n        });\n    }\n\n    function takeAction(layer, ev) {\n        switch (layer.feature.properties._behavior) {\n            case 'info':\n                // zoomToLayer(layer, ev);\n                openLayerPopup(layer, ev);\n                break;\n            case 'navigation':\n                zoomToLayer(layer, ev);\n                break;\n            case 'survey':\n                // zoomToLayer(layer, ev);\n                openLayerPopup(layer, ev);\n                openConfirmPopup(layer);\n                break;\n        }\n    }\n\n    function setLayerDefaultStyle(layer) {\n        layer.setStyle(objectsSettings.styles[layer.feature.properties._s1] || defaultObjectStyle);\n    }\n\n    function setLayerHoverStyle(layer) {\n        layer.setStyle(objectsSettings.styles[layer.feature.properties._s2]);\n    }\n\n    function setLayerActiveStyle(layer) {\n        switch (layer.feature.geometry.type) {\n            case \"Point\":\n                layer.setStyle(objectsSettings.styles['on_dialog_point']);\n                break;\n            case \"MultiLineString\":\n                layer.setStyle(objectsSettings.styles['on_dialog_line']);\n                break;\n            case \"Polygon\":\n                layer.setStyle(objectsSettings.styles['on_dialog_polygon']);\n                break;\n        }\n    }\n\n    function zoomToLayer(layer, ev) {\n        if (layer.feature.properties._zoom) {\n            map.setView(ev.latlng, layer.feature.properties._zoom);\n        } else {\n            if (layer.feature.geometry.type === \"Point\") {\n                var markerBounds = L.latLngBounds([layer.getLatLng()]);\n                map.fitBounds(markerBounds);\n            } else {\n                map.fitBounds(layer.getBounds(), { maxZoom: [0, 0] });\n            }\n        }\n    }\n\n    function openLayerPopup(layer, ev) {\n        var popup = layer.getPopup();\n        popup.setLatLng(ev.latlng).openOn(map);\n    }\n\n    function openConfirmPopup(layer) {\n        var dialogTitle = objectsSettings.dialog[layer.feature.properties._dtext] || 'Искате ли да оцените';\n        var dialogLink = '/geo/' + layer.feature.properties.id;\n        confirmPopup.removeClass('d-none');\n        confirmPopup.find('[data-confirm-title]').html(dialogTitle + '?');\n        confirmPopup.find('[data-confirm-link]').attr('href', dialogLink);\n    }\n\n    function locate() {\n        loading.removeClass('d-none');\n        map.locate({\n            setView: true,\n            maxZoom: objectsSettings.default_zoom\n        });\n    }\n\n    function setMapViewToMyLocation(e) {\n        loading.addClass('d-none');\n        var radius = e.accuracy / 2;\n\n        myLocationLayerGroup.eachLayer(function (layer) {\n            myLocationLayerGroup.removeLayer(layer);\n        });\n\n        var center = L.circle(e.latlng, {\n            color: '#fff',\n            fillColor: '#2A93EE',\n            fillOpacity: 1,\n            weight: 4,\n            opacity: 1,\n            radius: 5\n        }).addTo(myLocationLayerGroup);\n\n        L.circle(e.latlng, {\n            radius: e.accuracy / 2,\n            color: '#136AEC',\n            fillColor: '#136AEC',\n            fillOpacity: 0.15,\n            weight: 0\n        }).addTo(myLocationLayerGroup);\n\n        center.bindPopup(\"Намирате се в радиус от \" + radius + \" метра от тази локация\", {\n            offset: L.point(0, -10)\n        });\n        center.openPopup();\n    }\n\n    function setInitialMapView() {\n        loading.addClass('d-none');\n        map.setView(mapCenter, 17);\n    }\n})();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMi5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21hcC1tYWluLmpzPzVhZWIiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHsgbWFwQm94QXR0cmlidXRpb24sIG1hcEJveFVybCB9IGZyb20gJy4vbWFwLWNvbmZpZyc7XG5cbigoKSA9PiB7XG4gICAgaWYgKCFkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbWFwTWFpbicpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG4gICAgY29uc3QgbG9hZGluZyA9ICQoJy5sb2FkaW5nJyk7XG4gICAgY29uc3QgY29uZmlybVBvcHVwID0gJCgnLmNvbmZpcm0nKTtcbiAgICAkKGRvY3VtZW50KS5vbignY2xpY2snLCAnW2RhdGEtY29uZmlybS1jYW5jZWxdJywgZnVuY3Rpb24gKCkge1xuICAgICAgICBtYXAuY2xvc2VQb3B1cCgpO1xuICAgIH0pO1xuICAgIGNvbnN0IG1hcENlbnRlciA9IFs0Mi42OTc2NjQsIDIzLjMxNjYxMDNdO1xuICAgIGNvbnN0IGRlZmF1bHRPYmplY3RTdHlsZSA9IHtcbiAgICAgICAgY29sb3I6IFwiI2ZmOTcxMFwiLFxuICAgICAgICBvcGFjaXR5OiAwLjUsXG4gICAgICAgIHdpZHRoOiA1XG4gICAgfTtcbiAgICBsZXQgb2JqZWN0c1NldHRpbmdzID0ge307XG5cbiAgICBsZXQgbWFwID0gbmV3IEwubWFwKCdtYXBNYWluJywge1xuICAgICAgICB1cGRhdGVXaGVuWm9vbWluZzogZmFsc2VcbiAgICB9KTtcblxuICAgIGxldCBtYXBTdHlsZSA9IEwudGlsZUxheWVyKG1hcEJveFVybCwge1xuICAgICAgICBhdHRyaWJ1dGlvbjogbWFwQm94QXR0cmlidXRpb24sXG4gICAgICAgIG1heE5hdGl2ZVpvb206IDE5LFxuICAgICAgICBtYXhab29tOiAyMCxcbiAgICAgICAgbWluWm9vbTogMTEsXG4gICAgICAgIHVwZGF0ZVdoZW5ab29taW5nOiBmYWxzZVxuICAgIH0pO1xuICAgIG1hcFN0eWxlLmFkZFRvKG1hcCk7XG5cbiAgICBsZXQgdXBkYXRlTWFwVGhyb3R0bGU7XG4gICAgbWFwLm9uKCdkcmFnZW5kIHpvb21lbmQnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgIGNsZWFyVGltZW91dCh1cGRhdGVNYXBUaHJvdHRsZSk7XG4gICAgICAgIHVwZGF0ZU1hcFRocm90dGxlID0gc2V0VGltZW91dCh1cGRhdGVNYXAsIDIwMCk7XG4gICAgfSk7XG5cbiAgICBtYXAub24oJ2xvYWQnLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgIHVwZGF0ZU1hcCgoKSA9PiB7XG4gICAgICAgICAgICAvLyBsb2NhdGUoKTtcbiAgICAgICAgfSk7XG4gICAgfSk7XG5cbiAgICBsZXQgbXlMb2NhdGlvbkJ1dHRvbiA9IEwuQ29udHJvbC5leHRlbmQoe1xuICAgICAgICBvcHRpb25zOiB7XG4gICAgICAgICAgICBwb3NpdGlvbjogJ3RvcGxlZnQnXG4gICAgICAgIH0sXG4gICAgICAgIG9uQWRkOiAoKSA9PiB7XG4gICAgICAgICAgICBsZXQgY29udGFpbmVyID0gTC5Eb21VdGlsLmNyZWF0ZSgnYnV0dG9uJywgJ2xlYWZsZXQtYmFyIGxlYWZsZXQtY29udHJvbC1jdXN0b20gZmFyIGZhLWRvdC1jaXJjbGUnKTtcbiAgICAgICAgICAgIGNvbnRhaW5lci50eXBlID0gXCJidXR0b25cIjtcbiAgICAgICAgICAgIGNvbnRhaW5lci5vbmNsaWNrID0gZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIGxvY2F0ZSgpO1xuICAgICAgICAgICAgfTtcbiAgICAgICAgICAgIHJldHVybiBjb250YWluZXI7XG4gICAgICAgIH1cbiAgICB9KTtcbiAgICBtYXAuYWRkQ29udHJvbChuZXcgbXlMb2NhdGlvbkJ1dHRvbigpKTtcblxuICAgIGxldCBteUxvY2F0aW9uTGF5ZXJHcm91cCA9IEwubGF5ZXJHcm91cCgpO1xuICAgIG15TG9jYXRpb25MYXllckdyb3VwLmFkZFRvKG1hcCk7XG5cbiAgICBtYXAub24oJ2xvY2F0aW9uZm91bmQnLCBzZXRNYXBWaWV3VG9NeUxvY2F0aW9uKTtcbiAgICBtYXAub24oJ2xvY2F0aW9uZXJyb3InLCBzZXRJbml0aWFsTWFwVmlldyk7XG5cbiAgICBsZXQgZ2VvSnNvbkxheWVyID0gTC5nZW9KU09OKFtdLCB7XG4gICAgICAgIHN0eWxlOiBmdW5jdGlvbiAoZmVhdHVyZSkge1xuICAgICAgICAgICAgcmV0dXJuIG9iamVjdHNTZXR0aW5ncy5zdHlsZXNbZmVhdHVyZS5wcm9wZXJ0aWVzLl9zMV0gPyB7Li4ub2JqZWN0c1NldHRpbmdzLnN0eWxlc1tmZWF0dXJlLnByb3BlcnRpZXMuX3MxXX0gOiB7Li4uZGVmYXVsdE9iamVjdFN0eWxlfVxuICAgICAgICB9LFxuICAgICAgICBvbkVhY2hGZWF0dXJlOiBmdW5jdGlvbiAoZmVhdHVyZSwgbGF5ZXIpIHtcbiAgICAgICAgICAgIGlmIChmZWF0dXJlLnByb3BlcnRpZXMuX2JlaGF2aW9yID09PSAnaW5mbycgfHwgZmVhdHVyZS5wcm9wZXJ0aWVzLl9iZWhhdmlvciA9PT0gJ3N1cnZleScpIHtcbiAgICAgICAgICAgICAgICBsZXQgcG9wdXBDb250ZW50ID0gYDxwIGNsYXNzPVwidGV4dC1jZW50ZXJcIj4ke2ZlYXR1cmUucHJvcGVydGllcy50eXBlfTxiciAvPiR7ZmVhdHVyZS5wcm9wZXJ0aWVzLm5hbWV9PC9wPmA7XG4gICAgICAgICAgICAgICAgbGF5ZXIuYmluZFBvcHVwKHBvcHVwQ29udGVudCwge1xuICAgICAgICAgICAgICAgICAgICBjbG9zZUJ1dHRvbjogdHJ1ZSxcbiAgICAgICAgICAgICAgICAgICAgb2Zmc2V0OiBMLnBvaW50KDAsIC0yMClcbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgICAgICBsYXllci5vbigncG9wdXBjbG9zZScsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICAgICAgY29uZmlybVBvcHVwLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgICAgICAgICAgICAgICAgc2V0TGF5ZXJEZWZhdWx0U3R5bGUobGF5ZXIpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgICAgIGxheWVyLm9uKCdwb3B1cG9wZW4nLCBmdW5jdGlvbiAoKSB7XG4gICAgICAgICAgICAgICAgICAgIHNldExheWVyQWN0aXZlU3R5bGUobGF5ZXIpO1xuICAgICAgICAgICAgICAgIH0pO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgbGF5ZXIub24oJ2NsaWNrJywgZnVuY3Rpb24gKGV2KSB7XG4gICAgICAgICAgICAgICAgdGFrZUFjdGlvbihsYXllciwgZXYpO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICBsYXllci5vbignbW91c2VvdmVyJywgZnVuY3Rpb24gKCkge1xuICAgICAgICAgICAgICAgIGlmIChsYXllci5fcG9wdXAgJiYgbGF5ZXIuX3BvcHVwLmlzT3BlbigpKSB7XG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgaWYgKG9iamVjdHNTZXR0aW5ncy5zdHlsZXNbZmVhdHVyZS5wcm9wZXJ0aWVzLl9zMl0pIHtcbiAgICAgICAgICAgICAgICAgICAgc2V0TGF5ZXJIb3ZlclN0eWxlKGxheWVyKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIGxheWVyLm9uKCdtb3VzZW91dCcsIGZ1bmN0aW9uICgpIHtcbiAgICAgICAgICAgICAgICBpZiAobGF5ZXIuX3BvcHVwICYmIGxheWVyLl9wb3B1cC5pc09wZW4oKSkge1xuICAgICAgICAgICAgICAgICAgICByZXR1cm47XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgIGlmIChvYmplY3RzU2V0dGluZ3Muc3R5bGVzW2ZlYXR1cmUucHJvcGVydGllcy5fczFdKSB7XG4gICAgICAgICAgICAgICAgICAgIHNldExheWVyRGVmYXVsdFN0eWxlKGxheWVyKTtcbiAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICB9KTtcbiAgICAgICAgfSxcbiAgICAgICAgcG9pbnRUb0xheWVyOiBmdW5jdGlvbiAoZmVhdHVyZSwgbGF0bG5nKSB7XG4gICAgICAgICAgICByZXR1cm4gTC5jaXJjbGVNYXJrZXIobGF0bG5nLCBvYmplY3RzU2V0dGluZ3Muc3R5bGVzW2ZlYXR1cmUucHJvcGVydGllcy5fczFdKTtcbiAgICAgICAgfVxuICAgIH0pLmFkZFRvKG1hcCk7XG5cbiAgICBzZXRJbml0aWFsTWFwVmlldygpO1xuXG4gICAgZnVuY3Rpb24gdXBkYXRlTWFwKGZuID0gKCkgPT4ge30pIHtcbiAgICAgICAgbGV0IHpvb20gPSBtYXAuZ2V0Wm9vbSgpO1xuICAgICAgICBsZXQgY29vcmRzID0gbWFwLmdldEJvdW5kcygpO1xuXG4gICAgICAgICQuYWpheCh7XG4gICAgICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgICAgICAgaW46IGNvb3Jkcy5fc291dGhXZXN0LmxuZyArICcsJyArXG4gICAgICAgICAgICAgICAgICAgIGNvb3Jkcy5fbm9ydGhFYXN0LmxhdCArICcsJyArXG4gICAgICAgICAgICAgICAgICAgIGNvb3Jkcy5fc291dGhXZXN0LmxuZyArICcsJyArXG4gICAgICAgICAgICAgICAgICAgIGNvb3Jkcy5fc291dGhXZXN0LmxhdCArICcsJyArXG4gICAgICAgICAgICAgICAgICAgIGNvb3Jkcy5fbm9ydGhFYXN0LmxuZyArICcsJyArXG4gICAgICAgICAgICAgICAgICAgIGNvb3Jkcy5fc291dGhXZXN0LmxhdCArICcsJyArXG4gICAgICAgICAgICAgICAgICAgIGNvb3Jkcy5fbm9ydGhFYXN0LmxuZyArICcsJyArXG4gICAgICAgICAgICAgICAgICAgIGNvb3Jkcy5fbm9ydGhFYXN0LmxhdCArICcsJyArXG4gICAgICAgICAgICAgICAgICAgIGNvb3Jkcy5fc291dGhXZXN0LmxuZyArICcsJyArXG4gICAgICAgICAgICAgICAgICAgIGNvb3Jkcy5fbm9ydGhFYXN0LmxhdCxcbiAgICAgICAgICAgICAgICB6b29tOiB6b29tXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgdXJsOiBcIi9mcm9udC1lbmQvbWFwP1wiLFxuICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24gKHJlc3VsdHMpIHtcbiAgICAgICAgICAgICAgICBvYmplY3RzU2V0dGluZ3MgPSByZXN1bHRzLnNldHRpbmdzO1xuICAgICAgICAgICAgICAgIGdlb0pzb25MYXllci5jbGVhckxheWVycygpO1xuICAgICAgICAgICAgICAgIGdlb0pzb25MYXllci5hZGREYXRhKHJlc3VsdHMub2JqZWN0cyk7XG4gICAgICAgICAgICAgICAgZm4oKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gdGFrZUFjdGlvbihsYXllciwgZXYpIHtcbiAgICAgICAgc3dpdGNoIChsYXllci5mZWF0dXJlLnByb3BlcnRpZXMuX2JlaGF2aW9yKSB7XG4gICAgICAgICAgICBjYXNlICdpbmZvJzpcbiAgICAgICAgICAgICAgICAvLyB6b29tVG9MYXllcihsYXllciwgZXYpO1xuICAgICAgICAgICAgICAgIG9wZW5MYXllclBvcHVwKGxheWVyLCBldik7XG4gICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICBjYXNlICduYXZpZ2F0aW9uJzpcbiAgICAgICAgICAgICAgICB6b29tVG9MYXllcihsYXllciwgZXYpO1xuICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICAgICAgY2FzZSAnc3VydmV5JzpcbiAgICAgICAgICAgICAgICAvLyB6b29tVG9MYXllcihsYXllciwgZXYpO1xuICAgICAgICAgICAgICAgIG9wZW5MYXllclBvcHVwKGxheWVyLCBldik7XG4gICAgICAgICAgICAgICAgb3BlbkNvbmZpcm1Qb3B1cChsYXllcik7XG4gICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgIH1cbiAgICB9XG5cbiAgICBmdW5jdGlvbiBzZXRMYXllckRlZmF1bHRTdHlsZShsYXllcikge1xuICAgICAgICBsYXllci5zZXRTdHlsZShvYmplY3RzU2V0dGluZ3Muc3R5bGVzW2xheWVyLmZlYXR1cmUucHJvcGVydGllcy5fczFdIHx8IGRlZmF1bHRPYmplY3RTdHlsZSlcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBzZXRMYXllckhvdmVyU3R5bGUobGF5ZXIpIHtcbiAgICAgICAgbGF5ZXIuc2V0U3R5bGUob2JqZWN0c1NldHRpbmdzLnN0eWxlc1tsYXllci5mZWF0dXJlLnByb3BlcnRpZXMuX3MyXSlcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBzZXRMYXllckFjdGl2ZVN0eWxlKGxheWVyKSB7XG4gICAgICAgIHN3aXRjaCAobGF5ZXIuZmVhdHVyZS5nZW9tZXRyeS50eXBlKSB7XG4gICAgICAgICAgICBjYXNlIFwiUG9pbnRcIjpcbiAgICAgICAgICAgICAgICBsYXllci5zZXRTdHlsZShvYmplY3RzU2V0dGluZ3Muc3R5bGVzWydvbl9kaWFsb2dfcG9pbnQnXSk7XG4gICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICBjYXNlIFwiTXVsdGlMaW5lU3RyaW5nXCI6XG4gICAgICAgICAgICAgICAgbGF5ZXIuc2V0U3R5bGUob2JqZWN0c1NldHRpbmdzLnN0eWxlc1snb25fZGlhbG9nX2xpbmUnXSk7XG4gICAgICAgICAgICAgICAgYnJlYWs7XG4gICAgICAgICAgICBjYXNlIFwiUG9seWdvblwiOlxuICAgICAgICAgICAgICAgIGxheWVyLnNldFN0eWxlKG9iamVjdHNTZXR0aW5ncy5zdHlsZXNbJ29uX2RpYWxvZ19wb2x5Z29uJ10pO1xuICAgICAgICAgICAgICAgIGJyZWFrO1xuICAgICAgICB9XG4gICAgfVxuXG4gICAgZnVuY3Rpb24gem9vbVRvTGF5ZXIobGF5ZXIsIGV2KSB7XG4gICAgICAgIGlmIChsYXllci5mZWF0dXJlLnByb3BlcnRpZXMuX3pvb20pIHtcbiAgICAgICAgICAgIG1hcC5zZXRWaWV3KGV2LmxhdGxuZywgbGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLl96b29tKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIGlmIChsYXllci5mZWF0dXJlLmdlb21ldHJ5LnR5cGUgPT09IFwiUG9pbnRcIikge1xuICAgICAgICAgICAgICAgIGxldCBtYXJrZXJCb3VuZHMgPSBMLmxhdExuZ0JvdW5kcyhbbGF5ZXIuZ2V0TGF0TG5nKCldKTtcbiAgICAgICAgICAgICAgICBtYXAuZml0Qm91bmRzKG1hcmtlckJvdW5kcyk7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIG1hcC5maXRCb3VuZHMobGF5ZXIuZ2V0Qm91bmRzKCksIHttYXhab29tOiBbMCwgMF19KTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgIH1cblxuICAgIGZ1bmN0aW9uIG9wZW5MYXllclBvcHVwKGxheWVyLCBldikge1xuICAgICAgICBsZXQgcG9wdXAgPSBsYXllci5nZXRQb3B1cCgpO1xuICAgICAgICBwb3B1cC5zZXRMYXRMbmcoZXYubGF0bG5nKS5vcGVuT24obWFwKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBvcGVuQ29uZmlybVBvcHVwKGxheWVyKSB7XG4gICAgICAgIGxldCBkaWFsb2dUaXRsZSA9IG9iamVjdHNTZXR0aW5ncy5kaWFsb2dbbGF5ZXIuZmVhdHVyZS5wcm9wZXJ0aWVzLl9kdGV4dF0gfHwgJ9CY0YHQutCw0YLQtSDQu9C4INC00LAg0L7RhtC10L3QuNGC0LUnO1xuICAgICAgICBsZXQgZGlhbG9nTGluayA9ICcvZ2VvLycgKyBsYXllci5mZWF0dXJlLnByb3BlcnRpZXMuaWQ7XG4gICAgICAgIGNvbmZpcm1Qb3B1cC5yZW1vdmVDbGFzcygnZC1ub25lJyk7XG4gICAgICAgIGNvbmZpcm1Qb3B1cC5maW5kKCdbZGF0YS1jb25maXJtLXRpdGxlXScpLmh0bWwoYCR7ZGlhbG9nVGl0bGV9P2ApO1xuICAgICAgICBjb25maXJtUG9wdXAuZmluZCgnW2RhdGEtY29uZmlybS1saW5rXScpLmF0dHIoJ2hyZWYnLCBkaWFsb2dMaW5rKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBsb2NhdGUoKSB7XG4gICAgICAgIGxvYWRpbmcucmVtb3ZlQ2xhc3MoJ2Qtbm9uZScpO1xuICAgICAgICBtYXAubG9jYXRlKHtcbiAgICAgICAgICAgIHNldFZpZXc6IHRydWUsXG4gICAgICAgICAgICBtYXhab29tOiBvYmplY3RzU2V0dGluZ3MuZGVmYXVsdF96b29tXG4gICAgICAgIH0pO1xuICAgIH1cblxuICAgIGZ1bmN0aW9uIHNldE1hcFZpZXdUb015TG9jYXRpb24oZSkge1xuICAgICAgICBsb2FkaW5nLmFkZENsYXNzKCdkLW5vbmUnKTtcbiAgICAgICAgbGV0IHJhZGl1cyA9IGUuYWNjdXJhY3kgLyAyO1xuXG4gICAgICAgIG15TG9jYXRpb25MYXllckdyb3VwLmVhY2hMYXllcigobGF5ZXIpID0+IHtcbiAgICAgICAgICAgIG15TG9jYXRpb25MYXllckdyb3VwLnJlbW92ZUxheWVyKGxheWVyKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgbGV0IGNlbnRlciA9IEwuY2lyY2xlKGUubGF0bG5nLCB7XG4gICAgICAgICAgICBjb2xvcjogICAgICAgJyNmZmYnLFxuICAgICAgICAgICAgZmlsbENvbG9yOiAgICcjMkE5M0VFJyxcbiAgICAgICAgICAgIGZpbGxPcGFjaXR5OiAxLFxuICAgICAgICAgICAgd2VpZ2h0OiAgICAgIDQsXG4gICAgICAgICAgICBvcGFjaXR5OiAgICAgMSxcbiAgICAgICAgICAgIHJhZGl1czogICAgICA1XG4gICAgICAgIH0pLmFkZFRvKG15TG9jYXRpb25MYXllckdyb3VwKTtcblxuICAgICAgICBMLmNpcmNsZShlLmxhdGxuZywge1xuICAgICAgICAgICAgcmFkaXVzOiAgICAgIGUuYWNjdXJhY3kgLyAyLFxuICAgICAgICAgICAgY29sb3I6ICAgICAgICcjMTM2QUVDJyxcbiAgICAgICAgICAgIGZpbGxDb2xvcjogICAnIzEzNkFFQycsXG4gICAgICAgICAgICBmaWxsT3BhY2l0eTogMC4xNSxcbiAgICAgICAgICAgIHdlaWdodDogICAgICAwXG4gICAgICAgIH0pLmFkZFRvKG15TG9jYXRpb25MYXllckdyb3VwKTtcblxuICAgICAgICBjZW50ZXIuYmluZFBvcHVwKFwi0J3QsNC80LjRgNCw0YLQtSDRgdC1INCyINGA0LDQtNC40YPRgSDQvtGCIFwiICsgcmFkaXVzICsgXCIg0LzQtdGC0YDQsCDQvtGCINGC0LDQt9C4INC70L7QutCw0YbQuNGPXCIsIHtcbiAgICAgICAgICAgICAgICBvZmZzZXQ6IEwucG9pbnQoMCwgLTEwKVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIGNlbnRlci5vcGVuUG9wdXAoKTtcbiAgICB9XG5cbiAgICBmdW5jdGlvbiBzZXRJbml0aWFsTWFwVmlldygpIHtcbiAgICAgICAgbG9hZGluZy5hZGRDbGFzcygnZC1ub25lJyk7XG4gICAgICAgIG1hcC5zZXRWaWV3KG1hcENlbnRlciwgMTcpXG4gICAgfVxufSkoKTtcblxuXG5cbi8vIFdFQlBBQ0sgRk9PVEVSIC8vXG4vLyBhc3NldHMtc3JjL2Zyb250L2pzL21hcC1tYWluLmpzIl0sIm1hcHBpbmdzIjoiOzs7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFIQTtBQUtBO0FBQ0E7QUFDQTtBQUNBO0FBREE7QUFDQTtBQUdBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUxBO0FBT0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQURBO0FBR0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQVhBO0FBYUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRkE7QUFJQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQXpDQTtBQUNBO0FBMkNBO0FBQ0E7QUFDQTtBQUFBO0FBQ0E7QUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFVQTtBQVhBO0FBYUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFwQkE7QUFzQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFaQTtBQWNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQVRBO0FBV0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRkE7QUFJQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBTkE7QUFDQTtBQVFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUxBO0FBQ0E7QUFPQTtBQUNBO0FBREE7QUFHQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///2\n");

/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";
eval("\n\nvar _mapConfig = __webpack_require__(0);\n\n(function () {\n    if (!document.getElementById('mapMini')) {\n        return;\n    }\n    var mapCenter = [42.697664, 23.3166103];\n    //TODO draw the object and fit to its bounds\n\n    var map = new L.map('mapMini', {\n        center: mapCenter,\n        zoom: 15,\n        updateWhenZooming: false\n    });\n\n    var mapStyle = L.tileLayer(_mapConfig.mapBoxUrl, {\n        attribution: _mapConfig.mapBoxAttribution,\n        maxNativeZoom: 19,\n        maxZoom: 20,\n        minZoom: 11,\n        updateWhenZooming: false\n    });\n    mapStyle.addTo(map);\n})();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiMy5qcyIsInNvdXJjZXMiOlsid2VicGFjazovLy9hc3NldHMtc3JjL2Zyb250L2pzL21hcC1taW5pLmpzPzhhY2EiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0IHsgbWFwQm94QXR0cmlidXRpb24sIG1hcEJveFVybCB9IGZyb20gJy4vbWFwLWNvbmZpZyc7XG5cbigoKSA9PiB7XG4gICAgaWYgKCFkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnbWFwTWluaScpKSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG4gICAgY29uc3QgbWFwQ2VudGVyID0gWzQyLjY5NzY2NCwyMy4zMTY2MTAzXTtcbiAgICAvL1RPRE8gZHJhdyB0aGUgb2JqZWN0IGFuZCBmaXQgdG8gaXRzIGJvdW5kc1xuXG4gICAgbGV0IG1hcCA9IG5ldyBMLm1hcCgnbWFwTWluaScsIHtcbiAgICAgICAgY2VudGVyOiBtYXBDZW50ZXIsXG4gICAgICAgIHpvb206IDE1LFxuICAgICAgICB1cGRhdGVXaGVuWm9vbWluZzogZmFsc2VcbiAgICB9KTtcblxuICAgIGxldCBtYXBTdHlsZSA9IEwudGlsZUxheWVyKG1hcEJveFVybCwge1xuICAgICAgICBhdHRyaWJ1dGlvbjogbWFwQm94QXR0cmlidXRpb24sXG4gICAgICAgIG1heE5hdGl2ZVpvb206IDE5LFxuICAgICAgICBtYXhab29tOiAyMCxcbiAgICAgICAgbWluWm9vbTogMTEsXG4gICAgICAgIHVwZGF0ZVdoZW5ab29taW5nOiBmYWxzZVxuICAgIH0pO1xuICAgIG1hcFN0eWxlLmFkZFRvKG1hcCk7XG59KSgpO1xuXG5cblxuLy8gV0VCUEFDSyBGT09URVIgLy9cbi8vIGFzc2V0cy1zcmMvZnJvbnQvanMvbWFwLW1pbmkuanMiXSwibWFwcGluZ3MiOiI7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFIQTtBQUNBO0FBS0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBTEE7QUFPQTtBQUNBIiwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///3\n");

/***/ })
/******/ ]);