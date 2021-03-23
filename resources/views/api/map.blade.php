<!--
    Title : Api Layout 
    Date : 2021.02.25
    His :
      2021.03.03  Operation Division.
//-->
@extends('layout/header')
@section('content')
<style>
  /**
  *   Feather icon Size
  */
  .feather-16{
      width: 16px;
      height: 16px;
  }
  .feather-26{
      width: 26px;
      height: 26px;
  }
  .feather-36{
      width: 36px;
      height: 36px;
  }
</style>
<!-- Map Head -->

<!-- <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
  <div class="flip-card">   
    <div class="flip-card-inner">
      <div class="flip-card-front">         
        <button type="button" class="btn btn-outline-dark" id="api">{{ $api }}</button>
      </div>
      <div class="flip-card-back">         
        <button type="button" class="btn btn-outline-primary" id="api">{{ $api }}</button>
      </div>   
    </div>
  </div>
</div> -->

<!-- Map Body -->
<div class="map">
  <div class="col-12" style="padding:0px; margin:0px;">
    <div id="map" class="p-auto" style="width: 100%; height:500px; position: relative; overflow: hidden;"></div>
  </div>
</div>

<!-- Map Footer -->
<!-- <div class="row">
  <div class="accordion mt-3" id="accordionExample">
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingTwo">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          <span data-feather="smile" class="text-primary feather-36"></span>
          <span data-feather="frown" class="text-danger feather-36"></span>
          <span data-feather="meh" class="feather-36"></span>
        </button>
      </h2>
      <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
        <div class="accordion-body">
          - 상세내역 -
        </div>
      </div>
    </div>
  </div>
</div> -->

<!-- Script Push -->
@push('scripts')
  <!-- Naver Api Maps Start -->
  <script type="text/javascript" src="https://openapi.map.naver.com/openapi/v3/maps.js?ncpClientId={{ env('NAVER_MAPS_CLIENT_ID') }}&submodules=geocode"></script>
  <script>
    
    var mapDiv = document.getElementById('map');
    
    var map = new naver.maps.Map(mapDiv, {
      center: new naver.maps.LatLng(37.572025, 127.005028),
      //center: naver.maps.TransCoord.fromUTMKToTM128(189732.07095, 443575.073582),
      zoom: 6
    });
    
    function addMarker(y, x, icon) 
    {
      var position = new naver.maps.LatLng(y, x);
      var markerOptions = // new naver.maps.Marker(
          {
              position: position,
              icon: 
              {
                  url: '/img/'+icon+'.png',
                  size: new naver.maps.Size(25, 25),
                  origin: new naver.maps.Point(0, 0),
                  anchor: new naver.maps.Point(11, 25)
              }
          };
          
          var marker = new naver.maps.Marker(markerOptions);
          marker.setMap(map);
    }

    // Create Marker 
    @foreach( $marker as $stationName => $datas )
      addMarker({{ $datas['dmX'] }}, {{ $datas['dmY'] }}, 'blue');
    @endforeach
    
    // var marker = new naver.maps.Marker({
    //   position: new naver.maps.LatLng(37.572025, 127.005028),
    //   //position: new naver.maps.TransCoord.fromUTMKToTM128(189732.07095, 443575.073582),
    //   map: map
    // });
    
  </script>
  <!-- Naver Api Maps End -->
@endpush

@endsection 