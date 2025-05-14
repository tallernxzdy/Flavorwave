/**
 * @jest-environment jsdom
 */

describe('Kupon Slider', () => {
  let document;
  let sliderModule;
  let slidesContainer;
  let slides;
  let dots;
  let prevButton, nextButton;

  beforeAll(() => {
    // JSDOM inicializálása
    const { JSDOM } = require('jsdom');
    const dom = new JSDOM(`
      <div class="coupon-slider">
        <div class="slides" style="min-height: 0px;">
          <div class="slide" style="height: 0px;"></div>
          <div class="slide" style="height: 0px;"></div>
          <div class="slide" style="height: 0px;"></div>
        </div>
        <button class="prev"></button>
        <button class="next"></button>
        <div class="dots">
          <span class="dot"></span>
          <span class="dot"></span>
          <span class="dot"></span>
        </div>
      </div>
    `);
    
    document = dom.window.document;
    global.document = document;
    
    // Mock offsetHeight értékek beállítása
    Object.defineProperty(dom.window.HTMLElement.prototype, 'offsetHeight', {
      get() {
        if (this.classList.contains('slide')) {
          return this.textContent.includes('tall') ? 300 : 200;
        }
        return 0;
      }
    });

    // Timer mock
    jest.useFakeTimers();
    jest.spyOn(global, 'setInterval');
    jest.spyOn(global, 'clearInterval');

    // Modul betöltése
    sliderModule = require('./kupon.js');
  });

  beforeEach(() => {
    // Reset állapot
    slides = document.querySelectorAll('.slide');
    dots = document.querySelectorAll('.dot');
    prevButton = document.querySelector('.prev');
    nextButton = document.querySelector('.next');
    slidesContainer = document.querySelector('.slides');
  });

  afterEach(() => {
    jest.clearAllTimers();
  });

  test('inicializáláskor beállítja a max magasságot', () => {
    // Mock slide-ok
    slides[0].textContent = 'short';
    slides[1].textContent = 'tall';
    slides[2].textContent = 'medium';
    
    // Trigger load event
    window.dispatchEvent(new Event('load'));
    
    expect(slidesContainer.style.minHeight).toBe('300px');
    slides.forEach(slide => {
      expect(slide.style.height).toBe('300px');
    });
  });

  test('slide váltás működik', () => {
    window.dispatchEvent(new Event('load'));
    
    // Kezdetben az első slide aktív
    expect(slides[0].classList.contains('active')).toBe(true);
    expect(dots[0].classList.contains('active')).toBe(true);
    
    // Következő slide
    nextButton.click();
    expect(slides[1].classList.contains('active')).toBe(true);
    expect(dots[1].classList.contains('active')).toBe(true);
    
    // Előző slide
    prevButton.click();
    expect(slides[0].classList.contains('active')).toBe(true);
    
    // Körkörös működés
    prevButton.click();
    expect(slides[2].classList.contains('active')).toBe(true);
  });

  test('automatikus váltás működik', () => {
    window.dispatchEvent(new Event('load'));
    
    // Kezdeti állapot
    expect(slides[0].classList.contains('active')).toBe(true);
    
    // Timer elindul
    expect(setInterval).toHaveBeenCalled();
    
    // 7 másodperc eltelik
    jest.advanceTimersByTime(7000);
    expect(slides[1].classList.contains('active')).toBe(true);
    
    // Még 7 másodperc
    jest.advanceTimersByTime(7000);
    expect(slides[2].classList.contains('active')).toBe(true);
  });

  test('dots navigáció működik', () => {
    window.dispatchEvent(new Event('load'));
    
    dots[1].click();
    expect(currentIndex).toBe(1);
    expect(slides[1].classList.contains('active')).toBe(true);
    
    dots[2].click();
    expect(currentIndex).toBe(2);
  });

  test('hover események kezelése', () => {
    const slider = document.querySelector('.coupon-slider');
    window.dispatchEvent(new Event('load'));
    
    // Mouseenter leállítja az automatikus váltást
    slider.dispatchEvent(new Event('mouseenter'));
    expect(clearInterval).toHaveBeenCalled();
    
    // Mouseleave újraindítja
    slider.dispatchEvent(new Event('mouseleave'));
    expect(setInterval).toHaveBeenCalled();
  });

  test('resize esemény újraszámolja a magasságot', () => {
    window.dispatchEvent(new Event('load'));
    const originalHeight = slidesContainer.style.minHeight;
    
    // Mock új magasságok
    slides[0].textContent = 'new tall';
    slides[1].textContent = 'short';
    slides[2].textContent = 'short';
    
    window.dispatchEvent(new Event('resize'));
    expect(slidesContainer.style.minHeight).not.toBe(originalHeight);
  });

  test('gombnyomás újraindítja az automatikus váltást', () => {
    window.dispatchEvent(new Event('load'));
    
    const initialCalls = setInterval.mock.calls.length;
    nextButton.click();
    
    expect(clearInterval).toHaveBeenCalled();
    expect(setInterval.mock.calls.length).toBe(initialCalls + 1);
  });
});