<?php
/* 
* If this file is called directly, abort.
*/
if (!defined('ABSPATH')) {
	die(); 
}
use App\Admin\JobAdmin;
use App\Admin\GeneralAdmin;
use App\Admin\CompanyAdmin;
use App\Admin\SchoolAdmin;
use App\Admin\AccountAdmin;
use App\Admin\ResumeAdmin;
use App\Admin\ApplicationAdmin;
use App\Service\JobService;
use App\Service\CompanyService;
use App\Service\ResumeService;
use App\Service\SchoolService;
use App\Service\ApplicationService;
use App\Service\FileManageService;
use App\Helpers\CommonHelper;


(new JobAdmin);
(new GeneralAdmin);
(new CompanyAdmin);
(new SchoolAdmin);
(new AccountAdmin);
(new ResumeAdmin);
(new ResumeService);
(new SchoolService);
(new CompanyService);
(new JobService);
(new ApplicationService);
(new ApplicationAdmin);
(new FileManageService);