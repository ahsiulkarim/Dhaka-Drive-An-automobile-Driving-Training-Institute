<?php
$pageTitle = "Motorcycle Riding Course - Dhaka Drive";
include 'partials/header.php';
?>
<section id="course-detail">
    <div class="container">
        <?php display_message(); ?>
        <div class="course-detail-header">
            <img src="Picture/BIKE_5.png" alt="Motorcycle Riding Course">
            <div class="header-info">
                <h1>Motorcycle Riding Course</h1>
                <p class="course-price-display">Price: BDT 3,000</p>
                <p>Master the art of two-wheeling with our comprehensive motorcycle course. Ideal for new riders, we focus on building your confidence, control, and road safety awareness to handle Dhaka's dynamic traffic.</p>
                
                <form action="actions/enroll_process.php" method="POST">
                    <input type="hidden" name="course_name" value="Motorcycle Riding Course">
                    <div class="form-group">
                        <label for="user-location">Your Preferred Location</label>
                        <input type="text" id="user-location" name="preferred_location" placeholder="e.g., Mirpur, Gulshan, Uttara" required>
                    </div>
                    <div class="terms-container">
                        <input type="checkbox" id="terms-agree-bike" name="terms_agree" class="terms-checkbox" required>
                        <label for="terms-agree-bike">I have read and agree to the <a href="terms.php" target="_blank">Terms and Conditions</a>.</label>
                    </div>
                    <button type="submit" class="btn request-btn">Request to Enroll</button>
                </form>

            </div>
        </div>
        <div class="course-detail-body">
            <div class="detail-section">
                <h2>What You'll Learn</h2>
                <ul>
                    <li>Motorcycle controls and pre-ride checks.</li>
                    <li>Slow speed control, balancing, and maneuvering.</li>
                    <li>Proper gear shifting and braking techniques.</li>
                    <li>Executing safe turns and U-turns.</li>
                    <li>Emergency braking and hazard avoidance.</li>
                    <li>Strategies for safe lane positioning and traffic navigation.</li>
                </ul>
            </div>
            <div class="detail-section">
                <h2>Course Structure</h2>
                <p><strong>Total Duration:</strong> 15 Hours</p>
                <p><strong>Schedule:</strong> 10 classes, 1.5 hours each</p>
                <p><strong>Includes:</strong></p>
                <ul>
                    <li>Training on a 100cc-150cc motorcycle.</li>
                    <li>Provision of a DOT-approved helmet and safety gear.</li>
                    <li>One-on-one sessions with a certified motorcycle instructor.</li>
                    <li>Complete guidance for BRTA license application.</li>
                    <li>A final mock test to prepare for the official exam.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>