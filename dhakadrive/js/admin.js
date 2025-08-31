// This single event listener waits for the entire HTML document to be ready.
document.addEventListener('DOMContentLoaded', () => {
    console.log("Admin script loaded successfully!");

    // --- 1. SETUP & ELEMENT SELECTION ---
    // We select all the necessary elements from the page at the beginning.
    const adminTabs = document.querySelectorAll('.admin-tabs .tab-link');
    const tabContents = document.querySelectorAll('.tab-content');
    const userEditModal = document.getElementById('user-edit-modal');
    const usersTableBody = document.querySelector('#users-table tbody');
    const conversationList = document.getElementById('conversation-list');
    const messageThread = document.getElementById('admin-conversation-thread');

    // --- 2. LOGIC FOR THE MAIN ADMIN TABS ---
    // This handles switching between Enrollments, User Management, etc.
    adminTabs.forEach(tab => {
        tab.addEventListener('click', (event) => {
            // Because the tabs are now links (<a> tags), we must prevent them
            // from causing a full page reload.
            event.preventDefault(); 
            
            // Get the ID of the tab content we want to show from the link's href.
            // Example: "admin.php?tab=users" -> "users"
            const tabId = new URL(tab.href).searchParams.get('tab');

            // Update the URL in the browser's address bar without reloading the page.
            // This is good for bookmarking and navigation.
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.pushState({}, '', url);

            // Hide all tabs and content first.
            adminTabs.forEach(item => item.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            // Show the clicked tab and its corresponding content.
            tab.classList.add('active');
            const activeContent = document.getElementById(tabId);
            if (activeContent) {
                activeContent.classList.add('active');
            }
        });
    });

    // --- 3. LOGIC FOR THE "EDIT USER" MODAL ---
    if (userEditModal && usersTableBody) {
        // We use "event delegation": one listener on the table body to handle
        // clicks on any current or future 'edit' button inside it.
        usersTableBody.addEventListener('click', (event) => {
            // Check if the clicked element is an edit button.
            const editButton = event.target.closest('button[data-action="edit-user"]');

            if (editButton) {
                const userRow = editButton.closest('tr');
                
                // Get user data from the `data-*` attributes on the table row.
                const userId = userRow.dataset.userId;
                const userName = userRow.dataset.userName;
                const userMobile = userRow.dataset.userMobile;
                const userAddress = userRow.dataset.userAddress;

                // Populate the modal's form fields with this data.
                userEditModal.querySelector('#edit-user-id').value = userId;
                userEditModal.querySelector('#edit-user-name').value = userName;
                userEditModal.querySelector('#edit-user-mobile').value = userMobile;
                userEditModal.querySelector('#edit-user-address').value = userAddress;
                
                // Make the modal visible.
                userEditModal.style.display = 'flex';
            }
        });
    }

    // --- 4. UNIVERSAL MODAL CLOSE BUTTON ---
    // This will work for ANY modal on the page that has a close button.
    document.querySelectorAll('.modal-overlay .close-modal').forEach(button => {
        button.addEventListener('click', () => {
            // Find the closest parent modal and hide it.
            button.closest('.modal-overlay').style.display = 'none';
        });
    });
    
    // --- 5. CONFIRMATION FOR DANGEROUS ACTIONS ---
    // This adds a confirmation popup for any link with the 'confirm-action' class.
    document.querySelectorAll('.confirm-action').forEach(link => {
        link.addEventListener('click', (event) => {
            const message = link.dataset.confirmMessage || 'Are you sure you want to perform this action?';
            if (!confirm(message)) {
                // If the admin clicks "Cancel", stop the link from being followed.
                event.preventDefault(); 
            }
        });
    });

    // --- 6. MESSAGING INTERFACE LOGIC ---
    if (conversationList) {
        const currentUrl = new URL(window.location.href);
        const activeUserId = currentUrl.searchParams.get('user_id');

        // Highlight the currently active conversation in the list if one is selected in the URL.
        if (activeUserId) {
            // First, remove active class from any other item
            conversationList.querySelectorAll('li').forEach(li => li.classList.remove('active'));
            // Then, add it to the correct one
            const activeListItem = conversationList.querySelector(`li[data-userid="${activeUserId}"]`);
            if (activeListItem) {
                activeListItem.classList.add('active');
            }
        }
        
        // Automatically scroll the message thread to the bottom to show the latest messages.
        if (messageThread) {
            messageThread.scrollTop = messageThread.scrollHeight;
        }
    }

}); // End of DOMContentLoaded