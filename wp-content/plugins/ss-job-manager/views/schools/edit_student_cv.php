<section class="personal-section section" id="section_843435879">
  <div class="bg section-bg fill bg-fill bg-loaded"></div>
  <div class="section-content relative">
    <div class="row" id="row-596767654">
      <div id="col-1185629228" class="col personal-info medium-12 small-12 large-8">
        <div class="col-inner text-left" style="background-color:rgb(255, 255, 255);">
          <div id="text-1581982017" class="text">
            <h2 style="margin-bottom: 0;">Personal information</h2>
            <style>
              #text-1581982017 {
                font-size: 1rem;
                color: rgb(0, 0, 0);
              }

              #text-1581982017>* {
                color: rgb(0, 0, 0);
              }
            </style>
          </div>
          <div id="gap-322680476" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-322680476 {
                padding-top: 5px;
              }

              @media (min-width:550px) {
                #gap-322680476 {
                  padding-top: 15px;
                }
              }
            </style>
          </div>
          <div class="row row-small row-inner-full" id="row-1418627182">
            <div id="col-204352665" class="col medium-6 small-12 large-12">
              <div class="col-inner">
                <p>
                  <input value="<?php echo $resume->post_title?>" class="candidate-name" placeholder="Candidate Full Name*" type="text">
                  <br>
                </p>
              </div>
            </div> 
            <div id="col-1770729305" class="col medium-6 small-12 large-6">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_post_meta($resume_id, '_candidate_birthday', true);?>" class="candidate-dob" placeholder="Date of Birth* " type="date">
                  <br>
                </p>
              </div>
              <style>
                #col-1770729305>.col-inner {
                  margin: 0px 0px -25px 0px;
                }
              </style>
            </div>
            <div id="col-1687566960" class="col medium-6 small-12 large-6">
              <div class="col-inner">
                <p>
                    <?php $gender = get_post_meta($resume_id, '_candidate_gender', true);?>
                    <select class="candidate-gender">
                    <option <?php echo ($gender == 1) ? 'selected' : '';?> value="1">Male</option>
                    <option <?php echo ($gender == 2) ? 'selected' : '';?> value="2">Female</option>
                    </select>
                    <br>
                </p>
              </div>
              <style>
                #col-1687566960>.col-inner {
                  margin: 0px 0px -25px 0px;
                }
              </style>
            </div>
            <div id="col-1078479094" class="col medium-6 small-12 large-6">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_post_meta($resume_id, '_candidate_email', true)?>" class="candidate-email" placeholder="E-mail"  type="email" name="email-647">
                  <br>
                </p>
              </div>
            </div>
            <div id="col-556386308" class="col medium-6 small-12 large-6">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_post_meta($resume_id, '_candidate_phone', true)?>" class="candidate-phone" placeholder="Phone* " type="tel" name="sodienthoai">
                  <br>
                </p>
              </div>
            </div>
            
            <div id="col-104888616" class="col small-12 large-6">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_post_meta($resume_id, '_candidate_location', true)?>" class="candidate-address" placeholder="Address" type="text">
                  <br>
                </p>
              </div>
            </div>
          </div>
          <div id="gap-1961440829" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-1961440829 {
                padding-top: 10px;
              }

              @media (min-width:550px) {
                #gap-1961440829 {
                  padding-top: 20px;
                }
              }
            </style>
          </div>
          <div class="row row-small row-inner-full candidate-education-row" id="row-758761061">
            <div id="col-1065122372" class="col small-12 large-12">
              <div class="col-inner">
                <div id="text-423749978" class="text">
                  <h2 style="margin-bottom: 0;">Education</h2>
                  <style>
                    #text-423749978 {
                      font-size: 1rem;
                      color: rgb(0, 0, 0);
                    }

                    #text-423749978>* {
                      color: rgb(0, 0, 0);
                    }
                  </style>
                </div>
                <div id="gap-1330917324" class="gap-element clearfix" style="display:block; height:auto;">
                  <style>
                    #gap-1330917324 {
                      padding-top: 5px;
                    }

                    @media (min-width:550px) {
                      #gap-1330917324 {
                        padding-top: 15px;
                      }
                    }
                  </style>
                </div>
                <div class="row row-inner-full candidate-education-inner">
                  <?php $educations = get_post_meta($resume_id, '_candidate_education')?>
                  <?php if(count($educations) == 0):?>
                    <div id="col-677662108" class="col medium-6 small-12 large-6">
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
                    </div>
                    <?php endif;?>
                  <?php foreach($educations as $education):?>
                    <div id="col-677662108" class="col medium-6 small-12 large-6">
                      <div class="col-inner">
                        <p>
                            <input value="<?php echo $education[0]['location'] ?>" class="edu-university" placeholder="University" type="text">
                          <br>
                        </p>
                      </div>
                    </div>
                    <div id="col-2069040347" class="col medium-6 small-12 large-6">
                      <div class="col-inner">
                        <p>
                            <input value="<?php echo $education[0]['qualification'] ?>" class="edu-major" placeholder="Major" type="text">
                          <br>
                        </p>
                      </div>
                    </div>
                    <div id="col-566436318" class="col medium-6 small-12 large-6">
                      <div class="col-inner">
                        <p>
                          <input value="<?php echo $education[0]['date'] ?>" class="edu-time" placeholder="From - to" type="text">
                          <br>
                        </p>
                      </div>
                    </div>
                    <div id="col-535151683" class="col medium-6 small-12 large-6">
                      <div class="col-inner">
                        <p>
                          <input value="<?php echo $education[0]['gpa'] ?>" class="edu-gpa" placeholder="GPA" type="text">
                          <br>
                        </p>
                      </div>
                    </div>
                  <?php endforeach;?>
                </div>
              </div>
            </div>
            <div id="col-87517972" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <a class="add-item-btn">
                    <span class="dashicons dashicons-plus"></span>
                    <span>Add more your education</span>
                  </a>
                  <br>
                </p>
              </div>
              <style>
                #col-87517972>.col-inner {
                  margin: -25px 0px 0px 0px;
                }
              </style>
            </div>
          </div>
          <?php $experiences = get_post_meta($resume_id, '_candidate_experience')?>
          <div class="row row-small row-inner-full candidate-experiences-row" id="row-612944066">
            <div id="col-1304719053" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <strong>Experiences</strong>
                </p>
                <div class="row row-inner-full candidate-experience-inner" id="row-2069628102">
                <?php if(count($experiences) == 0):?>
                    <div id="col-1885368316" class="col medium-4 small-12 large-4">
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
                    </div>
                  <?php endif;?>
                  <?php foreach($experiences as $experience):?>
                    <div id="col-1885368316" class="col medium-4 small-12 large-4">
                      <div class="col-inner">
                        <p>
                          <input value="<?php echo $experience[0]['employer']?>" class="exp-company" placeholder="Company Name" type="text">
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
                          <input value="<?php echo $experience[0]['job_title']?>" class="exp-position" placeholder="Position" type="text">
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
                          <input value="<?php echo $experience[0]['date']?>" class="exp-time" placeholder="From - to" type="text">
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
                          <input value="<?php echo $experience[0]['notes']?>" class="exp-describe" placeholder="Describe your work..." type="text">
                          <br>
                        </p>
                      </div>
                      <style>
                        #col-2061565189>.col-inner {
                          margin: 0px 0px 0px 0px;
                        }
                      </style>
                    </div>
                  <?php endforeach;?>
                </div>
              </div>
            </div>
            <div id="col-1023302239" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <a class="add-item-btn">
                    <span class="dashicons dashicons-plus"></span>
                    <span>Add more your experiences </span>
                  </a>
                  <br>
                </p>
              </div>
              <style>
                #col-1023302239>.col-inner {
                  margin: -25px 0px 0px 0px;
                }
              </style>
            </div>
          </div>
          <div class="row row-small row-inner-full candidate-others-row" id="row-6713576">  
            <div id="col-1470127397" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <strong>Skills</strong>
                </p>
                <?php $skills = $terms = get_terms( array( 
                  'taxonomy' => 'resume_skill',
                  'hide_empty' => false
                  ));?> 
                  <?php $resume_skills = wp_get_post_terms($resume_id, 'resume_skill', array('fields' => 'ids'));?>
                    <select class="multi-select candidate-skills" name="skills[]" multiple="multiple">
                      <?php foreach($skills as $skill):?>
                      <option <?php echo in_array($skill->term_id, $resume_skills) ? 'selected' : ''?> value="<?php echo $skill->slug?>"><?php echo $skill->name?></option>
                      <?php endforeach;?>
                    </select>
              </div>
            </div>
          
            <div id="col-414224225" class="col small-12 large-12">
              <div class="col-inner">
              <p style="margin-bottom:10px;">Resume Submit here. Note: File max 5MB</p>
                <p>
                    <input type="hidden" class="resume-file">
                  <?php $resume_file = get_post_meta($resume_id, '_resume_file', true)?>
                  <?php if(!empty($resume_file)):?>
                    <input type="text" disabled class="display-file" value="<?php echo basename($resume_file) ?>">
                    <?php endif;?>
                  <input placeholder="Resume Submit here" type="file" name="file" id="resume-file-input" accept=".jpg,.jpeg,.png">
                  <br>
                </p>
              </div>
              <style>
                #col-414224225>.col-inner {
                  margin: 0px 0px -15px 0px;
                }
              </style>
            </div>
            <div id="col-41512661" class="col small-12 large-12">
              <div class="col-inner">
                <p style="margin-bottom:10px;">
                  <b>Why we need to hire you?</b>
                </p>
                <p>
                    <textarea class="candidate-about" placeholder="Describe yourself here..."><?php echo get_post_field('post_content', $resume_id);?></textarea>
                  <br>
                </p>
              </div>
              <style>
                #col-41512661>.col-inner {
                  margin: 0px 0px -15px 0px;
                }
              </style>
            </div>
            <div id="col-13781736" class="col small-12 large-12">
              <div class="col-inner">
                <p>By submitting this application you confirm that you are eligible to work in Canada. You hereby agree that all information stated here is valid and the application is being submitted by yourself and yourself only. <br> Also agree that you have read and understand the Employment Standards Rights and Employment Standards Act, 2000 (ESA) and have read the ESA Standards poster available here. <br> You have completed the WHMIS 2015 Training, GMP, 4 Steps to Health and Safety Certificate, AODA Training, and reviewed the Opus Workplace Harassment Policy. </p>
              </div>
            </div>
            <div id="col-1738924473" class="col small-12 large-12">
              <div class="col-inner text-right">
                <a class="submit-cv-btn button primary lowercase" style="border-radius:10px;padding:3px 20px 3px 20px;">
                  <span>Submit</span>
                </a>
              </div>
            </div>
          </div>
        </div>
        <style>
          #col-1185629228>.col-inner {
            padding: 25px 15px 15px 15px;
            margin: 0px 0px -25px 0px;
            border-radius: 15px;
          }

          @media (min-width:550px) {
            #col-1185629228>.col-inner {
              margin: 0px 0px -15px 0px;
            }
          }
        </style>
      </div>
      <div id="col-880135016" class="col medium-12 small-12 large-4 small-col-first">
        <div class="col-inner">
          <div class="row row-small job-card-row row-inner-full" id="row-485351379">
            <div id="col-225617382" class="col medium-5 small-6 large-5">
              <div class="col-inner" style="background-color:rgba(255, 255, 255, 0);">
                <div class="img has-hover user-avatar-detail x md-x lg-x y md-y lg-y" id="image_1887381361">
                  <div class="img-inner image-cover dark" style="padding-top:100%;">
                    <img class="account-img" width="315" height="289" src="<?php echo !empty(get_post_meta($resume_id, '_candidate_photo', true)) ? site_url().get_post_meta($resume_id, '_candidate_photo', true) : '/wp-content/uploads/empty_avatar.jpg';?>" class="attachment-original size-original" alt="" decoding="async" loading="lazy" sizes="(max-width: 315px) 100vw, 315px">
                    <input style="display: none;" type="file"  id="student-avatar-input" accept="image/*">
                    <input type="hidden" class="avatar-file">
                  </div>
                  <style>
                    #image_1887381361 {
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
            <div id="col-1637013452" class="col medium-6 small-6 large-7">
              <div class="col-inner" style="background-color:rgba(255, 255, 255, 0);">
                <div id="text-1253710802" class="text job-title-card">
                  <p style="font-weight:bold; margin-bottom: 0;">Profile</p>
                  <style>
                    #text-1253710802 {
                      font-size: 1.15rem;
                      color: rgb(0, 0, 0);
                    }

                    #text-1253710802>* {
                      color: rgb(0, 0, 0);
                    }
                  </style>
                </div>
                <div id="gap-1619175384" class="gap-element clearfix" style="display:block; height:auto;">
                  <style>
                    #gap-1619175384 {
                      padding-top: 5px;
                    }
                  </style>
                </div>
                <p><u>Personal information</u> <br><a class="job-preference-link">Job preferences</a></p>
                <div id="text-1952649710" class="text job-title-card">
                  <p style="font-weight:bold; margin-bottom: 0;">Account</p>
                  <style>
                    #text-1952649710 {
                      font-size: 1.1rem;
                      color: rgb(0, 0, 0);
                    }

                    #text-1952649710>* {
                      color: rgb(0, 0, 0);
                    }

                    @media (min-width:550px) {
                      #text-1952649710 {
                        font-size: 1.15rem;
                      }
                    }
                  </style>
                </div>
                <div id="gap-22045762" class="gap-element clearfix" style="display:block; height:auto;">
                  <style>
                    #gap-22045762 {
                      padding-top: 5px;
                    }
                  </style>
                </div>
                <p>Account Settings</p>
              </div>
            </div>
            <style>
              #row-485351379>.col>.col-inner {
                background-color: rgb(255, 255, 255);
                border-radius: 25px;
              }
            </style>
          </div>
        </div>
        <style>
          #col-880135016>.col-inner {
            margin: 30px 0px -25px 0px;
          }

          @media (min-width:550px) {
            #col-880135016>.col-inner {
              margin: 40px 0px 0px 0px;
            }
          }

          @media (min-width:850px) {
            #col-880135016>.col-inner {
              margin: 0px 0px 0px 0px;
            }
          }
        </style>
      </div>
    </div>
  </div>
  <style>
    #section_843435879 {
      padding-top: 26px;
      padding-bottom: 26px;
      background-color: rgb(232, 245, 255);
    }

    #section_843435879 .ux-shape-divider--top svg {
      height: 150px;
      --divider-top-width: 100%;
    }

    #section_843435879 .ux-shape-divider--bottom svg {
      height: 150px;
      --divider-width: 100%;
    }

    @media (min-width:550px) {
      #section_843435879 {
        padding-top: 40px;
        padding-bottom: 40px;
      }
    }

    @media (min-width:850px) {
      #section_843435879 {
        padding-top: 50px;
        padding-bottom: 50px;
      }
    }
  </style>
</section>
<section style="display: none" class="section job-preference-section" id="section_988483689">
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
                    <input style="display: none;" type="file"  id="student-avatar-input" accept="image/*">
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
                <p><a class="personal-link">Personal information</a><br><u>Job preferences</u> </p>
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