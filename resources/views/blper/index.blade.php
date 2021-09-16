<!doctype html>
<html lang="ko">
<head>
  <title>{{ env('BLPER') }} v{{ env('BLPER_VER') }}</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="블퍼::블로그 포스팅 작성을 위한 도우미 서비스">
  <meta name="keywords" content="포스팅 서비스, 실시간 이슈, 실시간 키워드, 연관 검색어">
  <meta name="author" content="graycha">
  <meta property="og:type" content="website" />
  <meta property="og:title" content="{{ env('APP_NAME') }}::{{ env('BLPER') }} v{{ env('BLPER_VER') }}" />
  <meta property="og:image" content="{{ env('BLPER_OG_IMG') }}">
  <meta property="og:description" content="블퍼::블로그 포스팅 작성을 위한 도우미 서비스">
  <meta property="og:url" content="{{ env('BLPER_URL') }}">
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  <link href="{{ mix('/css/common.css') }}" rel="stylesheet">
  <link href="{{ mix('/css/fontawesome.css') }}" rel="stylesheet">
  <link href="{{ mix('/css/blper.css') }}" rel="stylesheet">
  @if(env('APP_ENV') == 'P')
    @include('cmm.googletagmanager')
  @endif
</head>
<body>
<header>
  <div class="navbar navbar-dark bg-primary">
    <div class="container">
      <a href="{{ env('BLPER_URL') }}" class="navbar-brand bg-primary d-flex align-items-center" style="box-shadow:none;">
        <div>Blper v{{ env('BLPER_VER') }} - Blog + Helper</div>
      </a>
    </div>
  </div>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <form id="form" method="POST" onsubmit="return false;">
    <div class="header-container">
      <div class="row">
        <!-- <div class="col-7">
          <input type="text" class="form-control form-control-sm keywordInput" id="kw0" name="kw0" placeholder="키워드" autofocus required>
        </div> -->

        <div class="input-group mb-5" style="padding-right: 30px;">
          <input type="text" class="form-control form-control-lg keywordInput" placeholder="키워드 입력" aria-describedby="button-addon" id="kw0" name="kw0" autofocus required>
          <button class="btn btn-outline-primary" type="button" id="go"><i class="fas fa-search"></i></button>
          <button class="btn btn-outline-success" type="button" id="reset"><i class="fas fa-sync-alt"></i></button>
          <button class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#viewsMonthlyHisModal" id="history"><i class="fas fa-list"></i></button>
        </div>

        <!-- <div class="col-5">
          <div id="btnArea">
            <button type="button" class="btn btn-outline-primary btn-action float-start mx-1" id="go">
              <i class="fas fa-search"></i>
            </button>
            <button type="button" class="btn btn-outline-success btn-action float-start mx-1" id="reset">
              <i class="fas fa-sync-alt"></i>
            </button>
            <button type="button" class="btn btn-outline-danger btn-action float-start" data-bs-toggle="modal" data-bs-target="#viewsMonthlyHisModal" id="history">
              <i class="fas fa-list"></i>
            </button>
          </div>
        </div> -->
      </div>
    </div>
  </form>
</header>

