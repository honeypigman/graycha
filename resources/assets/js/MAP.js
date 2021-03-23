$(document).ready(function(){

    // Marker
    $(".marker").parent('div').on('mouseover', function(){
        $(this).css("z-index", "999");
    });
    $(".marker").parent('div').on('mouseleave', function(){
        $(this).css("z-index", "-1");
    });

    $(".marker").on('mouseover', function(){
        markerInfoDetail($(this));
    });

    $(".marker").on('click', function(){        
        markerInfoDetail($(this));
    });

    // Marker Clicked
    $(".marker-pick").parent('div').css("z-index", "-999");

    // Marker Detail Close
    $(document).on('click', '.btnClose', function(e) {
        // Reset
        $(".marker-pick").removeClass();
        $(".markerDetail").html('');
    });

    // Find Me
    $("#find-me").on("click",function(){
        
        if(!click)
        {
            click=true;
            $(this).css('background-image', 'url("")');
            $(this).append(spinnerBtn);
            
            findStation();
        }
    });
});


let click=false;
var initPoint = new naver.maps.LatLng(37.572025, 127.005028);
var mapDiv = document.getElementById('map');
var map = new naver.maps.Map(mapDiv, {
    center: initPoint,
    zoom: 5
});
var spinnerBtn= "<div class='spinner-border spinner-border-sm img-center' role='status'><span class='visually-hidden'>Loading...</span></div> ";

function markerInfoDetail(selector){

    // Marker Detail Setting
    $('.markerDetail').append('<div id="markerDetail" class="alert alert-success d-none" role="alert" ><h4>[<span id="city">[시도명]</span>]<span id="stationName">[측정소명]</span></h4><div class="text-center"><h5><span class="badge bg-light text-dark w-100 mb-2" id="msg">[메세지]</span></h5></div><div class="row"><div class="col-9"><div class="text-start">미세먼지 <span id="pm10">[미세먼지]</span> ㎍/m³</div><div class="text-start">초미세먼지 <span id="pm25">[초미세먼지]</span> ㎍/m³</div></div><div class="col-3" style="margin-top:-8px; margin-left:-20px;"><img id="emoticonDetail" src="/img/grade0.png"></div></div><div class="markerReport"><canvas class="my-4 w-100 chartjs-render-monitor" id="myChart" height="290" style="display: block;"></canvas></div><button type="button" class="btn btn-light btn-sm w-100 btnClose" data-bs-dismiss="toast">닫기</button></div>');
    
    var date = $(selector).find('span.date').data('date');
    var grade = $(selector).data('grade');
    var msg = $(selector).find('span.msg').data('msg');
    var pm10 = $(selector).find('span.pm10').data('pm10');
    var pm25 = $(selector).find('span.pm25').data('pm25');
    var city = $(selector).find('span.city').data('city');
    var stationName = $(selector).find('span.station').data('station');

    // Set Detail Info
    $(selector).find('span.marker-bg').addClass('bg_'+grade);
    $("#markerDetail").removeClass();
    $("#markerDetail").addClass('alert alert-success d-block');
    $("#markerDetail").addClass('bg_'+grade);
    
    // Set station Content
    $("#msg").empty().text(msg);
    $("#pm10").empty().text(pm10);
    $("#pm25").empty().text(pm25);
    $("#city").empty().text(city);
    $("#stationName").empty().text(stationName);
    $("#emoticonDetail").attr('src', '/img/'+grade+'.png');

    // Set Chart
    setChartStationTimeFlow(date, city, stationName);
}


function addMarker(grade, msg, date, x, y, pm10=0, pm25=0, city, station){
    var icon = 'grade'+grade;
    var position = new naver.maps.LatLng(x, y);
    var markerOptions = // new naver.maps.Marker(
    {
        position: position,
        icon: 
        {
            content: '<span class="marker '+icon+'" data-grade="'+icon+'">'
                    +'<span class="emoticon"></span>'
                    +'<span class="pm10" data-pm10="'+pm10+'"></span><span class="pm25" data-pm25="'+pm25+'"></span><span class="station" data-station="'+station+'"></span><span class="city" data-city="'+city+'"></span><span class="msg" data-msg="'+msg+'"></span><span class="date" data-date="'+date+'"></span>'
                    +'<span class="marker-bg"></sapn>'
                    +'</span>',
            size: new naver.maps.Size(27, 34),
            origin: new naver.maps.Point(0, 0),
            anchor: new naver.maps.Point(11, 34)
        }
    };
    
    var marker = new naver.maps.Marker(markerOptions);
    marker.setMap(map);
    
    // Marker Zoom-In
    naver.maps.Event.addListener(marker, 'click', function() {        
        // Reset
        $(".marker-pick").removeClass();
        zoomIn(x, y);
    });
}

