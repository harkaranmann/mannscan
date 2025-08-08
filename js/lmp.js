(function(global) {
  'use strict';
  
  // Error handling utility
  function handleError(error, element) {
    console.error('LMP Calculator Error:', error);
    if (element) {
      element.classList.add('error');
      const errorDiv = document.createElement('div');
      errorDiv.className = 'error-message';
      errorDiv.textContent = error.message || 'An error occurred';
      errorDiv.style.display = 'block';
      element.parentNode.insertBefore(errorDiv, element.nextSibling);
      
      // Remove error after 5 seconds
      setTimeout(() => {
        errorDiv.remove();
        element.classList.remove('error');
      }, 5000);
    }
  }
  
  // Clear previous errors
  function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.remove());
    document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
  }
  
  function calculateGestationData(lmpDateInput, todayDate = new Date()) {
    if (!lmpDateInput) {
      throw new Error('LMP date is required');
    }
    
    const lmpDate = new Date(lmpDateInput);
    const today = new Date(todayDate);
    
    if (isNaN(lmpDate.getTime())) {
      throw new Error('Invalid LMP date format');
    }
    
    if (lmpDate > today) {
      throw new Error('LMP date cannot be in the future');
    }
    
    // Check if date is more than 42 weeks ago (reasonable pregnancy limit)
    const maxDays = 42 * 7; // 42 weeks
    const diffTime = Math.abs(today - lmpDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays > maxDays) {
      throw new Error('LMP date is too far in the past (more than 42 weeks)');
    }
    
    const weeks = Math.floor(diffDays / 7);
    const days = diffDays % 7;
    
    // Calculate estimated due date (EDD)
    const eddDate = new Date(lmpDate);
    eddDate.setDate(eddDate.getDate() + 280); // 40 weeks = 280 days
    const edd = eddDate.toISOString().split('T')[0];

    return {
      weeks,
      days,
      totalDays: diffDays,
      edd,
      trimester: getTrimester(weeks)
    };
  }
  
  function getTrimester(weeks) {
    if (weeks <= 12) return '1st Trimester';
    if (weeks <= 27) return '2nd Trimester';
    return '3rd Trimester';
  }

  function calculateGestation() {
    try {
      clearErrors();
      
      const input = document.getElementById('lmpDate');
      const resultDiv = document.getElementById('lmpResult');
      
      if (!input || !resultDiv) {
        throw new Error('Required elements not found');
      }
      
      if (!input.value) {
        resultDiv.style.display = 'none';
        return;
      }
      
      const data = calculateGestationData(input.value);
      
      resultDiv.innerHTML = `
        <strong>Gestational Age:</strong><br>
        ${data.weeks} weeks ${data.days} days<br>
        <small>(${data.totalDays} total days)</small><br><br>
        <strong>Trimester:</strong> ${data.trimester}<br>
        <strong>Estimated Due Date:</strong> ${data.edd}
      `;
      resultDiv.style.display = 'block';
      
    } catch (error) {
      const input = document.getElementById('lmpDate');
      const resultDiv = document.getElementById('lmpResult');
      if (resultDiv) resultDiv.style.display = 'none';
      handleError(error, input);
    }
  }

  function toggleLMP() {
    try {
      const calculator = document.getElementById('lmpCalculator');
      if (calculator) {
        calculator.classList.toggle('show');
      }
    } catch (error) {
      console.error('Error toggling LMP calculator:', error);
    }
  }

  // Export functions
  global.calculateGestation = calculateGestation;
  global.toggleLMP = toggleLMP;

  // For testing
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = { calculateGestationData, getTrimester };
  } else {
    global.calculateGestationData = calculateGestationData;
    global.getTrimester = getTrimester;
  }
  
  // Initialize when DOM is ready
  if (typeof document !== 'undefined') {
    document.addEventListener('DOMContentLoaded', function() {
      // Add input validation
      const lmpInput = document.getElementById('lmpDate');
      if (lmpInput) {
        lmpInput.addEventListener('input', function() {
          clearErrors();
        });
        
        // Set max date to today
        lmpInput.max = new Date().toISOString().split('T')[0];
        
        // Set reasonable min date (42 weeks ago)
        const minDate = new Date();
        minDate.setDate(minDate.getDate() - (42 * 7));
        lmpInput.min = minDate.toISOString().split('T')[0];
      }
    });
  }
  
})(typeof window !== 'undefined' ? window : this);
