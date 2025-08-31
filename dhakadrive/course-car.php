<?php
$pageTitle = "Car Driving Course - Dhaka Drive";
include 'partials/header.php';
?>
<section id="course-detail">
    <div class="container">
        <?php display_message(); ?>
        <div class="course-detail-header">
            <img src="Picture/CAR_5.png" alt="Car Driving Course">
            <div class="header-info">
                <h1>Car Driving Course (Manual & Auto)</h1>
                <p class="course-price-display">Price: BDT 5,000</p>
                <p>Our most popular course, designed to take you from a complete novice to a confident, safe, and licensed driver. We cover everything you need to know to navigate the streets of Bangladesh.</p>
                
                <form action="actions/enroll_process.php" method="POST">
                    <input type="hidden" name="course_name" value="Car Driving Course">
                    <div class="form-group">
                        <label for="user-location">Your Preferred Location</label>
                        <input type="text" id="user-location" name="preferred_location" placeholder="e.g., Mirpur, Gulshan, Uttara" required>
                    </div>
                    <div class="terms-container">
                        <input type="checkbox" id="terms-agree-car" name="terms_agree" class="terms-checkbox" required>
                        <label for="terms-agree-car">I have read and agree to the <a href="terms.php" target="_blank">Terms and Conditions</a>.</label>
                    </div>
                    <button type="submit" class="btn request-btn">Request to Enroll</button>
                </form>

            </div>
        </div>
        <div class="course-detail-body">
            <div class="detail-section">
                <h2>What You'll Learn</h2>
                <ul>
                    <li>Vehicle basics: steering, pedals, gears (manual & auto)</li>
                    <li>Starting, stopping, and slow-speed maneuvering</li>
                    <li>Essential parking techniques: parallel, reverse, 90-degree</li>
                    <li>Navigating intersections, roundabouts, and U-turns</li>
                    <li>Understanding and following all BRTA traffic rules and signals</li>
                    <li>Defensive driving strategies for city and highway conditions</li>
                </ul>
            </div>
            <div class="detail-section">
                <h2>Course Structure</h2>
                <p><strong>Total Duration:</strong> 18 Hours</p>
                <p><strong>Schedule:</strong> 12 classes, 1.5 hours each</p>
                <p><strong>Includes:</strong></p>
                <ul>
                    <li>Use of a modern, dual-control training vehicle</li>
                    <li>All fuel and maintenance costs</li>
                    <li>One-on-one instruction with a certified trainer</li>
                    <li>Full assistance with the BRTA learner's permit and final license application</li>
                    <li>One mock test simulating the official BRTA exam</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>