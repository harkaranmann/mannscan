const { calculateGestationData } = require('../js/lmp');

describe('calculateGestationData', () => {
  test('calculates correct weeks and days', () => {
    const result = calculateGestationData('2024-01-01', '2024-01-15');
    expect(result).toEqual({ weeks: 2, days: 0, totalDays: 14 });
  });

  test('handles partial weeks', () => {
    const result = calculateGestationData('2024-06-01', '2024-06-10');
    expect(result).toEqual({ weeks: 1, days: 2, totalDays: 9 });
  });
});
