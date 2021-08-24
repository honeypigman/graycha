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
    <meta property="og:image" content="{{ env('BLPER_OR_IMG') }}">
    <meta property="og:description" content="블퍼::블로그 포스팅 작성을 위한 도우미 서비스">
    <meta property="og:url" content="{{ env('BLPER_URL') }}">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link href="{{ mix('/css/common.css') }}" rel="stylesheet">
    <link href="{{ mix('/css/blper.css') }}" rel="stylesheet">
    @if(env('APP_ENV') == 'P')
      @include('cmm.googletagmanager')
    @endif
</header>
<body>
<header>
  <div class="navbar navbar-dark bg-primary">
    <div class="container">
      <a href="{{ env('BLPER_URL') }}" class="navbar-brand bg-primary d-flex align-items-center" style="box-shadow:none;">
        <div>Blper v1.0 - Blog + Helper</div>
      </a>
    </div>
  </div>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <form id="form" method="POST" onsubmit="return false">
    <div class="header-container">
      <div class="row">
        <div class="col-8">
          <input type="text" class="form-control form-control-sm keywordInput" id="kw0" name="kw0" placeholder="키워드1" autofocus required>
        </div>
        <div class="col-4">
          <div id="btnArea">
            <button type="button" class="btn btn-outline-primary btn-action float-start mx-1" id="go">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </button>
            <button type="button" class="btn btn-outline-success btn-action float-start" id="reset">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-refresh-cw"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</header>

<main>
  <div class="main-container">
    <div class="row line1">
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
            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="검색어와 연관있는 키워드 정보를 제공합니다.">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
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
            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="최근 검색량이 높은 키워드 정보를 제공합니다.">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </span>
          </div>
          <div id="keywordList"></div>
        </div>
      </div>

      <!-- Seg1. RealTime Issue -->
      <div class="col py-1 line2-seg-2">
        <div class='frame-form' id="issueArea">
          <div id="issueTitle">실시간 이슈
            <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="실시간 이슈 정보를 제공합니다.">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </span>
          </div>
          <div id="issueList"></div>
        </div>        
      </div>
    </div>
  </div>
  
  <div class="footer">
    <p>
      <span class="float-start text-muted">* 본 서비스는 네이버, 다음, 구글에서 제공하는 서비스를 이용하여 제공되는 정보입니다.</span>
    </p>
    <p>
      <span class="float-start text-muted">Copyright(c)<?php echo date('Y')?> {{ env('ADMIN_EMAIL') }} All rights reserved.</span>
      <span class="badge rounded-pill  bg-light text-dark float-end" id="views" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#viewsHisModal">{{ $views ? $views : 0 }}</span>
    </p>
  </div>
</main>

<!-- Modal -->
<div class="modal fade" id="viewsHisModal" tabindex="-1" aria-labelledby="viewsModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="viewsModalLabel"> 검색이력 - {{ date('Y-m-d') }} </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="viewsModalBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">닫기</button>
      </div>
    </div>
  </div>
</div>

<!-- script -->
<script src="/js/bootstrap.bundle.min.js"></script>
<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.ui.min.js"></script>
<script src="{{ mix('/js/blper.js') }}"></script>
</body>
</html>