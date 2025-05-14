/**
 * @jest-environment jsdom
 */

require('./kezdolap');

describe('Heti ajánlatok konténer és stílus ellenőrzése', () => {
  test('Létrehozza a heti ajánlat konténert a megfelelő struktúrával', () => {
    const container = document.getElementById('weekly-deal');
    const heading = container.querySelector('h2');
    const countdown = container.querySelector('#countdown');
    
    // Alap struktúra ellenőrzése
    expect(container).not.toBeNull();
    expect(heading).not.toBeNull();
    expect(countdown).not.toBeNull();
    
    // Tartalom ellenőrzése
    expect(heading.textContent).toBe('Heti ajánlat: Minden pizza féláron!');
    expect(countdown.textContent).toBeTruthy(); // Csak azt nézzük, hogy van-e tartalom
  });

  test('A konténernek megfelelő stílusai vannak', () => {
    const container = document.getElementById('weekly-deal');
    
    // Főbb stílus tulajdonságok ellenőrzése
    expect(container.style.background).toContain('linear-gradient');
    expect(container.style.textAlign).toBe('center');
    expect(container.style.padding).toBe('3em 1em');
    expect(container.style.borderRadius).toBe('15px');
    expect(container.style.maxWidth).toBe('600px');
  });

  test('A címnek megfelelő stílusai vannak', () => {
    const heading = document.querySelector('#weekly-deal h2');
    
    expect(heading.style.fontSize).toBe('2em');
    expect(heading.style.fontWeight).toBe('bold');
    expect(heading.style.marginBottom).toBe('1em');
    expect(heading.style.color).toBe('rgb(255, 87, 51)'); // #ff5733 rgb formátumban
  });

  test('A visszaszámlálónak megfelelő stílusai vannak', () => {
    const countdown = document.getElementById('countdown');
    
    expect(countdown.style.fontSize).toBe('2.5em');
    expect(countdown.style.fontWeight).toBe('bold');
    expect(countdown.style.color).toBe('rgb(255, 69, 0)'); // #ff4500 rgb formátumban
    expect(countdown.style.animation).toContain('pulse 1.5s infinite');
  });

  test('A konténer a body elejére kerül beszúrásra', () => {
    const container = document.getElementById('weekly-deal');
    expect(document.body.firstChild).toBe(container);
  });
});