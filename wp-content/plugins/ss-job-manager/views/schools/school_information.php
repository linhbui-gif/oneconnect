<section class="section" id="section_1759437669">
  <div class="bg section-bg fill bg-fill bg-loaded"></div>
  <div class="section-content relative">
    <div class="row" id="row-463462707">
      <div id="col-1315376639" class="col personal-info medium-12 small-12 large-8">
        <div class="col-inner text-left" style="background-color:rgb(255, 255, 255);">
          <div id="text-3113725459" class="text">
            <h2 style="margin-bottom: 0;">School information</h2>
            <style>
              #text-3113725459 {
                font-size: 1rem;
                color: rgb(0, 0, 0);
              }

              #text-3113725459>* {
                color: rgb(0, 0, 0);
              }
            </style>
          </div>
          <div id="gap-1424993330" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-1424993330 {
                padding-top: 5px;
              }

              @media (min-width:550px) {
                #gap-1424993330 {
                  padding-top: 15px;
                }
              }
            </style>
          </div>
          <div class="row row-small row-inner-full" id="row-1854055404">
            <div id="col-102756810" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_the_title($school_id)?>" class="school-name" placeholder="School name*" type="text">
                  <br>
                </p>
              </div>
            </div>
            <div id="col-300608156" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_post_meta($school_id, '_school_address', true)?>" class="school-address" placeholder="Address (base on license)" type="text">
                  <br>
                </p>
              </div>
            </div>
             
            <div id="col-481117216" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_post_meta($school_id, '_school_website', true)?>" class="school-website" placeholder="Website" type="text">
                  <br>
                </p>
              </div>
            </div>
          </div>
          <div id="gap-383840969" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-383840969 {
                padding-top: 10px;
              }

              @media (min-width:550px) {
                #gap-383840969 {
                  padding-top: 20px;
                }
              }
            </style>
          </div>
          <div id="text-1927658067" class="text">
            <h4 style="margin-bottom: 0;">Location</h4>
            <style>
              #text-1927658067 {
                font-size: 1rem;
                color: rgb(0, 0, 0);
              }

              #text-1927658067>* {
                color: rgb(0, 0, 0);
              }
            </style>
          </div>
          <div id="gap-3752342" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-3752342 {
                padding-top: 5px;
              }

              @media (min-width:550px) {
                #gap-3752342 {
                  padding-top: 15px;
                }
              }
            </style>
          </div>
          <div class="row row-small row-inner-full" id="row-216398620">
            <div id="col-1722696256" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_post_meta($school_id, '_school_street_building', true)?>" class="school-street" placeholder="Street/Building" type="text">
                  <br>
                </p>
              </div>
            </div>
            <div id="col-986741511" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_post_meta($school_id, '_school_building_number', true)?>" class="school-bulding-number" placeholder="Address/Building number" type="text">
                  <br>
                </p>
              </div>
            </div>
            <div id="col-208919955" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_post_meta($school_id, '_school_additional_information', true)?>" class="school-additional-address" placeholder="Additional address information (optional)" type="text">
                  <br>
                </p>
              </div>
            </div>
          </div>
          <div id="text-662270070" class="text">
            <h4 style="margin-bottom: 0;">Personal Contact</h4>
            <style>
              #text-662270070 {
                font-size: 1rem;
                color: rgb(0, 0, 0);
              }

              #text-662270070>* {
                color: rgb(0, 0, 0);
              }
            </style>
          </div>
          <div id="gap-1626721376" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-1626721376 {
                padding-top: 5px;
              }

              @media (min-width:550px) {
                #gap-1626721376 {
                  padding-top: 15px;
                }
              }
            </style>
          </div>
          <div class="row row-small row-inner-full" id="row-1308214276">
            <div id="col-282643245" class="col medium-6 small-12 large-6">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_post_meta($school_id, '_school_email', true)?>" class="school-email" placeholder="E-mail" value="" type="email" name="email-647">
                  <br>
                </p>
              </div>
            </div>
            <div id="col-894293767" class="col medium-6 small-12 large-6">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_post_meta($school_id, '_school_phone', true)?>" class="school-phone" placeholder="Phone* " value="" type="tel" name="sodienthoai">
                  <br>
                </p>
              </div>
            </div>
            <div id="col-1781242488" class="col small-12 large-12">
              <div class="col-inner">
                <p>
                  <input value="<?php echo get_post_meta($school_id, '_school_position', true)?>" class="school-position" placeholder="Title" type="text">
                  <br>
                </p>
              </div>
            </div>
          </div>
          <div class="row row-inner-full" id="row-1313151676">
            <div id="col-2005035527" class="col small-12 large-12">
              <div class="col-inner text-right">
                <a class="submit-school-btn button primary lowercase" style="border-radius:10px;padding:3px 20px 3px 20px;">
                  <span>Submit</span>
                </a>
              </div>
            </div>
          </div>
        </div>
        <style>
          #col-1315376639>.col-inner {
            padding: 25px 15px 15px 15px;
            margin: 0px 0px -25px 0px;
            border-radius: 15px;
          }

          @media (min-width:550px) {
            #col-1315376639>.col-inner {
              margin: 0px 0px -15px 0px;
            }
          }
        </style>
      </div>
      <div id="col-1128034052" class="col medium-12 small-12 large-4 small-col-first">
        <div class="col-inner">
          <div class="row row-small job-card-row row-inner-full" id="row-1147796388">
            <div id="col-2059310188" class="col medium-5 small-6 large-5">
              <div class="col-inner" style="background-color:rgba(255, 255, 255, 0);">
                <div class="img has-hover company-avatar-detail x md-x lg-x y md-y lg-y" id="image_412275312">
                  <div class="img-inner image-cover dark" style="padding-top:100%;">
                    <img class="account-img" width="315" height="289" src="<?php echo !empty(get_user_meta($user_id, '_avatar', true)) ? site_url().get_user_meta($user_id, '_avatar', true) : '/wp-content/uploads/empty_avatar.jpg';?>" class="attachment-original size-original" alt="" decoding="async" loading="lazy" sizes="(max-width: 315px) 100vw, 315px">
                    <input style="display: none;" type="file"  id="avatar-input" accept="image/*">
                  </div>
                  <style>
                    #image_412275312 {
                      width: 100%;
                    }
                  </style>
                </div>
              </div>
            </div>
            <div id="col-1238958944" class="col medium-6 small-6 large-7">
              <div class="col-inner" style="background-color:rgba(255, 255, 255, 0);">
                <div id="text-2739294568" class="text job-title-card">
                  <p style="font-weight:bold; margin-bottom: 0;">Profile</p>
                  <style>
                    #text-2739294568 {
                      font-size: 1.15rem;
                      color: rgb(0, 0, 0);
                    }

                    #text-2739294568>* {
                      color: rgb(0, 0, 0);
                    }
                  </style>
                </div>
                <div id="gap-980238830" class="gap-element clearfix" style="display:block; height:auto;">
                  <style>
                    #gap-980238830 {
                      padding-top: 5px;
                    }
                  </style>
                </div>
                <p><u>Company information</u></p>
                <div id="text-3604233231" class="text job-title-card">
                  <p style="font-weight:bold; margin-bottom: 0;">Account</p>
                  <style>
                    #text-3604233231 {
                      font-size: 1.1rem;
                      color: rgb(0, 0, 0);
                    }

                    #text-3604233231>* {
                      color: rgb(0, 0, 0);
                    }

                    @media (min-width:550px) {
                      #text-3604233231 {
                        font-size: 1.15rem;
                      }
                    }
                  </style>
                </div>
                <div id="gap-335450195" class="gap-element clearfix" style="display:block; height:auto;">
                  <style>
                    #gap-335450195 {
                      padding-top: 5px;
                    }
                  </style>
                </div>
                <p>Account Settings</p>
              </div>
            </div>
            <style>
              #row-1147796388>.col>.col-inner {
                background-color: rgb(255, 255, 255);
                border-radius: 25px;
              }
            </style>
          </div>
        </div>
        <style>
          #col-1128034052>.col-inner {
            margin: 30px 0px -25px 0px;
          }

          @media (min-width:550px) {
            #col-1128034052>.col-inner {
              margin: 40px 0px 0px 0px;
            }
          }

          @media (min-width:850px) {
            #col-1128034052>.col-inner {
              margin: 0px 0px 0px 0px;
            }
          }
        </style>
      </div>
    </div>
  </div>
  <style>
    #section_1759437669 {
      padding-top: 26px;
      padding-bottom: 26px;
      background-color: rgb(232, 245, 255);
    }

    #section_1759437669 .ux-shape-divider--top svg {
      height: 150px;
      --divider-top-width: 100%;
    }

    #section_1759437669 .ux-shape-divider--bottom svg {
      height: 150px;
      --divider-width: 100%;
    }

    @media (min-width:550px) {
      #section_1759437669 {
        padding-top: 40px;
        padding-bottom: 40px;
      }
    }

    @media (min-width:850px) {
      #section_1759437669 {
        padding-top: 50px;
        padding-bottom: 50px;
      }
    }
  </style>
</section>