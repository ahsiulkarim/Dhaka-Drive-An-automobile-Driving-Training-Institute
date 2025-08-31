    <footer>
        <div class="container">
            <p>© <?php echo date("Y"); ?> Dhaka Drive. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- ============================================= -->
    <!--          UNIVERSAL MODALS START HERE          -->
    <!-- ============================================= -->

    <!-- Notification Modal (Used by app.js if needed in the future, but not active now) -->
    <div id="notification-modal" class="modal-overlay">
        <div class="modal-content">
            <span class="close-modal">×</span>
            <h2 class="modal-title">My Notifications</h2>
            <div id="notification-list" class="dashboard-list"></div>
        </div>
    </div>

    <!-- Schedule Class Modal -->
    <div id="schedule-modal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <span class="close-modal">×</span>
            <h2 class="modal-title">Schedule Your Class</h2>
            <p>Select a preferred time slot. We will do our best to accommodate.</p>
            <form id="schedule-form" action="actions/schedule_class.php" method="POST">
                <input type="hidden" name="enrollment_id" id="schedule-enrollment-id">
                <div class="form-group">
                    <label for="time-slot">Available Slots</label>
                    <select id="time-slot" name="time_slot" required>
                        <option value="Sat-Mon 9AM-11AM">Sat & Mon, 9AM - 11AM</option>
                        <option value="Sun-Tue 11AM-1PM">Sun & Tue, 11AM - 1PM</option>
                        <option value="Wed-Thu 2PM-4PM">Wed & Thu, 2PM - 4PM</option>
                        <option value="Fri 10AM-1PM">Friday, 10AM - 1PM (Weekend Batch)</option>
                    </select>
                </div>
                <button type="submit" class="btn">Confirm Schedule</button>
            </form>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="payment-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <span class="close-modal">×</span>
            <h2 class="modal-title">Complete Your Payment</h2>
            <p class="text-center">For Course: <strong id="payment-course-name"></strong></p>
            <p class="course-price-display">Amount: BDT <span id="payment-course-price"></span></p>
            <div class="payment-tabs">
                <button class="payment-tab-link active" data-tab="pay-mfs">MFS (bKash/Nagad)</button>
                <button class="payment-tab-link" data-tab="pay-card">Card</button>
                <button class="payment-tab-link" data-tab="pay-cash">Pay in Person</button>
            </div>
            <div id="pay-mfs" class="payment-tab-content active">
                <form action="actions/submit_payment.php" method="POST">
                    <input type="hidden" name="enrollment_id" class="payment-enrollment-id">
                    <input type="hidden" name="payment_method" value="MFS">
                    <p>Send payment to our Merchant bKash/Nagad: <strong>01700-000000</strong></p>
                    <div class="form-group">
                        <label for="mfs-trx-id">Enter Transaction ID (TrxID)</label>
                        <input type="text" name="trx_id" id="mfs-trx-id" placeholder="e.g., 9J8K7L6M5N" required>
                    </div>
                    <button type="submit" class="btn">Submit Payment Info</button>
                </form>
            </div>
            <div id="pay-card" class="payment-tab-content">
                <form action="actions/submit_payment.php" method="POST">
                    <input type="hidden" name="enrollment_id" class="payment-enrollment-id">
                    <input type="hidden" name="payment_method" value="Card">
                    <p class="text-center">This is a simulation. No real payment will be processed.</p>
                    <div class="form-group"><label>Card Number</label><input type="text" placeholder="XXXX XXXX XXXX XXXX" required></div>
                    <button type="submit" class="btn">Pay Now (Simulated)</button>
                </form>
            </div>
            <div id="pay-cash" class="payment-tab-content">
                <form action="actions/submit_payment.php" method="POST">
                    <input type="hidden" name="enrollment_id" class="payment-enrollment-id">
                    <input type="hidden" name="payment_method" value="Cash">
                    <p class="text-center">Your schedule will be confirmed after paying in person.</p>
                    <button type="submit" class="btn">I Will Pay in Person</button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="js/app.js"></script>
</body>
</html>