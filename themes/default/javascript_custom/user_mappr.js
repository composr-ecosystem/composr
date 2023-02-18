(function ($cms) {
    'use strict';

    $cms.templates.blockMainGoogleMapUsers = function blockMainGoogleMapUsers(params) {
        /*globals google,MarkerClusterer*/

        var cluster = Boolean(params.cluster),
            latitude = strVal(params.latitude),
            longitude = strVal(params.longitude),
            zoom = Number(params.zoom),
            region = strVal(params.region),
            dataJson = strVal(params.data),
            geolocateUser = Boolean(params.geolocateUser),
            setCoordUrl = strVal(params.setCoordUrl),
            username = strVal(params.username),
            usernamePrefix = strVal(params.usernamePrefix);

        var gMapsURL = 'https://maps.googleapis.com/maps/api/js?libraries=places,visualization&v=weekly&callback=googleMapUsersInitialise';
        if ($cms.configOption('google_apis_api_key') !== '') {
            gMapsURL += '&key=' + $cms.configOption('google_apis_api_key');
        }
        if (region !== '') {
            gMapsURL += '&region=' + region;
        }

        var scripts = [];
        if (cluster) {
            scripts.push('https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js');
        }
        scripts.push(gMapsURL);

        $cms.requireJavascript(scripts);

        window.googleMapUsersInitialise = function () {
            var bounds = new google.maps.LatLngBounds();
            var center = new google.maps.LatLng((latitude !== '' ? latitude : 0.0), (longitude !== '' ? longitude : 0.0));
            var map = new google.maps.Map(document.getElementById('map-canvas'), {
                zoom: zoom,
                center: center,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                overviewMapControl: true,
                overviewMapControlOptions: {
                    opened: true
                }
            });

            if (latitude === '') {
                if (navigator.geolocation !== undefined) {
                    try {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            map.setCenter(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
                        });
                    } catch (ignore) {}
                }
            }

            var infoWindow = new google.maps.InfoWindow();

            // Close InfoWindow when clicking anywhere on the map.
            google.maps.event.addListener(map, 'click', function () {
                infoWindow.close();
            });

            // Show markers
            var data = (dataJson !== '') ? JSON.parse(dataJson) : [],
                markers = [];
            for (var i = 0; i < data.length; i++) {
                addDataPoint(data[i], bounds, markers, infoWindow, map);
            }

            if (cluster) {
                /*var markerCluster = */new MarkerClusterer(map, markers);
            }
            // Fit the map around the markers, but only if we want the map centered
            if (params.center) {
                map.fitBounds(bounds);
            }

            if (geolocateUser && (setCoordUrl !== '')) {
                // Geolocation for current member to get stored onto the map
                if (navigator.geolocation !== undefined) {
                    try {
                        navigator.geolocation.getCurrentPosition(function (position) {
                            $cms.doAjaxRequest(setCoordUrl + position.coords.latitude + '_' + position.coords.longitude + $cms.keep());
                            var initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                            map.setCenter(initialLocation);
                            addDataPoint([username, position.coords.latitude, position.coords.longitude, ''], bounds, markers, infoWindow, map);
                        });
                    } catch (ignore) {}
                }
            }

            /*Sample code to grab clicked positions
             var last_point;
             google.maps.event.addListener(map, 'mousemove', function(point) \{
             last_point = point.latLng;
             \});
             google.maps.event.addListener(map, 'click', function() \{
             $util.inform(last_point.lat() + ', ' + last_point.lng());
             \});
             }*/
        };

        function addDataPoint(dataPoint, bounds, markers, infoWindow, map) {
            var latLng = new google.maps.LatLng(dataPoint[1], dataPoint[2]);
            bounds.extend(latLng);

            var markerOptions = {
                position: latLng,
                title: usernamePrefix + dataPoint[0]
            };

            /*{$,Reenable if you have put appropriate images in place
             var usergroup_icon = new GIcon(G_DEFAULT_ICON);
             usergroup_icon.image = '{$BASE_URL;/}/themes/default/images_custom/icons/map/usergroup_' + data_point[3]+'.png';
             marker_options.icon = usergroup_icon;
             }*/

            var marker = new google.maps.Marker(markerOptions);

            if (cluster) {
                markers.push(marker);
            } else {
                marker.setMap(map);
            }

            google.maps.event.addListener(marker, 'click', (function (argMarker, argMember) {
                return function () {
                    // Dynamically load a specific members details only when their marker is clicked
                    $cms.doAjaxRequest('{$FIND_SCRIPT_NOHTTP;,get_member_tooltip}?member=' + argMember + $cms.keep()).then(function (xhr) {
                        var content = xhr.responseXML && xhr.responseXML.querySelector('result').textContent;

                        if (content) {
                            infoWindow.setContent('<div class="global-middle-faux clearfix">' + content + '</div>');
                            infoWindow.open(map, argMarker);
                        }
                    });
                };
            }(marker, dataPoint[0]))); // These are the args passed to the dynamic function above
        }
    };
}(window.$cms));
