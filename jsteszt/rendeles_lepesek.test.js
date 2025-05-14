/**
 * @jest-environment jsdom
 */

const { JSDOM } = require('jsdom');
const fs = require('fs');
const path = require('path');

// HTML fájl betöltése
const html = fs.readFileSync(path.resolve(__dirname, './test.html'), 'utf8');
let dom;
let container;

// Mockoljuk az IntersectionObserver-t
beforeEach(() => {
    dom = new JSDOM(html, { runScripts: 'dangerously' });
    container = dom.window.document.body;

    global.IntersectionObserver = class MockIntersectionObserver {
        constructor(callback, options) {
            this.callback = callback;
            this.options = options;
            this.observers = [];
        }

        observe(target) {
            this.observers.push(target);
        }

        // Teszteléshez használt metódus
        triggerIntersection(target, isIntersecting) {
            this.callback([{ target, isIntersecting }]);
        }
    };
});

describe('Rendelési lépések animációja', () => {
    test('Létrehozza az IntersectionObserver-t', () => {
        require('./rendeles_lepesek');
        expect(IntersectionObserver).toHaveBeenCalled();
        expect(IntersectionObserver.mock.calls[0][1].threshold).toBe(0.5);
    });

    test('Figyeli a .step elemeket', () => {
        // Mock DOM létrehozása step elemekkel
        container.innerHTML = `
            <div class="step"></div>
            <div class="step"></div>
        `;

        require('./rendeles_lepesek');
        const steps = container.querySelectorAll('.step');
        const mockObserver = IntersectionObserver.mock.instances[0];

        expect(mockObserver.observe).toHaveBeenCalledTimes(2);
        steps.forEach(step => {
            expect(mockObserver.observe).toHaveBeenCalledWith(step);
        });
    });

    test('Hozzáadja a show osztályt, ha az elem látható', () => {
        container.innerHTML = `<div class="step"></div>`;
        require('./rendeles_lepesek');

        const step = container.querySelector('.step');
        const mockObserver = IntersectionObserver.mock.instances[0];

        // Triggereljük az intersection eseményt
        mockObserver.triggerIntersection(step, true);

        expect(step.classList.contains('show')).toBe(true);
    });

    test('Nem ad hozzá show osztályt, ha az elem nem látható', () => {
        container.innerHTML = `<div class="step"></div>`;
        require('./rendeles_lepesek');

        const step = container.querySelector('.step');
        const mockObserver = IntersectionObserver.mock.instances[0];

        // Triggereljük az intersection eseményt
        mockObserver.triggerIntersection(step, false);

        expect(step.classList.contains('show')).toBe(false);
    });
});