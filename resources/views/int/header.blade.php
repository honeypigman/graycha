<!--
    Title : Header Layout 
    Date : 2021.03.23
//-->
<!doctype html>
<html lang="ko">
  <head>
    <title>{{ env('APP_NAME') }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="graycha">
    <meta name="description" content="개인 프로젝트와 생각을 기록하는 공간입니다.">
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ env('APP_NAME') }}" />
    <meta property="og:description" content="개인 프로젝트와 생각을 기록하는 공간입니다." class="next-head">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:img" content="{{ env('APP_IMG') }}">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/graycha.css" rel="stylesheet">
    @if(env('APP_ENV') == 'P')
      @include('cmm.googletagmanager')
    @endif
  </head>
  
  <body>
  <header>
    @include('int.about')
    <div class="navbar navbar-dark bg-dark shadow-sm">
      <div class="container">
        <a href="#" class="navbar-brand d-flex align-items-center">
          <div><img class="logo" src="/img/grade1.png"/></div>
          <strong>{{ env('APP_NAME') }}</strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>
    </div>
  </header>

  <main>
    @yield('content')
  </main>

  <footer class="text-muted py-3">
    @include('int.footer')
  </footer>
  <script src="/js/bootstrap.min.js"></script>        
  </body>
</html>