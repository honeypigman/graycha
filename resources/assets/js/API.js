$(document).ready(function(){

    // Input Auto Select
    $("input").on("click",function(){
        this.select();
    });
    
    // Send By Key Enter
    $("input").on("keyup",function(key){
        if(key.keyCode==13) {
            send();
        }
    });

    // Send By Click
    $("#btnSend").on('click', function(){
        send();
    })  

    // Init
    //submit('list', 'GET', 'array');

    // Operation Setting
    $("#operation").on('change', function(){
        $("#resBody").empty().html('<span class="text-secondary">The data does not exist.</span>');

        var operation = $(this).val();
        $("div[id^='setForm_']").addClass('d-none');
        $("#setForm_"+operation).removeClass('d-none');
        
        // Set Uri
        var getUri = $("#"+operation+"Form [id='uri']").val();
        $("#"+operation+"Form [id='setUri']").val('');
        $("#"+operation+"Form [id='setUri']").val(getUri+'/'+operation);
    }); 
});


var send = function(){
    var api = $("#api").text();
    var id  = $("#api_id").text();
    var formData = $("#form").serializeObject();

    if( api=='kairspec' ){
        var operation = $("#operation").val();
        if(!operation){
            alert('Warn> Make a choice [Operation]');
            $("#operation").focus();
            return false;
        }
        // 초기화 설정
        $('#setOperation').remove();
        $('<input>').attr({
            type: 'hidden',
            id: 'setOperation',
            name: 'setOperation',
            value:operation
        }).appendTo($("#"+operation+"Form"));
        formData = $("#"+operation+"Form").serializeObject();
    }

    // Spinner
    var spinnerBtn= "<div class='spinner-border spinner-border-sm' role='status'></div><span><h6 class=\"text-secondary pl-1 d-inline\"> Loading...</h6></span>";
    $("#resBody").empty().append(spinnerBtn);
    
    $.ajax({
        method:"POST",
        url:"/send",
        dataType : 'JSON',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data:(formData),
        success : function(rs){
            
            var rows = 0;
            var result = rs.header['resultCode'];
            var msg = rs.header['resultMsg'];
            
            if(result == '00'){
                cnt=0;
                resResult = "";
                
                // API에 따른 응답결과 뷰 재진행 예정
                if( api=='kairspec' ){
                    
                    // Api Id에 따른 응답값 분기
                    switch(id){
                        case 'SCOA0901':
                            rows = rs.body['totalCount'];
                            break;
                        case 'SCOA0902':
                            rows = rs.body['numOfRows'];
                            break;
                        default:
                            rows = rs.body['numOfRows'];
                    }
                    if( rs.body['totalCount']>0 ){
                        $.each(rs.body['items'][['item']], function(k, v){               
                            if ( rows>1 ){
                                cnt=0;
                                $.each(v, function(k, v){
                                    resResult+=setResForm(cnt, k, v);
                                    cnt++;
                                });
                            }else{
                                resResult+=setResForm(cnt, k, v);                                                 
                            }
                            cnt++;
                        });
                    }else{
                        resResult+="<span class=\"text-secondary\">There are no search results.</span>";
                    }
                }

                $("#resBody").empty().append(resResult);

            }else{
                alert('Error> '+result+":"+msg);    
                return false;
            }
        },
        error : function(error){
            alert('Error>'+error);
        }
    });
}

var setResForm = function(no, k, v){
    var result = "<div class=\"accordion\" id=\"accordionExample\"></div><div class=\"accordion-item\">";    
    if( cnt==0 ){
        result+="<h2 class=\"accordion-header\" id=\"heading"+no+"\"><button class=\"accordion-button\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#collapse"+no+"\" aria-expanded=\"false\" aria-controls=\"collapse"+no+"\">";
        result+=v;
        result+="</button></h2>";
    }else{
        result+="<div id=\"collapse"+no+"\" class=\"accordion-collapse collapse show\" aria-labelledby=\"heading"+no+"\" data-bs-parent=\"#accordionExample\">";
            result+="<div class=\"accordion-body\">";
                result+="<div class=\"row\" style=\"margin:-20px;\">";
                    result+="<label class=\"col-sm-2 col-form-label\">"+k+"</label>";
                    result+="<div class=\"col-sm-10\">";
                        result+="<input type=\"text\" class=\"form-control-plaintext text-secondary\" value=\""+v+"\" disabled>";
                    result+="</div>";
                result+="</div>";
            result+="</div>";
        result+="</div>";
    }
    result+="</div></div>";

    return result;
}

/**
 * 
 * @param {*} action : list / show / store / update / destroy
 * @param {*} method  : get / post / delete / put
 */
var submit = function(action, method='POST', $type='json'){
    var formData = $("#form").serializeObject();
    $.ajax({
        method:method,
        url:'/api/'+action+'/'+$type,
        dataType : 'JSON',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data:(formData),
        success : function(rs){

            if( $type=='json' ){
                var jsonStringify = JSON.stringify(rs);
                var list = JSON.parse(jsonStringify);
            }
            apiList="";
            $.each(rs, function(){
                apiList+="<tr>";
                    apiList+="<td>"+this._id+"</td>";
                    apiList+="<td>"+this.reqdate+"</td>";
                    apiList+="<td>"+this.updated_at+"</td>";
                apiList+="</tr>";
            });
            $("#apiListBody").empty().append(apiList);

        },
        error : function(error){
            alert('Error>'+error);
        }
    });
} 