function zoomIn(x, y){
    var delta = 0,
        position = new naver.maps.LatLng(x, y),
        zoom = map.getZoom();

    var markerOptions = 
    {
        position: position,
        icon: 
        {
            content: '<span class="marker-pick"></span>',
            size: new naver.maps.Size(50, 50),
            origin: new naver.maps.Point(0, 0),
            anchor: new naver.maps.Point(11, 34)
        }
    };
    
    var marker = new naver.maps.Marker(markerOptions);
    marker.setMap(map);

    // Marker Position Center
    map.setCenter(position);

    delta = 12 - zoom;
    map.zoomBy(delta, new naver.maps.LatLng(x, (parseFloat(y)+parseFloat(0.029))), true);
}

function setChartStationTimeFlow(date, city, station){
    let obj_label = [];
    let obj_pm10 = [];
    let obj_pm25 = [];
    let setDate = date.substring(0,10);

    // Get Time Info
    $.ajax({
        method:"GET",
        url:"/api/find/station/timeflow/"+setDate+"/"+city+"/"+station,
        dataType : 'JSON',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success : function(rs){
            var jsonStringify = JSON.stringify(rs);
            
            $.each(rs, function(){
                obj_label.push((this.time).substring(0.2));
                obj_pm10.push(this['data']['pm10']);
                obj_pm25.push(this['data']['pm25']);
            });
            
            // Chart reset            
            $('#myChart').remove();
            $('.markerReport').append('<canvas class="my-4 w-100 chartjs-render-monitor" id="myChart" height="290" style="display: block;"></canvas>');
            
            // Set Chart
            var lineChartData = {
                labels: obj_label,
                datasets: [{
                    label: '미세',
                    borderColor: 'rgba(255, 206, 86)',
				    backgroundColor: 'rgba(255, 206, 86)',
                    fill: false,
                    data: obj_pm10,
                    yAxisID: 'y-axis-1',
                }, {
                    label: '초미세',
                    borderColor: 'rgba(255, 99, 132)',
				    backgroundColor: 'rgba(255, 99, 132)',
                    fill: false,
                    data: obj_pm25,
                    yAxisID: 'y-axis-2'
                }]
            };
            var ctx = document.getElementById('myChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: lineChartData,
                options: {
                    title: {
						display: true,
						text: date
					},
                    responsive: true,
					hoverMode: 'index',
					stacked: false,
                    scales: {
						yAxes: [{
							type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
							display: true,
							position: 'left',
							id: 'y-axis-1',
						}, {
							type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
							display: true,
							position: 'right',
							id: 'y-axis-2',

							// grid line settings
							gridLines: {
								drawOnChartArea: false, // only want the grid lines for one axis to show up
							},
						}],
					}
                }
            });
        },
        error : function(error){
            console.log('SetChartStationTimeFlow Error>'+error);
        }
    });    

}

