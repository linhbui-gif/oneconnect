(function($){
    $(document).ready(function(){
        $('.post-job-btn').click(function(){
            var data = {
                action: 'save_job_information',
                jobData: {}
            }
            data.jobData.jobTitle = $('.job-title').val();
            data.jobData.jobContent = $('.job-des').val();
            data.jobData._job_gender = $('.job-gender').val();
            data.jobData._job_location = $('.job-address').val();
            data.jobData.working_location = $('.working-location').val();
            data.jobData._job_certification = $('.job-certification').val();
            data.jobData._job_gender = $('.job-gender').val();
            data.jobData.shift = $('.shift').val();
            data.jobData.salary_range = $('.salary-range').val();
            data.jobData.experience_level = $('.experience-level').val();
            data.jobData.job_function = $('.job-function').val();
            data.jobData.job_listing_type = $('.job-type').val();
            data.jobData._job_benefit = $('.job-benefit').val();
            if(typeof jobInfo !== "undefined" && jobInfo.jobId){
                data.jobData.jobId = jobInfo.jobId
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
                    document.location.href = '/edit-job/jobId/'+response.data
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