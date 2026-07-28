function initialize() {

    $('form').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    const locationInputs = document.getElementsByClassName("map-input");

    const autocompletes = [];
    const geocoder = new google.maps.Geocoder;
    for (let i = 0; i < locationInputs.length; i++) {

        const input = locationInputs[i];
        const fieldKey = 'address';
        const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(fieldKey + "-longitude").value != '';

        const latitude = parseFloat(document.getElementById(fieldKey + "-latitude").value) || -33.8688;
        const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || 151.2195;

        const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
            center: {lat: latitude, lng: longitude},
            zoom: 13
        });
        const marker = new google.maps.Marker({
            map: map,
            position: {lat: latitude, lng: longitude},
        });

        marker.setVisible(isEdit);

        const autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.key = fieldKey;
        autocompletes.push({input: input, map: map, marker: marker, autocomplete: autocomplete});
    }

    for (let i = 0; i < autocompletes.length; i++) {
        const input = autocompletes[i].input;
        const autocomplete = autocompletes[i].autocomplete;
        const map = autocompletes[i].map;
        const marker = autocompletes[i].marker;

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            marker.setVisible(false);
            const place = autocomplete.getPlace();

            geocoder.geocode({'placeId': place.place_id}, function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    console.log(results);
                    const lat = results[0].geometry.location.lat();
                    const lng = results[0].geometry.location.lng();
                    document.getElementById('address1').value = results[0].address_components[0].long_name;
                    document.getElementById('address2').value = results[0].address_components[1].long_name;
                    setLocationCoordinates(autocomplete.key, lat, lng);
                    displayPostcode(results[0].address_components)
                }
            });

            if (!place.geometry) {
                window.alert("No details available for input: '" + place.name + "'");
                input.value = "";
                return;
            }

            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

        });
    }
}

function setLocationCoordinates(key, lat, lng) {
    const latitudeField = document.getElementById(key + "-" + "latitude");
    const longitudeField = document.getElementById(key + "-" + "longitude");
    latitudeField.value = lat;
    longitudeField.value = lng;
}

// displays the resulting post code in a div
function displayPostcode(address) {
    document.getElementById('post').value = '';
    document.getElementById('state').value = '';
    document.getElementById('districts').value = '';
    document.getElementById('city').value = '';

    for (p = address.length - 1; p >= 0; p--) {
        if (address[p].types.indexOf("postal_code") != -1) {
            document.getElementById('post').value = address[p].long_name;
        }
        if (address[p].types.indexOf("administrative_area_level_1") != -1) {
            document.getElementById('state').value = address[p].long_name;
        }
        if (address[p].types.indexOf("administrative_area_level_2") != -1) {
            document.getElementById('districts').value = address[p].long_name;
        }
        if (address[p].types.indexOf("locality") != -1) {
            document.getElementById('city').value = address[p].long_name;
        }
    }
}

