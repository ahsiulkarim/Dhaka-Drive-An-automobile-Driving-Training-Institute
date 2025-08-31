<?php
$pageTitle = "Bicycle Safety Program - Dhaka Drive";
include 'partials/header.php';
?>
<section id="course-detail">
    <div class="container">
        <?php display_message(); ?>
        <div class="course-detail-header">
            <img src="Picture/Bicyle_6.png" alt="Bicycle Safety Program">
            <div class="header-info">
                <h1>Bicycle Safety Program</h1>
                <p class="course-price-display">Price: BDT 1,000</p>
                <p>Learn to ride a bicycle or improve your road safety skills with our program for all ages. We make learning fun and focus on building habits that ensure a lifetime of safe cycling.</p>
                
                <form action="actions/enroll_process.php" method="POST">
                    <input type="hidden" name="course_name" value="Bicycle Safety Program">
                    <div class="form-group">
                        <label for="user-location">Your Preferred Location</label>
                        <input type="text" id="user-location" name="preferred_location" placeholder="e.g., Mirpur, Gulshan, Uttara" required>
                    </div>
                    <div class="terms-container">
                        <input type="checkbox" id="terms-agree-bicycle" name="terms_agree" class="terms-checkbox" required>
                        <label for="terms-agree-bicycle">I have read and agree to the <a href="terms.php" target="_blank">Terms and Conditions</a>.</label>
                    </div>
                    <button type="submit" class="btn request-btn">Request to Enroll</button>
                </form>

            </div>
        </div>
        <div class="course-detail-body">
            <div class="detail-section">
                <h2>What You'll Learn</h2>
                <ul>
                    <li>Learning to balance and pedal from scratch.</li>
                    <li>Proper braking and controlled stopping.</li>
                    <li>Using hand signals for turning and stopping.</li>
                    <li>Understanding basic road signs and traffic rules for cyclists.</li>
                    <li>How to wear a helmet correctly and perform bike safety checks.</li>
                    <li>Tips for sharing the road safely with cars and pedestrians.</li>
                </ul>
            </div>
            <div class="detail-section">
                <h2>Course Structure</h2>
                <p><strong>Total Duration:</strong> 6 Hours</p>
                <p><strong>Schedule:</strong> 4 classes, 1.5 hours each (Weekend batches available)</p>
                <p><strong>Includes:</strong></p>
                <ul>
                    <li>Bicycles of various sizes to suit all learners.</li>
                    <li>Safety helmets and protective pads.</li>
                    <li>Instruction in a safe, traffic-free environment.</li>
                    <li>A friendly, patient instructor for children and adults.</li>
                    <li>A certificate of completion.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>