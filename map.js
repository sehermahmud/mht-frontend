var i, cityName;
        var custLat = [23.901721, 23.902749, 23.902373];
        var custLong = [90.391389, 90.391128, 90.388338];
        //alert(arrayMy.length);
        for(i=0; i<arrayMy.length; i++){
          cityName = new google.maps.LatLng(custLat[i], custLong[i]);
          alert(cityName);      
        }
