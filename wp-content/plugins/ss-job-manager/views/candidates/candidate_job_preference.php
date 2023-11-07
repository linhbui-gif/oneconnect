<section class="section" id="section_988483689">
  <div class="bg section-bg fill bg-fill bg-loaded"></div>
  <div class="section-content relative">
    <div class="row" id="row-1564427311">
      <div id="col-943639118" class="col personal-info medium-12 small-12 large-8">
        <div class="col-inner text-left" style="background-color:rgb(255, 255, 255);">
          <div id="text-2939024588" class="text">
            <h2 style="margin-bottom: 0;">Edit Job Preferences</h2>
            <style>
              #text-2939024588 {
                font-size: 1rem;
                color: rgb(0, 0, 0);
              }

              #text-2939024588>* {
                color: rgb(0, 0, 0);
              }
            </style>
          </div>
          <div id="gap-1075030039" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-1075030039 {
                padding-top: 10px;
              }
            </style>
          </div>
          <p>Tell us what your’re looking for so we can customize your job hunting experiences</p>
          <div id="gap-24240488" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-24240488 {
                padding-top: 5px;
              }

              @media (min-width:550px) {
                #gap-24240488 {
                  padding-top: 15px;
                }
              }
            </style>
          </div>
          <div class="row row-small row-inner-full" id="row-1427631550">
            <div id="col-1414775382" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <strong>Job title</strong>
                </p>
                <?php $job_functions = $terms = get_terms( array( 
                  'taxonomy' => 'job_function',
                  'hide_empty' => false
                  ));?> 
                  <?php $resume_job_functions = wp_get_post_terms($resume_id, 'job_function', array('fields' => 'ids'));?>
                    <select class="multi-select candidate-job-function" name="job_functions[]" multiple="multiple">
                      <?php foreach($job_functions as $job_function):?>
                      <option <?php echo in_array($job_function->term_id, $resume_job_functions) ? 'selected' : ''?> value="<?php echo $job_function->slug?>"><?php echo $job_function->name?></option>
                      <?php endforeach;?>
                    </select>
              </div>
            </div>
            <div id="col-257391158" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <strong>Start day</strong>
                </p>
                <div id="gap-1633472877" class="gap-element clearfix" style="display:block; height:auto;">
                  <style>
                    #gap-1633472877 {
                      padding-top: 10px;
                    }
                  </style>
                </div>
                <p>
                <?php $start_days = get_terms( array( 
                  'taxonomy' => 'start_day',
                  'hide_empty' => false
                  ));?> 
                  <?php $resume_start_days = wp_get_post_terms($resume_id, 'start_day', array('fields' => 'ids'));?>
                      <?php foreach($start_days as $start_day):?>
                        <input <?php echo in_array($start_day->term_id, $resume_start_days) ? 'checked' : ''?> type="radio" id="<?php echo $start_day->slug?>" name="start_day" class="start_day" value="<?php echo $start_day->slug?>">
                        <label for="<?php echo $start_day->slug?>"><?php echo $start_day->name?></label><br>
                      <?php endforeach;?>
                </p>
              </div>
            </div>
            <div id="col-1112753277" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <strong>Workplaces</strong>
                </p>
                <div id="gap-460007187" class="gap-element clearfix" style="display:block; height:auto;">
                  <style>
                    #gap-460007187 {
                      padding-top: 10px;
                    }
                  </style>
                </div>
                <?php $workplaces = get_terms( array( 
                  'taxonomy' => 'workplace',
                  'hide_empty' => false
                  ));?> 
                  <?php $resume_workplaces = wp_get_post_terms($resume_id, 'workplace', array('fields' => 'ids'));?>
                    <select class="multi-select candidate-workplace" name="workplaces[]" multiple="multiple">
                      <?php foreach($workplaces as $workplace):?>
                      <option <?php echo in_array($workplace->term_id, $resume_workplaces) ? 'selected' : ''?> value="<?php echo $workplace->slug?>"><?php echo $workplace->name?></option>
                      <?php endforeach;?>
                    </select>
              </div>
            </div>
            <div id="col-1097128509" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <strong>Desired working location</strong>
                </p>
                <div id="gap-1092813808" class="gap-element clearfix" style="display:block; height:auto;">
                  <style>
                    #gap-1092813808 {
                      padding-top: 10px;
                    }
                  </style>
                </div>
                <?php $working_locations = get_terms( array( 
                  'taxonomy' => 'working_location',
                  'hide_empty' => false
                  ));?> 
                  <?php $resume_working_locations = wp_get_post_terms($resume_id, 'working_location', array('fields' => 'ids'));?>
                    <select class="multi-select candidate-working-location" name="working_locations[]" multiple="multiple">
                      <?php foreach($working_locations as $working_location):?>
                      <option <?php echo in_array($working_location->term_id, $resume_working_locations) ? 'selected' : ''?> value="<?php echo $working_location->slug?>"><?php echo $working_location->name?></option>
                      <?php endforeach;?>
                    </select>
              </div>
            </div>
            <div id="col-165459434" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <strong>Shifts</strong>
                </p>
                <div id="gap-512375471" class="gap-element clearfix" style="display:block; height:auto;">
                  <style>
                    #gap-512375471 {
                      padding-top: 10px;
                    }
                  </style>
                </div>
                <?php $shifts = get_terms( array( 
                  'taxonomy' => 'shift',
                  'hide_empty' => false
                  ));?> 
                  <?php $resume_shifts = wp_get_post_terms($resume_id, 'shift', array('fields' => 'ids'));?>
                    <select class="multi-select candidate-shift" name="shifts[]" multiple="multiple">
                      <?php foreach($shifts as $shift):?>
                      <option <?php echo in_array($shift->term_id, $resume_shifts) ? 'selected' : ''?> value="<?php echo $shift->slug?>"><?php echo $shift->name?></option>
                      <?php endforeach;?>
                    </select>
              </div>

            </div>
            <div id="col-1511126455" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <strong>Job types</strong>
                </p>
                <div id="gap-677565341" class="gap-element clearfix" style="display:block; height:auto;">
                  <style>
                    #gap-677565341 {
                      padding-top: 10px;
                    }
                  </style>
                </div>
                <?php $job_listing_types = get_terms( array( 
                  'taxonomy' => 'job_listing_type',
                  'hide_empty' => false
                  ));?> 
                  <?php $resume_job_listing_types = wp_get_post_terms($resume_id, 'job_listing_type', array('fields' => 'ids'));?>
                    <select class="multi-select candidate-job-type" name="job_types[]" multiple="multiple">
                      <?php foreach($job_listing_types as $job_listing_type):?>
                      <option <?php echo in_array($job_listing_type->term_id, $resume_job_listing_types) ? 'selected' : ''?> value="<?php echo $job_listing_type->slug?>"><?php echo $job_listing_type->name?></option>
                      <?php endforeach;?>
                    </select>
              </div>

            </div>
            <div id="col-1562653322" class="col small-12 large-12">
              <div class="col-inner text-right">
                <a class="submit-job-preference-btn button primary lowercase" style="border-radius:10px;padding:3px 20px 3px 20px;">
                  <span>Submit</span>
                </a>
              </div>
            </div>
          </div>
        </div>
        <style>
          #col-943639118>.col-inner {
            padding: 25px 15px 15px 15px;
            margin: 0px 0px -25px 0px;
            border-radius: 15px;
          }

          @media (min-width:550px) {
            #col-943639118>.col-inner {
              margin: 0px 0px -15px 0px;
            }
          }
        </style>
      </div>
      <div id="col-593630704" class="col medium-12 small-12 large-4 small-col-first">
        <div class="col-inner">
          <div class="row row-small job-card-row row-inner-full" id="row-1280264132">
            <div id="col-1419759907" class="col medium-5 small-6 large-5">
              <div class="col-inner" style="background-color:rgba(255, 255, 255, 0);">
                <div class="img has-hover user-avatar-detail x md-x lg-x y md-y lg-y" id="image_1430318926">
                  <div class="img-inner image-cover dark" style="padding-top:100%;">
                    <img class="account-img" width="315" height="289" src="<?php echo !empty(get_post_meta($resume_id, '_candidate_photo', true)) ? site_url().get_post_meta($resume_id, '_candidate_photo', true) : '/wp-content/uploads/empty_avatar.jpg';?>" class="attachment-original size-original" alt="" decoding="async" loading="lazy" sizes="(max-width: 315px) 100vw, 315px">
                    <input style="display: none;" type="file"  id="candidate-avatar-input" accept="image/*">
                    <input type="hidden" class="avatar-file">
                  </div>
                  <style>
                    #image_1430318926 {
                      width: 100%;
                    }
                  </style>
                </div>
                <style>
                  .user-avatar-detail .img-inner {
                    border-color: #FF5F00;
                  }
                </style>
              </div>
            </div>
            <div id="col-1967420196" class="col medium-6 small-6 large-7">
              <div class="col-inner" style="background-color:rgba(255, 255, 255, 0);">
                <div id="text-3955221872" class="text job-title-card">
                  <p style="font-weight:bold; margin-bottom: 0;">Profile</p>
                  <style>
                    #text-3955221872 {
                      font-size: 1.15rem;
                      color: rgb(0, 0, 0);
                    }

                    #text-3955221872>* {
                      color: rgb(0, 0, 0);
                    }
                  </style>
                </div>
                <div id="gap-1019667988" class="gap-element clearfix" style="display:block; height:auto;">
                  <style>
                    #gap-1019667988 {
                      padding-top: 5px;
                    }
                  </style>
                </div>
                <p><a href="/candidate-information">Personal information</a><br><u>Job preferences</u> </p>
                <div id="text-3227789376" class="text job-title-card">
                  <p style="font-weight:bold; margin-bottom: 0;">Account</p>
                  <style>
                    #text-3227789376 {
                      font-size: 1.1rem;
                      color: rgb(0, 0, 0);
                    }

                    #text-3227789376>* {
                      color: rgb(0, 0, 0);
                    }

                    @media (min-width:550px) {
                      #text-3227789376 {
                        font-size: 1.15rem;
                      }
                    }
                  </style>
                </div>
                <div id="gap-655095541" class="gap-element clearfix" style="display:block; height:auto;">
                  <style>
                    #gap-655095541 {
                      padding-top: 5px;
                    }
                  </style>
                </div>
                <p>Account Settings</p>
              </div>
            </div>
            <style>
              #row-1280264132>.col>.col-inner {
                background-color: rgb(255, 255, 255);
                border-radius: 25px;
              }
            </style>
          </div>
        </div>
        <style>
          #col-593630704>.col-inner {
            margin: 30px 0px -25px 0px;
          }

          @media (min-width:550px) {
            #col-593630704>.col-inner {
              margin: 40px 0px 0px 0px;
            }
          }

          @media (min-width:850px) {
            #col-593630704>.col-inner {
              margin: 0px 0px 0px 0px;
            }
          }
        </style>
      </div>
    </div>
  </div>
  <style>
    #section_988483689 {
      padding-top: 26px;
      padding-bottom: 26px;
      background-color: rgb(232, 245, 255);
    }

    #section_988483689 .ux-shape-divider--top svg {
      height: 150px;
      --divider-top-width: 100%;
    }

    #section_988483689 .ux-shape-divider--bottom svg {
      height: 150px;
      --divider-width: 100%;
    }

    @media (min-width:550px) {
      #section_988483689 {
        padding-top: 40px;
        padding-bottom: 40px;
      }
    }

    @media (min-width:850px) {
      #section_988483689 {
        padding-top: 50px;
        padding-bottom: 50px;
      }
    }
  </style>
</section>