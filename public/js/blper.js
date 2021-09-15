$(function(){[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function(t){return new bootstrap.Tooltip(t)});var t=1,s=$("#tabs").tabs();$("#issueArea").addClass("active"),$("#issueList").empty().html('<div class="set-spinner text-center"><div class="spinner-grow text-primary" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-warning" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-success" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-danger" role="status"><span class="visually-hidden">Loading...</span></div></div>'),$.ajax({method:"POST",url:"/blper/issue",dataType:"JSON",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},success:function(t){if("0000"==t.code){var s="";$.each(t.items,function(){s+="<div class='item-list' data-site='"+this.site+"' data-title=\""+this.title+"\" data-link='"+this.link+"'>",s+="<span class='item-skip' title='"+this.title+"'>",s+="<img class='list_log'src='/img/icon/logo-"+this.site+".png'>"+this.title,s+="</span>",s+="<span class='item-date'>"+this.date+"</span>",s+="</div>"}),$("#issueList").empty().append(s)}else $("#issueList").empty().append("<div class='set-top text-muted'>- 검색결과 없음 -</div>")},error:function(t){console.log("Error>"+t)}}),$("#keywordArea").addClass("active"),$("#keywordList").empty().html('<div class="set-spinner text-center"><div class="spinner-grow text-primary" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-warning" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-success" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-danger" role="status"><span class="visually-hidden">Loading...</span></div></div>'),$.ajax({method:"POST",url:"/blper/keyword",dataType:"JSON",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},success:function(t){if("0000"==t.code){var s="";$.each(t.items,function(){new Date(this.date),s+="<div class='item-list' data-path='hotkey' data-site='"+this.site+"' data-title=\""+this.title+'">',s+="<span class='item-skip' title='"+this.title+"'>",s+="<img class='list_log'src='/img/icon/logo-"+this.site+".png'>"+this.title,s+="</span>",s+="<span class='item-link float-end px-1' data-link='"+this.link+"'><i class='fas fa-external-link-alt'></i></span>",s+="</div>"}),$("#keywordList").empty().append(s)}else $("#keywordList").empty().append("<div class='set-top text-muted'>- 검색결과 없음 -</div>")},error:function(t){console.log("Error>"+t)}}),$("#kw0").focus(),$(".keywordInput").on("click",function(){this.select()}),$(".keywordInput").on("keypress",function(t){var r=a("kw0");if(13===t.keyCode&&i()){var o=n(r);e("GO",s,o)}}),$("#go").on("click",function(){var t=a("kw0");if(i()){var r=n(t);e("GO",s,r)}}),$("#reset").on("click",function(){location.reload()}),$("#views").on("click",function(){$("#viewsSearchHisModalBody").empty(),$.ajax({method:"POST",url:"/blper/views",dataType:"JSON",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},success:function(t){var s="";$.each(t,function(){var t="",e=4*Math.random();switch(Math.floor(e)+1){case 1:t="primary";break;case 2:t="secondary";break;case 3:t="success";break;case 4:t="dark";break;default:t="primary"}s+="<button type='button' class='searchWords btn btn-outline-"+t+" m-1'>"+this.query+"</button>"}),$("#viewsSearchHisModalBody").empty().append(s)},error:function(t){console.log("Error>"+t)}})}),$(document).on("click",".se-module-text",function(t){var s=parseInt($("#copyCnt").text());$("#copyCnt").text(s+1)}),$(document).on("click",".relation-word",function(t){var i=$(this).find(".relation-word-area").text();$("#kw0").val(i);var r=n(a("kw0"));e("ADD",s,r)}),s.on("click","i.fa-trash",function(){var t=$(this).closest("li").remove().attr("aria-controls");$("#"+t).remove(),s.tabs("refresh")}),$(document).on("click",".item-list",function(t){var r=$(this).data("path"),o=$(this).data("link"),l=$(this).data("title");if("hotkey"==r){$("#kw0").val(l);var d=a("kw0");if(i()){var c=n(d);e("GO",s,c)}}else window.open(o);return!1}),$(document).on("click",".item-link",function(t){var s=$(this).data("link");return window.open(s),!1}),$(document).on("click",".searchWords",function(t){$(".modal").modal("hide"),$("#kw0").val($(this).text());var r=a("kw0");if(i()){var o=n(r);e("GO",s,o)}});var e=function(t=null,s=null,e=null){$("#keyWordGrade").empty().text("-"),$("#monTotalCntPc").empty().text(0),$("#monTotalCntMo").empty().text(0),$("#monCnt_naver_b").empty().text(0),$("#monCnt_naver_c").empty().text(0),$("#monCnt_naver_w").empty().text(0),$("#monCnt_daum_b").empty().text(0),$("#monCnt_daum_c").empty().text(0),$("#monCnt_daum_w").empty().text(0),$("main").animate({scrollTop:0,scrollLeft:0},300),$("#tabs").addClass("active"),$("#wordArea").addClass("active");var a=$("#kw0").val(),i=$("#form").serialize();$.ajax({method:"POST",url:"/blper/find",dataType:"JSON",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},data:i,success:function(i){if("0000"==i.code){var n="";$.each(i.items,function(t){$.each(i.items[t],function(s){$.each(i.items[t][s],function(){"report"==s?$("#monCnt_"+t+"_"+this.type).empty().text(this.total):(n+="<div class='item-list' data-site='"+t+"' data-title=\""+this.title+"\" data-link='"+this.link+"' style='cursor:pointer'>",n+="<span class='item-skip' title='"+this.title+"'>",n+="<img class='list_log'src='/img/icon/logo-"+t+".png'><i class='fas fa-file-alt "+s+"-color'></i> "+this.title,n+="</span>",this.date&&(n+="<span class='item-date'>"+this.date+"</span>"),n+="</div>")})})});var r="";if($.each(i.word,function(t){$.each(i.word[t],function(s){"report"==s?($("#monTotalCntPc").empty().text(this.monTotalCntPc),$("#monTotalCntMo").empty().text(this.monTotalCntMo)):(r+="<div class='relation-word word-list'><span class='relation-word-area text-word'>",r+="<img class='list_log'src='/img/icon/logo-"+t+".png'>"+this.text,r+="</span>","naver"==t&&(r+="<span class='item-cnt'><div class='cnt-pc'><i class='fas fa-desktop fa-sm float-start'></i>"+this.pcCnt+"</div><div class='cnt-mo'><i class='fas fa-mobile-alt fa-sm float-start'></i>"+this.moCnt+"</div></span>"),r+="</div>")})}),e){$("#"+e).remove(),s.append("<div id='"+e+"' class='tab-form'>"+n+"</div>"),s.tabs("refresh"),"GO"==t&&$("#wordList").empty().append(r),$("b").css({color:"#fa8ba0"}),$("#keyWordGrade").empty().text(i.grade);var o="text-white";i.grade>0&&i.grade<3?o="text-success":i.grade>2&&i.grade<5?o="text-pramary":i.grade>4&&i.grade<8?o="text-warning":i.grade>7&&(o="text-danger"),$("#keyWordGrade").removeClass(),$("#keyWordGrade").addClass(o),$("#monthlyReport").removeClass("d-none"),c=a,p=$("#keyWordGrade").text(),v=$("#monTotalCntPc").text(),m=$("#monTotalCntMo").text(),u=$("#monCnt_naver_b").text(),h=$("#monCnt_naver_c").text(),f=$("#monCnt_daum_b").text(),y=$("#monCnt_daum_c").text(),g="",g+="<tr>",g+="<th scope='row'>"+c+"</th>",g+="<td>"+v+"<br/>"+m+"</td>",g+="<td>"+u+" <br/>"+f+"</td>",g+="<td>"+h+" <br/>"+y+"</td>",g+="<td>"+p+"</td>",g+="</tr>",$("#viewsMonthlyHisModalBody tbody").prepend(g)}i.views>0&&$("#views").empty().text(i.views)}else if("9998"==i.code){alert("검색 제한 횟수 초과!");var l=$("#tab-nav > li").first().remove().attr("aria-controls");$("#"+l).remove(),s.tabs("refresh"),$("#tabs").removeClass("active"),$("#wordArea").removeClass("active"),$("#wordList").empty()}else{var d="<div id='"+e+"' class='set-top text-muted'>- 검색결과 없음 -</div>";$("#"+e).remove(),s.append(d),s.tabs("refresh"),$("#wordList").empty().append(d)}var c,p,v,m,u,h,f,y,g},error:function(t){console.log("Error>"+t)}})};function a(t){var s=null;if((s=$("#"+t).val().replace(/[^ㄱ-ㅎ|ㅏ-ㅣ|가-힣a-z0-9\u318D\u119E\u11A2\u2022\u2025a\u00B7\uFE55\s-,. ]/gi,"")).replace(/ /g,""))return $("#"+t).val(s),s;$("#"+t).focus(),$("#"+t).val("")}function i(){var t=!1;return $("#kw0").val()&&($("#searchArea").removeClass("d-none"),$("#tab-nav").css({display:"block"}),$("#wordTitle").css({display:"block"}),$("#wordList").empty().html('<div class="set-spinner text-center"><div class="spinner-grow text-primary" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-warning" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-success" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-danger" role="status"><span class="visually-hidden">Loading...</span></div></div>'),t=!0),t}function n(e){var a="tab-content-"+t,i=$("<li><a href='#{href}'># #{word}</a><i class='fas fa-trash fa-sm' style='margin:7px 5px 0 -5px; cursor:pointer;'></i></li>".replace(/#\{href\}/g,"#"+a).replace(/#\{word\}/g,e));return s.find(".ui-tabs-nav").prepend(i),s.append("<div id='"+a+'\' class=\'tab-form\'><div class="set-spinner text-center"><div class="spinner-grow text-primary" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-warning" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-success" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-danger" role="status"><span class="visually-hidden">Loading...</span></div></div></div>'),s.tabs("refresh"),s.tabs({active:0}),t++,a}});
