<!doctype html>
<html lang="ko">
<head>
    <title>{{ env('APP_NAME') }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="graycha">
    <meta name="description" content="홈스쿨링::점보드-점과 점을 이어 자유롭게 선을 그려 출력한 후, 아이가 따라 그릴 수 있게해주세요.">
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ env('APP_NAME') }}" />
    <meta property="og:title" content="{{ env('APP_NAME') }}::{{ env('DTD') }} v{{ env('DTD_VER') }}" />
    <meta property="og:description" content="홈스쿨링::점보드-점과 점을 이어 자유롭게 선을 그려 출력한 후, 아이가 따라 그릴 수 있게해주세요.">
    <meta property="og:url" content="{{ env('DTD_URL') }}">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/jquery.ui.css" rel="stylesheet">
    <link href="{{ mix('/css/dtd.css') }}" rel="stylesheet">
</head>
<body>
<meta name="csrf-token" content="{{ csrf_token() }}">
<div id="print_wrap"> 
    <div class="header">
        <div class="alert alert-dark" role="alert">
            <div class="row">
                <div class="col-11">
                    <span class="text-start" style="display:flex;"><input type="text" id="title" name="title" value="" class="form-control w-100 text-start" placeholder="주제를 입력해 주세요! 점과 점을 클릭하여, 자유롭게 선을 만들어 주세요!" maxlength="10"></span>
                </div>
                <div class="col-1">
                    <a href="https://graycha.tistory.com/119" target="_blank"><img class="logo" src="/img/grade1.png"/ style="width:40px;"></a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="main">
        <div class="canvas float-start">
            <div class="area-top">
                <canvas id="canvas-top" width="782" height="490"></canvas>
                <div class="background-dot top-div"></div>
            </div>
        
            <div><hr></hr></div>

            <div class="area-bottom">
                <canvas id="canvas-bottom" width="782" height="490"></canvas>
                <div class="background-dot bottom-div"></div>
            </div>
        </div>
    </div>
    
    <div class="footer float-start">
        <p class="mb-0">Copyright 2021.honeypigman, all rights reserved.</p>
    </div>
</div>
<div class="views float-start">
{{ $views }}
</div>
<div class="options">
    <ul>
        <li><button type="button" level="1" class="level btn btn-outline-success mb-1 w-100 active">쉬움</button></li>
        <li><button type="button" level="2" class="level btn btn-outline-primary mb-1 w-100">보통</button></li>
        <li><button type="button" level="3" class="level btn btn-outline-danger mb-1 w-100">어려움</button></li>
        &nbsp;
        <li>
            <button type="button" level="0" class="show-option pre btn btn-outline-secondary w-100 float-start">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-corner-up-left"><polyline points="9 14 4 9 9 4"></polyline><path d="M20 20v-7a4 4 0 0 0-4-4H4"></path></svg>
            </button>
        </li>
        &nbsp;
        <li>
            <button type="button" level="0" class="show-option down btn btn-outline-primary w-100 float-start">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            </button>
        </li>
        <li>
            <button id="save" type="button" level="0" class="show-option btn btn-outline-success w-100 float-start" title="아이디어를 공유해주세요 ^_^">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-save"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
            </button>
        </li>
    </ul>
</div>
<div class="samples">
    Sample
    <div id="sample-list" class="d-grid gap-2">
        - None -
    </div>
</div>
<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.ui.min.js"></script>
<script src="{{ mix('/js/dtd.js') }}"></script>
</body>
</html>