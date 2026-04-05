document.addEventListener('DOMContentLoaded', function() {
    
    const evaluationForm = document.getElementById('evaluationForm');

    if (evaluationForm) {
        evaluationForm.addEventListener('submit', function(event) {
            
            // Grab all number inputs within the form
            const scoreInputs = evaluationForm.querySelectorAll('input[type="number"]');
            let isValid = true;

            scoreInputs.forEach(function(input) {
                const value = parseFloat(input.value);
                
                // Validate that scores are strictly between 0 and 100
                if (value < 0 || value > 100 || isNaN(value)) {
                    isValid = false;
                    input.style.borderColor = 'red'; // Highlight the bad input
                } else {
                    input.style.borderColor = '#ccc'; // Reset valid inputs
                }
            });

            if (!isValid) {
                event.preventDefault(); // Stop the form from submitting
                alert('Validation Error: All scores must be between 0 and 100.');
            }
        });
    }
});