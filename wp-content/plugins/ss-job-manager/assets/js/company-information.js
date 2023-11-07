(function($){
    $(document).ready(function(){
        $('.submit-company-btn').click(function(){
            var data = {
                action: 'save_company_information',
                companyData: {}
            }
            data.companyData.companyName = $('.company-name').val();
            data.companyData._company_address = $('.company-address').val();
            data.companyData._company_liscense = $('.company-liscense').val();
            data.companyData._company_industry = $('.company-industry').val();
            data.companyData._company_website = $('.company-website').val();
            data.companyData._company_email = $('.company-email').val();
            data.companyData._company_phone = $('.company-phone').val();
            data.companyData._company_title = $('.company-title').val();
            data.companyData._company_street_building = $('.company-bulding-number').val();
            data.companyData._company_building_number = $('.company-street').val();
            data.companyData._company_additional_information = $('.company-additional-address').val();
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