function findStation(div='AUTO', x=null, y=null)
{
    function success(position) {
        // Set Position
        if(div=='AUTO'){
            x  = position.coords.latitude;
            y = position.coords.longitude;
        }
        
        // Spinner
        $("#find-me").text('');
        $("#find-me").css('background-image', 'url("/img/point.png")');
        
        $.ajax({
            method:"GET",
            url:"/api/find/station/"+x+"/"+y,
            dataType : 'JSON',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success : function(rs){
                //var jsonStringify = JSON.stringify(rs);

                if(rs.mesure_date){
                    var date = rs.mesure_date;
                    var grade = rs.grade;
                    var msg = rs.msg;
                    var city = rs.city;
                    var stationName = rs.stationName;
                    var pm10 = rs.mesure_pm10;
                    var pm25 = rs.mesure_pm25;
                    
                    // Set Marker Detail
                    $(".markerDetail").empty();
                    $('.markerDetail').append('<div id="markerDetail" class="alert alert-success d-block" role="alert" ><h4>[<span id="city">[시도명]</span>]<span id="stationName">[측정소명]</span></h4><div class="text-center"><h5><span class="badge bg-light text-dark w-100 mb-2" id="msg">[메세지]</span></h5></div><div class="row"><div class="col-9"><div class="text-start">미세먼지 <span id="pm10">[미세먼지]</span> ㎍/m³</div><div class="text-start">초미세먼지 <span id="pm25">[초미세먼지]</span> ㎍/m³</div></div><div class="col-3" style="margin-top:-8px; margin-left:-20px;"><img id="emoticonDetail" src="/img/grade0.png"></div></div><div class="markerReport"><canvas class="my-4 w-100 chartjs-render-monitor" id="myChart" height="290" style="display: block;"></canvas></div><button type="button" class="btn btn-light btn-sm w-100 btnClose" data-bs-dismiss="toast">닫기</button></div>');
                    $("#markerDetail").addClass('bg_grade'+grade);
                    
                    // Set station Content
                    $("#msg").empty().text(msg);
                    $("#pm10").empty().text(pm10);
                    $("#pm25").empty().text(pm25);
                    $("#city").empty().text(city);
                    $("#stationName").empty().text(stationName);
                    $("#emoticonDetail").attr('src', '/img/grade'+grade+'.png');

                    var marker = new naver.maps.Marker({
                        map: map,
                        position: new naver.maps.LatLng(x, y)
                    });
                    
                    // Set Poly Line
                    var polyline = new naver.maps.Polyline({
                        map: map,
                        path: [
                            new naver.maps.LatLng(x, y),
                            new naver.maps.LatLng(rs.dmX, rs.dmY)
                        ],
                        strokeColor: '#5347AA',
                        strokeStyle: 'longdash',
                        strokeOpacity: 0.5,
                        strokeWeight: 2
                    });

                    // Set Chart
                    setChartStationTimeFlow(date, city, stationName)

                    // Auto Zoom
                    zoomIn(rs.dmX, rs.dmY);

                    // Spinner Reset
                    spinner('submit', '검색');

                    click = false;
                }
            },
            error : function(error){
                console.log('FindStation Error>'+error);
                click=false;
            }
        });
    }        
    function error() {
        alert('ERR>Unable to retrieve your location.');
        click=false;
    }

    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(success, error);
    }
}

var infoWindow = new naver.maps.InfoWindow({
    anchorSkew: true
});

map.setCursor('pointer');

function searchAddressToCoordinate(address) 
{
    naver.maps.Service.geocode({
        query: address
        }, function(status, response) {

        if (status === naver.maps.Service.Status.ERROR) {
            setAlert('검색한 Geocode정보가 ['+address+']가 올바르지 않습니다.');
            // Spinner Reset
            spinner('submit', '검색');
            return false;
        }

        if (response.v2.meta.totalCount === 0) {
            setAlert('검색한 정보 ['+address+']가 올바르지 않습니다.');
            // Spinner Reset
            spinner('submit', '검색');
            return false;
        }

        
        {

            var htmlAddresses = [],
                item = response.v2.addresses[0],
                position = new naver.maps.Point(item.x, item.y);

            findStation('SEARCH', item.y, item.x);
        }
    });
}

function initGeocoder() {
    if (!map.isStyleMapReady) {
        return;
    }

    $('#address').on('keydown', function(e) {
        var keyCode = e.which;

        if (keyCode === 13) { // Enter Key
            spinner('submit');
            searchAddressToCoordinate($('#address').val());
        }
    });

    $('#submit').on('click', function(e) {
        if(!$('#address').val()){
            setAlert('검색할 주소 정보를 입력해주세요.');
            $('#address').focus();
            return false;
        }
        click=true;
        
        spinner('submit');

        e.preventDefault();
        searchAddressToCoordinate($('#address').val());
    });
}

naver.maps.onJSContentLoaded = initGeocoder;
naver.maps.Event.once(map, 'init_stylemap', initGeocoder);

function setAlert(msg){
    $("#search-alert").removeClass('d-none');
    $("#search-alert-msg").text(msg);
    setInterval(function(){
        $("#search-alert").addClass('d-none');
        $("#search-alert-msg").empty();
    }, 5000);

}

function spinner(selector, reset=null){
    $('#'+selector).text('');
    $('#'+selector).append(spinnerBtn);

    if(reset){
        $('#'+selector).text('');
        $('#'+selector).text('검색');
    }
}