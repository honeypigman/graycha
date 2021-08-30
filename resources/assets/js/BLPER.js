$(function () {
    // ToolTip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Tabs
    var tabCounter = 1;
    var tabs = $("#tabs").tabs();

    // 실시간 이슈
    hotIssue();

    // 실시간 키워드
    hotKeyword();

    // 기본설정
    $("#kw0").focus();

    $(".keywordInput").on("click", function () {
        this.select();
    });

    $(".keywordInput").on("keyup", function (key) {

        var word = validKeyword('kw0');

        if (key.keyCode == 13) {
            if (setInit()) {
                // Add Tab and Submit
                var tabs_id = addTabs(word);
                submit('GO', tabs, tabs_id);
            }
        }
    });

    // Search
    $("#go").on("click", function () {

        var word = validKeyword('kw0');

        if (setInit()) {
            // Add Tab and Submit
            var tabs_id = addTabs(word);
            submit('GO', tabs, tabs_id);
        }
    });

    // Reset
    $("#reset").on("click", function () {
        location.reload();
        // $("form")[0].reset();
    });

    // Views
    $("#views").on('click', function () {
        // His Body
        $("#viewsModalBody").empty();

        $.ajax({
            method: "POST",
            url: "/blper/views",
            dataType: 'JSON',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (rs) {
                var list = '';
                $.each(rs, function () {

                    var color = '';
                    var random = Math.random() * 4;
                    var num = Math.floor(random) + 1;

                    switch (num) {
                        case 1:
                            color = 'primary';
                            break;
                        case 2:
                            color = 'secondary';
                            break;
                        case 3:
                            color = 'success';
                            break;
                        case 4:
                            color = 'dark';
                            break;
                        default:
                            color = 'primary';
                    }
                    list += "<button type='button' class='btn btn-outline-" + color + " m-1'>" + (this.query) + "</button>";
                })
                $("#viewsModalBody").empty().append(list);
            },
            error: function (error) {
                console.log('Error>' + error);
            }
        });
    });

    // Section Copy
    $(document).on("click", ".se-module-text", function (e) {
        var curCnt = parseInt($("#copyCnt").text());
        $("#copyCnt").text(curCnt + 1);
    })

    // Relation Word Click - Add Tabs
    $(document).on("click", ".relation-word", function (e) {

        $("#kw0").val($(this).text());

        var word = validKeyword('kw0');

        // Add Tab and Submit
        var tabs_id = addTabs(word);

        submit('ADD', tabs, tabs_id);
    });

    // Close icon: removing the tab on click
    tabs.on("click", "svg.feather-x", function () {
        var panelId = $(this).closest("li").remove().attr("aria-controls");
        $("#" + panelId).remove();
        tabs.tabs("refresh");
    });


    // Selector Card
    $(document).on("click", ".item-list", function (e) {

        var path = $(this).data('path');
        var link = $(this).data('link');
        var title = $(this).data('title');

        if (path == 'hotkey') {
            $("#kw0").val(title);

            var word = validKeyword('kw0');

            if (setInit()) {
                // Add Tab and Submit
                var tabs_id = addTabs(word);
                submit('GO', tabs, tabs_id);
            }
        } else {
            window.open(link);
        }

        return false;
    })

    $(document).on("click", ".item-link", function (e) {
        var link = $(this).data('link');
        window.open(link);

        return false;
    })

    var submit = function (action = null, tabs = null, id = null) {

        // Set Scroll Point 
        $('main').animate({ scrollTop: 0, scrollLeft: 0 }, 300);

        // Background Staus
        $("#tabs").addClass("active");
        $("#wordArea").addClass("active");

        var formData = $("#form").serialize();

        $.ajax({
            method: "POST",
            url: "/blper/find",
            dataType: 'JSON',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: (formData),
            success: function (rs) {

                if (rs.code == '0000') {
                    var cnt = 1;
                    var list = '';
                    $.each(rs['items'], function () {
                        list += "<div class='item-list' data-site='" + (this.site) + "' data-title=\"" + this.title + "\" data-link='" + this.link + "' style='cursor:pointer'>";
                        list += "<span class='item-skip' title='" + (this.title) + "'>";
                        list += "<img class='list_log'src='/img/icon/logo-" + (this.site) + ".png'>" + this.title;
                        list += "</span>";
                        list += "<span class='item-date'>" + (this.date) + "</span>";
                        list += "</div>";
                    });

                    // 연관 검색어
                    var cnt = 1;
                    var words = "";
                    $.each(rs['words'], function () {
                        words += "<div class='relation-word word-list'><span class='text-word'>" + this.text + "</span></div>";
                        cnt++;
                    })

                    if (id) {

                        console.log('Tabs ID[' + rs.code + '] = ' + id);

                        // Search List
                        $("#" + id).remove();
                        tabs.append("<div id='" + id + "' class='tab-form'>" + list + "</div>");
                        tabs.tabs("refresh");

                        // Word List
                        if (action == 'GO') {
                            $("#wordList").empty().append(words);
                        }
                        $("b").css({ 'color': '#fa8ba0' });
                    }

                    // Views
                    if (rs.views > 0) {
                        $("#views").empty().text(rs.views);
                    }
                }

                else if (rs.code == '9998') {
                    alert('검색 제한 횟수 초과!');
                    var panelId = $("#tab-nav > li").first().remove().attr("aria-controls");
                    $("#" + panelId).remove();
                    tabs.tabs("refresh");

                    $("#tabs").removeClass("active");
                    $("#wordArea").removeClass("active");
                    $("#wordList").empty();
                }

                else {

                    console.log('Tabs ID[' + rs.code + '] = ' + id);

                    var msg = "<div id='" + id + "' class='set-top text-muted'>- 검색결과 없음 -</div>";

                    // Search List
                    $("#" + id).remove();
                    tabs.append(msg);
                    tabs.tabs("refresh");

                    // Word List
                    $("#wordList").empty().append(msg);
                }

            },
            error: function (error) {
                console.log('Error>' + error);
            }
        });
    }

    // 실시간 이슈
    function hotIssue() {

        // Background Staus
        $("#issueArea").addClass("active");

        // Spinner
        $("#issueList").empty().html(spinner());

        $.ajax({
            method: "POST",
            url: "/blper/issue",
            dataType: 'JSON',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (rs) {
                if (rs.code == '0000') {
                    var cnt = 1;
                    var list = "";
                    $.each(rs['items'], function () {

                        // Set Card
                        list += "<div class='item-list' data-site='" + (this.site) + "' data-title=\"" + this.title + "\" data-link='" + this.link + "'>";
                        list += "<span class='item-skip' title='" + (this.title) + "'>";
                        list += "<img class='list_log'src='/img/icon/logo-" + (this.site) + ".png'>" + (this.title);
                        list += "</span>";
                        list += "<span class='item-date'>" + (this.date) + "</span>";
                        list += "</div>";

                        cnt++;
                    })

                    $("#issueList").empty().append(list);
                } else {
                    var msg = "<div class='set-top text-muted'>- 검색결과 없음 -</div>";
                    $("#issueList").empty().append(msg);
                }
            },
            error: function (error) {
                console.log('Error>' + error);
            }
        });
    }

    // 실시간 키워드
    function hotKeyword() {

        // Background Staus
        $("#keywordArea").addClass("active");

        // Spinner
        $("#keywordList").empty().html(spinner());


        $.ajax({
            method: "POST",
            url: "/blper/keyword",
            dataType: 'JSON',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (rs) {

                if (rs.code == '0000') {
                    var cnt = 1;
                    var list = "";
                    $.each(rs['items'], function () {
                        var setDate = new Date(this.date);

                        // Set Card
                        list += "<div class='item-list' data-path='hotkey' data-site='" + (this.site) + "' data-title=\"" + this.title + "\">";
                        list += "<span class='item-skip' title='" + (this.title) + "'>";
                        list += "<img class='list_log'src='/img/icon/logo-" + (this.site) + ".png'>" + (this.title);
                        list += "</span>";
                        list += "<span class='item-link float-end px-1' data-link='" + this.link + "'><svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-external-link'><path d='M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6'></path><polyline points='15 3 21 3 21 9'></polyline><line x1='10' y1='14' x2='21' y2='3'></line></svg></span>";
                        // list+="<span class='item-date'>"+(setDate.toLocaleString()).substr(2,9)+"</span>";
                        list += "</div>";

                        cnt++;
                    })

                    $("#keywordList").empty().append(list);
                } else {
                    var msg = "<div class='set-top text-muted'>- 검색결과 없음 -</div>";
                    $("#keywordList").empty().append(msg);
                }
            },
            error: function (error) {
                console.log('Error>' + error);
            }
        });
    }

    function validKeyword(id) {
        var result = null;
        result = ($("#" + id).val()).replace(/[^ㄱ-ㅎ|ㅏ-ㅣ|가-힣a-z0-9\u318D\u119E\u11A2\u2022\u2025a\u00B7\uFE55\s-,. ]/gi, '');


        if (!result.replace(/ /g, '')) {
            $("#" + id).focus();
            $("#" + id).val('');
        } else {
            $("#" + id).val(result);
            return result;
        }
    }

    function setInit() {
        var result = false;
        if ($("#kw0").val()) {
            $("#tab-nav").css({ 'display': 'block' });
            $("#wordTitle").css({ 'display': 'block' });
            $("#wordList").empty().html(spinner());

            result = true;
        }
        return result;
    }

    function spinner() {
        return "<div class=\"set-spinner text-center\"><div class=\"spinner-grow text-primary\" role=\"status\"><span class=\"visually-hidden\">Loading...</span></div><div class=\"spinner-grow text-warning\" role=\"status\"><span class=\"visually-hidden\">Loading...</span></div><div class=\"spinner-grow text-success\" role=\"status\"><span class=\"visually-hidden\">Loading...</span></div><div class=\"spinner-grow text-danger\" role=\"status\"><span class=\"visually-hidden\">Loading...</span></div></div>";
    }

    function addTabs(word) {

        // var is_already = $("#tab-nav li:contains(" + word + ")").length;
        // if (is_already > 0) {
        //     return false;
        // }

        var id = "tab-content-" + tabCounter;
        var tabTemplate = "<li><a href='#{href}'># #{word}</a> <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-x' style='margin:5px 5px 0 -8px;'><line x1='18' y1='6' x2='6' y2='18'></line><line x1='6' y1='6' x2='18' y2='18'></line></svg></li>";
        var li = $(tabTemplate.replace(/#\{href\}/g, "#" + id).replace(/#\{word\}/g, word));

        tabs.find(".ui-tabs-nav").prepend(li);
        tabs.append("<div id='" + id + "' class='tab-form'>" + spinner() + "</div>");
        tabs.tabs("refresh");
        // tabs.tabs({ active: (tabCounter-1) });
        // 첫번째 탭
        tabs.tabs({ active: 0 });

        tabCounter++;

        return id;
    }
})