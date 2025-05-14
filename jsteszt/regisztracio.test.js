/**
 * @jest-environment jsdom
 */

describe('Jelszó láthatóság váltó gomb tesztje', () => {
  let document;
  let toggleButton;
  let passwordInput;
  let mockEvent;

  beforeEach(() => {
    // Mock DOM létrehozása
    document = `
      <html>
        <body>
          <input type="password" id="password">
          <button id="togglePassword">
            <svg>
              <path id="eye-icon"></path>
            </svg>
          </button>
        </body>
      </html>
    `;

    // JSDOM inicializálása
    const dom = new JSDOM(document, { runScripts: 'dangerously' });
    global.document = dom.window.document;
    
    // HTML elemek kinyerése
    passwordInput = document.getElementById('password');
    toggleButton = document.getElementById('togglePassword');
    
    // Mock esemény létrehozása
    mockEvent = {
      preventDefault: jest.fn()
    };

    // Tesztelendő szkript betöltése
    require('./regisztracio.php');
  });

  test('Az eseménykezelő hozzá van adva a gombhoz', () => {
    const clickSpy = jest.spyOn(toggleButton, 'addEventListener');
    toggleButton.dispatchEvent(new Event('click'));
    expect(clickSpy).toHaveBeenCalledWith('click', expect.any(Function));
  });

  test('Kattintás megakadályozza az alapértelmezett viselkedést', () => {
    toggleButton.click(mockEvent);
    expect(mockEvent.preventDefault).toHaveBeenCalled();
  });

  test('Jelszó láthatóvá tétele', () => {
    // Kezdetben password típusú
    expect(passwordInput.type).toBe('password');
    
    // Első kattintás
    toggleButton.click(mockEvent);
    expect(passwordInput.type).toBe('text');
    
    // SVG ikon ellenőrzése
    const svgPath = toggleButton.querySelector('svg').innerHTML;
    expect(svgPath).toContain('M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173');
  });

  test('Jelszó elrejtése', () => {
    // Először láthatóvá tesszük
    toggleButton.click(mockEvent);
    expect(passwordInput.type).toBe('text');
    
    // Második kattintás
    toggleButton.click(mockEvent);
    expect(passwordInput.type).toBe('password');
    
    // SVG ikon ellenőrzése
    const svgPath = toggleButton.querySelector('svg').innerHTML;
    expect(svgPath).toContain('M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7');
  });

  test('Ikon váltása a jelszó láthatóság szerint', () => {
    const initialIcon = toggleButton.querySelector('svg').innerHTML;
    
    // Első kattintás
    toggleButton.click(mockEvent);
    const visibleIcon = toggleButton.querySelector('svg').innerHTML;
    expect(visibleIcon).not.toBe(initialIcon);
    
    // Második kattintás
    toggleButton.click(mockEvent);
    const hiddenIcon = toggleButton.querySelector('svg').innerHTML;
    expect(hiddenIcon).toBe(initialIcon);
  });
});