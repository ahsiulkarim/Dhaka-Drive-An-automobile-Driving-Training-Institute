<?php 
$pageTitle = "Dhaka Driving Institute | Home";
include 'partials/header.php'; 
?>
    <section id="hero">
        <div class="container">
            <h1>Your Journey to Safe Driving Starts Here</h1>
            <p>Expert training for Car, Motorcycle, Scooter, and Bicycle. Learn with the best in Bangladesh!</p>
            <a href="#courses" class="btn">Explore All Courses</a>
        </div>
    </section>
    <section id="why-us">
        <div class="container">
            <h2 class="section-title">Why Learn With Us?</h2>
            <div class="features">
                <div class="feature-item"><h3>BRTA Guideline Courses</h3><p>Our curriculum is designed following official BRTA guidelines to ensure your success.</p></div>
                <div class="feature-item"><h3>Expert Instructors</h3><p>Our certified instructors are patient, professional, and dedicated to teaching you safe driving.</p></div>
                <div class="feature-item"><h3>License Assistance</h3><p>We guide you through the entire process of applying for your official driving license.</p></div>
                <div class="feature-item"><h3>Flexible Timings</h3><p>We offer classes in the morning, afternoon, and on weekends to fit your schedule.</p></div>
            </div>
        </div>
    </section>
    <section id="courses">
        <div class="container">
            <h2 class="section-title">Our Driving Courses</h2>
            <div class="course-list">
                <div class="course-card">
                    <img src="Picture/CAR.png" alt="Car driving lesson">
                    <div class="course-card-content">
                        <h3>Car Driving Course (Manual & Auto)</h3>
                        <ul>
                            <li><strong>Perfect For:</strong> Absolute beginners aiming to drive a car.</li>
                            <li><strong>You'll Learn:</strong> Traffic rules, parking, city & highway driving.</li>
                        </ul>
                        <a href="course-car.php" class="btn">View Details</a>
                    </div>
                </div>
                <div class="course-card">
                    <img src="Picture/BIKE_6.png" alt="Motorcycle riding lesson">
                    <div class="course-card-content">
                        <h3>Motorcycle Riding Course</h3>
                         <ul>
                            <li><strong>Perfect For:</strong> New riders wanting to master two-wheelers.</li>
                            <li><strong>You'll Learn:</strong> Balancing, gear shifting, and road safety.</li>
                        </ul>
                        <a href="course-bike.php" class="btn">View Details</a>
                    </div>
                </div>
                <div class="course-card">
                    <img src="Picture/Scooter.png" alt="Scooter riding lesson">
                    <div class="course-card-content">
                        <h3>Scooter Riding Lessons</h3>
                         <ul>
                            <li><strong>Perfect For:</strong> Easy city commuting, students, and office-goers.</li>
                            <li><strong>You'll Learn:</strong> Easy handling, balance, and navigating traffic.</li>
                        </ul>
                        <a href="course-scooter.php" class="btn">View Details</a>
                    </div>
                </div>
                <div class="course-card">
                    <img src="Picture/Bicyle.png" alt="Bicycle safety training">
                    <div class="course-card-content">
                        <h3>Bicycle Safety Program</h3>
                        <ul>
                            <li><strong>Perfect For:</strong> Children and adults learning to cycle safely.</li>
                            <li><strong>You'll Learn:</strong> Balancing, road signs, and safe cycling habits.</li>
                        </ul>
                        <a href="course-bicycle.php" class="btn">View Details</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="testimonials">
        <div class="container">
            <h2 class="section-title">What Our Students Say</h2>
            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <p>"Dhaka Drive made learning so easy! My instructor was incredibly patient. I passed my test on the first try. Highly recommended!"</p>
                    <div class="testimonial-author">- Sadia Islam, Car Driving Student</div>
                </div>
                <div class="testimonial-card">
                    <p>"I was always scared of riding a motorcycle in Dhaka traffic. The instructors here gave me the confidence and skills I needed. Thank you!"</p>
                    <div class="testimonial-author">- Rifat Ahmed, Motorcycle Student</div>
                </div>
                 <div class="testimonial-card">
                    <p>"The license assistance service is a lifesaver. They guided me through every step of the BRTA paperwork, which saved me a lot of hassle."</p>
                    <div class="testimonial-author">- Nabil Haque, Car Driving Student</div>
                </div>
            </div>
        </div>
    </section>
    <section id="faq">
        <div class="container">
            <h2 class="section-title">Common Questions</h2>
            <div class="faq-item"><h4>Do I need my own vehicle for the course?</h4><p>No, we provide the training vehicle (car, motorcycle, or scooter) and safety gear for all our beginner courses.</p></div>
            <div class="faq-item"><h4>What documents are required to enroll?</h4><p>You will need a copy of your National ID (NID) card or Birth Certificate. You can upload your document from your dashboard after signing up.</p></div>
            <div class="faq-item"><h4>Do you help with the BRTA license test?</h4><p>Yes! Our course fully prepares you for the test, and we provide complete assistance with the application process.</p></div>
        </div>
    </section>
    <!-- This is the "After" code -->
    <section id="contact">
        <div class="container">
            <div class="contact-box">
              <!-- THE FIX IS HERE: Added a wrapper div for the first column -->
                <div class="contact-column">
                <h2>Get Started Today!</h2>
                <p>Call us or visit our office to book your spot. Your driving adventure awaits!</p>
                <a href="tel:+8801712345678" class="btn">Call Us Now</a>
                </div>
            <!-- THE FIX IS HERE: Renamed the second div to match -->
            <div class="contact-column">
                <div class="contact-details">
                    <p><strong>Phone:</strong> +8801817738447</p>
                    <p><strong>Email:</strong> info@dhakadrive.com.bd<zz/p>
                    <p><strong>Location:</strong> House 33, Road 3, Aftabnagar, Dhaka-1212, Bangladesh</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'partials/footer.php'; ?>