/**
 * @jest-environment jsdom
 */

describe('Visszaszámláló', () => {
  let document;
  let countdownFunction;

  beforeAll(() => {
    const { JSDOM } = require('jsdom');
    const dom = new JSDOM(`
      <div id="countdown">
        <span class="days">00</span> :
        <span class="hours">00</span> :
        <span class="minutes">00</span> :
        <span class="seconds">00</span>
      </div>
    `);
    document = dom.window.document;
    
    // Mock dátum kezelés
    jest.useFakeTimers();
    jest.spyOn(global, 'setInterval');
    
    // Betöltjük a tesztelendő függvényt
    countdownFunction = require('./countdown.js');
  });

  afterEach(() => {
    jest.clearAllTimers();
  });

  test('helyesen jeleníti meg a hátralévő időt', () => {
    // Mock dátum: 3 nap, 2 óra, 1 perc és 30 másodperc van hátra
    const mockNow = new Date();
    const mockEndDate = new Date(mockNow.getTime() + 
      (3 * 24 * 60 * 60 * 1000) + // 3 nap
      (2 * 60 * 60 * 1000) +      // 2 óra
      (1 * 60 * 1000) +           // 1 perc
      30000);                     // 30 másodperc
    
    // Felülírjuk az eredeti endDate-t
    const originalDate = global.Date;
    global.Date = jest.fn(() => mockNow);
    global.Date.prototype.setDate = originalDate.prototype.setDate;
    
    countdownFunction();
    
    // Előretekerjük az időt 1 másodperccel
    jest.advanceTimersByTime(1000);
    
    expect(document.querySelector('.days').textContent).toBe('03');
    expect(document.querySelector('.hours').textContent).toBe('02');
    expect(document.querySelector('.minutes').textContent).toBe('01');
    expect(document.querySelector('.seconds').textContent).toBe('30');
    
    // Visszaállítjuk az eredeti Date objektumot
    global.Date = originalDate;
  });

  test('megjeleníti az "ajánlat véget ért" üzenetet', () => {
    const mockNow = new Date();
    const mockEndDate = new Date(mockNow.getTime() - 1000); // Múltbeli dátum
    
    const originalDate = global.Date;
    global.Date = jest.fn(() => mockNow);
    global.Date.prototype.setDate = originalDate.prototype.setDate;
    
    countdownFunction();
    jest.advanceTimersByTime(1000);
    
    expect(document.getElementById('countdown').innerHTML)
      .toBe('<strong>Az ajánlat véget ért!</strong>');
    
    global.Date = originalDate;
  });
});


describe('Képváltó', () => {
  let document;
  let changeImageFunction;

  beforeAll(() => {
    const { JSDOM } = require('jsdom');
    const dom = new JSDOM(`
      <div id="image-slider-1">
        <img src="image1.jpg" alt="Image 1">
        <img src="image2.jpg" alt="Image 2">
        <img src="image3.jpg" alt="Image 3">
      </div>
    `);
    document = dom.window.document;
    
    jest.useFakeTimers();
    jest.spyOn(global, 'setInterval');
    
    changeImageFunction = require('./imageSlider.js');
  });

  afterEach(() => {
    jest.clearAllTimers();
  });

  test('helyesen váltja a képeket', () => {
    changeImageFunction('image-slider-1');
    
    const images = document.querySelectorAll('#image-slider-1 img');
    
    // Kezdetben az első kép aktív
    expect(images[0].classList.contains('active')).toBe(true);
    expect(images[1].classList.contains('active')).toBe(false);
    expect(images[2].classList.contains('active')).toBe(false);
    
    // Előretekerjük 3 másodperccel
    jest.advanceTimersByTime(3000);
    
    // Most a második képnek kell aktívnak lennie
    expect(images[0].classList.contains('active')).toBe(false);
    expect(images[1].classList.contains('active')).toBe(true);
    expect(images[2].classList.contains('active')).toBe(false);
    
    // Még 3 másodperc
    jest.advanceTimersByTime(3000);
    
    // Harmadik kép aktív
    expect(images[0].classList.contains('active')).toBe(false);
    expect(images[1].classList.contains('active')).toBe(false);
    expect(images[2].classList.contains('active')).toBe(true);
    
    // Még 3 másodperc - vissza az első képhez
    jest.advanceTimersByTime(3000);
    expect(images[0].classList.contains('active')).toBe(true);
  });

  test('több slider is működik egyszerre', () => {
    // Adjunk hozzá egy második slidert
    document.body.innerHTML += `
      <div id="image-slider-2">
        <img src="slide1.jpg" alt="Slide 1">
        <img src="slide2.jpg" alt="Slide 2">
      </div>
    `;
    
    changeImageFunction('image-slider-1');
    changeImageFunction('image-slider-2');
    
    const slider1Images = document.querySelectorAll('#image-slider-1 img');
    const slider2Images = document.querySelectorAll('#image-slider-2 img');
    
    // Kezdeti állapot
    expect(slider1Images[0].classList.contains('active')).toBe(true);
    expect(slider2Images[0].classList.contains('active')).toBe(true);
    
    // 3 másodperc után
    jest.advanceTimersByTime(3000);
    expect(slider1Images[1].classList.contains('active')).toBe(true);
    expect(slider2Images[1].classList.contains('active')).toBe(true);
  });
});
