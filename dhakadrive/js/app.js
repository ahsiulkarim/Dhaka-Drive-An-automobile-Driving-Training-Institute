document.addEventListener('DOMContentLoaded', () => {

    // --- Universal UI Handlers ---

    // Password visibility toggle
    document.querySelectorAll('.toggle-password-icon').forEach(icon => {
        icon.textContent = 'SHOW';
        icon.addEventListener('click', function() {
            const wrapper = this.closest('.password-wrapper');
            const input = wrapper.querySelector('input');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            this.textContent = isPassword ? 'HIDE' : 'SHOW';
        });
    });

    // Modal close buttons
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', e => {
            e.target.closest('.modal-overlay').style.display = 'none';
        });
    });

    // --- Dashboard Specific Logic ---
    if (document.getElementById('dashboard')) {
        // Dashboard tabs
        document.querySelectorAll('.dash-tab-link').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.dash-tab-link').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.dash-tab-content').forEach(c => c.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab).classList.add('active');
            });
        });
        
        // Auto-scroll conversation to bottom
        const conversationThread = document.getElementById('user-conversation-thread');
        if (conversationThread) {
            conversationThread.scrollTop = conversationThread.scrollHeight;
        }
    }
});

// --- JAVASCRIPT CODE TO BE ADDED IN app.js ---

document.addEventListener('DOMContentLoaded', () => {

    // ... (keep all your existing app.js code like the password toggle, etc.) ...

    const notificationButton = document.getElementById('notification-btn');
    const notificationModal = document.getElementById('notification-modal');
    const notificationList = document.getElementById('notification-list');
    const notifCountSpan = document.querySelector('.notif-count');
    
    if (notificationButton && notificationModal && notificationList) {
        
        notificationButton.addEventListener('click', async (event) => {
            event.preventDefault();

            // Show a loading message while fetching data
            notificationList.innerHTML = '<p>Loading notifications...</p>';
            notificationModal.style.display = 'flex'; // Use 'flex' to match your CSS

            try {
                // Use the modern Fetch API to call your PHP script
                const response = await fetch('actions/notifications.php');
                
                // Check if the server responded with an error code
                if (!response.ok) {
                    throw new Error(`Server responded with status: ${response.status}`);
                }
                
                const data = await response.json();

                if (data.success) {
                    if (data.notifications.length > 0) {
                        // If notifications were found, build the HTML list
                        notificationList.innerHTML = data.notifications.map(n => `
                            <div class="notification-item ${!n.is_read ? 'unread' : ''}">
                                <p>${n.message}</p>
                                <small>${n.timestamp}</small>
                            </div>
                        `).join('');
                    } else {
                        // If the array is empty, show a message
                        notificationList.innerHTML = '<p>You have no notifications.</p>';
                    }
                    
                    // The notifications are now read, so remove the red counter dot
                    if (notifCountSpan) {
                        notifCountSpan.remove();
                    }

                } else {
                    // Handle errors returned by the PHP script in the JSON response
                    notificationList.innerHTML = `<p style="color: red;">Error: ${data.message || 'Could not load notifications.'}</p>`;
                }
                
            } catch (error) {
                // Handle network errors or issues with the fetch call itself
                console.error('Fetch error:', error);
                notificationList.innerHTML = '<p style="color: red;">Could not connect to the server. Please try again later.</p>';
            }
        });
    }
});

// Inside js/app.js
const response = await fetch('actions/notifications.php'); // This path is now correct

