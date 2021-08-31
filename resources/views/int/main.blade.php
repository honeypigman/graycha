  @include('int.section')

  <div class="album py-5 bg-light">
    <div class="container">

      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
        <!-- K-Airspec -->
        <div class="col">
          <div class="card shadow-sm">            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <div class="card-body">
                    <h5 class="card-title">
                      <a href="{{ env('MISEDO_URL') }}" target="_blank">
                        <button type="button" class="btn btn-outline-secondary w-100">{{env('MISEDO')}} v{{env('MISEDO_VER')}}</button>
                      </a>
                    </h5>
                    <p class="card-text">
                        공공데이터포털(www.data.go.kr)을 통해, 전국 시도별 측정소로부터 실시간 대기오염 측정정보를 수신 받아, 미세먼지와 초미세먼지 농도의 시간별 정보를 지도에 표시해주는 서비스입니다.
                    </p>
                    <div>
                    </div>
                </div>
            </div>
          </div>
        </div>

        <!-- API Manager -->
        <div class="col">
          <div class="card shadow-sm">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="{{ env('APIMNG_URL') }}" target="_blank">
                        <button type="button" class="btn btn-outline-secondary w-100">{{env('APIMNG')}} v{{env('APIMNG_VER')}}</button>
                      </a>
                    </h5>
                    <p class="card-text">
                        Open API 송수신 데이터를 인터페이스화하여 관리하는 서비스입니다.
                    </p>
                    <div></div>
                </div>
            </div>
          </div>
        </div>

        <!-- Dot To Dot -->
        <div class="col">
          <div class="card shadow-sm">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <div class="card-body">
                    <h5 class="card-title">
                      <a href="{{ env('DTD_URL') }}" target="_blank">
                        <button type="button" class="btn btn-outline-secondary w-100">{{env('DTD')}} v{{env('DTD_VER')}}</button>
                      </a>
                    </h5>
                    <p class="card-text">
                      점과 점을 이어 자유롭게 선을 그릴 수 있는 서비스입니다.
                    </p>
                    <div></div>
                </div>
            </div>
          </div>
        </div>

        <!-- Blog Searching -->
        <div class="col">
          <div class="card shadow-sm">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center">
                <div class="card-body">
                    <h5 class="card-title">
                      <a href="{{env('BLPER_URL')}}" target="_blank">
                        <button type="button" class="btn btn-outline-secondary w-100">{{env('BLPER')}} v{{env('BLPER_VER')}}</button>
                      </a>
                    </h5>
                    <p class="card-text">
                      정보검색과 이슈정보 탐색을 목적으로한 포스팅 도우미 서비스입니다. 다음, 네이버, 구글 API서비스의 데이터를 기반으로 정보를 제공합니다.
                    </p>
                    <div></div>
                </div>
            </div>
          </div>
        </div>

        <!-- Watting.. -->
      </div>
    </div>
  </div>