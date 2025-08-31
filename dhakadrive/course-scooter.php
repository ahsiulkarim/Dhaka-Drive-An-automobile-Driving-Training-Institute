<?php
$pageTitle = "Scooter Riding Lessons - Dhaka Drive";
include 'partials/header.php';
?>
<section id="course-detail">
    <div class="container">
        <?php display_message(); ?>
        <div class="course-detail-header">
            <img src="Picture/Scooter_6.png" alt="Scooter Riding Lessons">
            <div class="header-info">
                <h1>Scooter Riding Lessons</h1>
                <p class="course-price-display">Price: BDT 2,500</p>
                <p>The perfect choice for easy city commuting. Our scooter lessons are designed for students, office-goers, and anyone looking for a hassle-free way to learn to ride.</p>
                
                <form action="actions/enroll_process.php" method="POST">
                    <input type="hidden" name="course_name" value="Scooter Riding Lessons">
                    <div class="form-group">
                        <label for="user-location">Your Preferred Location</label>
                        <input type="text" id="user-location" name="preferred_location" placeholder="e.g., Mirpur, Gulshan, Uttara" required>
                    </div>
                    <div class="terms-container">
                        <input type="checkbox" id="terms-agree-scooter" name="terms_agree" class="terms-checkbox" required>
                        <label for="terms-agree-scooter">I have read and agree to the <a href="terms.php" target="_blank">Terms and Conditions</a>.</label>
                    </div>
                    <button type="submit" class="btn request-btn">Request to Enroll</button>
                </form>

            </div>
        </div>
        <div class="course-detail-body">
            <div class="detail-section">
                <h2>What You'll Learn</h2>
                <ul>
                    <li>Automatic scooter controls: throttle, brakes, and indicators</li>
                    <li>Achieving stable balance at low and medium speeds</li>
                    <li>Effortless handling and turning in city traffic</li>
                    <li>Safe braking and stopping techniques</li>
                    <li>Building confidence for daily commuting</li>
                    <li>Basic road safety and awareness</li>
                </ul>
            </div>
            <div class="detail-section">
                <h2>Course Structure</h2>
                <p><strong>Total Duration:</strong> 12 Hours</p>
                <p><strong>Schedule:</strong> 8 classes, 1.5 hours each</p>
                <p><strong>Includes:</strong></p>
                <ul>
                    <li>Use of a modern, easy-to-handle automatic scooter</li>
                    <li>Provision of a certified safety helmet</li>
                    <li>Patient and friendly one-on-one instruction</li>
                    <li>Focus on practical skills for urban riding</li>
                    <li>Flexible scheduling to suit your needs</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>