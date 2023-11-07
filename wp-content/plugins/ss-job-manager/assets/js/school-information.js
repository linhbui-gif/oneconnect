(function($){
    $(document).ready(function(){
        $('.submit-school-btn').click(function(){
            var data = {
                action: 'save_school_information',
                schoolData: {}
            }
            data.schoolData.schoolName = $('.school-name').val();
            data.schoolData._school_address = $('.school-address').val();
            data.schoolData._school_website = $('.school-website').val();
            data.schoolData._school_email = $('.school-email').val();
            data.schoolData._school_phone = $('.school-phone').val();
            data.schoolData._school_position = $('.school-position').val();
            data.schoolData._school_street_building = $('.school-bulding-number').val();
            data.schoolData._school_building_number = $('.school-street').val();
            data.schoolData._school_additional_information = $('.school-additional-address').val();
            $.ajax({
                url: admin_ajax_url.ajaxurl,
                type: 'Post',
                data: data,
                context: this,
                beforeSend: function() {
                    SlickLoader.enable();
                },
                success: function(response) {
                    SlickLoader.disable();
                    toastr.success('Saved data');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    //Làm gì đó khi có lỗi xảy ra
                    SlickLoader.disable();
                    if (jqXHR.responseJSON) {
                        toastr.error(jqXHR.responseJSON.data, { timeOut: 3000 })
                    } else if (jqXHR.responseText) {
                        toastr.error(jqXHR.responseText, { timeOut: 3000 })
                    }
                }
            })

        })
    });
})(jQuery);