/**
 * @jest-environment jsdom
 */

const { JSDOM } = require('jsdom');
const fs = require('fs');
const path = require('path');

describe('Shaker slider működése', () => {
    let dom;
    let document;
    let window;

    beforeEach(() => {
        // Mock DOM létrehozása
        dom = new JSDOM(`
            <!DOCTYPE html>
            <html>
            <body>
                <div class="shaker-master">
                    <div class="shaker-slides">
                        <div class="shaker-slide"></div>
                        <div class="shaker-slide"></div>
                        <div class="shaker-slide"></div>
                    </div>
                    <button class="shaker-prev"></button>
                    <button class="shaker-next"></button>
                    <div class="shaker-dots">
                        <span class="shaker-dot"></span>
                        <span class="shaker-dot"></span>
                        <span class="shaker-dot"></span>
                    </div>
                </div>
            </body>
            </html>
        `, { runScripts: 'dangerously' });

        document = dom.window.document;
        window = dom.window;

        // Mockoljuk a setInterval-t és clearInterval-t
        jest.useFakeTimers();
        window.setInterval = jest.fn();
        window.clearInterval = jest.fn();

        require('./shakek');
    });

    test('Slider inicializálása', () => {
        const slides = document.querySelector('.shaker-slides');
        expect(slides.style.transform).toBe('translateX(-0%)');
    });

    test('Következő slide-ra váltás', () => {
        const nextButton = document.querySelector('.shaker-next');
        nextButton.click();

        const slides = document.querySelector('.shaker-slides');
        expect(slides.style.transform).toBe('translateX(-100%)');
    });

    test('Előző slide-ra váltás', () => {
        const prevButton = document.querySelector('.shaker-prev');
        prevButton.click();

        const slides = document.querySelector('.shaker-slides');
        expect(slides.style.transform).toBe('translateX(-200%)'); // Mert 3 slide van
    });

    test('Dot-ra kattintás vált a megfelelő slide-ra', () => {
        const dots = document.querySelectorAll('.shaker-dot');
        dots[1].click();

        const slides = document.querySelector('.shaker-slides');
        expect(slides.style.transform).toBe('translateX(-100%)');
    });

    test('Automatikus váltás indítása', () => {
        expect(setInterval).toHaveBeenCalledTimes(1);
        expect(setInterval).toHaveBeenCalledWith(expect.any(Function), 9000);
    });

    test('Automatikus váltás megállítása hover esetén', () => {
        const slider = document.querySelector('.shaker-master');
        slider.dispatchEvent(new window.Event('mouseenter'));

        expect(clearInterval).toHaveBeenCalledTimes(1);
    });

    test('Automatikus váltás újraindítása hover után', () => {
        const slider = document.querySelector('.shaker-master');
        slider.dispatchEvent(new window.Event('mouseenter'));
        slider.dispatchEvent(new window.Event('mouseleave'));

        expect(setInterval).toHaveBeenCalledTimes(2);
    });
});