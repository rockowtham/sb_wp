(function ($) {
    "use strict";



    $('#ad_catsdiv').hide();
    $('#ad_tagsdiv').hide();
    $('#ad_conditiondiv').hide();
    $('#ad_typediv').hide();
    $('#ad_warrantydiv').hide();

    /* icheck callback */
    $('.skin-minimal .list li input').iCheck({
        checkboxClass: 'icheckbox_minimal',
        radioClass: 'iradio_minimal',
        increaseArea: '20%'
    });

    /* select callback */
    $(".select-2").select2({
        allowClear: true,
        width: '100%'
    });

    /* get custom template when we use category base form */
    var is_category_based = $("#is_category_based").val();
    function getCustomTemplate_admin(ajax_url, catId, updateId, is_top)
    {
        /*For Category Templates*/
        $.post(carspot_ajax_url.ajax_url, {action: 'sb_get_sub_template_admin', 'cat_id': catId, 'is_update': updateId, }).done(function (response)
        {
            if ($.trim(response) != "")
            {
                $("#dynamic-fields_admin").html(response);
                $('.skin-minimal .list li input').iCheck({
                    checkboxClass: 'icheckbox_minimal',
                    radioClass: 'iradio_minimal',
                    increaseArea: '20%'
                });
                $('#dynamic-fields select').select2();
//                if ($('#input_ad_post_form_type').val() == 1) {
//                    sbDropzone_image();
//                }

                carspot_inputTags();
				
 			$( document ).ready(function() {
				//$('.dynamic-form-date-fields').datepicker({});
				$('.dynamic-form-date-fields').datepicker({
					timepicker: false,
					dateFormat: 'yyyy-mm-dd',
					language: {
						days: [carspot_ajax_url.Sunday, carspot_ajax_url.Monday, carspot_ajax_url.Tuesday, carspot_ajax_url.Wednesday, carspot_ajax_url.Thursday, carspot_ajax_url.Friday, carspot_ajax_url.Saturday],
						daysShort: [carspot_ajax_url.Sun, carspot_ajax_url.Mon, carspot_ajax_url.Tue, carspot_ajax_url.Wed, carspot_ajax_url.Thu, carspot_ajax_url.Fri, carspot_ajax_url.Sat],
						daysMin: [carspot_ajax_url.Su, carspot_ajax_url.Mo, carspot_ajax_url.Tu, carspot_ajax_url.We, carspot_ajax_url.Th, carspot_ajax_url.Fr, carspot_ajax_url.Sa],
						months: [carspot_ajax_url.January, carspot_ajax_url.February, carspot_ajax_url.March, carspot_ajax_url.April, carspot_ajax_url.May, carspot_ajax_url.June, carspot_ajax_url.July, carspot_ajax_url.August, carspot_ajax_url.September, carspot_ajax_url.October, carspot_ajax_url.November, carspot_ajax_url.December],
						monthsShort: [carspot_ajax_url.Jan, carspot_ajax_url.Feb, carspot_ajax_url.Mar, carspot_ajax_url.Apr, carspot_ajax_url.May, carspot_ajax_url.Jun, carspot_ajax_url.Jul, carspot_ajax_url.Aug, carspot_ajax_url.Sep, carspot_ajax_url.Oct, carspot_ajax_url.Nov, carspot_ajax_url.Dec],
						today: carspot_ajax_url.Today,
						clear: carspot_ajax_url.Clear,
						dateFormat: 'mm/dd/yyyy',
						firstDay: 0
					},
				});
			});	
            }
            $('#sb_loading').hide();
            if (is_category_based == 1)
            {
                if (is_top)
                {
                    $.post(carspot_ajax_url.ajax_url, {action: 'sb_get_car_total', }).done(function (cartTotal)
                    {
                        $('#sb-quick-cart-price').html(cartTotal);
                    });
                }
            }

        });
        /*For Category Templates*/
    }


    /* ============END============== */

    /* Tags in admin side classified adds */
    function carspot_inputTags()
    {
        $('#tags').tagsInput({
            'width': '100%',
            'height': '5px;',
            'defaultText': '',
        });
    }


    /* Working on Make, Model, Version etc */
    $('#ad_cat_sub_div').hide();
    $('#ad_cat_sub_sub_div').hide();
    $('#ad_cat_sub_sub_sub_div').hide();
    if ($('#is_update').val() != "")
    {
        var level = $('#is_level').val();
        if (level >= 2)
        {
            $('#ad_cat_sub_div').show();
        }
        if (level >= 3)
        {
            $('#ad_cat_sub_sub_div').show();
        }
        if (level >= 4)
        {
            $('#ad_cat_sub_sub_sub_div').show();
        }

        var country_level = $('#country_level').val();
        if (country_level >= 2)
        {
            $('#ad_country_sub_div').show();
        }
        if (country_level >= 3)
        {
            $('#ad_country_sub_sub_div').show();
        }
        if (country_level >= 4)
        {
            $('#ad_country_sub_sub_sub_div').show();
        }

    }


    /* Level 1 Select Make */
    $('#ad_cat').on('change', function ()
    {
        $('#sb_loading').show();
        $.post(carspot_ajax_url.ajax_url, {action: 'sb_get_sub_cat', cat_id: $("#ad_cat").val(), }).done(function (response)
        {
            $('#sb_loading').hide();
            $("#ad_cat_sub").val('');
            $("#ad_cat_sub_sub").val('');
            $("#ad_cat_sub_sub_sub").val('');
            if ($.trim(response) != "" && $.trim(response) != 0)
            {
                $('#ad_cat_id').val($("#ad_cat").val());
                $('#ad_cat_sub_div').show();
                $('#ad_cat_sub').html(response);
                $('#ad_cat_sub_sub_div').hide();
                $('#ad_cat_sub_sub_sub_div').hide();
            }
            else
            {
                $('#ad_cat_sub_div').hide();
                $('#ad_cat_sub_sub_div').hide();
                $('#ad_cat_sub_sub_sub_div').hide();

            }
            /*For Category Templates*/
            getCustomTemplate_admin(carspot_ajax_url.ajax_url, $("#ad_cat").val(), $("#is_update").val(), true);
			
		
            /*For Category Templates*/

        });
    });

    /* Level 2 Select Model */
    $('#ad_cat_sub').on('change', function ()
    {
        $('#sb_loading').show();
        $.post(carspot_ajax_url.ajax_url, {action: 'sb_get_sub_cat', cat_id: $("#ad_cat_sub").val(), }).done(function (response)
        {
            $('#sb_loading').hide();
            $("#ad_cat_sub_sub").val('');
            $("#ad_cat_sub_sub_sub").val('');
            if ($.trim(response) != "" && $.trim(response) != 0)
            {
                $('#ad_cat_id').val($("#ad_cat_sub").val());
                $('#ad_cat_sub_sub_div').show();
                $('#ad_cat_sub_sub').html(response);
                $('#ad_cat_sub_sub_sub_div').hide();
            }
            else
            {
                $('#ad_cat_sub_sub_div').hide();
                $('#ad_cat_sub_sub_sub_div').hide();
            }
            /*For Category Templates*/
            getCustomTemplate_admin(carspot_ajax_url.ajax_url, $("#ad_cat_sub").val(), $("#is_update").val(), false);
            /*For Category Templates*/
        });
    });

    /* Level 3 Select Version */
    $('#ad_cat_sub_sub').on('change', function ()
    {
        $('#sb_loading').show();
        $.post(carspot_ajax_url.ajax_url, {action: 'sb_get_sub_cat', cat_id: $("#ad_cat_sub_sub").val(), }).done(function (response)
        {
            $('#sb_loading').hide();
            $("#ad_cat_sub_sub_sub").val('');
            if ($.trim(response) != "" && $.trim(response) != 0)
            {
                $('#ad_cat_id').val($("#ad_cat_sub_sub").val());
                $('#ad_cat_sub_sub_sub_div').show();
                $('#ad_cat_sub_sub_sub').html(response);
            }
            else
            {
                $('#ad_cat_sub_sub_sub_div').hide();
            }
            /*For Category Templates*/
            getCustomTemplate_admin(carspot_ajax_url.ajax_url, $("#ad_cat_sub_sub").val(), $("#is_update").val(), false);
            /*For Category Templates*/

        });
    });

    /* Level 4 */
    $('#ad_cat_sub_sub_sub').on('change', function ()
    {
        $('#ad_cat_id').val($("#ad_cat_sub_sub_sub").val());
        /*For Category Templates*/
        getCustomTemplate_admin(carspot_ajax_url.ajax_url, $("#ad_cat_sub_sub_sub").val(), $("#is_update").val(), false);
        /*For Category Templates*/

    });

    /*============END=============*/
    /*Country Selection*/
    $('#ad_country_sub_div').hide();
    $('#ad_country_sub_sub_div').hide();
    $('#ad_country_sub_sub_sub_div').hide();
    $('#ad_country_sub_sub_sub_div').hide();
    if ($('#is_update').val() != "")
    {
        var country_level = $('#country_level').val();
        if (country_level >= 2)
        {
            $('#ad_country_sub_div').show();
        }
        if (country_level >= 3)
        {
            $('#ad_country_sub_sub_div').show();
        }
        if (country_level >= 4)
        {
            $('#ad_country_sub_sub_sub_div').show();
        }
    }
    /* Level 1 */
    $('#ad_country').on('change', function ()
    {
        $('#sb_loading').show();
        $.post(carspot_ajax_url.ajax_url, {
            action: 'sb_get_sub_states', 
            cat_id: $("#ad_country").val(),
        }).done(function (response)
        {
            $('#sb_loading').hide();
            $("#ad_country_states").val('');
            $("#ad_country_cities").val('');
            $("#ad_country_towns").val('');
            if ($.trim(response) != "")
            {
                $('#ad_country_id').val($("#ad_cat").val());
                $('#ad_country_sub_div').show();
                $('#ad_country_states').html(response);
                $('#ad_country_sub_sub_div').hide();
                $('#ad_country_sub_sub_sub_div').hide();
            }
            else
            {
                $('#ad_country_sub_div').hide();
                $('#ad_country_sub_sub_div').hide();
                $('#ad_country_sub_sub_sub_div').hide();

            }

        });
    });

    /* Level 2 */
    $('#ad_country_states').on('change', function ()
    {
        $('#sb_loading').show();
        $.post(carspot_ajax_url.ajax_url, {
            action: 'sb_get_sub_states',
            cat_id: $("#ad_country_states").val(), 
        }).done(function (response)
        {
            $('#sb_loading').hide();
            $("#ad_country_cities").val('');
            $("#ad_country_towns").val('');
            if ($.trim(response) != "" && $.trim(response) != 0)
            {
                $('#ad_country_id').val($("#ad_country_states").val());
                $('#ad_country_sub_sub_div').show();
                $('#ad_country_cities').html(response);
                $('#ad_country_sub_sub_sub_div').hide();
            }
            else
            {
                $('#ad_country_sub_sub_div').hide();
                $('#ad_country_sub_sub_sub_div').hide();
            }
        });
    });

    /* Level 3 */
    $('#ad_country_cities').on('change', function ()
    {
        $('#sb_loading').show();
        $.post(carspot_ajax_url.ajax_url, {
            action: 'sb_get_sub_states', 
            cat_id: $("#ad_country_cities").val(),
        }).done(function (response)
        {
            $('#sb_loading').hide();
            $("#ad_country_towns").val('');
            if ($.trim(response) != "" && $.trim(response) != 0)
            {
                $('#ad_country_id').val($("#ad_country_cities").val());
                $('#ad_country_sub_sub_sub_div').show();
                $('#ad_country_towns').html(response);
            }
            else
            {
                $('#ad_country_sub_sub_sub_div').hide();
            }
        });
    });
    /*==============END===============*/

    /*====== Price Type ======*/

    /*working when change the price type from dropdown*/
      var price = $('#hiden_pric').val();
    if(price == ''){
        $('#ad_pricees').hide();
    }
    $(document).on('change', '#ad_price_type', function ()
    {
      
        if (this.value == "on_call" || this.value == "free" || this.value == "no_price" || this.value == "")
        {
            $('#ad_price').attr("data-parsley-required", "false")
            $('#ad_price').val('');
            $('#ad_pricees').hide();
        }
        else
        {
            $('#ad_price').attr("data-parsley-required", "true")
            $('#ad_pricees').show();
        }
    });
    /* ==============END================ */

    /* gallery images on admin side.  */
    var meta_gallery_frame;
    $('#carspot_ad_gallery_button').on('click', function (e) {
        // sonu code here.
        if (meta_gallery_frame) {
            meta_gallery_frame.open();
            return;
        }
        // Sets up the media library frame
        meta_gallery_frame = wp.media.frames.meta_gallery_frame = wp.media({
            title: carspot_ajax_url.ads_img_title,
            button: {text: carspot_ajax_url.ads_img_upload_btn},
            library: {type: 'image'},
            multiple: true
        });
        // Create Featured Gallery state. This is essentially the Gallery state, but selection behavior is altered.
        meta_gallery_frame.states.add([
            new wp.media.controller.Library({
                priority: 20,
                toolbar: 'main-gallery',
                filterable: 'uploaded',
                library: wp.media.query(meta_gallery_frame.options.library),
                multiple: meta_gallery_frame.options.multiple ? 'reset' : false,
                editable: true,
                allowLocalEdits: true,
                displaySettings: true,
                displayUserSettings: true
            }),
        ]);
        var idsArray;
        var attachment;
        meta_gallery_frame.on('open', function () {
            var selection = meta_gallery_frame.state().get('selection');
            var library = meta_gallery_frame.state('gallery-edit').get('library');
            var ids = $('#carspot_photo_arrangement_').val();
            if (ids) {
                idsArray = ids.split(',');
                idsArray.forEach(function (id) {
                    attachment = wp.media.attachment(id);
                    attachment.fetch();
                    selection.add(attachment ? [attachment] : []);
                });
            }
        });
        meta_gallery_frame.on('ready', function () {
            $('.media-modal').addClass('no-sidebar');
        });
        var images;
        // When an image is selected, run a callback.
        //meta_gallery_frame.on('update', function() {          
        var count = 0;
        meta_gallery_frame.on('select', function () {
            var imageIDArray = [];
            var imageHTML = '';
            var metadataString = '';
            images = meta_gallery_frame.state().get('selection');
            imageHTML += '<ul class="carspot_gallery">';
            images.each(function (attachment) {
                // get image object
                console.debug(attachment.attributes);
                //push/add the ids in array
                imageIDArray.push(attachment.attributes.id);
                imageHTML += '<li><div class="carspot_gallery_container"><span class="carspot_delete_icon"><img id="' + attachment.attributes.id + '" src="' + attachment.attributes.url + '" style="max-width:100%;"></span></div></li>';
            });
            imageHTML += '</ul>';
            metadataString = imageIDArray.join(",");
            if (metadataString) {
                $("#carspot_photo_arrangement_").val(metadataString);
                $("#carspot_admin_gall_render").html(imageHTML);
            }
        });
        // Finally, open the modal
        meta_gallery_frame.open();
    });

    $(document.body).on('click', '.carspot_delete_icon', function (event) {
        event.preventDefault();
        if (confirm(carspot_ajax_url.ads_img_upload_btn))
        {
            var removedImage = $(this).children('img').attr('id');
            var oldGallery = $("#carspot_photo_arrangement_").val();
            var newGallery = oldGallery.replace(',' + removedImage, '').replace(removedImage + ',', '').replace(removedImage, '');
            //var newGallery = oldGallery.replace(','+removedImage,'');
            $(this).parents().eq(1).remove();
            $("#carspot_photo_arrangement_").val(newGallery);
        }
    });
    /*==============*/

})(jQuery);


