@extends('int/header')

@section('content')
<div class="alert alert-danger" role="alert" style="width:100%">
  <h1 class="alert-heading"> 403. That’s an error :(</h1>
  <p>요청하신 권한이 존재하지 않습니다.</p>
</div>
@endsection
<script> setInterval(function(){ location.href="/"; }, 3000);</script>
<!-- 참고.https://ko.wikipedia.org/wiki/HTTP_%EC%83%81%ED%83%9C_%EC%BD%94%EB%93%9C -->