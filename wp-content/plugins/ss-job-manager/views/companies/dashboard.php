<section class="section" id="section_1097750487">
  <div class="bg section-bg fill bg-fill bg-loaded"></div>
  <div class="section-content relative">
    <div class="row" id="row-1846191346">
      <div id="col-934362682" class="col personal-info candidate-row medium-12 small-12 large-8">
        <div class="col-inner text-left" style="background-color:rgb(255, 255, 255);">
          <div class="icon-box featured-box icon-box-left text-left">
            <div class="icon-box-img" style="width: 28px">
              <div class="icon">
                <div class="icon-inner" style="color:rgb(32, 94, 255);">
                  <svg width="24" height="22" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M20.996 17.9608C20.936 15.5908 20.038 13.6247 18.518 12.2347C18.9907 12.0531 19.4936 11.9628 20 11.9688C23 11.9688 24 14.7187 24 16.5947C24 17.3747 22.614 17.8288 20.996 17.9608ZM5.744 12.3488C4.234 13.7788 3.274 15.7487 3.05 17.9627C1.412 17.8387 0 17.3807 0 16.5947C0 14.7187 1 11.9688 4 11.9688C4.68 11.9688 5.258 12.1087 5.744 12.3507V12.3488ZM20 10.9688C19.2044 10.9688 18.4413 10.6527 17.8787 10.0901C17.3161 9.52746 17 8.7644 17 7.96875C17 7.1731 17.3161 6.41004 17.8787 5.84743C18.4413 5.28482 19.2044 4.96875 20 4.96875C20.7956 4.96875 21.5587 5.28482 22.1213 5.84743C22.6839 6.41004 23 7.1731 23 7.96875C23 8.7644 22.6839 9.52746 22.1213 10.0901C21.5587 10.6527 20.7956 10.9688 20 10.9688ZM4 10.9688C3.20435 10.9688 2.44129 10.6527 1.87868 10.0901C1.31607 9.52746 1 8.7644 1 7.96875C1 7.1731 1.31607 6.41004 1.87868 5.84743C2.44129 5.28482 3.20435 4.96875 4 4.96875C4.79565 4.96875 5.55871 5.28482 6.12132 5.84743C6.68393 6.41004 7 7.1731 7 7.96875C7 8.7644 6.68393 9.52746 6.12132 10.0901C5.55871 10.6527 4.79565 10.9688 4 10.9688ZM12 8.96875C10.9391 8.96875 9.92172 8.54732 9.17157 7.79718C8.42143 7.04703 8 6.02962 8 4.96875C8 3.90788 8.42143 2.89047 9.17157 2.14032C9.92172 1.39018 10.9391 0.96875 12 0.96875C13.0609 0.96875 14.0783 1.39018 14.8284 2.14032C15.5786 2.89047 16 3.90788 16 4.96875C16 6.02962 15.5786 7.04703 14.8284 7.79718C14.0783 8.54732 13.0609 8.96875 12 8.96875ZM12 10.9688C17.334 10.9688 19 14.4688 19 18.2188C19 21.9688 5 21.9688 5 18.2188C5 14.4688 6.666 10.9688 12 10.9688Z" fill="#0322DD"></path>
                  </svg>
                </div>
              </div>
            </div>
            <div class="icon-box-text last-reset">
              <div id="text-738468272" class="text">
                <h2>Candidate</h2>
                <style>
                  #text-738468272 {
                    color: rgb(32, 94, 255);
                  }

                  #text-738468272>* {
                    color: rgb(32, 94, 255);
                  }
                </style>
              </div>
            </div>
          </div>
          <div id="gap-2131181328" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-2131181328 {
                padding-top: 5px;
              }

              @media (min-width:550px) {
                #gap-2131181328 {
                  padding-top: 10px;
                }
              }
            </style>
          </div>
          <div class="row row-small row-inner-full" id="row-1522226028">
            <div id="col-1549554188" class="col medium-6 small-12 large-6">
              <div class="col-inner">
                <p>
                  <select class="candidate-jobs">
                    <?php foreach($all_jobs as $job):?>
                        <option <?php echo $jobId == $job->ID ? 'selected' : ''?> value="<?php echo $job->ID?>"><?php echo $job->post_title?></option>
                    <?php endforeach;?>
                    
                  </select>
                  <br>
                </p>
              </div>
              <style>
                #col-1549554188>.col-inner {
                  margin: 0px 0px -25px 0px;
                }
              </style>
            </div>
            <div id="col-998992752" class="col medium-6 small-12 large-6">
              <div class="col-inner">
                <p>
                  <select class="candidate-filter-status">
                    <option value="">All status</option>
                    <option value="new">New</option>
                    <option value="reviewing">Reviewing</option>
                    <option value="interviewed">Interviewed</option>
                    <option value="rejected">Rejected</option>
                    <option value="hired">Hired</option>
                  </select>
                  <br>
                </p>
              </div>
              <style>
                #col-998992752>.col-inner {
                  margin: 0px 0px -25px 0px;
                }
              </style>
            </div>
          </div>
          <div class="row row-small row-inner-full candidate-collection" id="row-1209321888">
            <?php foreach($all_job_candidates as $candidate):?>
                <?php 
                    $resume_id = get_post_meta($candidate->ID, '_resume_id', true);
                    include JOB_MANAGER_VIEW_PATH . '/companies/candidate_card.php';
                ?>
                
            <?php endforeach;?>
          </div>
          <!-- <div class="row row-inner-full" id="row-47898565">
            <div id="col-584204106" class="col small-12 large-12">
              <div class="col-inner text-center">
                <ul class="page-numbers nav-pagination links text-center">
                  <li>
                    <span aria-current="page" class="page-number current">1</span>
                  </li>
                  <li>
                    <a class="page-number">2</a>
                  </li>
                  <li>
                    <a class="next page-number">
                      <i class="icon-angle-right"></i>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div> -->
        </div>
        <style>
          #col-934362682>.col-inner {
            padding: 25px 15px 15px 15px;
            margin: 0px 0px -25px 0px;
            border-radius: 15px;
          }

          @media (min-width:550px) {
            #col-934362682>.col-inner {
              margin: 0px 0px -15px 0px;
            }
          }
        </style>
      </div>
      <div id="col-1726778482" class="col medium-4 small-12 large-4">
        <div class="col-inner">
          <div class="row row-inner-full" id="row-283183376">
            <div id="col-790322920" class="col medium-12 small-12 large-12">
              <div class="col-inner text-left" style="background-color:rgb(255, 255, 255);">
                <div id="text-785412708" class="text">
                  <h2>Jobs</h2>
                  <style>
                    #text-785412708 {
                      text-align: center;
                      color: rgb(32, 94, 255);
                    }

                    #text-785412708>* {
                      color: rgb(32, 94, 255);
                    }
                  </style>
                </div>
                <div class="row row-collapse align-center row-inner-full" id="row-1385685308">
                  <div id="col-2019730267" class="col medium-3 small-12 large-3">
                    <div class="col-inner text-center">
                      <div id="text-1731440080" class="text">
                        <p style="margin-bottom: 5px;">
                          <span style="text-decoration: underline;">Open</span>
                        </p>
                        <style>
                          #text-1731440080 {
                            color: #205eff;
                          }

                          #text-1731440080>* {
                            color: #205eff;
                          }
                        </style>
                      </div>
                      <div id="text-655090776" class="text">
                        <p style="margin-bottom: 0;">
                          <strong><?php echo $active_job_count?></strong>
                        </p>
                        <style>
                          #text-655090776 {
                            font-size: 1.95rem;
                            color: rgb(34, 210, 171);
                          }

                          #text-655090776>* {
                            color: rgb(34, 210, 171);
                          }
                        </style>
                      </div>
                    </div>
                  </div>
                  <div id="col-2019730267" class="col medium-3 small-12 large-3">
                    <div class="col-inner text-center">
                      <div id="text-1731440080" class="text">
                        <p style="margin-bottom: 5px;">
                          <span style="text-decoration: underline;">Waiting</span>
                        </p>
                        <style>
                          #text-1731440080 {
                            color: #205eff;
                          }

                          #text-1731440080>* {
                            color: #205eff;
                          }
                        </style>
                      </div>
                      <div id="text-655090776" class="text">
                        <p style="margin-bottom: 0;">
                          <strong><?php echo $pending_job_count?></strong>
                        </p>
                        <style>
                          #text-655090776 {
                            font-size: 1.95rem;
                            color: rgb(34, 210, 171);
                          }

                          #text-655090776>* {
                            color: rgb(34, 210, 171);
                          }
                        </style>
                      </div>
                    </div>
                  </div>
                  <div id="col-2019730267" class="col medium-3 small-12 large-3">
                    <div class="col-inner text-center">
                      <div id="text-1731440080" class="text">
                        <p style="margin-bottom: 5px;">
                          <span style="text-decoration: underline;">Rejected</span>
                        </p>
                        <style>
                          #text-1731440080 {
                            color: #205eff;
                          }

                          #text-1731440080>* {
                            color: #205eff;
                          }
                        </style>
                      </div>
                      <div id="text-655090776" class="text">
                        <p style="margin-bottom: 0;">
                          <strong><?php echo $reject_job_count?></strong>
                        </p>
                        <style>
                          #text-655090776 {
                            font-size: 1.95rem;
                            color: rgb(34, 210, 171);
                          }

                          #text-655090776>* {
                            color: rgb(34, 210, 171);
                          }
                        </style>
                      </div>
                    </div>
                  </div>
                  <div id="col-344487031" class="col medium-3 small-12 large-3">
                    <div class="col-inner text-center">
                      <div id="text-1577749300" class="text">
                        <p style="margin-bottom: 5px;">
                          <span style="text-decoration: underline;">Expired</span>
                        </p>
                        <style>
                          #text-1577749300 {
                            color: #205eff;
                          }

                          #text-1577749300>* {
                            color: #205eff;
                          }
                        </style>
                      </div>
                      <div id="text-1169210418" class="text">
                        <p style="margin-bottom: 0;">
                          <strong><?php echo $expired_job_count?></strong>
                        </p>
                        <style>
                          #text-1169210418 {
                            font-size: 1.95rem;
                            color: #22d2ab;
                          }

                          #text-1169210418>* {
                            color: #22d2ab;
                          }
                        </style>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <style>
                #col-790322920>.col-inner {
                  padding: 25px 15px 25px 15px;
                  margin: 0px 0px -25px 0px;
                  border-radius: 15px;
                }

                @media (min-width:550px) {
                  #col-790322920>.col-inner {
                    margin: 0px 0px 0px 0px;
                  }
                }
              </style>
            </div>
            <div id="col-542070145" class="col medium-12 small-12 large-12">
              <div class="col-inner text-left" style="background-color:rgb(255, 255, 255);">
                <div id="text-2610636656" class="text">
                  <h2>Candidates</h2>
                  <style>
                    #text-2610636656 {
                      text-align: left;
                      color: #1b1d1f;
                    }

                    #text-2610636656>* {
                      color: #1b1d1f;
                    }
                  </style>
                </div>
                <table class="candidate-row-col-dashboard">
                  <tbody>
                    <tr>
                      <td>New</td>
                      <td><?php echo $new_candidate_count?></td>
                    </tr>
                    <tr>
                      <td>Reviewing</td>
                      <td><?php echo $reviewing_candidate_count?></td>
                    </tr>
                    <tr>
                      <td>Interviewed</td>
                      <td><?php echo $interview_candidate_count?></td>
                    </tr>
                    <tr>
                      <td>Rejected</td>
                      <td><?php echo $reject_candidate_count?></td>
                    </tr>
                    <tr>
                      <td>Hired</td>
                      <td><?php echo $hired_candidate_count?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <style>
                #col-542070145>.col-inner { 
                    padding: 25px 15px 10px 15px;
                    margin: 0px 0px -25px 0px;
                    border-radius: 15px;
                }
              </style>
            </div>
          </div>
        </div>
      </div>
      <div id="col-2114783702" class="col personal-info candidate-row medium-12 small-12 large-12">
        <div class="col-inner text-left" style="background-color:rgb(255, 255, 255);">
          <div class="icon-box featured-box icon-box-left text-left">
            <div class="icon-box-img" style="width: 28px">
              <div class="icon">
                <div class="icon-inner" style="color:rgb(32, 94, 255);">
                  <svg width="24" height="22" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M20.996 17.9608C20.936 15.5908 20.038 13.6247 18.518 12.2347C18.9907 12.0531 19.4936 11.9628 20 11.9688C23 11.9688 24 14.7187 24 16.5947C24 17.3747 22.614 17.8288 20.996 17.9608ZM5.744 12.3488C4.234 13.7788 3.274 15.7487 3.05 17.9627C1.412 17.8387 0 17.3807 0 16.5947C0 14.7187 1 11.9688 4 11.9688C4.68 11.9688 5.258 12.1087 5.744 12.3507V12.3488ZM20 10.9688C19.2044 10.9688 18.4413 10.6527 17.8787 10.0901C17.3161 9.52746 17 8.7644 17 7.96875C17 7.1731 17.3161 6.41004 17.8787 5.84743C18.4413 5.28482 19.2044 4.96875 20 4.96875C20.7956 4.96875 21.5587 5.28482 22.1213 5.84743C22.6839 6.41004 23 7.1731 23 7.96875C23 8.7644 22.6839 9.52746 22.1213 10.0901C21.5587 10.6527 20.7956 10.9688 20 10.9688ZM4 10.9688C3.20435 10.9688 2.44129 10.6527 1.87868 10.0901C1.31607 9.52746 1 8.7644 1 7.96875C1 7.1731 1.31607 6.41004 1.87868 5.84743C2.44129 5.28482 3.20435 4.96875 4 4.96875C4.79565 4.96875 5.55871 5.28482 6.12132 5.84743C6.68393 6.41004 7 7.1731 7 7.96875C7 8.7644 6.68393 9.52746 6.12132 10.0901C5.55871 10.6527 4.79565 10.9688 4 10.9688ZM12 8.96875C10.9391 8.96875 9.92172 8.54732 9.17157 7.79718C8.42143 7.04703 8 6.02962 8 4.96875C8 3.90788 8.42143 2.89047 9.17157 2.14032C9.92172 1.39018 10.9391 0.96875 12 0.96875C13.0609 0.96875 14.0783 1.39018 14.8284 2.14032C15.5786 2.89047 16 3.90788 16 4.96875C16 6.02962 15.5786 7.04703 14.8284 7.79718C14.0783 8.54732 13.0609 8.96875 12 8.96875ZM12 10.9688C17.334 10.9688 19 14.4688 19 18.2188C19 21.9688 5 21.9688 5 18.2188C5 14.4688 6.666 10.9688 12 10.9688Z" fill="#0322DD"></path>
                  </svg>
                </div>
              </div>
            </div>
            <div class="icon-box-text last-reset">
              <div id="text-2798770622" class="text">
                <h2>Interview</h2>
                <style>
                  #text-2798770622 {
                    color: rgb(32, 94, 255);
                  }

                  #text-2798770622>* {
                    color: rgb(32, 94, 255);
                  }
                </style>
              </div>
            </div>
          </div>
          <div id="gap-832892149" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-832892149 {
                padding-top: 5px;
              }

              @media (min-width:550px) {
                #gap-832892149 {
                  padding-top: 10px;
                }
              }
            </style>
          </div>
          <div class="row row-small row-inner-full" id="row-1795348371">
            <div id="col-855238674" class="col medium-3 small-12 large-3">
              <div class="col-inner">
                <p>
                    <select class="interview-jobs">
                        <?php foreach($all_jobs as $job):?>
                            <option <?php echo $jobId == $job->ID ? 'selected' : ''?> value="<?php echo $job->ID?>"><?php echo $job->post_title?></option>
                        <?php endforeach;?>
                    
                  </select>
                  <br>
                </p>
              </div>
              <style>
                #col-855238674>.col-inner {
                  margin: 0px 0px -25px 0px;
                }
              </style>
            </div>
            <!-- <div id="col-2128290229" class="col medium-3 small-12 large-3">
              <div class="col-inner">
                <p>
                    <select class="interview-filter-status">
                    <option value="All status">All status</option>
                    <option value="new">New</option>
                    <option value="reviewing">Reviewing</option>
                    <option value="interviewed">Interviewed</option>
                    <option value="rejected">Rejected</option>
                    <option value="hired">Hired</option>
                  </select>
                  <br>
                </p>
              </div>
              <style>
                #col-2128290229>.col-inner {
                  margin: 0px 0px -25px 0px;
                }
              </style>
            </div> -->
            <div id="col-480386474" class="col medium-6 small-12 large-6">
              <div class="col-inner">
                <p>
                  <input type="search" id="woocommerce-product-search-field-0" class="search-field mb-0 interview-search-keyword" placeholder="Search..." value="" name="s" autocomplete="off">
                  <br>
                </p>
              </div>
              <style>
                #col-480386474>.col-inner {
                  margin: 0px 0px -25px 0px;
                }
              </style>
            </div>
          </div>
          <div class="row row-small row-inner-full" id="row-864922881">
            <div id="col-2114691688" class="col small-12 large-12">
              <div class="col-inner" style="background-color:rgba(223, 236, 255, 0.5);">
                <table class="dashboard-table-interview">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Candidate</th>
                      <th>Time</th>
                      <th>Date</th>
                      <th>Status</th>
                      <!-- <th>Note from HR</th> -->
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody class="interview-collection">
                    <?php foreach($interview_job_candidates as $candidate):?>
                        <?php $resume_id = get_post_meta($candidate->ID, '_resume_id', true);?>
                        <tr class="interview-card" data-candidate="<?php echo $candidate->ID?>">
                            <td>
                              <img decoding="async" src="<?php echo !empty(get_post_meta($resume_id, '_candidate_photo', true)) ? site_url().get_post_meta($resume_id, '_candidate_photo', true) : '/wp-content/uploads/empty_avatar.jpg';?>"  width="40" height="40">
                            </td>
                            <td><?php echo get_post( $resume_id )->post_title; ?></td>
                            <td class="candidate-interview-time"><?php echo get_post_meta($candidate->ID, 'interview_time', true)?></td>
                            <td class="candidate-interview-date"><?php echo get_post_meta($candidate->ID, 'interview_date', true)?></td>
                            <td>Interviewed</td>
                            <!-- <td>Note from HR</td> -->
                            <td>
                                <a class="interview-btn button primary" style="border-radius:10px">
                                    <span>Interview</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach;?> 
                  </tbody>
                </table>
              </div>
              <style>
                #col-2114691688>.col-inner {
                  padding: 15px 15px 15px 15px;
                  border-radius: 15px;
                }
              </style>
            </div>
          </div>
          <!-- <div class="row row-inner-full" id="row-1361271120">
            <div id="col-495994208" class="col small-12 large-12">
              <div class="col-inner text-center">
                <ul class="page-numbers nav-pagination links text-center">
                  <li>
                    <span aria-current="page" class="page-number current">1</span>
                  </li>
                  <li>
                    <a class="page-number">2</a>
                  </li>
                  <li>
                    <a class="next page-number">
                      <i class="icon-angle-right"></i>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div> -->
        </div>
        <style>
          #col-2114783702>.col-inner {
            padding: 25px 15px 15px 15px;
            margin: 0px 0px -25px 0px;
            border-radius: 15px;
          }

          @media (min-width:550px) {
            #col-2114783702>.col-inner {
              margin: 25px 0px -15px 0px;
            }
          }
        </style>
      </div>
    </div>
  </div>
  <style>
    #section_1097750487 {
      padding-top: 26px;
      padding-bottom: 26px;
      background-color: rgb(232, 245, 255);
    }

    #section_1097750487 .ux-shape-divider--top svg {
      height: 150px;
      --divider-top-width: 100%;
    }

    #section_1097750487 .ux-shape-divider--bottom svg {
      height: 150px;
      --divider-width: 100%;
    }

    @media (min-width:550px) {
      #section_1097750487 {
        padding-top: 40px;
        padding-bottom: 40px;
      }
    }

    @media (min-width:850px) {
      #section_1097750487 {
        padding-top: 50px;
        padding-bottom: 50px;
      }
    }
  </style>
</section>
<div class="modal" style="display: none;">
  <div class="modal-content">
    <span class="close">&times;</span>
    <div class="popup-interview-candidate">
        <div>
            <label for="interview-date">Interview Date</label>
            <input name="interview-date" type="date" class="interview-date">
        </div>
        <div>
            <label for="interview-time">Interview Time</label>
            <input type="text" class="interview-time">
        </div>  
        <div>
            <a class="update-interview-btn button primary" style="border-radius:10px">
                <span>Update</span>
            </a>
        </div>
    </div>
  </div>
</div>
