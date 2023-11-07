(function($){
    $(document).ready(function(){
        function filter_candidate(){
            var data = {
                action: 'filter_candidate',
                jobId: $('.candidate-jobs').val()
            }
            if($('.candidate-filter-status').val()){
                data.status = $('.candidate-filter-status').val()
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
                    SlickLoader.disable();
                    $('.candidate-collection').html(response.data.html);
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
        }
        $('body').on('change', '.candidate-jobs', function () {
            filter_candidate()
        })
        $('body').on('change', '.candidate-filter-status', function () {
            filter_candidate()
        })
        $('body').on('change', '.candidate-single-status', function () {
            var data = {
                action: 'save_application_status',
                applicationId: $(this).closest('.candidate-card').data('candidate'),
                status: $(this).val()
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
                    SlickLoader.disable();
                    location.reload(true);
                    
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
        $('body').on('change', '.interview-jobs', function () {
            filter_interview()
        })
        //setup before functions
        var typingTimer;                //timer identifier
        var doneTypingInterval = 1000;  //time in ms, 5 second for example
        var inputKeyWord = $('.interview-search-keyword');
        //on keyup, start the countdown
        inputKeyWord.on('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function(){
                filter_interview()
            }, doneTypingInterval);
        });
        //on keydown, clear the countdown
        inputKeyWord.on('keydown', function () {
            clearTimeout(typingTimer);
        });
        function filter_interview(){
            var data = {
                action: 'filter_interview',
                jobId: $('.interview-jobs').val()
            }
            if($('.interview-search-keyword').val()){
                data.keyword = $('.interview-search-keyword').val()
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
                    SlickLoader.disable();
                    $('.interview-collection').html(response.data.html);
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
        }
        $('body').on('click', '.interview-btn', function () {
            $(".modal").show();
            if($(this).closest('.interview-card').find('.candidate-interview-time').html()){
                $('.interview-time').val($(this).closest('.interview-card').find('.candidate-interview-time').html())
            }
            if($(this).closest('.interview-card').find('.candidate-interview-date').html()){ 
                $('.interview-date').val($(this).closest('.interview-card').find('.candidate-interview-date').html())
            }
            localStorage.setItem('activeInterviewCandidate', $(this).closest('.interview-card').data('candidate'));    
            
        })
        $('body').on('click', '.close', function () {
            $(".modal").hide();
        })
        $('.update-interview-btn').click(function(){
            var data = {
                action: 'update_interview',
                candidateId: localStorage.getItem('activeInterviewCandidate'),
                interviewDate: $('.interview-date').val(),
                interviewTime: $('.interview-time').val()
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
                    $(".modal").hide();
                    SlickLoader.disable();
                    const activeCandidate = localStorage.getItem('activeInterviewCandidate')
                    $("tr[data-candidate='" + activeCandidate +"']").find('.candidate-interview-time').html($('.interview-time').val());
                    $("tr[data-candidate='" + activeCandidate +"']").find('.candidate-interview-date').html($('.interview-date').val());
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