var level = null;
var canvas = null;
var ctx = null;

var arrLine = new Array();
let history = new Array();

// var MAX_WIDTH = $(".top-div").width();
// var MAX_HEIGHT = $(".top-div").height();
var MAX_WIDTH = 0;
var MAX_HEIGHT = 0;
    
var MAX_WIDTH_DISTANCE = 0;
var MAX_HEIGHT_DISTANCE = 0;
    
var MARGIN_LEFT = 23;
var MARGIN_TOP = 23;

var firstClick = true;
    
var firstPoint = "";
var secondPoint = "";

var spinnerBtn= "<div class='spinner-border spinner-border-sm img-center' role='status'><span class='visually-hidden'>Loading...</span></div> ";

$(document).ready(function() {
    canvas = document.getElementById('canvas-top');
    ctx = canvas.getContext('2d');
    
    fn_SettingLevel(1);
    fn_SettingSampleList();
    
    $(".level").click(function() {
        $("#title").val('');
        $(".level").removeClass('active');
        $(this).addClass('active');
        fn_SettingLevel($(this).attr("level"));
    });

    $(".pre").click(function(){
        fn_PreDrawLine();
    });

    $(".down").on('click', function(){
        
        var tmp = $(this).html();
        $(this).empty().append(spinnerBtn);
        
        $('html').scrollTop(0);
        
        setTimeout(function() { 
            var filename = $("#title").val();
            if(!filename) filename = 'none';
            html2canvas(document.body).then(function(canvas) {
                var doc = new jspdf.jsPDF('p', 'mm', 'a4');     // jspdf객체 생성
                var imgData = canvas.toDataURL('image/png');    // 캔버스를 이미지로 변환
                doc.addImage(imgData, 'PNG', 0, 0);             // 이미지를 기반으로 pdf생성
                doc.save(filename+'.pdf');                      // pdf저장
            });

            $(".down").empty().append(tmp);
        }, 500);            
        
    });
    
    $( ".show-option" ).tooltip({
        show: {
            effect: "slideDown",
            delay: 100
        }
    });

    $("#save").click(function(){          
        var title = $("#title").val();
        if(!title) title = 'none';
        $.ajax({
            method:"POST",
            url:"/kids/dtd/save",
            dataType : 'JSON',
            data:{
                level:level,
                title:title,
                lines:arrLine
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success : function(rs){
                var jsonStringify = JSON.stringify(rs);
                if(rs.result){
                    fn_SettingSampleList();
                }
            },
            error : function(error){
            }
        });    
    });
});

function fn_SettingLevel(_level) {
    level = _level;
    fn_InitLevel();
    
    if(_level == 1) {
        MAX_WIDTH = 7;
        MAX_HEIGHT = 6;
        MAX_WIDTH_DISTANCE = 120;
        MAX_HEIGHT_DISTANCE = 85;
    } else if (_level == 2) {
        MAX_WIDTH = 12;
        MAX_HEIGHT = 7;
        MAX_WIDTH_DISTANCE = 65;
        MAX_HEIGHT_DISTANCE = 70;   
    } else if (_level == 3) {
        MAX_WIDTH = 16;
        MAX_HEIGHT = 10;
        MAX_WIDTH_DISTANCE = 49;
        MAX_HEIGHT_DISTANCE = 48;
    }
    
    arrLine = new Array();
    
    // Create Dot
    $(".top-div").empty();
    for(var y = 0; y < MAX_HEIGHT; y++) {
        for(var x = 0; x < MAX_WIDTH; x++) {
            $(".top-div").append("<div id='" + x + "-" + y + "' class='dot'></div>")
            $("#" + x + "-" + y).css("left", MARGIN_LEFT + x * MAX_WIDTH_DISTANCE);
            $("#" + x + "-" + y).css("top", MARGIN_TOP + y * MAX_HEIGHT_DISTANCE);
        }
    }            
    $(".bottom-div").empty();
    for(var y = 0; y < MAX_HEIGHT; y++) {
        for(var x = 0; x < MAX_WIDTH; x++) {
            $(".bottom-div").append("<div id='bottom-" + x + "-" + y + "' class='dot'></div>")
            $("#bottom-" + x + "-" + y).css("left", MARGIN_LEFT + x * MAX_WIDTH_DISTANCE);
            $("#bottom-" + x + "-" + y).css("top", MARGIN_TOP + y * MAX_HEIGHT_DISTANCE);
        }
    }

    fn_ClearCanvas();
    
    $(".dot").click(function() {
        $('.dot').hover(function(){
            $(this).css('background-color','#ffb52b');
        }, function() {
            $(this).css('background-color','black');
        });
        $(".dot").css("background-color", "black");


        if(firstClick) {
            firstClick = false;
            firstPoint = $(this).attr("id");
            
            $(this).css('background-color','#ffb52b');
            $(this).hover(function(){
                $(this).css('background-color','#ffb52b');
            }, function() {
                $(this).css('background-color','#ffb52b');
            });
        } else {
            if(firstClick == false && firstPoint != $(this).attr("id")) {
                firstClick = true;
                secondPoint = $(this).attr("id");
                
                fn_ArrDrawLine(firstPoint, secondPoint);
            } else if(firstClick == false && firstPoint == $(this).attr("id")) {
                firstClick = true;
                
                firstPoint = "";
                secondPoint = "";
            }
        }
    });
}

function fn_InitLevel() {
    firstClick = true;
    
    firstPoint = "";
    secondPoint = "";
}

function fn_ArrDrawLine(_p1, _p2) 
{
    // 기존 클릭 삭제
    var existItem = false;
    var item = "";
    for(var i=0; i<arrLine.length; i++) {
        var point = arrLine[i].split(",");
        var p1 = point[0];
        var p2 = point[1];
        
        if((_p1 == p1 && _p2 == p2)) {
            existItem = true;
            item = p1 + "," + p2;
            break;
        } else if ((_p1 == p2 && _p2 == p1)) {
            existItem = true;
            item = p1 + "," + p2;
            break;
        }
    }
    
    if(existItem) {
        arrLine.splice($.inArray(item, arrLine), 1);
    } else {
        arrLine.push(_p1 + "," + _p2);
    }
    
    // draw
    fn_DrawLineAll();
    
}

function fn_PreDrawLine(){
    arrLine.splice((arrLine.length)-1, 1);
    // draw
    fn_DrawLineAll();
}

function fn_ClearCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

function fn_DrawLineAll() {
    fn_ClearCanvas();
    
    var dot_Width = $(".dot").width();
    var dot_Height = $(".dot").height();

    //console.log(arrLine);
    
    for(var i=0; i<arrLine.length; i++) {
        var point = arrLine[i].split(",");

        var p1 = $("#" + point[0]);
        var p2 = $("#" + point[1]);
        var xs = parseFloat(p1.css("left").split("px")[0]) + (dot_Width/2);
        var ys = parseFloat(p1.css("top").split("px")[0]) + (dot_Height/2);
        var xe = parseFloat(p2.css("left").split("px")[0]) + (dot_Width/2);
        var ye = parseFloat(p2.css("top").split("px")[0]) + (dot_Height/2);

        ctx.beginPath();            // Draw
        ctx.setLineDash([0, 0]);    // Line Style
        ctx.moveTo(xs, ys);         // Draw Start
        ctx.lineTo(xe, ye);         // Draw End
        
        ctx.lineWidth = 3;
        ctx.strokeStyle = "#ffb52b";
        ctx.stroke();
        ctx.closePath();
    }
}

function fn_SettingCanvas(level, array){
    
    fn_SettingLevel(level);

    // Set Array
    arrLine = [];
    var array_split = array.split('|');
    for(var i = 0; i<array_split.length; i++){
        arrLine.push(array_split[i]);
    }

    // draw
    fn_DrawLineAll();
}

function fn_SettingSampleList(){

    $.ajax({
        method:"POST",
        url:"/kids/dtd/sample",
        dataType : 'JSON',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success : function(rs){
            //var jsonStringify = JSON.stringify(rs);
            var list = "";
            $.each(rs, function(){
                list+="<div class='card' style='width: 10rem;'>";
                list+="<button class='btn btn-"+this.color+"' type='button' onClick=\"fn_SettingCanvas('"+this.level+"','"+this.spot+"')\">"+this.title+"</button>";
                list+="</div>";
            });
            $("#sample-list").empty().append(list);
        },
        error : function(error){
        }
    }); 
}