<div class="accordion">
    <div id="accordion-4103140343" class="accordion-item filter-job-type">
        <a id="accordion-4103140343-label" href="#" class="accordion-title plain active" aria-expanded="true" aria-controls="accordion-4103140343-content">
        <button class="toggle" aria-label="Toggle">
            <i class="icon-angle-down"></i>
        </button>
        <span>Job Type</span>
        </a>
        <div id="accordion-4103140343-content" class="accordion-inner filter-job-type-inner" style="display: block;" aria-labelledby="accordion-4103140343-label">
        <?php $job_types = $terms = get_terms( array( 
                'taxonomy' => 'job_listing_type'
        ));?>      
        <?php foreach($job_types as $job_type):?>
            <label>
                <input type="checkbox" name="job_type-checkbox" value="<?php echo $job_type->term_id?>">
                <span class="wpcf7-list-item-label"><?php echo $job_type->name?></span>
            </label>
        <?php endforeach; ?> 
        </div>
    </div>
    <div id="accordion-1006959391" class="accordion-item filter-ex-level">
        <a id="accordion-1006959391-label" href="#" class="accordion-title plain active" aria-expanded="true" aria-controls="accordion-1006959391-content">
        <button class="toggle" aria-label="Toggle">
            <i class="icon-angle-down"></i>
        </button>
        <span>Experience Level</span>
        </a>
        <div id="accordion-1006959391-content" class="accordion-inner filter-ex-level-inner" aria-labelledby="accordion-1006959391-label" style="display: block;">
        <?php $experience_levels = $terms = get_terms( array( 
                'taxonomy' => 'experience_level'
        ));?>      
        <?php foreach($experience_levels as $experience_level):?>
            <label>
                <input type="checkbox" name="experience_level-checkbox" value="<?php echo $experience_level->term_id?>">
                <span class="wpcf7-list-item-label"><?php echo $experience_level->name?></span>
            </label>
        <?php endforeach; ?> 
        </div>
    </div>
    <div id="accordion-1513387961" class="accordion-item filter-workplace">
        <a id="accordion-1513387961-label" href="#" class="accordion-title plain active" aria-expanded="true" aria-controls="accordion-1513387961-content">
        <button class="toggle" aria-label="Toggle">
            <i class="icon-angle-down"></i>
        </button>
        <span>Workplace</span>
        </a>
        <div id="accordion-1513387961-content" class="accordion-inner filter-workplace-inner" aria-labelledby="accordion-1513387961-label" style="display: block;">
        <?php $workplaces = $terms = get_terms( array( 
                'taxonomy' => 'workplace'
        ));?> 
        <?php foreach($workplaces as $workplace):?>
                <label>
                    <input type="checkbox" name="workplace-checkbox" value="<?php echo $workplace->term_id?>">
                    <span class="wpcf7-list-item-label"><?php echo $workplace->name?></span>
                </label>
            <?php endforeach; ?> 
        </div>
    </div>
    <div id="accordion-1513387961" class="accordion-item filter-workplace">
        <a id="accordion-1513387961-label" href="#" class="accordion-title plain active" aria-expanded="true" aria-controls="accordion-1513387961-content">
        <button class="toggle" aria-label="Toggle">
            <i class="icon-angle-down"></i>
        </button>
        <span>Salary Range</span>
        </a>
        <?php $salary_ranges = $terms = get_terms( array( 
                'taxonomy' => 'salary_range'
        ));?> 
        <div id="accordion-1513387961-content" class="accordion-inner filter-workplace-inner" aria-labelledby="accordion-1513387961-label" style="display: block;">
        <?php foreach($salary_ranges as $salary_range):?>
                <label>
                    <input type="checkbox" name="salary_range-checkbox" value="<?php echo $salary_range->term_id?>">
                    <span class="wpcf7-list-item-label"><?php echo $salary_range->name?></span>
                </label>
            <?php endforeach; ?> 
        </div>
    </div>
    <div id="accordion-1513387961" class="accordion-item filter-workplace">
        <a id="accordion-1513387961-label" href="#" class="accordion-title plain active" aria-expanded="true" aria-controls="accordion-1513387961-content">
        <button class="toggle" aria-label="Toggle">
            <i class="icon-angle-down"></i>
        </button>
        <span>Location</span>
        </a>
        <?php $locations = $terms = get_terms( array( 
                'taxonomy' => 'working_location'
        ));?> 
        <div id="accordion-1513387961-content" class="accordion-inner filter-workplace-inner" aria-labelledby="accordion-1513387961-label" style="display: block;">
        <?php foreach($locations as $location):?>
                <label>
                    <input type="checkbox" name="working_location-checkbox" value="<?php echo $location->term_id?>">
                    <span class="wpcf7-list-item-label"><?php echo $location->name?></span>
                </label>
            <?php endforeach; ?> 
        </div>
    </div>
    <div id="accordion-1513387961" class="accordion-item filter-workplace">
        <a id="accordion-1513387961-label" href="#" class="accordion-title plain active" aria-expanded="true" aria-controls="accordion-1513387961-content">
        <button class="toggle" aria-label="Toggle">
            <i class="icon-angle-down"></i>
        </button>
        <span>Shift</span>
        </a>
        <?php $shifts = $terms = get_terms( array( 
                'taxonomy' => 'shift'
        ));?> 
        <div id="accordion-1513387961-content" class="accordion-inner filter-workplace-inner" aria-labelledby="accordion-1513387961-label" style="display: block;">
        <?php foreach($shifts as $shift):?>
                <label>
                    <input type="checkbox" name="shift-checkbox" value="<?php echo $shift->term_id?>">
                    <span class="wpcf7-list-item-label"><?php echo $shift->name?></span>
                </label>
            <?php endforeach; ?> 
        </div>
    </div>
    <div id="accordion-1513387961" class="accordion-item filter-workplace">
        <a id="accordion-1513387961-label" href="#" class="accordion-title plain active" aria-expanded="true" aria-controls="accordion-1513387961-content">
        <button class="toggle" aria-label="Toggle">
            <i class="icon-angle-down"></i>
        </button>
        <span>Experience</span>
        </a>
        <?php $experiences = $terms = get_terms( array( 
                'taxonomy' => 'experience'
        ));?> 
        <div id="accordion-1513387961-content" class="accordion-inner filter-workplace-inner" aria-labelledby="accordion-1513387961-label" style="display: block;">
        <?php foreach($experiences as $experience):?>
                <label>
                    <input type="checkbox" name="experience-checkbox" value="<?php echo $experience->term_id?>">
                    <span class="wpcf7-list-item-label"><?php echo $experience->name?></span>
                </label>
            <?php endforeach; ?> 
        </div>
    </div>
    <div id="accordion-1513387961" class="accordion-item filter-workplace">
        <a id="accordion-1513387961-label" href="#" class="accordion-title plain active" aria-expanded="true" aria-controls="accordion-1513387961-content">
        <button class="toggle" aria-label="Toggle">
            <i class="icon-angle-down"></i>
        </button>
        <span>Job Function</span>
        </a>
        <?php $job_functions = $terms = get_terms( array( 
                'taxonomy' => 'job_function'
        ));?> 
        <div id="accordion-1513387961-content" class="accordion-inner filter-workplace-inner" aria-labelledby="accordion-1513387961-label" style="display: block;">
        <?php foreach($job_functions as $job_function):?>
                <label>
                    <input type="checkbox" name="job_function-checkbox" value="<?php echo $job_function->term_id?>">
                    <span class="wpcf7-list-item-label"><?php echo $job_function->name?></span>
                </label>
            <?php endforeach; ?> 
        </div>
    </div>
</div>