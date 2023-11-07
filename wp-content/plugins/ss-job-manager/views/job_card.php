<style>
    .job-card-row > .col > .col-inner {
    padding: 15px 15px 15px 15px;
    background-color: rgb(255,255,255);
    border-radius: 25px;
    }
</style>
<div id="col-401709115" class="col medium-6 small-10 large-4">
  <div class="col-inner">
    <div class="icon-heart-o" style="font-family:fl-icons;"></div>
    <div id="text-2605760779" class="text job-title-card">
      <p style="font-weight:bold; margin-bottom: 0;"><?php echo get_the_title();?></p>
      <style>
        #text-2605760779 {
          font-size: 1.1rem;
          color: rgb(0, 0, 0);
        }

        #text-2605760779>* {
          color: rgb(0, 0, 0);
        }

        @media (min-width:550px) {
          #text-2605760779 {
            font-size: 1.3rem;
          }
        }
      </style>
    </div>
    <div id="gap-2144275768" class="gap-element clearfix" style="display:block; height:auto;">
      <style>
        #gap-2144275768 {
          padding-top: 10px;
        }

        @media (min-width:550px) {
          #gap-2144275768 {
            padding-top: 10px;
          }
        }
      </style>
    </div>
    <div class="icon-box featured-box icon-box-left text-left">
      <div class="icon-box-img" style="width: 20px">
        <div class="icon">
          <div class="icon-inner">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M8.00002 14.6667L7.61118 15.1236L8.00002 15.4545L8.38886 15.1236L8.00002 14.6667ZM3.99202 10.4247L4.48903 10.0885L4.4889 10.0883L3.99202 10.4247ZM12.0087 10.4247L12.5056 10.7609L12.5056 10.7609L12.0087 10.4247ZM11.7247 2.81666L11.3179 3.25768L11.318 3.25776L11.7247 2.81666ZM4.27535 2.81666L4.68209 3.25776L4.68218 3.25768L4.27535 2.81666ZM8.38886 14.2097C6.6232 12.7073 5.33077 11.3331 4.48903 10.0885L3.49501 10.7608C4.41949 12.1277 5.79906 13.5816 7.61118 15.1236L8.38886 14.2097ZM11.5118 10.0884C10.6695 11.3331 9.37685 12.7072 7.61118 14.2097L8.38886 15.1236C10.201 13.5816 11.5807 12.1278 12.5056 10.7609L11.5118 10.0884ZM12.7334 6.79999C12.7334 7.75078 12.3547 8.84251 11.5117 10.0885L12.5056 10.7609C13.4289 9.39614 13.9334 8.07143 13.9334 6.79999H12.7334ZM11.318 3.25776C12.2491 4.11638 12.7334 5.27461 12.7334 6.79999H13.9334C13.9334 4.99205 13.3451 3.49472 12.1314 2.37556L11.318 3.25776ZM8.00002 1.93333C9.25487 1.93333 10.3526 2.36723 11.3179 3.25768L12.1315 2.37565C10.9528 1.28832 9.56739 0.733328 8.00002 0.733328V1.93333ZM4.68218 3.25768C5.64746 2.36723 6.74517 1.93333 8.00002 1.93333V0.733328C6.43265 0.733328 5.04724 1.28832 3.86853 2.37565L4.68218 3.25768ZM3.26669 6.79999C3.26669 5.27461 3.75092 4.11638 4.68209 3.25776L3.86862 2.37556C2.6549 3.49472 2.06669 4.99205 2.06669 6.79999H3.26669ZM4.4889 10.0883C3.64554 8.84239 3.26669 7.7507 3.26669 6.79999H2.06669C2.06669 8.07151 2.57139 9.39627 3.49514 10.761L4.4889 10.0883ZM9.40002 6.66666C9.40002 7.43986 8.77322 8.06666 8.00002 8.06666V9.26666C9.43596 9.26666 10.6 8.1026 10.6 6.66666H9.40002ZM8.00002 5.26666C8.77322 5.26666 9.40002 5.89346 9.40002 6.66666H10.6C10.6 5.23072 9.43596 4.06666 8.00002 4.06666V5.26666ZM6.60002 6.66666C6.60002 5.89346 7.22682 5.26666 8.00002 5.26666V4.06666C6.56408 4.06666 5.40002 5.23072 5.40002 6.66666H6.60002ZM8.00002 8.06666C7.22682 8.06666 6.60002 7.43986 6.60002 6.66666H5.40002C5.40002 8.1026 6.56408 9.26666 8.00002 9.26666V8.06666Z" fill="#A0A6AD"></path>
            </svg>
          </div>
        </div>
      </div>
      <div class="icon-box-text last-reset">
        <?php
        $location = get_post_meta(get_the_ID(),'_job_location', true);
        // $location_terms = get_the_terms(get_the_ID(), 'location');
        // if ( $location_terms && ! is_wp_error( $location_terms ) ) {
        //     $location = $location_terms[0]->name;
        // } 
        // ?>
        <p><?php echo $location;?></p>
      </div>
    </div>
    <div class="icon-box featured-box icon-box-left text-left">
      <div class="icon-box-img" style="width: 20px">
        <div class="icon">
          <div class="icon-inner">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M13.3333 4.33334H11.3333V3.00001C11.3333 2.26001 10.74 1.66667 9.99998 1.66667H5.99998C5.25998 1.66667 4.66665 2.26001 4.66665 3.00001V4.33334H2.66665C1.92665 4.33334 1.33331 4.92667 1.33331 5.66667V13C1.33331 13.74 1.92665 14.3333 2.66665 14.3333H13.3333C14.0733 14.3333 14.6666 13.74 14.6666 13V5.66667C14.6666 4.92667 14.0733 4.33334 13.3333 4.33334ZM5.99998 3.00001H9.99998V4.33334H5.99998V3.00001ZM13.3333 13H2.66665V11.6667H13.3333V13ZM13.3333 9.66667H2.66665V5.66667H4.66665V7H5.99998V5.66667H9.99998V7H11.3333V5.66667H13.3333V9.66667Z" fill="#A0A6AD"></path>
            </svg>
          </div>
        </div>
      </div>
      <div class="icon-box-text last-reset">
        <?php
            $type = '';
            if(empty($type)){
              $type_terms = get_the_terms(get_the_ID(), 'job_listing_type');
              if ( $type_terms && ! is_wp_error( $type_terms ) ) {
                  $type = $type_terms[0]->name;
              }
            }
            ?>
        <p><?php echo $type;?></p>
      </div>
    </div>
    <div class="icon-box featured-box icon-box-left text-left">
      <div class="icon-box-img" style="width: 20px">
        <div class="icon">
          <div class="icon-inner">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g clip-path="url(#clip0_112_5035)">
                <path d="M12.6667 2.66666H12V1.33333L10.6667 1.33333V2.66666L5.33333 2.66666V1.33333L4 1.33333V2.66666H3.33333C2.59333 2.66666 2.00667 3.26666 2.00667 3.99999L2 13.3333C2 14.0667 2.59333 14.6667 3.33333 14.6667L12.6667 14.6667C13.4 14.6667 14 14.0667 14 13.3333L14 3.99999C14 3.26666 13.4 2.66666 12.6667 2.66666ZM12.6667 13.3333L3.33333 13.3333L3.33333 6.66666L12.6667 6.66666L12.6667 13.3333ZM12.6667 5.33333L3.33333 5.33333L3.33333 3.99999L12.6667 3.99999V5.33333ZM6 9.33333H4.66667L4.66667 8L6 8V9.33333ZM8.66667 9.33333L7.33333 9.33333V8H8.66667V9.33333ZM11.3333 9.33333H10V8L11.3333 8V9.33333ZM6 12H4.66667V10.6667H6V12ZM8.66667 12L7.33333 12V10.6667L8.66667 10.6667V12ZM11.3333 12H10V10.6667H11.3333V12Z" fill="#A0A6AD"></path>
              </g>
              <defs>
                <clipPath id="clip0_112_5035">
                  <rect width="16" height="16" fill="white"></rect>
                </clipPath>
              </defs>
            </svg>
          </div>
        </div>
      </div>
      <div class="icon-box-text last-reset">
        <?php
            $shift = '';
            $shift_terms = get_the_terms(get_the_ID(), 'shift');
            if ( $shift_terms && ! is_wp_error( $shift_terms ) ) {
                $shift = $shift_terms[0]->name;
            } 
            ?>
        <p><?php echo $shift;?></p>
      </div>
    </div>
    <div id="gap-1501349485" class="gap-element clearfix" style="display:block; height:auto;">
      <style>
        #gap-1501349485 {
          padding-top: 5px;
        }
      </style>
    </div>
    <div class="salary-btn-job-card">
      <div id="text-1057536645" class="text salary-job-card">
        <p style="margin-bottom:0">
          <?php
              $salary = get_post_meta(get_the_ID(),'_job_salary', true);
              if(empty($salary)){
                $salary_terms = get_the_terms(get_the_ID(), 'salary_range');
                if ( $salary_terms && ! is_wp_error( $salary_terms ) ) {
                    $salary = $salary_terms[0]->name;
                    ?>
                    <span style="font-size: 20px;">
                        <strong>$<?php echo $salary?> /month</strong>
                    </span>
                    <?php
                } 
              }
              else{
                ?>
                  <span style="font-size: 20px;">
                      <strong><?php echo $salary?></strong>
                  </span>
                  <?php
              }
          ?>
        </p>
        <style>
          #text-1057536645 {
            color: rgb(0, 0, 0);
          }

          #text-1057536645>* {
            color: rgb(0, 0, 0);
          }
        </style>
      </div>
      <a class="button success is-small button-job-card" style="border-radius:99px;" href="<?php the_permalink()?>">
        <span>Apply</span>
      </a>
    </div>
  </div>
</div>