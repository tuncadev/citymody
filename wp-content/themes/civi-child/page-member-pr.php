<?php get_header(); ?>
	<div id="content" class="site-content">
		<div class="main-content content-page">
			<div class="container">
				<div class="site-layout no-sidebar">
					<div id="primary" class="content-area">
						<main id="main" class="site-main">
							<div class="member_profile">
								<div class="member_profile-header">
									<h2>Membership Details</h2>
								</div>	
								<?php candidate_membership_plan(); ?>
							</div>						
						</main>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php get_footer(); ?>