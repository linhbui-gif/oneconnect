<div id="content" role="main" class="content-area">
  <section class="section" id="section_1167052335">
    <div class="bg section-bg fill bg-fill bg-loaded"></div>
    <div class="section-content relative">
      <div class="row" id="row-28849269">
        <div id="col-1649201108" class="col personal-info medium-12 small-12 large-8">
          <div class="col-inner text-left">
            <div class="row row-inner-full" id="row-584006941">
              <div id="col-1549403581" class="col small-12 large-12">
                <div class="col-inner">
                  <div id="text-1378613517" class="text">
                    <h2 style="margin-bottom: 0;">Post a Jobs</h2>
                    <style>
                      #text-1378613517 {
                        font-size: 1.2rem;
                        color: rgb(0, 0, 0);
                      }

                      #text-1378613517>* {
                        color: rgb(0, 0, 0);
                      }
                    </style>
                  </div>
                </div>
                <style>
                  #col-1549403581>.col-inner {
                    margin: 0px 0px -25px 0px;
                  }

                  @media (min-width:550px) {
                    #col-1549403581>.col-inner {
                      margin: 0px 0px -15px 0px;
                    }
                  }
                </style>
              </div>
            </div>
            <div class="row row-inner-full" id="row-695982255">
              <div id="col-464302081" class="col small-12 large-12">
                <div class="col-inner">
                  <a class="button primary" style="border-radius:82px;" href="/post-job">
                    <i class="icon-plus" aria-hidden="true"></i>
                    <span>Post a free job</span>
                  </a>
                  <div id="gap-1660121397" class="gap-element clearfix" style="display:block; height:auto;">
                    <style>
                      #gap-1660121397 {
                        padding-top: 10px;
                      }
                    </style>
                  </div>
                  <div id="stack-1566870888" class="stack tab-employer-post-job stack-row justify-start items-stretch">
                    <div id="text-2385420432" class="text">
                      <p>
                        <a href="/job-panel?status=publish" class="<?php echo $status == 'publish' ? 'active' : ''?>">Approved</a>
                      </p>
                    </div>
                    <div id="text-1782048576" class="text">
                      <p>
                        <a href="/job-panel?status=pending" class="<?php echo $status == 'pending' ? 'active' : ''?>">Waiting</a>
                      </p>
                    </div>
                    <div id="text-2231759755" class="text">
                      <p>
                        <a href="/job-panel?status=reject" class="<?php echo $status == 'reject' ? 'active' : ''?>">Declined</a>
                      </p>
                    </div>
                    <style>
                      #stack-1566870888>* {
                        --stack-gap: 2rem;
                      }
                    </style>
                  </div>
                  <div id="gap-213253539" class="gap-element clearfix" style="display:block; height:auto;">
                    <style>
                      #gap-213253539 {
                        padding-top: 20px;
                      }
                    </style>
                  </div>
                  <div class="row row-small job-card-row row-inner-full row-box-shadow-1 row-box-shadow-2-hover" id="row-1866492667">
                    <?php foreach($jobs as $job):?>
                            <?php include JOB_MANAGER_VIEW_PATH . '/jobs/post_job_card.php'; ?>
                        <?php endforeach;?>
                    <style>
                      #row-1866492667>.col>.col-inner {
                        padding: 15px 15px 15px 15px;
                        background-color: rgb(255, 255, 255);
                        border-radius: 25px;
                      }
                    </style>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <style>
            #col-1649201108>.col-inner {
              margin: 0px 0px -25px 0px;
              border-radius: 15px;
            }
          </style>
        </div>
        <div id="col-134665724" class="col medium-12 small-12 large-4 small-col-first">
          <div class="col-inner">
            <div class="row row-small job-card-row row-inner-full" id="row-1792591021">       
              <div id="col-1412307067" class="col medium-6 small-6 large-7">
                <div class="col-inner" style="background-color:rgba(255, 255, 255, 0);">
                  <div id="text-1259292241" class="text job-title-card">
                    <p style="font-weight:bold; margin-bottom: 0;">Profile</p>
                    <style>
                      #text-1259292241 {
                        font-size: 1.15rem;
                        color: rgb(0, 0, 0);
                      }

                      #text-1259292241>* {
                        color: rgb(0, 0, 0);
                      }
                    </style>
                  </div>
                  <div id="gap-555026634" class="gap-element clearfix" style="display:block; height:auto;">
                    <style>
                      #gap-555026634 {
                        padding-top: 5px;
                      }
                    </style>
                  </div>
                  <p><a href="/company-information"></a></p>
                  <div id="text-1044314476" class="text job-title-card">
                    <p style="font-weight:bold; margin-bottom: 0;">Account</p>
                    <style>
                      #text-1044314476 {
                        font-size: 1.1rem;
                        color: rgb(0, 0, 0);
                      }

                      #text-1044314476>* {
                        color: rgb(0, 0, 0);
                      }

                      @media (min-width:550px) {
                        #text-1044314476 {
                          font-size: 1.15rem;
                        }
                      }
                    </style>
                  </div>
                  <div id="gap-1499606011" class="gap-element clearfix" style="display:block; height:auto;">
                    <style>
                      #gap-1499606011 {
                        padding-top: 5px;
                      }
                    </style>
                  </div>
                  <p>Account Settings</p>
                </div>
              </div>
              <style>
                #row-1792591021>.col>.col-inner {
                  background-color: rgb(255, 255, 255);
                  border-radius: 25px;
                }
              </style>
            </div>
          </div>
          <style>
            #col-134665724>.col-inner {
              margin: 30px 0px -25px 0px;
            }

            @media (min-width:550px) {
              #col-134665724>.col-inner {
                margin: 40px 0px 0px 0px;
              }
            }

            @media (min-width:850px) {
              #col-134665724>.col-inner {
                margin: 30px 0px 0px 0px;
              }
            }
          </style>
        </div>
      </div>
    </div>
    <style>
      #section_1167052335 {
        padding-top: 30px;
        padding-bottom: 30px;
        background-color: rgb(232, 245, 255);
      }

      #section_1167052335 .ux-shape-divider--top svg {
        height: 150px;
        --divider-top-width: 100%;
      }

      #section_1167052335 .ux-shape-divider--bottom svg {
        height: 150px;
        --divider-width: 100%;
      }
    </style>
  </section>
</div>