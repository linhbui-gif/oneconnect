(function($){
    $(document).ready(function(){
          $('.detail-job-wrap .button-job-card').click(function(){
            let jobId = $('.detail-job-wrap').attr('jobId')
            data = {
                action: 'save_application_information',
                jobId: jobId
            }
            $.ajax({
                url: admin_ajax_url.ajaxurl,
                type: 'Post',
                data: data,
                context: this,
                beforeSend: function() {
                    SlickLoader.enable();
                },
                success: function(response) {
                    $(this).removeClass('button-job-card');
                    SlickLoader.disable();
                    toastr.success('Applied job successfully');
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