(function($){
    var filterItems = ['job_type','experience_level','workplace','salary_range','working_location','shift','experience','job_function']
    $(document).ready(function(){
        $('.accordion-item input').change(function(){ 
            filterExploreJob();          
        })    

        function filterExploreJob(){
            data = {
                action: "filter_explore_jobs",
                paged: localStorage.getItem('currentPage') ? localStorage.getItem('currentPage') : 1,
                keyword: $('.searchform .search-field').val()
            }         
            filterItems.forEach(p => {
                data[p] = []
                $('input[name="'+p+'-checkbox"]:checked').each(function() {
                    data[p].push($(this).val());
                })
            })    
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
                    localStorage.removeItem('currentPage');
                    if(response.data){
                        if(response.data.paged == 1){
                            $('.job-card-row').html(response.data.html);
                        }
                        else{
                            $('.job-card-row').append(response.data.html);
                        }
                    }
                    console.log(response);
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
        $('.searchform .submit-button').click(function(e){
            e.prevenDefault()
        })    
        //setup before functions
        var typingTimer;                //timer identifier
        var doneTypingInterval = 1000;  //time in ms, 5 second for example
        var inputKeyWord = $('.searchform .search-field');
        //on keyup, start the countdown
        inputKeyWord.on('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function(){
                filterExploreJob()
            }, doneTypingInterval);
        });
        //on keydown, clear the countdown
        inputKeyWord.on('keydown', function () {
            clearTimeout(typingTimer);
        });
    });
})(jQuery);