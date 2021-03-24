<!doctype html>
<html lang="ko">
  <head>
    <meta charset="UTF-8">
    <meta name="description" content="전국 시도별 미세먼지 시간별 정보를 지도에서 확인하세요!">
    <meta name="keywords" content="미세도, 미세먼지, 초미세먼지">
    <meta name="author" content="graycha">
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ env('APP_NAME') }}::{{ env('MISEMAP') }} v{{ env('MISEMAP_VER') }}" />
    <meta property="og:description" content="전국 시도별 미세먼지 시간별 정보를 지도에서 확인하세요!">
    <meta property="og:url" content="https://www.graycha.net/map/misedo">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <title>{{ env('MISEMAP') }} v{{ env('MISEMAP_VER') }}</title>
    <script src="/js/jquery.min.js"></script>
    <link href="{{ mix('/css/common.css') }}" rel="stylesheet">
    <link href="{{ mix('/css/map.css') }}" rel="stylesheet">
    @if(env('APP_ENV') == 'P')
      @include('cmm.googletagmanager')
    @endif
</header>
<body class="text-center">

  <div class="alert alert-warning notice" role="alert">
    <div>데이터출처 : 한국환경공단에서 제공하는 대기오염정보 입니다.</div>
  </div>

  <div class="option-bar">  
    <!-- 현재위치 -->
    <div id="find-me"></div>
  </div>

  <div id="map" class="map">
    <div class="markerDetail"></div>
  </div>

  <div class="count">
    <span class="text-muted">{{ $views }}</span>
  </div>

  <div class="serach-bar">
    <div class="alert alert-danger d-none" id="search-alert" role="alert">
      <img src="/img/icon/alert-triangle.svg"/> <span id="search-alert-msg"></span>
    </div>
    <div class="addr-bar">
      <input type="text" class="form-control" id="address" placeholder='주소를 입력해주세요.(읍/면/동)'>
      <span class="input-group-text" id="submit">검색</span>
    </div>
  </div>
  <script src="/js/jquery.ui.min.js"></script>
  <script src="/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha" crossorigin="anonymous"></script>
  <script type="text/javascript" src="https://openapi.map.naver.com/openapi/v3/maps.js?ncpClientId={{ env('NAVER_MAPS_CLIENT_ID') }}&submodules=geocoder"></script>
  <script src="{{ mix('/js/map.js') }}"></script>
  <script>
    $(".count").on('click',function(){
      alert(1);
    });
    // Create Marker 
    @foreach( $marker as $stationName => $datas )
      //var content = "<div class='text-start'>미세먼지 : {{ $datas['mesure_pm10'] }} ㎍/m³</div><div class='text-start'>초미세먼지 : {{ $datas['mesure_pm25']}} ㎍/m³</div>";
      // 마커 생성
      // param[1] : 등급
      // param[2] : 메세지
      // param[3] : 측정일
      // param[4] : 경도
      // param[5] : 위도f
      // param[6] : 미세먼지 농도
      // param[7] : 초미세먼지 농도
      // param[8] : 시도명
      // param[9] : 측정소명
      addMarker({{ $datas['grade'] }}, '{{ $datas['msg'] }}', '{{ $datas['mesure_date'] }}', {{ $datas['dmX'] }}, {{ $datas['dmY'] }}, '{{ $datas['mesure_pm10'] }}', '{{ $datas['mesure_pm25'] }}', '{{ $datas['city'] }}', '{{ $stationName }}');
    @endforeach
  </script>
  <!-- Naver Api Maps End -->
</body>
</html>