document.addEventListener('DOMContentLoaded', () => {

    // --- Universal UI Handlers ---

    // Password visibility toggle
    document.querySelectorAll('.toggle-password-icon').forEach(icon => {
        icon.textContent = 'SHOW';
        icon.addEventListener('click', function() {
            const wrapper = this.closest('.password-wrapper');
            const input = wrapper.querySelector('input');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            this.textContent = isPassword ? 'HIDE' : 'SHOW';
        });
    });

    // Universal Modal close buttons
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', e => {
            e.target.closest('.modal-overlay').style.display = 'none';
        });
    });

    // --- Notification Modal Logic (THE CORE FIX) ---

    const notificationButton = document.getElementById('notification-btn');
    const notificationModal = document.getElementById('notification-modal');
    const notificationList = document.getElementById('notification-list');
    
    // Only add the event listener if the notification button exists on the page
    if (notificationButton && notificationModal && notificationList) {
        
        notificationButton.addEventListener('click', async (event) => {
            // CRITICAL: This line prevents the browser from following the href="#" link
            event.preventDefault();

            // Show a loading message while fetching data from the server
            notificationList.innerHTML = '<p>Loading notifications...</p>';
            notificationModal.style.display = 'flex'; // Display the modal

            try {
                // Use the Fetch API to call your PHP script in the background
                const response = await fetch('actions/notifications.php');
                
                if (!response.ok) {
                    throw new Error(`Server responded with status: ${response.status}`);
                }
                
                const data = await response.json();

                if (data.success) {
                    if (data.notifications && data.notifications.length > 0) {
                        // If notifications were found, build the HTML list
                        notificationList.innerHTML = data.notifications.map(n => `
                            <div class="notification-item ${!n.is_read ? 'unread' : ''}">
                                <p>${n.message}</p>
                                <small>${n.timestamp}</small>
                            </div>
                        `).join('');
                    } else {
                        // If the notifications array is empty
                        notificationList.innerHTML = '<p>You have no notifications.</p>';
                    }
                    
                    // The notifications are now considered "read", so remove the red counter dot
                    const notifCountSpan = notificationButton.querySelector('.notif-count');
                    if (notifCountSpan) {
                        notifCountSpan.remove();
                    }

                } else {
                    // Handle logical errors returned by the PHP script (e.g., "success": false)
                    notificationList.innerHTML = `<p style="color: red;">Error: ${data.message || 'Could not load notifications.'}</p>`;
                }
                
            } catch (error) {
                // Handle network errors or issues with the fetch call itself
                console.error('Fetch error:', error);
                notificationList.innerHTML = '<p style="color: red;">Could not connect to the server. Please try again later.</p>';
            }
        });
    }

    // --- Dashboard Specific Logic ---
    if (document.getElementById('dashboard')) {
        // Dashboard tabs
        document.querySelectorAll('.dash-tab-link').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.dash-tab-link').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.dash-tab-content').forEach(c => c.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab).classList.add('active');
            });
        });
        
        // Auto-scroll conversation to bottom
        const conversationThread = document.getElementById('user-conversation-thread');
        if (conversationThread) {
            conversationThread.scrollTop = conversationThread.scrollHeight;
        }

        // --- Payment & Schedule Modal Triggers (Example for Dashboard) ---
        document.getElementById('my-courses-list').addEventListener('click', (e) => {
            const paymentModal = document.getElementById('payment-modal');
            const scheduleModal = document.getElementById('schedule-modal');

            // Handle "Make Payment" button click
            if (e.target.classList.contains('btn-pay') && paymentModal) {
                // Logic to open and populate the payment modal would go here
                paymentModal.style.display = 'flex';
            }

            // Handle "Schedule Class" button click
            if (e.target.classList.contains('schedule-btn') && scheduleModal) {
                // Logic to open and populate the schedule modal would go here
                scheduleModal.style.display = 'flex';
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {

    // --- Password visibility toggle ---
    document.querySelectorAll('.toggle-password-icon').forEach(icon => {
        icon.textContent = 'SHOW';
        icon.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            this.textContent = isPassword ? 'HIDE' : 'SHOW';
        });
    });

    // --- Universal Modal close buttons ---
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', e => {
            e.target.closest('.modal-overlay').style.display = 'none';
        });
    });

    // --- Notification Modal Logic ---
    const notificationButton = document.getElementById('notification-btn');
    const notificationModal = document.getElementById('notification-modal');
    const notificationList = document.getElementById('notification-list');
    
    if (notificationButton) {
        notificationButton.addEventListener('click', async (event) => {
            // THE MOST IMPORTANT FIX: This line stops the browser from trying to follow the link.
            event.preventDefault();

            // Show the modal with a "Loading..." message
            notificationList.innerHTML = '<p>Loading notifications...</p>';
            notificationModal.style.display = 'flex';

            try {
                // Fetch the notification data from the server in the background
                const response = await fetch('actions/notifications.php');
                const data = await response.json();

                if (data.success && data.notifications) {
                    if (data.notifications.length > 0) {
                        // If we got notifications, build the HTML list
                        notificationList.innerHTML = data.notifications.map(n => `
                            <div class="notification-item ${!n.is_read ? 'unread' : ''}">
                                <p>${n.message}</p>
                                <small>${n.timestamp}</small>
                            </div>
                        `).join('');
                    } else {
                        // If the user has no notifications
                        notificationList.innerHTML = '<p>You have no new notifications.</p>';
                    }
                    
                    // Remove the red counter dot since the user has now seen the notifications
                    const notifCountSpan = notificationButton.querySelector('.notif-count');
                    if (notifCountSpan) {
                        notifCountSpan.remove();
                    }
                } else {
                    // Show an error message if the server reported a problem
                    notificationList.innerHTML = `<p style="color: red;">Error: ${data.message || 'Could not load notifications.'}</p>`;
                }
                
            } catch (error) {
                // Show an error if the connection to the server failed
                console.error('Notification fetch error:', error);
                notificationList.innerHTML = '<p style="color: red;">Could not connect to the server. Please check your connection and try again.</p>';
            }
        });
    }

    // --- Dashboard Specific Logic ---
    if (document.getElementById('dashboard')) {
        // Dashboard tabs
        document.querySelectorAll('.dash-tab-link').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.dash-tab-link').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.dash-tab-content').forEach(c => c.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab).classList.add('active');
            });
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    console.log("app.js script has loaded successfully!"); // Test message

    const notificationButton = document.getElementById('notification-btn');
    const notificationModal = document.getElementById('notification-modal');
    const notificationList = document.getElementById('notification-list');

    if (notificationButton) {
        console.log("Notification button was found on the page.");

        notificationButton.addEventListener('click', async (event) => {
            // This is the most important part of the fix.
            // It stops the browser from trying to follow the link's href.
            event.preventDefault(); 
            
            console.log("Notification button CLICKED!");

            // Show the modal immediately with a loading state
            notificationList.innerHTML = '<p>Loading...</p>';
            notificationModal.style.display = 'flex';

            try {
                // Fetch data from the server
                const response = await fetch('actions/notifications.php');
                const data = await response.json();

                console.log("Data received from server:", data);

                if (data.success) {
                    if (data.notifications && data.notifications.length > 0) {
                        notificationList.innerHTML = data.notifications.map(n => `
                            <div class="notification-item">
                                <p>${n.message}</p>
                                <small>${n.timestamp}</small>
                            </div>
                        `).join('');
                    } else {
                        notificationList.innerHTML = '<p>You have no notifications.</p>';
                    }
                    // Remove the red counter dot
                    const notifCountSpan = notificationButton.querySelector('.notif-count');
                    if (notifCountSpan) {
                        notifCountSpan.remove();
                    }
                } else {
                    notificationList.innerHTML = `<p style="color:red;">Error: ${data.message}</p>`;
                }
            } catch (error) {
                console.error("Error fetching notifications:", error);
                notificationList.innerHTML = `<p style="color:red;">A network error occurred. Could not fetch notifications.</p>`;
            }
        });
    } else {
        console.log("Notification button was NOT found on this page.");
    }

    // Universal Modal close button
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', e => {
            e.target.closest('.modal-overlay').style.display = 'none';
        });
    });
});

