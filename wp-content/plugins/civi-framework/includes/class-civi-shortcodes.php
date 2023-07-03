<?php

if (!defined("ABSPATH")) {
    exit();
}
if (!class_exists("Civi_Shortcode_Jobs")) {
    /**
     * Class Civi_Shortcode_Jobs
     */
    class Civi_Shortcode_Jobs
    {
        /**
         * Constructor.
         */
        public function __construct()
        {
            //Employer
            add_shortcode("civi_dashboard", [$this, "dashboard_employer"]);
            add_shortcode("civi_jobs", [$this, "jobs"]);
            add_shortcode("civi_jobs_performance", [$this, "jobs_performance"]);
            add_shortcode("civi_jobs_submit", [$this, "jobs_submit"]);
            add_shortcode("civi_applicants", [$this, "applicants"]);
            add_shortcode("civi_candidates", [$this, "candidates"]);
            add_shortcode("civi_user_package", [$this, "user_package"]);
            add_shortcode("civi_messages", [$this, "messages"]);
            add_shortcode("civi_company", [$this, "company"]);
            add_shortcode("civi_submit_company", [$this, "submit_company"]);
            add_shortcode("civi_settings", [$this, "employer_settings"]);
            add_shortcode("civi_meetings", [$this, "employer_meetings"]);
            add_shortcode("civi-payment", [$this, "payment"]);
            add_shortcode("civi_payment_completed", [
                $this,
                "payment_completed",
            ]);

            //Candidate
            add_shortcode("civi_candidate_dashboard", [
                $this,
                "dashboard_candidate",
            ]);
            add_shortcode("civi_candidate_settings", [
                $this,
                "candidate_settings",
            ]);
            add_shortcode("civi_my_jobs", [$this, "my_jobs"]);
            add_shortcode("civi_candidate_company", [
                $this,
                "candidate_company",
            ]);
            add_shortcode("civi_candidate_profile", [
                $this,
                "candidate_profile",
            ]);
            add_shortcode("civi_candidate_my_review", [
                $this,
                "candidate_my_review",
            ]);
            add_shortcode("civi_candidate_meetings", [
                $this,
                "candidate_meetings",
            ]);
						add_shortcode("civi_candidate_membership", [
							$this,
							"candidate_membership",
						]);
        }

        /**
         * Dashboard Employer
         */
        public function dashboard_employer()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_employer", (array)$current_user->roles)) {
                civi_get_template("dashboard/employer/dashboard.php");
            } else {
?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "civi-framework"
                                                                            ); ?></p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Dashboard Candidate
         */
        public function dashboard_candidate()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_candidate", (array)$current_user->roles)) {
                civi_get_template("dashboard/candidate/dashboard.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Candidate to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Jobs
         */
        public function jobs()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_employer", (array)$current_user->roles)) {
                civi_get_template("dashboard/employer/jobs.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Jobs Performance
         */
        public function jobs_performance()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_employer", (array)$current_user->roles)) {
                civi_get_template("dashboard/employer/jobs-performance.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Jobs Submit
         */
        public function jobs_submit()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_employer", (array)$current_user->roles)) {
                civi_get_template("jobs/submit.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Applicants
         */
        public function applicants()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_employer", (array)$current_user->roles)) {
                civi_get_template("dashboard/employer/applicants.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Candidates
         */
        public function candidates()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_employer", (array)$current_user->roles)) {
                civi_get_template("dashboard/employer/candidates.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * User Package
         */
        public function user_package()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_employer", (array)$current_user->roles)) {
                civi_get_template("dashboard/employer/user-package.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Messages
         */
        public function messages()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_employer", (array)$current_user->roles) || in_array("civi_user_candidate", (array)$current_user->roles)) {
                civi_get_template("dashboard/messages/messages.php");
            } else { ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer,Candidate to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }

            return ob_get_clean();
        }

        /**
         * Meetings
         */
        public function employer_meetings()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_employer", (array)$current_user->roles)) {
                civi_get_template("dashboard/employer/meetings.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Company
         */
        public function company()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_employer", (array)$current_user->roles)) {
                civi_get_template("dashboard/employer/company.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Submit Company
         */
        public function submit_company()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_employer", (array)$current_user->roles)) {
                civi_get_template("company/submit.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Settings employer
         */
        public function employer_settings()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_employer", (array)$current_user->roles)) {
                civi_get_template("dashboard/employer/settings.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Settings candidate
         */
        public function candidate_settings()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_candidate", (array)$current_user->roles)) {
                civi_get_template("dashboard/candidate/settings.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Candidate to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Package
         */
        public function package()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_employer", (array)$current_user->roles)) {
                civi_get_template("package/package.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Candidate or Employer to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Payment
         */
        public function payment()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_employer", (array)$current_user->roles)) {
                civi_get_template("payment/payment.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Payment_completed
         */
        public function payment_completed()
        {
            ob_start();
            global $current_user;
            if (
                in_array("civi_user_candidate", (array)$current_user->roles) ||
                in_array("civi_user_employer", (array)$current_user->roles)
            ) {
                civi_get_template("payment/payment-completed.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Employer to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * My Jobs
         */
        public function my_jobs()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_candidate", (array)$current_user->roles)) {
                civi_get_template("dashboard/candidate/my-jobs.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Candidate to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Candidate Profile
         */
        public function candidate_company()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_candidate", (array)$current_user->roles)) {
                civi_get_template("dashboard/candidate/company.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Candidate to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Candidate Profile
         */
        public function candidate_profile()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_candidate", (array)$current_user->roles)) {
                civi_get_template("dashboard/candidate/profile.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Candidate to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Candidate Reviews
         */
        public function candidate_my_review()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_candidate", (array)$current_user->roles)) {
                civi_get_template("dashboard/candidate/my-review.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Candidate to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
            <?php
            }
            return ob_get_clean();
        }

        /**
         * Candidate Reviews
         */
        public function candidate_meetings()
        {
            ob_start();
            global $current_user;
            if (in_array("civi_user_candidate", (array)$current_user->roles)) {
                civi_get_template("dashboard/candidate/meetings.php");
            } else {
            ?>
                <p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
                                                                                "Please access the role Candidate to view",
                                                                                "civi-framework"
                                                                            ); ?>
                </p>
			<?php
            }
            return ob_get_clean();
        }
				public function candidate_membership()
				{
						ob_start();
						global $current_user;
						if (in_array("civi_user_candidate", (array)$current_user->roles)) {
							civi_get_template("dashboard/candidate/membership.php");
						} else {
						?>
							<p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e(
																							"Please access the role Candidate to view",
																							"civi-framework"
																						); ?>
							</p>
						<?php
						}
						return ob_get_clean();
			}
    }

    new Civi_Shortcode_Jobs();
}
