// bejelentkezes.test.js
import '@testing-library/jest-dom';
import { setupPasswordToggle } from './bejelentkezes';

describe('Bejelentkezés JavaScript Tests', () => {
  beforeEach(() => {
    document.body.innerHTML = `
      <input type="password" id="password" />
      <span id="togglePassword">
        <svg>
          <path class="initial-path" />
        </svg>
      </span>
    `;
  });

  test('setupPasswordToggle does nothing if elements are missing', () => {
    document.body.innerHTML = ''; // Üres DOM
    setupPasswordToggle();
    expect(document.body.innerHTML).toBe(''); // Nem dob hibát
  });

  test('setupPasswordToggle toggles password visibility to text on click', () => {
    setupPasswordToggle();
    const togglePassword = document.getElementById('togglePassword');
    const jelszoInput = document.getElementById('password');
    const ikon = togglePassword.querySelector('svg');

    togglePassword.click();

    expect(jelszoInput.type).toBe('text');
    expect(ikon.innerHTML).toContain('M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8');
    expect(ikon.innerHTML).toContain('M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0');
  });

  test('setupPasswordToggle toggles password visibility back to password on second click', () => {
    setupPasswordToggle();
    const togglePassword = document.getElementById('togglePassword');
    const jelszoInput = document.getElementById('password');
    const ikon = togglePassword.querySelector('svg');

    togglePassword.click(); // Első kattintás: text
    togglePassword.click(); // Második kattintás: password

    expect(jelszoInput.type).toBe('password');
    expect(ikon.innerHTML).toContain('M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5');
    expect(ikon.innerHTML).toContain('M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8');
  });

  test('setupPasswordToggle prevents default event behavior', () => {
    setupPasswordToggle();
    const togglePassword = document.getElementById('togglePassword');
    const event = new Event('click', { cancelable: true });
    event.preventDefault = jest.fn();

    togglePassword.dispatchEvent(event);

    expect(event.preventDefault).toHaveBeenCalled();
  });
});