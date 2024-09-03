jQuery(document).ready(function ($) {
    let is_triggered = false;
    let page = 1;
    let load_more   = function (){
        page++;
        $.ajax({
            type     : "post",
            dataType : "json",
            url      : bic_contest.ajaxurl,
            data     : {
                            action : "get_gallery_entries",
                            search : $('#bic_gallery_search').val(),
                            page   : page
                        },
            success  : function(response) {
                            if(response.status === "OK") {
                                $("#gallery-container").append(response.entries)                            }
                            is_triggered =false;
                        }
        })
    }

    $(window).scroll(function () {
        if(is_triggered)return;
        if ($(document).height() - $(window).scrollTop() < 1000) {
            is_triggered = true;
            load_more();
        }
    });
    $('#bic_gallery_search').on('keyup',function () {
        page = 1;
        $("#gallery-container").html("");
        $.ajax({
            type     : "post",
            dataType : "json",
            url      : bic_contest.ajaxurl,
            data     : {
                action : "get_gallery_entries",
                search : $('#bic_gallery_search').val(),
                page   : page
            },
            success  : function(response) {
                if(response.status === "OK") {
                    $("#gallery-container").append(response.entries)
                }
            }
        })
    })
});