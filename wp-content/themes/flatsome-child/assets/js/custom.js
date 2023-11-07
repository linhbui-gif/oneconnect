jQuery(document).ready(function($) { 
    $('.multi-select').select2({
        placeholder: 'Select an option',
      });
    $('.register-candidate').click(function(){
        $('.section-choose-job-type').hide()
        $('.section-signup').show()
        $('.section-signup input[name="role"]').val('candidate')
    })
    $('.register-employer').click(function(){
        $('.section-choose-job-type').hide()
        $('.section-signup').show()
        $('.section-signup input[name="role"]').val('company')
    })
    $('.register-school').click(function(){
        $('.section-choose-job-type').hide()
        $('.section-signup').show()
        $('.section-signup input[name="role"]').val('school')
    })
    $('.account-img').click(() => {
            $('#avatar-input').trigger('click');
        })

        $("#avatar-input").on('change', function() {
            $('.account-img').attr('src', URL.createObjectURL(this.files[0]));
            let file_data = this.files[0]
            if (file_data.size > 5 * 1024 * 1024) {
                toastr.error('File size must be less than 5 Mb'); 
            }
            form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('action', 'avatar_upload');

            $.ajax({
                url: admin_ajax_url.ajaxurl,
                type: 'POST',
                contentType: false,
                processData: false,
                data: form_data,
                success: function(response) {}
            });
        });

})