<main>
  <div class="main-container">

    <div class="row line0 d-none" id="monthlyReport">
      <div class="col py-1 line0-seg-1">
        <div class='report-form text-start' id="reportArea">
          <div id="reportTitle">
            월간 검색량   
            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="최근 한달간 조회된 pc / mo 검색 횟수를 제공합니다.(* 데이터 출처 : 네이버)">
              <i class="far fa-question-circle"></i>
            </span>
          </div>
          <div class="report-cnt-form">
            <span class="report-mo-cnt pc-color"><i class="fas fa-desktop fa-2x"></i><div>[pc]</div></span>
            <span class="report-mo-cnt mo-color"><i class="fas fa-mobile-alt fa-2x"></i><div>[mo]</div></span>
          </div>
          <div class="report-cnt-form">
            <span class="report-mo-cnt cnt-font" id="monTotalCntPc">0</span>
            <span class="report-mo-cnt cnt-font" id="monTotalCntMo">0</span>
          </div>
        </div>
      </div>

      <div class="col-sm-7 py-1 line0-seg-2">
        <div class='report-form text-start' id="reportArea">
          <div id="reportTitle">
            유형별 문서수
            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="웹문서, 블로그, 카페 전체 문서수 정보를 제공합니다.(* 데이터 출처 : 네이버, 다음)">
              <i class="far fa-question-circle"></i>
            </span>
          </div>
          <div class="report-cnt-form">
            <span class="report-mo-cnt b-color"><i class="fas fa-file-alt fa-2x"></i><div>[blog]</div></span>
            <span class="report-mo-cnt c-color"><i class="fas fa-file-alt fa-2x"></i><div>[cafe]</div></span>
            <span class="report-mo-cnt w-color"><i class="fas fa-file-alt fa-2x"></i><div>[web]</div></span>
          </div>
          <div>
              <div class="report-cnt-form" style="margin-top:-10px;">
                <span class="report-cnt-log" >N</span>
                <span class="report-mo-cnt cnt-font" id="monCnt_naver_b">0</span>
                <span class="report-mo-cnt cnt-font" id="monCnt_naver_c">0</span>
                <span class="report-mo-cnt cnt-font" id="monCnt_naver_w">0</span>
              </div>
              <div class="report-cnt-form" style="margin-top:-20px;">
                <span class="report-cnt-log">D</span>
                <span class="report-mo-cnt cnt-font" id="monCnt_daum_b">0</span>
                <span class="report-mo-cnt cnt-font" id="monCnt_daum_c">0</span>
                <span class="report-mo-cnt cnt-font" id="monCnt_daum_w">0</span>
              </div>
          </div>
        </div>
      </div>

      <div class="col py-1 line0-seg-3">
        <div class='report-form text-start' id="reportArea">
          <div id="reportResultTitle">
            결과
            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" data-html="true" title="Blper 데이터를 통해 산출된 키워드 등급을 제공합니다.(* 구간: 1등급 ~ 10등급 )">
              <i class="far fa-question-circle"></i>
            </span>
          </div>
          <div class="report-result"><span id="keyWordGrade">-</span></div>
        </div>
      </div>

      <!-- Chart -->
      <div class="col-12" id="ketywordTrendChart" style="height:300px;"></div>

    </div>

    <div class="row line1 d-none" id="searchArea">
      <!-- Seg1. Search List -->
      <div class="col py-1 line1-seg-1">
        <div id="tabs" class="frame-form">
          <ul id="tab-nav"></ul>
        </div>
      </div>
        
      <!-- Seg2. Relation Keyword -->
      <div class="col py-1 line1-seg-2">
        <div class='frame-form text-start' id="wordArea">
          <div id="wordTitle">
            연관 검색어   
            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="연관 키워드 정보를 제공합니다.(* 데이터 출처 : 구글, 네이버)">
              <i class="far fa-question-circle"></i>
            </span>
          </div>
          <div id="wordList"></div>
        </div>
      </div>
    </div>

    <!-- Line2 -->
    <div class="row line2">
      <!-- Seg1. RealTime Keyword -->
      <div class="col py-1 line2-seg-1">
        <div class='frame-form' id="keywordArea">
          <div id="keywordTitle">실시간 키워드
            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="실시간 키워드 정보를 제공합니다.(* 데이터 출처 : 구글)">
            <i class="far fa-question-circle"></i>
            </span>
          </div>
          <div id="keywordList"></div>
        </div>
      </div>

      <!-- Seg1. RealTime Issue -->
      <div class="col py-1 line2-seg-2">
        <div class='frame-form' id="issueArea">
          <div id="issueTitle">실시간 이슈
            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="실시간 이슈 정보를 제공합니다.(* 데이터 출처 : 한국정책 브리핑)">
            <i class="far fa-question-circle"></i>
            </span>
          </div>
          <div id="issueList"></div>
        </div>        
      </div>
    </div>
  </div>
 
  <div class="footer">
    <div class="float-start w-100">
      <div class="text-muted">* 본 서비스는 {{ env('BLPER_DATA_ROOT') }} 연동서비스를 이용한 데이터 기반의 정보를 제공하고 있습니다.</div>
      <div class="text-muted">
        Copyright(c)<?php echo date('Y')?> {{ env('ADMIN_EMAIL') }} All rights reserved.
        <span class="badge rounded-pill  bg-light text-dark float-end" id="views" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#viewsSearchHisModal">{{ $views ? $views : 0 }}</span>
      </div>
    </div>
  </div>
</main>

<!-- Modal Search Keyword His -->
<div class="modal fade" id="viewsSearchHisModal" tabindex="-1" aria-labelledby="viewsSearchHisModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="viewsSearchHisModalLabel"> 검색이력 - {{ date('Y-m-d') }} </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="viewsSearchHisModalBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Keyword Monthly His -->
<div class="modal fade" id="viewsMonthlyHisModal" tabindex="-1" aria-labelledby="viewsMonthlyHisModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-lg-down">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="viewsMonthlyHisModalLabel"> 검색이력 - {{ date('Y-m-d') }} </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="viewsMonthlyHisModalBody">
        <table class="table table-striped text-center">
          <thead>
            <tr>
              <th scope="col">검색어</th> 
              <th scope="col">PC/MO<br/>검색량</th>
              <th scope="col">블로그<br/>문서수</th>
              <th scope="col">카페<br/>문서수</th>
              <th scope="col">등급</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
      </div>
    </div>
  </div>
</div>

<!-- script -->
<script src="/js/bootstrap.bundle.min.js"></script>
<script src="/js/chart.min.js"></script>
<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.ui.min.js"></script>
<script src="{{ mix('/js/blper.js') }}"></script>
</body>
</html>