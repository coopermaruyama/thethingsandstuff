<?php
// $store = Mage::app()->getStore();
// $code  = $store->getCode();
//     $_enable = Mage::getStoreConfig('stores/general/trego_store_enabled', $code);
//     if($_enable){
//         $_apikey = Mage::getStoreConfig('stores/general/trego_store_apikey', $code);
//         $_address = Mage::getStoreConfig('stores/general/trego_store_address', $code);
//         $_zoom = Mage::getStoreConfig('stores/general/trego_store_zoom', $code);
//         $_center_lat = Mage::getStoreConfig('stores/general/trego_store_center_lat', $code);
//         $_center_lng = Mage::getStoreConfig('stores/general/trego_store_center_lng', $code);
ini_set('display_errors',1);
error_reporting(E_ALL);
?>    


<script type="text/javascript">

  function loadScript() {
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&' +
    'callback=initialize';
    document.body.appendChild(script);
  }

  window.onload = loadScript;

  function getDist() {
    var address = document.getElementById("searchboxinput").value

    geocoder = new google.maps.Geocoder();

    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        console.log(results[0]);
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }











        // function getDist() {
        //   addr = document.getElementById("searchboxinput").value;

        //   geocoder.geocode({ 'address': addr }, function(results, status) {
        //     if(status == google.maps.GeocoderStatus.OK) {
        //       map.setCenter(results[0].geometry.location);
        //       gResult = results[0].geometry.location;
        //       console.log(addr);
        //       var marker = new google.maps.Marker({
        //         map: map,
        //         position: results[0].geometry.location
        //       });
        //       var service = new google.maps.DistanceMatrixService();
        //       service.getDistanceMatrix(
        //       {
        //         origins: [latlng],
        //         destinations: [gResult],
        //         travelMode: google.maps.TravelMode.DRIVING,
        //         unitSystem: google.maps.UnitSystem.IMPERIAL
        //       }, callback);
        //       console.log(results);
        //     } else {
        //       alert("Failed to geocode because: " + status);
        //     }

        //   });
        // }

        // function callback(response, status) {
        //  if (status == google.maps.DistanceMatrixStatus.OK) {
        //     var origins = response.originAddresses;
        //     var destinations = response.destinationAddresses;
        //     var outputTo = document.getElementById("dist");
        //     outputTo.innerHTML = ' ';

        //     for (var i = 0; i < origins.length; i++) {
        //       var results = response.rows[i].elements;
        //       for (var j = 0; j < results.length; j++) {

        //         var element = results[j];
        //         var distance = element.distance.text;
        //         var duration = element.duration.text;
        //         var from = origins[i];
        //         var to = destinations[j];

        //         outputTo.innerHTML += '<p><strong> Distance from: </strong>TheThingsAndStuff</p><p><strong>To: </strong>' + to + '</p><h3>' + distance + '</h3>';

        //         console.log(from + " " + to + " " + distance + " " + duration);
        //       }
        //     }
        //   }
        // }
      </script>

      <div id="store_map"></div>

      <div class="block distSearch" id="mapDistance">

        <div class="block-title" style="">
          <strong>Get Delivery Cost</strong> 
        </div>

        <div id="omnibox">
          <style>.searchbox{background-color:#fff;border:1px solid transparent;border-right:0;border-radius:2px 0 0 2px;box-sizing:border-box;-moz-box-sizing:border-box;height:32px;outline:none;padding:0 11px 0 13px;width:400px;vertical-align:top}.searchbox-shadow{box-shadow:0 2px 6px rgba(0,0,0,0.3),0 4px 15px -5px rgba(0,0,0,0.0)}.searchbox-shadow:hover, .searchbox-shadow:focus{border-color:#4d90fe !important;margin-left:-1px;}.searchbox,.searchbox-shadow{-webkit-transition:all 200ms cubic-bezier(0.52,0,0.48,1);-moz-transition:all 200ms cubic-bezier(0.52,0,0.48,1);-ms-transition:all 200ms cubic-bezier(0.52,0,0.48,1);-o-transition:all 200ms cubic-bezier(0.52,0,0.48,1);transition:all 200ms cubic-bezier(0.52,0,0.48,1)}.searchbox input{background-color:transparent;border:0;font-size:15px;line-height:30px;font-weight:300;margin:0;outline:0;padding:0;width:100%;box-shadow:none;height:auto !important}.searchboxinput{line-height:30px;width:100%}.searchbutton{background-color:#4d90fe;background-image:-webkit-linear-gradient(top,#4d90fe,#4787ed);border:0;border-radius:0 2px 2px 0;box-shadow:0 2px 6px rgba(0,0,0,0.3);height:32px;position:relative;text-align:center;vertical-align:top;width:15%;}.searchbutton:hover{background-color:#357ae8;background-image:-webkit-linear-gradient(top,#4d90fe,#357ae8)}.searchbutton:focus{border:1px solid transparent;box-shadow:0 2px 6px rgba(0,0,0,0.3),inset 0 0 0 1px rgba(255,255,255,0.5);outline:none}.searchbutton:active{box-shadow:0 2px 6px rgba(0,0,0,0.3),inset 2px 0 6px -1px rgba(0,0,0,0.3)}.searchbutton::before{background:url(//maps.gstatic.com/tactile/omnibox/search-button.png);content:"";display:block;height:17px;margin:0 auto;width:17px}
          </style>

          <div class="searchbox searchbox-shadow " id="searchbox" tabindex="-1" role="search" style="width: 85%;float: left;">
            <form id="searchbox_form" action="javascript:getDist()">
             <input class="searchboxinput tactile-searchbox-input" type="text" autocomplete="off" aria-label="Search" id="searchboxinput" name="q" tabindex="1" dir="ltr" spellcheck="false">
           </form>
         </div> 

         <button class="searchbutton" id="omnisearchbtn" aria-label="Search"
         onclick="getDist()" tabindex="3"></button>
       </div>

       <div id="dist"></div>
     </div>
