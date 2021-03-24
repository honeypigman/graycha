<!--
    Title : Header Layout 
    Date : 2020.12.30
//-->
<!doctype html>
<html lang="ko">
  <head>
    <title>{{ env('APIMNG') }} v{{ env('APIMNG_VER') }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="graycha">
    <meta name="description" content="OpenAPI Manager">
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ env('APIMNG') }}" />
    <meta property="og:description" content="OpenApi관리하는 공간입니다." class="next-head">
    <meta property="og:url" content="{{ env('APIMNG_URL') }}">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <script src="/js/jquery.min.js"></script>
    <link href="{{ mix('/css/common.css') }}" rel="stylesheet">
    <link href="/css/flip.css" rel="stylesheet">
    @if(env('APP_ENV') == 'P')
      @include('cmm.googletagmanager')
    @endif
  </head>

  <body class="text-center">
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="/">{{ env('APP_NAME') }}</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </header>

    @include('api/layout/nav')

    @if(Request::segment(1)=='map')
      @yield('content')
    @else
      <div class="container-fluid">
      <div class="row">
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        @yield('content')
      </main>
    @endif

  @include('api/layout/bottom')