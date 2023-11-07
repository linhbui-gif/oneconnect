<?php
namespace App\Admin;

use App\Admin\GeneralAdmin;

class ResumeAdmin extends GeneralAdmin{
    public function __construct()
    {
        add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
        add_filter('resume_post_statuses', array($this, 'resume_post_statuses'), 101, 1);
		add_filter('resume_manager_resume_education_fields', array($this, 'resume_manager_resume_education_fields'), 101, 1);
		add_filter('resume_manager_user_can_edit_resume', array($this, 'resume_manager_user_can_edit_resume'), 101, 2);
		
    }
	
	function resume_manager_user_can_edit_resume($can_edit, $resume_id){
		return true;
	}
	function resume_manager_resume_education_fields($fields){
		unset($fields['notes']);
		$fields['gpa'] = [
			'label'       => __( 'GPA', 'wp-job-manager-resumes' ),
			'name'        => 'resume_education_gpa[]',
			'placeholder' => '',
			'description' => ''
		];
		return $fields;
	}
	
    function resume_post_statuses($statuses){
        unset($statuses['draft']);
        unset($statuses['expired']);
        unset($statuses['hidden']);
        unset($statuses['preview']);
        unset($statuses['pending']);
        unset($statuses['pending_payment']);
        unset($statuses['publish']);
        $statuses['not_verified'] = _x( 'Not Verified', 'job_application', 'wp-job-manager-applications' );
        $statuses['verified'] = _x( 'Verified', 'job_application', 'wp-job-manager-applications' );
        return $statuses;
    }
    

    public function add_meta_boxes() {
		add_meta_box( 'resume_status', __( 'Resume Status', 'wp-job-manager-applications' ), [ $this, 'resume_status' ], 'resume', 'side', 'high' );
		remove_meta_box( 'submitdiv', 'resume', 'side' );
	}

    function get_resume_post_statuses() {
		return apply_filters(
			'resume_post_statuses',
			[
				'draft'           => _x( 'Draft', 'post status', 'wp-job-manager-resumes' ),
				'expired'         => _x( 'Expired', 'post status', 'wp-job-manager-resumes' ),
				'hidden'          => _x( 'Hidden', 'post status', 'wp-job-manager-resumes' ),
				'preview'         => _x( 'Preview', 'post status', 'wp-job-manager-resumes' ),
				'pending'         => _x( 'Pending approval', 'post status', 'wp-job-manager-resumes' ),
				'pending_payment' => _x( 'Pending payment', 'post status', 'wp-job-manager-resumes' ),
				'publish'         => _x( 'Published', 'post status', 'wp-job-manager-resumes' ),
			]
		);
	}

	/**
	 * Publish meta box
	 */
	public function resume_status( $post ) {
		
		?>
		<div class="submitbox" id="submitpost">
			<div id="minor-publishing">
				<div id="misc-publishing-actions">
					<div class="misc-pub-section misc-pub-post-status">
						<div id="post-status-select">
							<select name='post_status' id='post_status'>
								
							</select>
						</div>
					</div>
				</div>
			</div>
			<div id="major-publishing-actions">
				<div id="delete-action">
					<a class="submitdelete deletion" href="<?php echo get_delete_post_link( $post->ID ); ?>"><?php _e( 'Move to Trash', 'wp-job-manager-applications' ); ?></a>
				</div>
				<div id="publishing-action">
					<span class="spinner"></span>
					<input name="save" class="button button-primary" type="submit" value="<?php _e( 'Save', 'wp-job-manager-applications' ); ?>">
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<?php
	}

    
}