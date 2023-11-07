<div id="gap-607499221" class="gap-element clearfix has-block tooltipstered" style="display:block; height:auto;">
  <style>
    #gap-607499221 {
      padding-top: 15px;
    }
  </style>
</div>
<?php if(is_user_logged_in()):?>
    <?php 
    $user = wp_get_current_user();
    $user_id = $user->ID;
    $url = '#';
    foreach ($user->roles as $role) {
        $title = get_role($role)->name;
        if($role == 'company'){
            $title = 'Company';
            $company_args = array (
                'post_type' => 'company',
                'author'        =>  $user_id,
                'posts_per_page'    => 1,
                'post_status' => 'any'
            );
    
            $companies = get_posts($company_args);
            $url = '/company-information';
            if(count($companies) == 0) continue;
            $post_id = $companies[0]->ID;
            break;
        }
        if($role == 'candidate'){
            $title = 'Candidate';
            $candidate_args = array (
                'post_type' => 'candidate',
                'author'        =>  $user_id,
                'posts_per_page'    => 1,
                'post_status' => 'any'
            );
    
            $candidates = get_posts($candidate_args);
            $url = '/candidate-information';
            if(count($candidates) == 0) continue;
            $post_id = $candidates[0]->ID;
            break;
        }
        if($role == 'school'){
            $title = 'School';
            $school_args = array (
                'post_type' => 'school',
                'author'        =>  $user_id,
                'posts_per_page'    => 1,
                'post_status' => 'any'
            );
    
            $schooles = get_posts($school_args);
            $url = '/school-information';
            if(count($schooles) == 0) continue;
            $post_id = $schooles[0]->ID;
            break;
        }
    }
    ?>
<div class="row row-collapse logged-in-accounthover" id="row-631078241">
  <div id="col-395158351" class="col medium-2 small-12 large-2">
    <div class="col-inner">
      <div class="img has-hover x md-x lg-x y md-y lg-y" id="image_963109743">
        <div class="img-inner image-cover dark" style="padding-top:100%;">
          <img src="<?php echo !empty(get_user_meta($user_id, '_avatar', true)) ? site_url().get_user_meta($user_id, '_avatar', true) : '/wp-content/uploads/empty_avatar.jpg';?>" class="attachment-original size-original" alt="" decoding="async" loading="lazy">
        </div>
        <style>
          #image_963109743 {
            width: 100%;
          }
        </style>
      </div>
    </div>
    <style>
      #col-395158351>.col-inner {
        margin: 3px 0px -7px 0px;
      }
    </style>
  </div>
  <div id="col-1447443189" class="col medium-10 small-12 large-10">
    <div class="col-inner">
      <div id="text-904724557" class="text">
        <p>
          <strong><?php echo !empty($post_id) ? get_the_title($post_id) : $user->display_name?></strong>
        </p>
        <style>
          #text-904724557 {
            font-size: 0.9rem;
            color: rgb(0, 0, 0);
          }

          #text-904724557>* {
            color: rgb(0, 0, 0);
          }
        </style>
      </div>
      <div id="text-536905597" class="text">
        <p><?php echo $title?></p>
        <style>
          #text-536905597 {
            font-size: 0.9rem;
          }
        </style>
      </div>
    </div>
    <style>
      #col-1447443189>.col-inner {
        padding: 0px 0px 0px 10px;
      }
    </style>
  </div>
  <div id="col-700427073" class="col small-12 large-12">
    <div class="col-inner">
      <a href="<?php echo $url?>" class="button primary is-outline is-small expand" style="border-radius:99px;padding:0px 0px 0px 0px;">
        <span>View profile</span>
      </a>
    </div>
    <style>
      #col-700427073>.col-inner {
        margin: 10px 0px 10px 0px;
      }
    </style>
  </div>
  <div id="col-140404968" class="col small-12 large-12">
    <div class="col-inner">
      <div id="text-864249145" class="text">
        <a href="<?php echo wp_logout_url('/member-login')?>">
          <img width="14" src="/wp-content/uploads/2023/06/sign-out-icon.svg"> Sign out
        </a>
        <style>
          #text-864249145 {
            font-size: 0.85rem;
            color: rgb(0, 0, 0);
          }

          #text-864249145>* {
            color: rgb(0, 0, 0);
          }
        </style>
      </div>
    </div>
  </div>
</div>
<?php else:?>
<div class="row row-collapse guest-accounthover" id="row-1450001737">
  <div id="col-1205336635" class="col medium-6 small-12 large-6">
    <div class="col-inner">
      <a href="/member-login" class="button primary is-outline is-small expand" style="border-radius:99px;padding:0px 0px 0px 0px;">
        <span>Login</span>
      </a>
    </div>
    <style>
      #col-1205336635>.col-inner {
        padding: 0px 5px 0px 0px;
      }
    </style>
  </div>
  <div id="col-158762366" class="col medium-6 small-12 large-6">
    <div class="col-inner">
      <a href="/member-register" class="button primary is-small expand" style="border-radius:99px;padding:0px 0px 0px 0px;">
        <span>Signup</span>
      </a>
    </div>
    <style>
      #col-158762366>.col-inner {
        padding: 0px 0px 0px 5px;
      }
    </style>
  </div>
</div>
<?php endif;?>
<div id="gap-405137991" class="gap-element clearfix" style="display:block; height:auto;">
  <style>
    #gap-405137991 {
      padding-top: 15px;
    }
  </style>
</div>