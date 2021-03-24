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
    <meta name="description" content="개인 프로젝트를 관리하는 공간입니다.">
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ env('APP_NAME') }}" />
    <meta property="og:description" content="개인 프로젝트를 관리하는 공간입니다." class="next-head">
    <meta property="og:url" content="{{ env('APP_URL') }}">
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
    <div class="collapse bg-dark" id="navbarHeader">
      <div class="container">
        <div class="row">
          <div class="col-sm-8 col-md-7 py-4">
            <h4 class="text-white">About Me</h4>
            <p class="text-muted">GrayCha WebSite</p>
          </div>
          <div class="col-sm-4 offset-md-1 py-4">
            <h4 class="text-white">Contact</h4>
            <ul class="list-unstyled">
              <li>
                <a href="{{ env('LINK_GITHUB') }}" target="_blank" class="text-white">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-github "><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
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