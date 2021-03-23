<!--
    Title : Api Layout 
    Date : 2021.02.25
    His :
      2021.03.03  Operation Division.
//-->
@extends('api/layout/header')
@section('content')
  <!-- Apicode Info //-->
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div class="flip-card">   
      <div class="flip-card-inner">
        <div class="flip-card-front">         
          <button type="button" class="btn btn-outline-dark" id="api">{{ $result['api'] }}</button>
        </div>
        <div class="flip-card-back">         
          <button type="button" class="btn btn-outline-primary" id="api">{{ $result['api'] }}</button>
        </div>   
      </div>
    </div>

    <span><span id="api_id">{{ $result['spec']['id'] }}</span> - <span id="svc_name">{{ $result['spec']['serviceName'] }}</span></span>
    <span class="text-end">
      <button type="button" class="btn btn-primary btn-sm" id="btnSend">SEND</button>
    </span>
  </div>

  <!-- Apicode Form //-->
  <!-- CSRF-TOKEN //-->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <p>
    <ul class="nav nav-tabs">
      <li class="nav-item">
        <a class="nav-link bg-muted active" aria-current="page" href="#">Req</a>
      </li>
    </ul>
    @if(!empty($result['spec']))
        <!-- 
          Api별 코드분기
         -->
        @if( $result['api'] == 'kairspec' )
          <div class="row mt-2">
            <label class="col-sm-2 col-form-label">Operation</label>
            <div class="col-sm-10">
              {!! Func::select('operation', $result['code']['operation']) !!}
            </div>

            <div id="reqBody" class="row text-center">
            @foreach($result['spec']['operation'] as $operation=>$segment)
            <form id="{{ $operation }}Form" method="POST">
            @csrf
              <div id="setForm_{{ $operation }}" class="d-none">
              <hr class="mt-3">
              <div class="row mt-2">
                <label class="col-sm-2 col-form-label">URI</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control-plaintext text-secondary" name="uri" id="uri" value="{{ $result['spec']['url'] }}" readOnly>
                  <input type="hidden" name="setUri" id="setUri" value="" readOnly>
                </div>
              </div>
              @foreach($segment['req'] as $name=>$data)
                <div class="row mt-2">
                  <label class="col-sm-2 col-form-label">{{ $name }}</label>
                  <div class="col-sm-3">
                    <input type="text" class="form-control" id="{{ strtolower($name) }}" name="data[{{ ($name) }}]" maxlength="{{ $data['len'] }}" value="{{ $data['val'] }}" @if($name=='ServiceKey') readOnly @endif>
                  </div>
                  <div class="col-sm-7">
                    <input type="text" class="form-control-plaintext text-secondary" value="{{ $data['des'] }}" disabled>
                  </div>
                </div>
              @endforeach
              </div>
            </form>
            @endforeach
          </div>
          </div>
        @endif
    @endif
    </form>
  </p>

  <p>
  <!-- <table class="table table-sm table-hover">
    <thead class="table-light">
        <th class="col-md-2">_id</th>
        <th class="col-md-5">reqdate</th>
        <th class="col-md-5">updated</th>
    </thead>
    <tbody id="apiListBody">
        <tr>
            <td colspan="3" align=center class="text-secondary">The data does not exist.</td>
        </tr>
    </tbody>
  </table> -->
    <ul class="nav nav-tabs">
      <li class="nav-item">
        <a class="nav-link bg-muted active" aria-current="page" href="#">Res</a>
      </li>
    </ul>
    <div class="mt-2 row">
      <div id="resBody" class="col-sm-12 text-start mt-2 text-center">
        <span class="text-secondary">The data does not exist.</span>
      </div>
    </div>
  </p>

<!-- Script Push -->
@push('scripts')
    <script src="{{ mix('/js/api.js') }} "></script>
    <!-- <script src="/js/kairspec.js"></script> -->
@endpush

@endsection 