$(function(){[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function(t){return new bootstrap.Tooltip(t)});var t=1,s=$("#tabs").tabs();$("#issueArea").addClass("active"),$("#issueList").empty().html('<div class="set-spinner text-center"><div class="spinner-grow text-primary" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-warning" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-success" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-danger" role="status"><span class="visually-hidden">Loading...</span></div></div>'),$.ajax({method:"POST",url:"/blper/issue",dataType:"JSON",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},success:function(t){if("0000"==t.code){var s="";$.each(t.items,function(){s+="<div class='item-list' data-site='"+this.site+"' data-title=\""+this.title+"\" data-link='"+this.link+"'>",s+="<span class='item-skip' title='"+this.title+"'>",s+="<img class='list_log'src='/img/icon/logo-"+this.site+".png'>"+this.title,s+="</span>",s+="<span class='item-date'>"+this.date+"</span>",s+="</div>"}),$("#issueList").empty().append(s)}else $("#issueList").empty().append("<div class='set-top text-muted'>- 검색결과 없음 -</div>")},error:function(t){console.log("Error>"+t)}}),$("#keywordArea").addClass("active"),$("#keywordList").empty().html('<div class="set-spinner text-center"><div class="spinner-grow text-primary" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-warning" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-success" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-danger" role="status"><span class="visually-hidden">Loading...</span></div></div>'),$.ajax({method:"POST",url:"/blper/keyword",dataType:"JSON",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},success:function(t){if("0000"==t.code){var s="";$.each(t.items,function(){new Date(this.date),s+="<div class='item-list' data-path='hotkey' data-site='"+this.site+"' data-title=\""+this.title+'">',s+="<span class='item-skip' title='"+this.title+"'>",s+="<img class='list_log'src='/img/icon/logo-"+this.site+".png'>"+this.title,s+="</span>",s+="<span class='item-link float-end px-1' data-link='"+this.link+"'><i class='fas fa-external-link-alt'></i></span>",s+="</div>"}),$("#keywordList").empty().append(s)}else $("#keywordList").empty().append("<div class='set-top text-muted'>- 검색결과 없음 -</div>")},error:function(t){console.log("Error>"+t)}}),$("#kw0").focus(),$(".keywordInput").on("click",function(){this.select()}),$(".keywordInput").on("keypress",function(t){var o=e("kw0");if(13===t.keyCode&&i()){var r=n(o);a("GO",s,r)}}),$("#go").on("click",function(){var t=e("kw0");if(i()){var o=n(t);a("GO",s,o)}}),$("#reset").on("click",function(){location.reload()}),$("#views").on("click",function(){$("#viewsSearchHisModalBody").empty(),$.ajax({method:"POST",url:"/blper/views",dataType:"JSON",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},success:function(t){var s="";$.each(t,function(){var t="",a=4*Math.random();switch(Math.floor(a)+1){case 1:t="primary";break;case 2:t="secondary";break;case 3:t="success";break;case 4:t="dark";break;default:t="primary"}s+="<button type='button' class='btn btn-outline-"+t+" m-1'>"+this.query+"</button>"}),$("#viewsSearchHisModalBody").empty().append(s)},error:function(t){console.log("Error>"+t)}})}),$(document).on("click",".se-module-text",function(t){var s=parseInt($("#copyCnt").text());$("#copyCnt").text(s+1)}),$(document).on("click",".relation-word",function(t){var i=$(this).find(".relation-word-area").text();$("#kw0").val(i);var o=n(e("kw0"));a("ADD",s,o)}),s.on("click","i.fa-trash",function(){var t=$(this).closest("li").remove().attr("aria-controls");$("#"+t).remove(),s.tabs("refresh")}),$(document).on("click",".item-list",function(t){var o=$(this).data("path"),r=$(this).data("link"),l=$(this).data("title");if("hotkey"==o){$("#kw0").val(l);var d=e("kw0");if(i()){var c=n(d);a("GO",s,c)}}else window.open(r);return!1}),$(document).on("click",".item-link",function(t){var s=$(this).data("link");return window.open(s),!1});var a=function(t=null,s=null,a=null){$("#monTotalCntPc").empty().text(0),$("#monTotalCntMo").empty().text(0),$("#monCnt_naver_b").empty().text(0),$("#monCnt_naver_c").empty().text(0),$("#monCnt_naver_w").empty().text(0),$("#monCnt_daum_b").empty().text(0),$("#monCnt_daum_c").empty().text(0),$("#monCnt_daum_w").empty().text(0),$("main").animate({scrollTop:0,scrollLeft:0},300),$("#tabs").addClass("active"),$("#wordArea").addClass("active");var e=$("#kw0").val(),i=$("#form").serialize();$.ajax({method:"POST",url:"/blper/find",dataType:"JSON",headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")},data:i,success:function(i){if("0000"==i.code){var n="";$.each(i.items,function(t){$.each(i.items[t],function(s){$.each(i.items[t][s],function(){"report"==s?$("#monCnt_"+t+"_"+this.type).empty().text(this.total):(n+="<div class='item-list' data-site='"+t+"' data-title=\""+this.title+"\" data-link='"+this.link+"' style='cursor:pointer'>",n+="<span class='item-skip' title='"+this.title+"'>",n+="<img class='list_log'src='/img/icon/logo-"+t+".png'><i class='fas fa-file-alt "+s+"-color'></i> "+this.title,n+="</span>",this.date&&(n+="<span class='item-date'>"+this.date+"</span>"),n+="</div>")})})});var o="";$.each(i.word,function(t){$.each(i.word[t],function(s){"report"==s?($("#monTotalCntPc").empty().text(this.monTotalCntPc),$("#monTotalCntMo").empty().text(this.monTotalCntMo)):(o+="<div class='relation-word word-list'><span class='relation-word-area text-word'>",o+="<img class='list_log'src='/img/icon/logo-"+t+".png'>"+this.text,o+="</span>","naver"==t&&(o+="<span class='item-cnt'><div class='cnt-pc'><i class='fas fa-desktop fa-sm float-start'></i>"+this.pcCnt+"</div><div class='cnt-mo'><i class='fas fa-mobile-alt fa-sm float-start'></i>"+this.moCnt+"</div></span>"),o+="</div>")})}),a&&($("#"+a).remove(),s.append("<div id='"+a+"' class='tab-form'>"+n+"</div>"),s.tabs("refresh"),"GO"==t&&$("#wordList").empty().append(o),$("b").css({color:"#fa8ba0"}),$("#monthlyReport").removeClass("d-none"),d=e,c=$("#monTotalCntPc").text(),p=$("#monTotalCntMo").text(),v=$("#monCnt_naver_b").text(),m=$("#monCnt_naver_c").text(),u=$("#monCnt_naver_w").text(),h=$("#monCnt_daum_b").text(),f=$("#monCnt_daum_c").text(),y=$("#monCnt_daum_w").text(),w="",w+="<tr>",w+="<th scope='row'>"+d+"</th>",w+="<td>"+c+"</td>",w+="<td>"+p+"</td>",w+="<td>"+v+" <br/>"+h+"</td>",w+="<td>"+m+" <br/>"+f+"</td>",w+="<td>"+u+" <br/>"+y+"</td>",w+="<td>-</td>",w+="</tr>",$("#viewsMonthlyHisModalBody tbody").append(w)),i.views>0&&$("#views").empty().text(i.views)}else if("9998"==i.code){alert("검색 제한 횟수 초과!");var r=$("#tab-nav > li").first().remove().attr("aria-controls");$("#"+r).remove(),s.tabs("refresh"),$("#tabs").removeClass("active"),$("#wordArea").removeClass("active"),$("#wordList").empty()}else{var l="<div id='"+a+"' class='set-top text-muted'>- 검색결과 없음 -</div>";$("#"+a).remove(),s.append(l),s.tabs("refresh"),$("#wordList").empty().append(l)}var d,c,p,v,m,u,h,f,y,w},error:function(t){console.log("Error>"+t)}})};function e(t){var s=null;if((s=$("#"+t).val().replace(/[^ㄱ-ㅎ|ㅏ-ㅣ|가-힣a-z0-9\u318D\u119E\u11A2\u2022\u2025a\u00B7\uFE55\s-,. ]/gi,"")).replace(/ /g,""))return $("#"+t).val(s),s;$("#"+t).focus(),$("#"+t).val("")}function i(){var t=!1;return $("#kw0").val()&&($("#tab-nav").css({display:"block"}),$("#wordTitle").css({display:"block"}),$("#wordList").empty().html('<div class="set-spinner text-center"><div class="spinner-grow text-primary" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-warning" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-success" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-danger" role="status"><span class="visually-hidden">Loading...</span></div></div>'),t=!0),t}function n(a){var e="tab-content-"+t,i=$("<li><a href='#{href}'># #{word}</a><i class='fas fa-trash fa-sm' style='margin:7px 5px 0 -5px; cursor:pointer;'></i></li>".replace(/#\{href\}/g,"#"+e).replace(/#\{word\}/g,a));return s.find(".ui-tabs-nav").prepend(i),s.append("<div id='"+e+'\' class=\'tab-form\'><div class="set-spinner text-center"><div class="spinner-grow text-primary" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-warning" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-success" role="status"><span class="visually-hidden">Loading...</span></div><div class="spinner-grow text-danger" role="status"><span class="visually-hidden">Loading...</span></div></div></div>'),s.tabs("refresh"),s.tabs({active:0}),t++,e}});
