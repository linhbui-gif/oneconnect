(function($){
    $(document).ready(function(){
        $('body').on('click', '.close', function () {
            $(".modal").hide();
        })
        $('.view-invoice-btn').click(function(){
            var data = {
                action: 'get_invoice_detail',
                jobId: $(this).closest('.single-invoice').data('job')
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
                    $('.popup-invoice').html(response.data)
                    SlickLoader.disable();
                    $(".modal").show();
                    
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