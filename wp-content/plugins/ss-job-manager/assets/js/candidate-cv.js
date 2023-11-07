(function($){
    $(document).ready(function(){
          $('.account-img').click(() => {
            $('#candidate-avatar-input').trigger('click');
          })
          $("#candidate-avatar-input").on('change', function() {
              $('.account-img').attr('src', URL.createObjectURL(this.files[0]));
              let file_data = this.files[0]
              if (file_data.size > 5 * 1024 * 1024) {
                  toastr.error('File size must be less than 5 Mb'); 
              }
              form_data = new FormData();
              form_data.append('file', file_data);
              form_data.append('action', 'upload_file');

              $.ajax({
                  url: admin_ajax_url.ajaxurl,
                  type: 'POST',
                  contentType: false,
                  processData: false,
                  data: form_data,
                  success: function(response) 
                  {
                      $('.avatar-file').val(response.data);
                  }
              });
          });
          $('body').on('change', '#resume-file-input', function() {
            
            $this = $(this);
            file_data = $(this).prop('files')[0];
            if (file_data.size > 5 * 1024 * 1024) {
                toastr.error('File size must be less than 5 Mb'); 
            }
            form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('action', 'upload_file');
            
            $.ajax({
                url: admin_ajax_url.ajaxurl,
                type: 'POST',
                contentType: false,
                processData: false,
                data: form_data,
                beforeSend: function() {
                    SlickLoader.enable();
                },
                success: function (response) {
                    SlickLoader.disable();
                    $('.resume-file').val(response.data);
                    $('.display-file').hide()
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    //Làm gì đó khi có lỗi xảy ra
                    SlickLoader.disable();
                }
            });
        });

        $('.submit-job-preference-btn').click(function(){
            var data = {
                action: 'save_resume_job_preference'
            }
            if(data && data.resumeId){
              data.resumeId = data.resumeId
            }
            data.job_listing_type = $('.candidate-job-type').val();
            data.workplace = $('.candidate-workplace').val();
            data.shift = $('.candidate-shift').val();
            data.job_function = $('.candidate-job-function').val();
            data.working_location = $('.candidate-working-location').val();
            data.start_day = $('.start_day').val();
            data._candidate_photo = $('.avatar-file').val()
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
        $('.submit-cv-btn').click(function(){
            var data = {
                action: 'save_resume_information'
            }
            if(data && data.resumeId){
              data.resumeId = data.resumeId
            }
            if($('.candidate-name').length > 0){
                data.candidateName = $('.candidate-name').val()
            }
            if($('.candidate-dob').length > 0){
                data._candidate_birthday = $('.candidate-dob').val()                
            }
            if($('.candidate-gender').length > 0){
                data._candidate_gender = $('.candidate-gender').val()     
            }
            if($('.candidate-email').length > 0){
                data._candidate_email = $('.candidate-email').val()     
            }
            if($('.candidate-phone').length > 0){
                data._candidate_phone = $('.candidate-phone').val()     
            }
            if($('.candidate-address').length > 0){
                data._candidate_location = $('.candidate-address').val()     
            }
            if($('.resume-file').length > 0){
                data._resume_file = $('.resume-file').val()     
            }
            if($('.candidate-about').length > 0){
                data.candidateAbout = $('.candidate-about').val()
            }
            data._candidate_photo = $('.avatar-file').val()
           data.resume_skill = $('.candidate-skills').val();
           data.resume_education_location = []
           data.resume_education_qualification = []
           data.resume_education_date = []
           data.resume_education_gpa = []
           data.resume_experience_employer = []
           data.resume_experience_job_title = []
           data.resume_experience_date = []
           data.resume_experience_notes = []
           $('.edu-university').each(function(){   
            data.resume_education_location.push($(this).val())
           })
           $('.edu-major').each(function(){
            data.resume_education_qualification.push($(this).val())
           })
           $('.edu-time').each(function(){
            data.resume_education_date.push($(this).val())
           })
           $('.edu-gpa').each(function(){
            data.resume_education_gpa.push($(this).val())
           })
           $('.exp-company').each(function(){
            data.resume_experience_employer.push($(this).val())
           })
           $('.exp-position').each(function(){
            data.resume_experience_job_title.push($(this).val())
           })
           $('.exp-time').each(function(){
            data.resume_experience_date.push($(this).val())
           })
           $('.exp-describe').each(function(){
            data.resume_experience_notes.push($(this).val())
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

        $('.candidate-experiences-row .add-item-btn').click(function(){
            var htmlString = `<div id="col-1885368316" class="col medium-4 small-12 large-4">
                      <div class="col-inner">
                        <p>
                          <input class="exp-company" placeholder="Company Name" type="text">
                          <br>
                        </p>
                      </div>
                      <style>
                        #col-1885368316>.col-inner {
                          margin: 0px 0px -15px 0px;
                        }
                      </style>
                    </div>
                    <div id="col-1694309535" class="col medium-4 small-12 large-4">
                      <div class="col-inner">
                        <p>
                          <input class="exp-position" placeholder="Position" type="text">
                          <br>
                        </p>
                      </div>
                      <style>
                        #col-1694309535>.col-inner {
                          margin: 0px 0px -15px 0px;
                        }
                      </style>
                    </div>
                    <div id="col-1242845482" class="col medium-4 small-12 large-4">
                      <div class="col-inner">
                        <p>
                          <input class="exp-time" placeholder="From - to" type="text">
                          <br>
                        </p>
                      </div>
                      <style>
                        #col-1242845482>.col-inner {
                          margin: 0px 0px -15px 0px;
                        }
                      </style>
                    </div>
                    <div id="col-2061565189" class="col small-12 large-12">
                      <div class="col-inner">
                        <p>
                          <input class="exp-describe" placeholder="Describe your work..." type="text">
                          <br>
                        </p>
                      </div>
                      <style>
                        #col-2061565189>.col-inner {
                          margin: 0px 0px 0px 0px;
                        }
                      </style>
                    </div>`;
            $('.candidate-experience-inner').append(htmlString)
        })

        $('.candidate-education-row .add-item-btn').click(function(){
            var htmlString = `<div id="col-677662108" class="col medium-6 small-12 large-6">
                <div class="col-inner">
                  <p>
                      <input class="edu-university" placeholder="University" type="text">
                    <br>
                  </p>
                </div>
              </div>
              <div id="col-2069040347" class="col medium-6 small-12 large-6">
                <div class="col-inner">
                  <p>
                      <input class="edu-major" placeholder="Major" type="text">
                    <br>
                  </p>
                </div>
              </div>
              <div id="col-566436318" class="col medium-6 small-12 large-6">
                <div class="col-inner">
                  <p>
                    <input class="edu-time" placeholder="From - to" type="text">
                    <br>
                  </p>
                </div>
              </div>
              <div id="col-535151683" class="col medium-6 small-12 large-6">
                <div class="col-inner">
                  <p>
                    <input class="edu-gpa" placeholder="GPA" type="text">
                    <br>
                  </p>
                </div>
              </div>`;
            $('.candidate-education-inner').append(htmlString)
        })
    });
})(jQuery);