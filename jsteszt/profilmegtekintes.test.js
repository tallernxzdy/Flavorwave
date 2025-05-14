const { JSDOM } = require('jsdom');
const fs = require('fs');
const path = require('path');

// Load the HTML file
const html = fs.readFileSync(path.resolve(__dirname, '../profilom.php'), 'utf8');

describe('Profilom JavaScript Functionality', () => {
  let dom;
  let window;
  let document;
  let $;
  let bootstrap;

  beforeEach(() => {
    // Create a DOM environment
    dom = new JSDOM(html, {
      runScripts: 'dangerously',
      resources: 'usable'
    });
    window = dom.window;
    document = window.document;
    $ = require('jquery')(window);
   
    // Mock global objects
    global.document = document;
    global.window = window;
   
    // Mock Bootstrap
    bootstrap = {
      Modal: jest.fn(function() {
        return {
          show: jest.fn(),
          hide: jest.fn()
        };
      })
    };
    global.bootstrap = bootstrap;
   
    // Set up test user data
    document.body.innerHTML += `
      <script>
        window.originalData = {
          teljes_nev: 'Test User',
          felhasznalo_nev: 'testuser',
          email_cim: 'test@example.com',
          tel_szam: '+36201234567',
          lakcim: 'Test Address'
        };
      </script>
    `;
   
    // Add form elements for testing
    document.body.innerHTML += `
      <form id="profile-form">
        <div class="info-item" id="teljes_nev_item">
          <span class="info-value" id="teljes_nev_value">Test User</span>
          <input type="text" class="form-control edit-input" name="teljes_nev" value="Test User" style="display: none;">
          <i class="fas fa-pencil-alt edit-icon"></i>
        </div>
        <div class="info-item" id="email_cim_item">
          <span class="info-value" id="email_cim_value">test@example.com</span>
          <input type="email" class="form-control edit-input" name="email_cim" value="test@example.com" style="display: none;">
          <i class="fas fa-pencil-alt edit-icon"></i>
        </div>
      </form>
      <div id="confirmModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-footer">
              <button class="btn btn-secondary" data-bs-dismiss="modal"></button>
              <button class="btn btn-primary" onclick="submitForm()"></button>
            </div>
          </div>
        </div>
      </div>
    `;
  });

  afterEach(() => {
    jest.clearAllMocks();
  });

  describe('toggleEdit()', () => {
    it('should toggle between display and input field', () => {
      // Get the function from the script
      const toggleEdit = new Function(`
        ${document.querySelector('script').textContent}
        return toggleEdit;
      `)();
     
      // Initial state - span visible, input hidden
      const valueSpan = document.getElementById('teljes_nev_value');
      const inputField = document.querySelector('input[name="teljes_nev"]');
     
      expect(valueSpan.style.display).toBe('inline');
      expect(inputField.style.display).toBe('none');
     
      // First call - show input, hide span
      toggleEdit('teljes_nev');
     
      expect(valueSpan.style.display).toBe('none');
      expect(inputField.style.display).toBe('block');
     
      // Second call - show span, hide input
      toggleEdit('teljes_nev');
     
      expect(valueSpan.style.display).toBe('inline');
      expect(inputField.style.display).toBe('none');
    });
  });

  describe('hideInput()', () => {
    it('should hide input field and update span value', () => {
      const hideInput = new Function(`
        ${document.querySelector('script').textContent}
        return hideInput;
      `)();
     
      // Set up test state with input visible
      const valueSpan = document.getElementById('teljes_nev_value');
      const inputField = document.querySelector('input[name="teljes_nev"]');
      inputField.style.display = 'block';
      valueSpan.style.display = 'none';
      inputField.value = 'Updated Name';
     
      hideInput('teljes_nev');
     
      expect(inputField.style.display).toBe('none');
      expect(valueSpan.style.display).toBe('inline');
      expect(valueSpan.textContent).toBe('Updated Name');
    });

    it('should show "Nincs megadva" when input is empty', () => {
      const hideInput = new Function(`
        ${document.querySelector('script').textContent}
        return hideInput;
      `)();
     
      const valueSpan = document.getElementById('teljes_nev_value');
      const inputField = document.querySelector('input[name="teljes_nev"]');
      inputField.value = '';
     
      hideInput('teljes_nev');
     
      expect(valueSpan.textContent).toBe('Nincs megadva');
    });
  });

  describe('validateAndShowModal()', () => {
    it('should show modal when validation passes', () => {
      const validateAndShowModal = new Function(`
        ${document.querySelector('script').textContent}
        return validateAndShowModal;
      `)();
     
      // Set valid email and phone
      document.querySelector('input[name="email_cim"]').value = 'valid@example.com';
     
      validateAndShowModal();
     
      expect(bootstrap.Modal).toHaveBeenCalled();
      expect(bootstrap.Modal.mock.results[0].value.show).toHaveBeenCalled();
    });

    it('should show alert for invalid email', () => {
      const validateAndShowModal = new Function(`
        ${document.querySelector('script').textContent}
        return validateAndShowModal;
      `)();
     
      const showAlert = jest.fn();
      global.showAlert = showAlert;
     
      // Set invalid email
      document.querySelector('input[name="email_cim"]').value = 'invalid-email';
     
      validateAndShowModal();
     
      expect(showAlert).toHaveBeenCalledWith('danger', 'Helytelen email cím formátum!');
      expect(bootstrap.Modal).not.toHaveBeenCalled();
    });

    it('should show alert for invalid phone number', () => {
      const validateAndShowModal = new Function(`
        ${document.querySelector('script').textContent}
        return validateAndShowModal;
      `)();
     
      const showAlert = jest.fn();
      global.showAlert = showAlert;
     
      // Set invalid phone
      document.querySelector('input[name="tel_szam"]').value = '123';
     
      validateAndShowModal();
     
      expect(showAlert).toHaveBeenCalledWith('danger', 'Helytelen telefonszám formátum! (Pl. +36201234567)');
      expect(bootstrap.Modal).not.toHaveBeenCalled();
    });
  });

  describe('submitForm()', () => {
    it('should enable form submission', () => {
      const submitForm = new Function(`
        ${document.querySelector('script').textContent}
        return submitForm;
      `)();
     
      const form = document.getElementById('profile-form');
      form.onsubmit = function() { return false; };
     
      submitForm();
     
      expect(form.onsubmit()).toBe(true);
    });
  });

  describe('resetForm()', () => {
    it('should reset form fields to original values', () => {
      const resetForm = new Function(`
        ${document.querySelector('script').textContent}
        return resetForm;
      `)();
     
      // Change some values
      document.querySelector('input[name="teljes_nev"]').value = 'Changed Name';
      document.getElementById('teljes_nev_value').textContent = 'Changed Name';
     
      resetForm();
     
      expect(document.querySelector('input[name="teljes_nev"]').value).toBe('Test User');
      expect(document.getElementById('teljes_nev_value').textContent).toBe('Test User');
    });

    it('should hide all input fields', () => {
      const resetForm = new Function(`
        ${document.querySelector('script').textContent}
        return resetForm;
      `)();
     
      // Show an input field
      document.querySelector('input[name="teljes_nev"]').style.display = 'block';
     
      resetForm();
     
      expect(document.querySelector('input[name="teljes_nev"]').style.display).toBe('none');
    });
  });

  describe('showAlert()', () => {
    it('should create and display an alert', () => {
      const showAlert = new Function(`
        ${document.querySelector('script').textContent}
        return showAlert;
      `)();
     
      const profileContent = document.querySelector('.profile-content');
     
      showAlert('success', 'Test message');
     
      const alert = document.querySelector('.alert');
      expect(alert).not.toBeNull();
      expect(alert.className).toContain('alert-success');
      expect(alert.textContent).toBe('Test message');
     
      // Check that it's prepended to profile content
      expect(profileContent.firstChild).toBe(alert);
    });

    it('should remove alert after timeout', () => {
      jest.useFakeTimers();
     
      const showAlert = new Function(`
        ${document.querySelector('script').textContent}
        return showAlert;
      `)();
     
      showAlert('danger', 'Temporary message');
     
      const alert = document.querySelector('.alert');
      expect(alert).not.toBeNull();
     
      // Advance timers by 3 seconds
      jest.advanceTimersByTime(3000);
     
      expect(document.querySelector('.alert')).toBeNull();
     
      jest.useRealTimers();
    });
  });
});