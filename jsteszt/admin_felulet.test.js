// admin_felulet.test.js
import '@testing-library/jest-dom';
import { setupOperationChangeListener, setupEditEtelListener, editUser, saveUserChanges, blockE } from './admin_felulet';

// Mock fetch
global.fetch = jest.fn();

describe('Admin Felület JavaScript Tests', () => {
  beforeEach(() => {
    document.body.innerHTML = `
      <select id="operation">
        <option value="add">Hozzáadás</option>
        <option value="edit">Szerkesztés</option>
        <option value="delete">Törlés</option>
      </select>
      <div id="add-form" class="form-section"></div>
      <div id="edit-form" class="form-section"></div>
      <div id="delete-form" class="form-section"></div>
      <select name="edit_etel">
        <option value="1">Teszt Étel</option>
      </select>
      <input name="edit_nev" />
      <input name="edit_egyseg_ar" />
      <textarea name="edit_leiras"></textarea>
      <input name="edit_kaloria" />
      <textarea name="edit_osszetevok"></textarea>
      <textarea name="edit_allergenek"></textarea>
      <select name="edit_kategoria_id">
        <option value="1">Kategória 1</option>
      </select>
      <div id="currentKepInfo"></div>
      <input id="editUserId" />
      <input id="currentJogSzint" />
      <span id="currentJogSzintText"></span>
      <input type="radio" name="jog_szint" id="jogSzintFelhasznalo" value="0" />
      <input type="radio" name="jog_szint" id="jogSzintDolgozo" value="2" />
      <form id="editUserForm"></form>
      <input type="number" id="testInput" />
    `;
    jest.clearAllMocks();
  });

  test('setupOperationChangeListener shows correct form based on selection', () => {
    setupOperationChangeListener();
    const operationSelect = document.getElementById('operation');
    operationSelect.value = 'add';
    operationSelect.dispatchEvent(new Event('change'));

    expect(document.getElementById('add-form').style.display).toBe('block');
    expect(document.getElementById('edit-form').style.display).toBe('none');
    expect(document.getElementById('delete-form').style.display).toBe('none');
  });

  test('setupEditEtelListener fetches and populates form correctly', async () => {
    const mockData = {
      nev: 'Teszt Étel',
      egyseg_ar: 1000,
      leiras: 'Leírás',
      kaloria: 500,
      osszetevok: 'Összetevők',
      allergenek: 'Allergének',
      kategoria_id: 1,
      kep_url: 'teszt.jpg'
    };
    fetch.mockResolvedValueOnce({
      json: jest.fn().mockResolvedValueOnce(mockData)
    });

    setupEditEtelListener();
    const editEtelSelect = document.querySelector('select[name="edit_etel"]');
    editEtelSelect.value = '1';
    editEtelSelect.dispatchEvent(new Event('change'));

    await new Promise(resolve => setTimeout(resolve, 0));

    expect(fetch).toHaveBeenCalledWith('admin_felulet.php?action=get_etel&id=1');
    expect(document.querySelector('input[name="edit_nev"]').value).toBe('Teszt Étel');
    expect(document.querySelector('input[name="edit_egyseg_ar"]').value).toBe('1000');
    expect(document.querySelector('textarea[name="edit_leiras"]').value).toBe('Leírás');
    expect(document.querySelector('input[name="edit_kaloria"]').value).toBe('500');
    expect(document.querySelector('textarea[name="edit_osszetevok"]').value).toBe('Összetevők');
    expect(document.querySelector('textarea[name="edit_allergenek"]').value).toBe('Allergének');
    expect(document.querySelector('select[name="edit_kategoria_id"]').value).toBe('1');
    expect(document.getElementById('currentKepInfo')).toHaveTextContent('teszt.jpg');
  });

  test('editUser sets form values and disables correct radio buttons', () => {
    editUser(1, 0);
    expect(document.getElementById('editUserId').value).toBe('1');
    expect(document.getElementById('currentJogSzint').value).toBe('0');
    expect(document.getElementById('currentJogSzintText').textContent).toBe('Felhasználó');
    expect(document.getElementById('jogSzintFelhasznalo').disabled).toBe(true);
    expect(document.getElementById('jogSzintDolgozo').disabled).toBe(false);
    expect(document.getElementById('jogSzintDolgozo').checked).toBe(true);
  });

  test('saveUserChanges alerts when no radio is selected', () => {
    global.alert = jest.fn();
    const result = saveUserChanges();
    expect(alert).toHaveBeenCalledWith('Kérjük, válasszon jogosultsági szintet!');
    expect(result).toBe(false);
  });

  test('saveUserChanges submits form when radio is selected', () => {
    const form = document.getElementById('editUserForm');
    form.submit = jest.fn();
    document.getElementById('jogSzintFelhasznalo').checked = true;
    const result = saveUserChanges();
    expect(form.submit).toHaveBeenCalled();
    expect(result).toBe(true);
  });

  test('blockE prevents e/E key press', () => {
    const event = new KeyboardEvent('keydown', { key: 'e' });
    event.preventDefault = jest.fn();
    blockE(event);
    expect(event.preventDefault).toHaveBeenCalled();
  });

  test('blockE allows other key press', () => {
    const event = new KeyboardEvent('keydown', { key: 'a' });
    event.preventDefault = jest.fn();
    blockE(event);
    expect(event.preventDefault).not.toHaveBeenCalled();
  });
});