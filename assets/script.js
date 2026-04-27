document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. FORM VALIDATION LOGIC ---
    const evaluationForm = document.getElementById('evaluationForm');

    if (evaluationForm) {
        evaluationForm.addEventListener('submit', function(event) {
            const scoreInputs = evaluationForm.querySelectorAll('input[type="number"]');
            let isValid = true;

            scoreInputs.forEach(function(input) {
                const value = parseFloat(input.value);
                if (value < 0 || value > parseFloat(input.getAttribute('max')) || isNaN(value)) {
                    isValid = false;
                    input.style.borderColor = 'red'; 
                } else {
                    input.style.borderColor = 'var(--border)'; 
                }
            });

            if (!isValid) {
                event.preventDefault(); 
                alert('Validation Error: All scores must be between 0 and 100.');
            }
        });
    }

    // --- 2. DARK/LIGHT MODE TOGGLE LOGIC ---
    const themeBtn = document.getElementById('themeToggle');
    
    if (themeBtn) {
        // Set the initial icon based on what the <head> script loaded
        const currentTheme = document.documentElement.getAttribute('data-theme');
        themeBtn.textContent = currentTheme === 'dark' ? '🌙' : '☀️';

        // Listen for clicks
        themeBtn.addEventListener('click', function() {
            const html = document.documentElement;
            const nextTheme = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', nextTheme);
            localStorage.setItem('theme', nextTheme);
            themeBtn.textContent = nextTheme === 'dark' ? '🌙' : '☀️';
        });
    }

    // --- 3. PASSWORD VISIBILITY TOGGLE ---
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function(e) {
            e.preventDefault(); // Stop form submission just in case
            
            // Swap between 'password' and 'text' types
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Swap the emoji
            this.textContent = type === 'password' ? '👁️' : '🙈';
        });
    }

    // --- 4. TOAST NOTIFICATION AUTO-DISMISS ---
    const toasts = document.querySelectorAll('.toast-notification');
    toasts.forEach(toast => {
        setTimeout(() => {
            toast.classList.add('hide'); // Triggers the smooth CSS fade-out animation
            setTimeout(() => toast.remove(), 500); // Wait for animation to finish, then delete from page
        }, 4000); 
    });

    // --- 5. REAL-TIME TABLE FILTERING ---
    const searchInputs = document.querySelectorAll('.search-form input[type="text"]');
    searchInputs.forEach(searchInput => {
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const table = this.closest('.management-layout').querySelector('.dashboard-table');
            
            if (table) {
                // Grab ONLY the direct rows of the main table body (ignoring nested detail tables)
                    const mainTbody = table.querySelector('tbody');
                    const rows = mainTbody.querySelectorAll(':scope > tr:not([id^="detail-"]):not(:has(.empty-message))');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(filter)) {
                        row.style.display = ''; // Show if there is a match
                    } else {
                        row.style.display = 'none'; // Hide if no match
                        
                        // If this row has an expanded detail dropdown (View Results page), hide that too
                        const detailId = row.querySelector('.btn-edit')?.getAttribute('onclick')?.match(/'([^']+)'/)?.[1];
                        if (detailId) {
                            const detailRow = document.getElementById(detailId);
                            if (detailRow) detailRow.style.display = 'none';
                        }
                    }
                });
            }
        });
    });
    // --- 6. CUSTOM DANGER MODALS ---
    const deleteForms = document.querySelectorAll('.delete-form');
    if (deleteForms.length > 0) {
        // Inject the custom modal HTML into the page dynamically
        const modalHTML = `
            <div class="danger-overlay" id="dangerOverlay">
                <div class="danger-card">
                    <h3>Are you sure?</h3>
                    <p id="dangerMessage">This action cannot be undone.</p>
                    <div class="danger-actions">
                        <button type="button" class="btn-cancel" id="cancelDelete">Cancel</button>
                        <button type="button" class="btn-danger" id="confirmDelete">Yes, Delete</button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        const overlay = document.getElementById('dangerOverlay');
        const btnCancel = document.getElementById('cancelDelete');
        const btnConfirm = document.getElementById('confirmDelete');
        const msgElement = document.getElementById('dangerMessage');
        let formToSubmit = null;

        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Stop the form from submitting immediately!
                formToSubmit = this; // Remember which row they clicked
                // Grab the custom warning message hidden in the HTML
                const customMsg = this.getAttribute('data-confirm-msg');
                msgElement.textContent = customMsg;
                overlay.classList.add('active'); // Show our sleek modal
            });
        });

        // If they click Cancel, hide it
        btnCancel.addEventListener('click', () => {
            overlay.classList.remove('active');
            formToSubmit = null;
        });

        // If they click Yes, Delete, submit the specific form they clicked
        btnConfirm.addEventListener('click', () => {
            if (formToSubmit) formToSubmit.submit();
        });
    }

    // --- 7. CSV FILE UPLOADER UX ---
    // Changes the text of the drop-zone to match the file they uploaded
    const fileInput = document.getElementById('csv_file');
    const fileText = document.getElementById('file-upload-text');
    if (fileInput && fileText) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                fileText.textContent = "📄 " + this.files[0].name;
            } else {
                fileText.textContent = 'Click to choose a .csv file';
            }
        });
    }
});

