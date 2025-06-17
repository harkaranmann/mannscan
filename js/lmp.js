(function(global){
  function calculateGestationData(lmpDateInput, todayDate = new Date()) {
    const lmpDate = new Date(lmpDateInput);
    const today = new Date(todayDate);
    if (isNaN(lmpDate)) {
      throw new Error('Invalid LMP date');
    }
    const diffTime = Math.abs(today - lmpDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    const weeks = Math.floor(diffDays / 7);
    const days = diffDays % 7;
    return { weeks, days, totalDays: diffDays };
  }

  function calculateGestation() {
    const input = document.getElementById('lmpDate');
    const resultDiv = document.getElementById('lmpResult');
    if (!input || !resultDiv) return;
    const lmpDate = new Date(input.value);
    const today = new Date();
    if (lmpDate > today || isNaN(lmpDate)) {
      resultDiv.style.display = 'none';
      return;
    }
    const data = calculateGestationData(lmpDate, today);
    resultDiv.innerHTML = `
        <strong>Gestational Age:</strong><br>
        ${data.weeks} weeks ${data.days} days<br>
        <small>(${data.totalDays} total days)</small>
      `;
    resultDiv.style.display = 'block';
  }

  function toggleLMP() {
    const calculator = document.getElementById('lmpCalculator');
    if (calculator) {
      calculator.classList.toggle('show');
    }
  }

  global.calculateGestation = calculateGestation;
  global.toggleLMP = toggleLMP;

  if (typeof module !== 'undefined' && module.exports) {
    module.exports = { calculateGestationData };
  } else {
    global.calculateGestationData = calculateGestationData;
  }
})(typeof window !== 'undefined' ? window : this);
