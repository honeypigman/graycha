$(function () {
    // ToolTip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Tabs
    var tabCounter = 1;
    var tabs = $("#tabs").tabs();

    // Chard Default
    setChart();

    // 실시간 이슈
    hotIssue();

    // 실시간 키워드
    hotKeyword();

    // 기본설정
    $("#kw0").focus();

    $(".keywordInput").on("click", function () {
        this.select();
    });

    $(".keywordInput").on("keypress", function (key) {

        var word = validKeyword('kw0');

        if (key.keyCode === 13) {
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

    // Trend
    $("#trend").on("click", function () {
        monthlyTrend();
    });
    $("#btn-trend-exchange").on("click", function () {
        monthlyTrend();
    });

    // Views
    $("#views").on('click', function () {
        // His Body
        $("#viewsSearchHisModalBody").empty();

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
                    list += "<button type='button' class='searchWords btn btn-outline-" + color + " m-1'>" + (this.query) + "</button>";
                })
                $("#viewsSearchHisModalBody").empty().append(list);
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

        var getText = $(this).find('.relation-word-area').text();

        $("#kw0").val(getText);

        var word = validKeyword('kw0');

        if (setInit()) {
            // Add Tab and Submit
            var tabs_id = addTabs(word);

            submit('ADD', tabs, tabs_id);
        }
    });

    // Close icon: removing the tab on click
    tabs.on("click", "i.fa-trash", function () {
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

    // 검색이력 찾기
    $(document).on("click", ".searchWords", function (e) {

        $(".modal").modal("hide");

        $("#kw0").val($(this).text());
        var word = validKeyword('kw0');

        if (setInit()) {
            // Add Tab and Submit
            var tabs_id = addTabs(word);
            submit('GO', tabs, tabs_id);
        }
    });

    $(document).on("click", "canvas", function (e) {

        $(".modal").modal("hide");
        $("#kw0").val($(this).data('keyword'));
        var word = validKeyword('kw0');

        if (setInit()) {
            // Add Tab and Submit
            var tabs_id = addTabs(word);
            submit('GO', tabs, tabs_id);
        }

    });

    var submit = function (action = null, tabs = null, id = null) {

        // MonthlyReport Reset
        setInitMonthlyInfo();

        // Set Scroll Point 
        $('main').animate({ scrollTop: 0, scrollLeft: 0 }, 300);

        // Background Staus
        $("#tabs").addClass("active");
        $("#wordArea").addClass("active");

        // Keyword
        var keyword = $("#kw0").val();
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

                var no_msg = "<div id='" + id + "' class='set-top text-muted'>- 검색결과 없음 -</div>";

                if (rs.code == '0000') {

                    // 검색결과
                    var cnt = 1;
                    var list = '';
                    $.each(rs['items'], function (site) {
                        $.each(rs['items'][site], function (contents) {
                            $.each(rs['items'][site][contents], function () {
                                if (contents == 'report') {
                                    $("#monCnt_" + site + "_" + (this.type)).empty().text((this.total));
                                } else {
                                    list += "<div class='item-list' data-site='" + (site) + "' data-title=\"" + this.title + "\" data-link='" + this.link + "' style='cursor:pointer'>";
                                    list += "<span class='item-skip' title='" + (this.title) + "'>";
                                    list += "<img class='list_log'src='/img/icon/logo-" + (site) + ".png'><i class='fas fa-file-alt " + (contents) + "-color'></i> " + this.title;
                                    list += "</span>";
                                    if (this.date)
                                        list += "<span class='item-date'>" + (this.date) + "</span>";
                                    list += "</div>";
                                }
                            });
                        });
                    });

                    // 연관 검색어
                    var cnt = 1;
                    var words = "";

                    $.each(rs.word, function (target) {
                        $.each(rs.word[target], function (contents) {
                            if (contents == 'report') {
                                $("#monTotalCntPc").empty().text(this.monTotalCntPc);
                                $("#monTotalCntMo").empty().text(this.monTotalCntMo);
                            } else {
                                words += "<div class='relation-word word-list'><span class='relation-word-area text-word'>";
                                words += "<img class='list_log'src='/img/icon/logo-" + (target) + ".png'>" + this.text;
                                words += "</span>";
                                if (target == 'naver') {
                                    words += "<span class='item-cnt'><div class='cnt-pc'><i class='fas fa-desktop fa-sm float-start'></i>" + (this.pcCnt) + "</div><div class='cnt-mo'><i class='fas fa-mobile-alt fa-sm float-start'></i>" + (this.moCnt) + "</div></span>";
                                }
                                words += "</div>";
                            }
                        })
                    });

                    // Set Chart
                    setChart(rs['trend']);

                    if (id) {
                        // Search List
                        $("#" + id).remove();
                        tabs.append("<div id='" + id + "' class='tab-form'>" + list + "</div>");
                        tabs.tabs("refresh");

                        // Word List
                        //if (action == 'GO') 
                        {
                            $("#wordList").empty().append(words);
                        }
                        $("b").css({ 'color': '#fa8ba0' });

                        // Keyword Grade
                        $("#keyWordGrade").empty().text(rs.grade);
                        var gradeColor = 'text-white';
                        if (rs.grade > 0 && rs.grade < 3) {
                            gradeColor = 'text-success';
                        } else if (rs.grade > 2 && rs.grade < 5) {
                            gradeColor = 'text-pramary';
                        } else if (rs.grade > 4 && rs.grade < 8) {
                            gradeColor = 'text-warning';
                        } else if (rs.grade > 7) {
                            gradeColor = 'text-danger';
                        }
                        $("#keyWordGrade").removeClass();
                        $("#keyWordGrade").addClass(gradeColor);

                        // Line Show
                        $("#monthlyReport").removeClass('d-none');

                        // Monthly His ADD
                        monthlyReportHisAdd(keyword);
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
                    // Search List
                    $("#" + id).remove();
                    tabs.append(no_msg);
                    tabs.tabs("refresh");
                }

                // Word List
                if (!words) {
                    $("#wordList").empty().append(no_msg);
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
                        list += "<span class='item-link float-end px-1' data-link='" + this.link + "'><i class='fas fa-external-link-alt'></i></span>";
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

    // 월별 트랜드 추이
    function monthlyTrend() {

        $("#viewsMonthlyTrendModalBody").empty().html(spinner());

        $.ajax({
            method: "POST",
            url: "/blper/trend",
            dataType: 'JSON',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (rs) {

                if (rs.code == '0000') {

                    $("#viewsMonthlyTrendModalBody").empty();

                    var cnt = 1;
                    var chart = "";

                    $.each(rs['items'], function (idx) {

                        var id = 'trendChart_' + idx;

                        // 초기화
                        $('#' + id).remove();
                        $('#viewsMonthlyTrendModalBody').append('<div class="flex-grow-1 ps-4"><canvas class="my-4 bchartjs-render-monitor" id="' + id + '"" data-keyword="' + this.keyword + '" ></canvas></div>');

                        datas = {
                            labels: this.period,
                            datasets: [
                                {
                                    label: '추이',
                                    backgroundColor: 'rgb(255 183 0 / 72%)',
                                    fill: false,
                                    data: this.ratio,
                                    yAxisID: 'y-axis-1',
                                }
                            ]
                        };

                        var ctx = document.getElementById(id).getContext('2d');
                        var { id } = new Chart(ctx, {
                            type: 'bar',
                            data: datas,
                            options: {
                                plugins: {
                                    title: {
                                        display: true,
                                        text: this.keyword
                                    },
                                    legend: {
                                        display: false
                                    }
                                },
                                maintainAspectRatio: false,
                                responsive: false
                            }
                        });

                        cnt++;
                    })
                }
            },
            error: function (error) {
                console.log('Error>' + error);
            }
        });
    }

    function validKeyword(id) {
        var result = null;
        result = ($("#" + id).val()).replace(/[^ㄱ-힣a-zA-Z0-9\u318D\u119E\u11A2\u2022\u2025a\u00B7\uFE55\s-,. ]/gi, '');

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

            $("#monthlyReport").addClass('d-none');
            $("#searchArea").removeClass('d-none');
            $("#tab-nav").css({ 'display': 'block' });
            $("#wordTitle").css({ 'display': 'block' });
            $("#wordList").empty().html(spinner());

            result = true;
        }
        return result;
    }

    function spinner() {
        return "<div class=\"set-spinner text-center w-100\"><div class=\"spinner-grow text-primary\" role=\"status\"><span class=\"visually-hidden\">Loading...</span></div><div class=\"spinner-grow text-warning\" role=\"status\"><span class=\"visually-hidden\">Loading...</span></div><div class=\"spinner-grow text-success\" role=\"status\"><span class=\"visually-hidden\">Loading...</span></div><div class=\"spinner-grow text-danger\" role=\"status\"><span class=\"visually-hidden\">Loading...</span></div></div>";
    }

    function addTabs(word) {

        // var is_already = $("#tab-nav li:contains(" + word + ")").length;
        // if (is_already > 0) {
        //     return false;
        // }

        var id = "tab-content-" + tabCounter;
        var tabTemplate = "<li><a href='#{href}'># #{word}</a><i class='fas fa-trash fa-sm' style='margin:7px 5px 0 -5px; cursor:pointer;'></i></li>";
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

    function setInitMonthlyInfo() {
        $("#keyWordGrade").empty().text('-');

        $("#monTotalCntPc").empty().text(0);
        $("#monTotalCntMo").empty().text(0);

        $("#monCnt_naver_b").empty().text(0);
        $("#monCnt_naver_c").empty().text(0);
        $("#monCnt_naver_w").empty().text(0);

        $("#monCnt_daum_b").empty().text(0);
        $("#monCnt_daum_c").empty().text(0);
        $("#monCnt_daum_w").empty().text(0);
    }

    function monthlyReportHisAdd(word) {
        var grade = $("#keyWordGrade").text();

        var monTotalCntPc = $("#monTotalCntPc").text();
        var monTotalCntMo = $("#monTotalCntMo").text();

        var monCnt_naver_b = $("#monCnt_naver_b").text();
        var monCnt_naver_c = $("#monCnt_naver_c").text();
        // var monCnt_naver_w = $("#monCnt_naver_w").text();

        var monCnt_daum_b = $("#monCnt_daum_b").text();
        var monCnt_daum_c = $("#monCnt_daum_c").text();
        // var monCnt_daum_w = $("#monCnt_daum_w").text();

        var tr = "";
        tr += "<tr>"
        tr += "<th scope='row'>" + word + "</th>";
        tr += "<td>" + monTotalCntPc + "<br/>" + monTotalCntMo + "</td>";

        tr += "<td>" + monCnt_naver_b + " <br/>" + monCnt_daum_b + "</td>";
        tr += "<td>" + monCnt_naver_c + " <br/>" + monCnt_daum_c + "</td>";
        // tr += "<td>" + monCnt_naver_w + " <br/>" + monCnt_daum_w + "</td>";

        tr += "<td>" + grade + "</td>";
        tr += "</tr>"

        $("#viewsMonthlyHisModalBody tbody").prepend(tr);

    }

    function setChart(datas = null) {

        let trend_all = [];
        let trend_bg = [];
        let trend_period = [];
        // let trend_pc = [];
        // let trend_mo = [];

        // 초기화
        $('#myChart').remove();
        $('#ketywordTrendChart').append('<canvas class="my-4 w-100 bchartjs-render-monitor" id="myChart" style="display: block;"></canvas>');

        // 검색 시
        if (datas) {

            var trend_sum_ratio = 0;
            // 데이터 셋팅
            $.each(datas, function (device) {
                $.each(datas[device], function () {
                    trend_sum_ratio += this.ratio;

                    trend_bg.push('rgb(255 183 0 / 72%)');
                    eval('trend_' + device).push(this.ratio);
                    if (trend_period.indexOf(this.period) < 0)
                        trend_period.push(this.period);

                });
            });

            // 평균값
            if (trend_sum_ratio > 0) {
                trend_bg.push('rgb(0 76 216 / 72%)');
                trend_all.push(Math.floor(trend_sum_ratio / datas['all'].length));
                trend_period.push('평균');
            }
        }

        datas = {
            labels: trend_period,
            datasets: [
                {
                    label: '추이',
                    backgroundColor: trend_bg,
                    fill: false,
                    data: trend_all,
                    yAxisID: 'y-axis-1',
                }
                // {
                //     label: 'pc',
                //     backgroundColor: 'rgb(75 182 55 / 72%)',
                //     fill: false,
                //     data: trend_pc,
                //     yAxisID: 'y-axis-1',
                // }, {
                //     label: 'mobile',
                //     backgroundColor: 'rgb(0 76 216 / 72%)',
                //     fill: false,
                //     data: trend_mo,
                //     yAxisID: 'y-axis-1'
                // } 
            ]
        };

        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: datas,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: '키워드 추이'
                    },
                    legend: {
                        display: false
                    }
                },
                maintainAspectRatio: false,
                responsive: true
            }
        });
    }
})