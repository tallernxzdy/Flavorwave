/**
 * @jest-environment jsdom
 */

const fs = require('fs');
const path = require('path');

// Betöltjük a HTML-t a jsdom-ba
const html = fs.readFileSync(path.resolve(__dirname, './index.html'), 'utf8');

describe('Quiz Logic', () => {
  let document;
  let quizScript;
  
  beforeAll(() => {
    // JSDOM környezet létrehozása
    const { JSDOM } = require('jsdom');
    const dom = new JSDOM(html, { runScripts: 'dangerously' });
    document = dom.window.document;
    
    // Mockoljuk a DOM elemeket
    document.body.innerHTML = `
      <div class="quiz-question active">
        <div class="quiz-options">
          <button data-type="meat">Húsos</button>
          <button data-type="veggie">Zöldséges</button>
          <button data-type="cheese">Sajtos</button>
        </div>
      </div>
      <div class="quiz-question">
        <div class="quiz-options">
          <button data-spice="mild">Enyhe</button>
          <button data-spice="medium">Közepes</button>
          <button data-spice="hot">Erős</button>
        </div>
      </div>
      <div class="quiz-question">
        <div class="quiz-options">
          <button data-cheese="normal">Normál</button>
          <button data-cheese="extra">Extra</button>
        </div>
      </div>
      <div class="quiz-result-card">
        <p id="recommended-food"></p>
        <img id="food-image" src="">
        <a id="order-link" href="#"></a>
      </div>
    `;
    
    // Betöltjük a tesztelendő scriptet
    quizScript = require('./quiz.js');
  });

  test('should initialize quiz correctly', () => {
    expect(document.querySelector('.quiz-question.active')).not.toBeNull();
    expect(document.querySelector('.quiz-result-card.show')).toBeNull();
  });

  test('should handle question navigation', () => {
    const firstQuestion = document.querySelector('.quiz-question.active');
    const firstOption = firstQuestion.querySelector('button');
    
    firstOption.click();
    
    expect(document.querySelector('.quiz-question.active')).not.toBe(firstQuestion);
  });

  test('should recommend food based on answers', () => {
    // Végiglépkedünk a kérdéseken
    const questions = document.querySelectorAll('.quiz-question');
    questions[0].querySelector('[data-type="meat"]').click();
    questions[1].querySelector('[data-spice="hot"]').click();
    questions[2].querySelector('[data-cheese="extra"]').click();
    
    // Ellenőrizzük az eredményt
    const recommendedFood = document.getElementById('recommended-food');
    const foodImage = document.getElementById('food-image');
    const orderLink = document.getElementById('order-link');
    
    expect(recommendedFood.textContent).toBe('Húsos Pizza extra csípős extra sajttal');
    expect(foodImage.src).toContain('pizza2.jpg');
    expect(orderLink.href).toContain('pizza.php?type=meat&spice=hot&cheese=extra');
  });

  test('should handle veggie option correctly', () => {
    // Reset
    document.querySelector('.quiz-result-card').classList.remove('show');
    document.querySelectorAll('.quiz-question')[0].classList.add('active');
    
    questions[0].querySelector('[data-type="veggie"]').click();
    questions[1].querySelector('[data-spice="medium"]').click();
    questions[2].querySelector('[data-cheese="normal"]').click();
    
    expect(document.getElementById('recommended-food').textContent)
      .toBe('Zöldséges Pizza közepesen csípős');
  });
});