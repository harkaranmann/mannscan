const { calculateGestationData } = require('../js/lmp');

describe('calculateGestationData', () => {
  test('calculates correct weeks and days', () => {
    const result = calculateGestationData('2024-01-01', '2024-01-15');
    expect(result).toEqual({
      weeks: 2,
      days: 0,
      totalDays: 14,
      edd: '2024-10-07',
      trimester: '1st Trimester'
    });
  });

  test('handles partial weeks', () => {
    const result = calculateGestationData('2024-06-01', '2024-06-10');
    expect(result).toEqual({
      weeks: 1,
      days: 2,
      totalDays: 9,
      edd: '2025-03-08',
      trimester: '1st Trimester'
    });
  });
});