/*-------------------------------------------------------------------------------------------------------------------------------------*/

document.addEventListener('DOMContentLoaded', () => {
    console.log("Simplified app.js has loaded!");

    // --- LOGIC FOR THE "MAKE PAYMENT" BUTTON ---
    const paymentModal = document.getElementById('payment-modal');
    const paymentButtons = document.querySelectorAll('.btn-pay'); // Get ALL payment buttons on the page

    // Check if the modal exists on the page
    if (paymentModal) {
        console.log("Payment modal found.");

        // Loop through each "Make Payment" button we found
        paymentButtons.forEach(button => {
            console.log("Attaching click listener to a payment button.");

            button.addEventListener('click', () => {
                console.log("A 'Make Payment' button was CLICKED!");

                // Get the data from the button that was clicked
                const courseName = button.dataset.courseName;
                const enrollmentId = button.dataset.enrollId;
                
                // A simple map of course prices
                const prices = {
                    "Car Driving Course": 5000,
                    "Motorcycle Riding Course": 3000,
                    "Scooter Riding Lessons": 2500,
                    "Bicycle Safety Program": 1000
                };
                const price = prices[courseName] || 0;
                
                // Find the elements inside the modal and update them
                paymentModal.querySelector('#payment-course-name').textContent = courseName;
                paymentModal.querySelector('#payment-course-price').textContent = price.toLocaleString();
                paymentModal.querySelector('#payment-enrollment-id').value = enrollmentId;

                // Make the modal visible
                paymentModal.style.display = 'flex';
            });
        });

        // Make the modal's close button work
        const closeModalButton = paymentModal.querySelector('.close-modal');
        if (closeModalButton) {
            closeModalButton.addEventListener('click', () => {
                paymentModal.style.display = 'none';
            });
        }
        
    } else {
        console.log("Payment modal was NOT found on this page.");
    }
});