jQuery(document).ready(function ($) {
    let $country = $('select.select2.acfcs__dropdown--countries');
    let $city    = $('select.select2.acfcs__dropdown--states');
    let getUrlParameter = function (sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    };
    let validateForm    = function (){
        $error = getUrlParameter('error');
        switch ($error){
            case "email_exits":
                $("#email-invalid").removeClass('hidden');
                break;
            case "img_0_size":
                $("#img-error-0-size").removeClass('hidden');
                break;
            case "img_1_size":
                $("#img-error-1-size").removeClass('hidden');
                break;
            case "img_2_size":
                $("#img-error-2-size").removeClass('hidden');
                break;

            case "img_0_filename":
                $("#img-error-0-filename").removeClass('hidden');
                break;
            case "img_1_filename":
                $("#img-error-1-filename").removeClass('hidden');
                break;
            case "img_2_filename":
                $("#img-error-2-filename").removeClass('hidden');
                break;
        }
    }
    validateForm();

    $country.select2({
        allowClear: false,
    });
    $country.on("select2:select", function (e) {
        $city.val(null).trigger("change");
     });
    $country.on("change", function (e) {
        $city.val(null).trigger("change");
     });
    $city.select2({
        allowClear: false,
        ajax: {
            url: city_selector_vars.ajaxurl,
            dataType: 'json',
            type: "POST",
            data: function () {
                return {
                    action: 'get_states_call',
                    country_code: $('select.select2.acfcs__dropdown--countries').select2('val'),
                    post_id: "",
                    show_labels: 1
                };
            },
            processResults: function (data) {
                let result =[];
                data.forEach(function (arrayItem) {
                    result.push({
                        "id": arrayItem.country_state,
                        "text": arrayItem.state_name
                    })
                });
                return {
                        results:result
                };
            }
        }
    });

    $('.add-container').click(function (e) {
      let $file =  $(this).data('file');
      $("#"+$file).trigger('click');
    });
    $('.edit-img').click(function (e) {
      let $file =  $(this).data('file');
      let $file_Elem =  $("#"+$file);
      $file_Elem.trigger('click');
      $file_Elem.parent().find('.bic-menu-btn').trigger('click');
    });
    $('.delete-img').click(function (e) {
        let $file      =  $(this).data('file');
        let $file_Elem =  $("#"+$file);
        $file_Elem.val('');
        $file_Elem.parent().find('.img-container').addClass('hidden');
        $file_Elem.parent().find('.circle').removeClass('hidden');
        $file_Elem.parent().find('.add-container').removeClass('hidden');
        $file_Elem.parent().find('.delete_this').val('confirmed');
        $file_Elem.parent().find('.bic-menu-btn').trigger('click');
    });

    $('.file-uploader').on('change', function(event) {
        let current_element = $(this);
        let $file = this.files[0];
        const validImageTypes = ['image/jpg', 'image/jpeg', 'image/png'];

        if($file.size>10485760) {
            current_element.parent().find('.size-error').removeClass('hidden');
            $('#wp-submit').attr('disabled','disabled');
            return;
        }else if(!validImageTypes.includes($file.type)){
            current_element.parent().find('.type-error').removeClass('hidden');
            $('#wp-submit').attr('disabled','disabled');
            return;
        }else{
            current_element.parent().find('.error').addClass('hidden');
            $('#wp-submit').removeAttr('disabled');
        }
        // let $name = current_element.data('name');
        // $("#"+$name).val(event.target.files[0].name);
        let $circle =  current_element.data('circle');
        $("#"+$circle).addClass('hidden');
        let $img = current_element.data('img');

        if ($file) {
            let reader = new FileReader();
            reader.onload = function (event) {
                $("#" + $img).attr("src", event.target.result);
                $("#" + $img).parent().removeClass('hidden');
                $("#" + $img).removeClass('hidden');
                current_element.parent().find('.add-container').addClass('hidden');
            }
            reader.readAsDataURL($file);
        }
    });

    $('.bic-menu-btn').click(function (){
        let $overlay =  $(this).data('overlay');
        $("#"+$overlay).toggleClass('overlay-full-opacity');
    });
});