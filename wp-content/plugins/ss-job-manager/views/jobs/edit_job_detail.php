<section class="section" id="section_1690306891">
  <div class="bg section-bg fill bg-fill bg-loaded"></div>
  <div class="section-content relative">
    <div class="row" id="row-1329469078">
      <div id="col-1032203863" class="col personal-info medium-12 small-12 large-8">
        <div class="col-inner text-left" style="background-color:rgb(255, 255, 255);">
          <div id="text-1269857603" class="text">
            <h2 style="margin-bottom: 0;">Job Detail</h2>
            <style>
              #text-1269857603 {
                font-size: 1rem;
                color: rgb(0, 0, 0);
              }

              #text-1269857603>* {
                color: rgb(0, 0, 0);
              }
            </style>
          </div>
          <div id="gap-1763179578" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-1763179578 {
                padding-top: 5px;
              }

              @media (min-width:550px) {
                #gap-1763179578 {
                  padding-top: 15px;
                }
              }
            </style>
          </div>
          <div class="row row-inner-full" id="row-490066752">
            <div id="col-368971304" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_the_title($job_id);?>" class="job-title" placeholder="Job Title" type="text">
                  <br>
                </p>
              </div>
            </div>
          </div>
          <div id="text-1841291974" class="text">
            <h4 style="margin-bottom: 0;">Location</h4>
            <style>
              #text-1841291974 {
                font-size: 1rem;
                color: rgb(0, 0, 0);
              }

              #text-1841291974>* {
                color: rgb(0, 0, 0);
              }
            </style>
          </div>
          <div id="gap-735480578" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-735480578 {
                padding-top: 5px;
              }

              @media (min-width:550px) {
                #gap-735480578 {
                  padding-top: 15px;
                }
              }
            </style>
          </div>
          <div class="row row-small row-inner-full" id="row-1144759287">
            <div id="col-1244241905" class="col medium-6 small-12 large-6">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_post_meta($job_id, '_job_location', true)?>" class="job-address" placeholder="Address" type="text">
                  <br>
                </p>
              </div>
            </div>
            <div id="col-1567364984" class="col medium-6 small-12 large-6">
              <div class="col-inner">
                <p>
                <?php $locations = get_terms( array( 
                  'taxonomy' => 'working_location',
                  'hide_empty' => false
                  ));?> 
                  <?php $job_locations = wp_get_post_terms($job_id, 'working_location', array('fields' => 'ids'));?>
                    <select class="working-location" name="working-location">
                      <?php foreach($locations as $location):?>
                      <option <?php echo in_array($location->term_id, $job_locations) ? 'selected' : ''?> value="<?php echo $location->slug?>"><?php echo $location->name?></option>
                      <?php endforeach;?>
                    </select>
                  <br>
                </p>
              </div>
            </div>
            <div id="col-1080070621" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                <?php $experience_levels = get_terms( array( 
                  'taxonomy' => 'experience_level',
                  'hide_empty' => false
                  ));?> 
                  <?php $job_experience_levels = wp_get_post_terms($job_id, 'experience_level', array('fields' => 'ids'));?>
                    <select class="experience-level" name="experience-level">
                      <?php foreach($experience_levels as $experience_level):?>
                      <option <?php echo in_array($experience_level->term_id, $job_experience_levels) ? 'selected' : ''?> value="<?php echo $experience_level->slug?>"><?php echo $experience_level->name?></option>
                      <?php endforeach;?>
                    </select>
                  <br>
                </p>
              </div>
            </div>
            <div id="col-1054173571" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_post_meta($job_id, '_job_certification', true)?>" class="job-certification" placeholder="Certificate/Education" type="text">
                  <br>
                </p>
              </div>
            </div>
            <div id="col-996942397" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                <?php $salary_ranges =  get_terms( array( 
                  'taxonomy' => 'salary_range',
                  'hide_empty' => false
                  ));?> 
                  <?php $job_salary_ranges = wp_get_post_terms($job_id, 'salary_range', array('fields' => 'ids'));?>
                    <select class="salary-range" name="salary-range">
                      <?php foreach($salary_ranges as $salary_range):?>
                      <option <?php echo in_array($salary_range->term_id, $job_salary_ranges) ? 'selected' : ''?> value="<?php echo $salary_range->slug?>"><?php echo $salary_range->name?></option>
                      <?php endforeach;?>
                    </select>
                  <br>
                </p>
              </div>
            </div>
            <div id="col-1979313703" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                <?php $shifts =  get_terms( array( 
                  'taxonomy' => 'shift',
                  'hide_empty' => false
                  ));?> 
                  <?php $job_shifts = wp_get_post_terms($job_id, 'shift', array('fields' => 'ids'));?>
                    <select class="shift" name="shift">
                      <?php foreach($shifts as $shift):?>
                      <option <?php echo in_array($shift->term_id, $job_shifts) ? 'selected' : ''?> value="<?php echo $shift->slug?>"><?php echo $shift->name?></option>
                      <?php endforeach;?>
                    </select>
                  <br>
                </p>
              </div>
            </div>
          </div>
          <div id="text-2928786908" class="text">
            <h4 style="margin-bottom: 0;">Gender</h4>
            <style>
              #text-2928786908 {
                font-size: 1rem;
                color: rgb(0, 0, 0);
              }

              #text-2928786908>* {
                color: rgb(0, 0, 0);
              }
            </style>
          </div>
          <div id="gap-1859694959" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-1859694959 {
                padding-top: 5px;
              }

              @media (min-width:550px) {
                #gap-1859694959 {
                  padding-top: 15px;
                }
              }
            </style>
          </div>
          <div class="row row-small row-inner-full" id="row-1636895160">
            <div id="col-243544653" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                    <?php $gender = get_post_meta($job_id, '_job_gender', true);?>
                  <select class="job-gender">
                    <option <?php echo $gender == 1 ? 'selected' : ''?> value="1">Male</option>
                    <option <?php echo $gender == 2 ? 'selected' : ''?> value="2">Female</option>
                  </select>
                  <br>
                </p>
              </div>
              <style>
                #col-243544653>.col-inner {
                  margin: 0px 0px -30px 0px;
                }
              </style>
            </div>
          </div>
          <div id="text-1207226623" class="text">
            <h4 style="margin-bottom: 0;">Job Description</h4>
            <style>
              #text-1207226623 {
                font-size: 1rem;
                color: rgb(0, 0, 0);
              }

              #text-1207226623>* {
                color: rgb(0, 0, 0);
              }
            </style>
          </div>
          <div id="gap-721903520" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-721903520 {
                padding-top: 5px;
              }

              @media (min-width:550px) {
                #gap-721903520 {
                  padding-top: 15px;
                }
              }
            </style>
          </div>
          <div class="row row-small row-inner-full" id="row-1073299541">
            <div id="col-1416717497" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <textarea class="job-des" placeholder="Describe job here..."><?php echo get_post_field('post_content', $job_id); ?></textarea>
                  <br>
                </p>
              </div>
              <style>
                #col-1416717497>.col-inner {
                  margin: 0px 0px -15px 0px;
                }
              </style>
            </div>
            <div id="col-1253152613" class="col small-12 large-12">
              <div class="col-inner">
                <div id="text-3635829632" class="text">
                  <p>
                    <strong>Job Function</strong>
                  </p>
                  <style>
                    #text-3635829632 {
                      color: rgb(0, 0, 0);
                    }

                    #text-3635829632>* {
                      color: rgb(0, 0, 0);
                    }
                  </style>
                </div>
                <p>
                <?php $job_job_functions = wp_get_post_terms($job_id, 'job_function', array('fields' => 'ids'));?>
                <?php $job_functions =  get_terms( array( 
                  'taxonomy' => 'job_function',
                  'hide_empty' => false
                  ));?> 
                    <select class="job-function" name="job-function">
                      <?php foreach($job_functions as $job_function):?>
                      <option <?php echo in_array($job_function->term_id, $job_job_functions) ? 'selected' : ''?> value="<?php echo $job_function->slug?>"><?php echo $job_function->name?></option>
                      <?php endforeach;?>
                    </select>
                  <br>
                </p>
              </div>
            </div>
            <div id="col-1253152613" class="col small-12 large-12">
              <div class="col-inner">
                <div id="text-3635829632" class="text">
                  <p>
                    <strong>Job Type</strong>
                  </p>
                  <style>
                    #text-3635829632 {
                      color: rgb(0, 0, 0);
                    }

                    #text-3635829632>* {
                      color: rgb(0, 0, 0);
                    }
                  </style>
                </div>
                <p>
                <?php $job_job_types = wp_get_post_terms($job_id, 'job_listing_type', array('fields' => 'ids'));?>
                <?php $job_types =  get_terms( array( 
                  'taxonomy' => 'job_listing_type',
                  'hide_empty' => false
                  ));?> 
                    <select class="job-type" name="job-type">
                      <?php foreach($job_types as $job_type):?>
                      <option <?php echo in_array($job_type->term_id, $job_job_types) ? 'selected' : ''?> value="<?php echo $job_type->slug?>"><?php echo $job_type->name?></option>
                      <?php endforeach;?>
                    </select>
                  <br>
                </p>
              </div>
            </div>
            <div id="col-1696358747" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <textarea class="job-benefit" placeholder="Other benefit after sign contract"><?php echo get_post_meta($job_id, '_job_benefit', true)?></textarea>
                  <br>
                </p>
              </div>
            </div>
          </div>
          <div class="row row-inner-full" id="row-2117978022">
            <div id="col-826984947" class="col small-12 large-12">
              <div class="col-inner text-right">
                <a class="post-job-btn button primary lowercase" style="border-radius:10px;padding:3px 20px 3px 20px;">
                  <span>Submit</span>
                </a>
              </div>
            </div>
          </div>
        </div>
        <style>
          #col-1032203863>.col-inner {
            padding: 25px 15px 15px 15px;
            margin: 0px 0px -25px 0px;
            border-radius: 15px;
          }

          @media (min-width:550px) {
            #col-1032203863>.col-inner {
              margin: 0px 0px -15px 0px;
            }
          }
        </style>
      </div>
      <div id="col-868272666" class="col medium-12 small-12 large-4 small-col-first">
        <div class="col-inner">
          <div class="row row-small job-card-row row-inner-full" id="row-83454381">     
            <div id="col-1343659572" class="col medium-6 small-6 large-7">
              <div class="col-inner" style="background-color:rgba(255, 255, 255, 0);">
                <div id="text-2347217552" class="text job-title-card">
                  <p style="font-weight:bold; margin-bottom: 0;">Profile</p>
                  <style>
                    #text-2347217552 {
                      font-size: 1.15rem;
                      color: rgb(0, 0, 0);
                    }

                    #text-2347217552>* {
                      color: rgb(0, 0, 0);
                    }
                  </style>
                </div>
                <div id="gap-1292534786" class="gap-element clearfix" style="display:block; height:auto;">
                  <style>
                    #gap-1292534786 {
                      padding-top: 5px;
                    }
                  </style>
                </div>
                <p><a href="/company-information"></a></p>
                <div id="text-4031948753" class="text job-title-card">
                  <p style="font-weight:bold; margin-bottom: 0;">Account</p>
                  <style>
                    #text-4031948753 {
                      font-size: 1.1rem;
                      color: rgb(0, 0, 0);
                    }

                    #text-4031948753>* {
                      color: rgb(0, 0, 0);
                    }

                    @media (min-width:550px) {
                      #text-4031948753 {
                        font-size: 1.15rem;
                      }
                    }
                  </style>
                </div>
                <div id="gap-1437227242" class="gap-element clearfix" style="display:block; height:auto;">
                  <style>
                    #gap-1437227242 {
                      padding-top: 5px;
                    }
                  </style>
                </div>
                <p>Account Settings</p>
              </div>
            </div>
            <style>
              #row-83454381>.col>.col-inner {
                background-color: rgb(255, 255, 255);
                border-radius: 25px;
              }
            </style>
          </div>
        </div>
        <style>
          #col-868272666>.col-inner {
            margin: 30px 0px -25px 0px;
          }

          @media (min-width:550px) {
            #col-868272666>.col-inner {
              margin: 40px 0px 0px 0px;
            }
          }

          @media (min-width:850px) {
            #col-868272666>.col-inner {
              margin: 0px 0px 0px 0px;
            }
          }
        </style>
      </div>
    </div>
  </div>
  <style>
    #section_1690306891 {
      padding-top: 26px;
      padding-bottom: 26px;
      background-color: rgb(232, 245, 255);
    }

    #section_1690306891 .ux-shape-divider--top svg {
      height: 150px;
      --divider-top-width: 100%;
    }

    #section_1690306891 .ux-shape-divider--bottom svg {
      height: 150px;
      --divider-width: 100%;
    }

    @media (min-width:550px) {
      #section_1690306891 {
        padding-top: 40px;
        padding-bottom: 40px;
      }
    }

    @media (min-width:850px) {
      #section_1690306891 {
        padding-top: 50px;
        padding-bottom: 50px;
      }
    }
  </style>
</section>