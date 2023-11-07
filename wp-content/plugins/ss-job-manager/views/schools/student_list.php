<section class="section" id="section_870509188">
  <div class="bg section-bg fill bg-fill bg-loaded"></div>
  <div class="section-content relative">
    <div class="row" id="row-1520978057">
      <div id="col-52205092" class="col small-12 large-12">
        <div class="col-inner">
          <div id="text-1129113351" class="text">
            <h2 style="margin-bottom: 0;">Student List</h2>
            <style>
              #text-1129113351 {
                font-size: 1.2rem;
                color: rgb(0, 0, 0);
              }

              #text-1129113351>* {
                color: rgb(0, 0, 0);
              }
            </style>
          </div>
        </div>
        <style>
          #col-52205092>.col-inner {
            margin: 0px 0px -25px 0px;
          }

          @media (min-width:550px) {
            #col-52205092>.col-inner {
              margin: 0px 0px -15px 0px;
            }
          }
        </style>
      </div>
      <!-- <div id="col-1441792835" class="col small-12 large-12">
        <div class="col-inner">
          <div class="row row-small row-inner-full" id="row-74687574">
            <div id="col-117065814" class="col medium-3 small-12 large-3">
              <div class="col-inner">
                <p>
                  <select>
                    <option value="Finance">Finance</option>
                  </select>
                  <br>
                </p>
              </div>
              <style>
                #col-117065814>.col-inner {
                  margin: 0px 0px -25px 0px;
                }
              </style>
            </div>
            <div id="col-1764343916" class="col medium-3 small-12 large-3">
              <div class="col-inner">
                <p>
                  <select>
                    <option value="Date">Date</option>
                  </select>
                  <br>
                </p>
              </div>
              <style>
                #col-1764343916>.col-inner {
                  margin: 0px 0px -25px 0px;
                }
              </style>
            </div>
            <div id="col-1338155535" class="col medium-3 small-12 large-3">
              <div class="col-inner">
                <p>
                  <select>
                    <option value="Newest">Newest</option>
                  </select>
                  <br>
                </p>
              </div>
              <style>
                #col-1338155535>.col-inner {
                  margin: 0px 0px -25px 0px;
                }
              </style>
            </div>
          </div>
        </div>
        <style>
          #col-1441792835>.col-inner {
            margin: 0px 0px -50px 0px;
          }
        </style>
      </div> -->
      <div id="col-883979025" class="col candidate-row small-12 large-12">
        <div class="col-inner" style="background-color:rgb(255,255,255);">
          <div class="row row-collapse row-inner-full" id="row-554598578">
            <div id="col-280819010" class="col medium-6 small-12 large-6">
              <div class="col-inner">
                <div id="text-713528255" class="text">
                  <h2 style="margin-bottom:5px">List of students</h2>
                  <style>
                    #text-713528255 {
                      color: rgb(32, 94, 255);
                    }

                    #text-713528255>* {
                      color: rgb(32, 94, 255);
                    }
                  </style>
                </div>
                <p>Finance</p>
              </div>
            </div>
            <div id="col-1921098024" class="col medium-6 small-12 large-6">
              <div class="col-inner text-right">
                <a href="/student-information" class="button primary" style="border-radius:10px;">
                  <span>Add students</span>
                </a>
              </div>
            </div>
          </div>
          <table class="dashboard-table-interview">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Status</th>
                <th>Major</th>
                <th>Location</th>
                <th>Available date</th>
                <!-- <th>Salary</th> -->
              </tr>
            </thead>
            <tbody>
                <?php foreach($resumes as $resume): get_terms()?> 
                    <?php $resume_job_functions = wp_get_post_terms($resume_id, 'job_function');?>
                    <tr>
                        <td>
                        <img decoding="async" src="<?php echo !empty(get_post_meta($resume->ID, '_candidate_photo', true)) ? site_url().get_post_meta($resume->ID, '_candidate_photo', true) : '/wp-content/uploads/empty_avatar.jpg';?>"  width="40" height="40">
                        </td>
                        <td><?php echo $resume->post_title?></td>
                        <td>Intership</td>
                        <td><?php echo count($resume_job_functions) > 0 ? $resume_job_functions[0]->name : ''?></td>
                        <td><?php echo get_post_meta($resume->ID, '_candidate_location', true)?></td>
                        <td><?php echo $resume->post_date ?></td>
                        <!-- <td>$1,350</td> -->
                    </tr>
                <?php endforeach;?>
              
              
            </tbody>
          </table>
          <div id="gap-1807688811" class="gap-element clearfix" style="display:block; height:auto;">
            <style>
              #gap-1807688811 {
                padding-top: 10px;
              }
            </style>
          </div>
          <!-- <ul class="page-numbers nav-pagination links text-center">
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
          </ul> -->
        </div>
        <style>
          #col-883979025>.col-inner {
            padding: 25px 15px 25px 15px;
            border-radius: 15px;
          }
        </style>
      </div>
    </div>
  </div>
  <style>
    #section_870509188 {
      padding-top: 30px;
      padding-bottom: 30px;
      background-color: rgb(232, 245, 255);
    }

    #section_870509188 .ux-shape-divider--top svg {
      height: 150px;
      --divider-top-width: 100%;
    }

    #section_870509188 .ux-shape-divider--bottom svg {
      height: 150px;
      --divider-width: 100%;
    }
  </style>
</section>