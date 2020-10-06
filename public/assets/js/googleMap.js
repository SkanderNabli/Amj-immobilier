let map = new google.maps.Map(document.getElementById('map-small'), {
    zoom: 13
});
let geocoder = new google.maps.Geocoder();

let address = "{{ property.address }} {{ property.postalCode }} {{ property.city }}";

geocoder.geocode({'address': address}, function(results, status) {
    if (status === 'OK') {

        map.setCenter(results[0].geometry.location);
        let marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location,
            icon: 'assets/img/map-marker.png'
    });
    } else {
        alert('Le géocode n\'a pas abouti pour la raison suivante: ' + status);
    }
});

