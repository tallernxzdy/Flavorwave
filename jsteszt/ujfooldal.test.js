/**
 * @jest-environment jsdom
 */

const { JSDOM } = require('jsdom');
const fs = require('fs');
const path = require('path');

describe('Új főoldal funkcionalitás', () => {
    let dom;
    let document;
    let window;

    beforeEach(() => {
        // Mock DOM létrehozása
        dom = new JSDOM(`
            <!DOCTYPE html>
            <html>
            <body>
                <!-- Weekly Deals Slider -->
                <div class="image-slider">
                    <img>
                    <img>
                </div>
                
                <!-- Coupon Slider -->
                <div class="slide"></div>
                <div class="slide"></div>
                <span class="dot"></span>
                <span class="dot"></span>
                <button class="prev"></button>
                <button class="next"></button>
                
                <!-- Shaker Slider -->
                <div class="shaker-slide"></div>
                <div class="shaker-slide"></div>
                <span class="shaker-dot"></span>
                <span class="shaker-dot"></span>
                <button class="shaker-prev"></button>
                <button class="shaker-next"></button>
                
                <!-- Countdown -->
                <span class="days"></span>
                <span class="hours"></span>
                <span class="minutes"></span>
                <span class="seconds"></span>
            </body>
            </html>
        `, { runScripts: 'dangerously' });

        document = dom.window.document;
        window = dom.window;

        // Mockoljuk a setInterval-t és Date objektumot
        jest.useFakeTimers();
        window.setInterval = jest.fn();
        window.Date = jest.fn(() => ({
            getDay: () => 3, // Csütörtök
            getHours: () => 12,
            getMinutes: () => 0,
            getSeconds: () => 0,
            setDate: jest.fn(),
            setHours: jest.fn(),
            getDate: jest.fn(),
        }));

        // Mock AOS
        window.AOS = {
            init: jest.fn()
        };

        require('./ujfooldal');
    });

    test('Weekly Deals Slider inicializálása', () => {
        const images = document.querySelectorAll('.image-slider img');
        expect(images[0].classList.contains('active')).toBe(true);
        expect(setInterval).toHaveBeenCalled();
    });

    test('Coupon Slider működése', () => {
        const nextButton = document.querySelector('.next');
        nextButton.click();

        const slides = document.querySelectorAll('.slide');
        expect(slides[1].classList.contains('active')).toBe(true);
    });

    test('Shaker Slider működése', () => {
        const shakerNextButton = document.querySelector('.shaker-next');
        shakerNextButton.click();

        const shakerSlides = document.querySelectorAll('.shaker-slide');
        expect(shakerSlides[1].classList.contains('active')).toBe(true);
    });

    test('Visszaszámláló frissítése', () => {
        const daysElement = document.querySelector('.days');
        const hoursElement = document.querySelector('.hours');
        
        // Triggereljük a countdown frissítést
        const updateCountdown = require('./ujfooldal').updateCountdown;
        updateCountdown();
        
        expect(daysElement.textContent).toBeTruthy();
        expect(hoursElement.textContent).toBeTruthy();
    });

    test('AOS inicializálása', () => {
        expect(window.AOS.init).toHaveBeenCalledWith({
            duration: 800,
            easing: 'ease-out-back',
            delay: 200,
            offset: 100,
            once: false
        });
    });
});