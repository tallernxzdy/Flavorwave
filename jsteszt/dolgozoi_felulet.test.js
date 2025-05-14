// Setup JSDOM environment for testing
const { JSDOM } = require('jsdom');
const fs = require('fs');
const path = require('path');

// Load the HTML file
const html = fs.readFileSync(path.resolve(__dirname, '../dolgozoi_felulet.php'), 'utf8');

// Mock jQuery and global environment
let dom;
let window;
let document;
let $;

beforeEach(() => {
  // Create a DOM environment
  dom = new JSDOM(html, {
    runScripts: 'dangerously',
    resources: 'usable'
  });
  window = dom.window;
  document = window.document;
  $ = require('jquery')(window);
 
  // Mock global jQuery
  global.$ = $;
  global.document = document;
 
  // Mock AJAX functions
  $.post = jest.fn((url, data, callback) => {
    if (data.ajax === 'get_order_details') {
      callback(JSON.stringify({
        html: '<div class="table-responsive"><table>...</table></div>'
      }));
    } else if (data.ajax === 'refresh_dropdowns') {
      callback(JSON.stringify({
        pending: [{id: 1, felhasznalo_nev: 'User1'}],
        in_progress: [{id: 2, felhasznalo_nev: 'User2'}],
        completed: [{id: 3, felhasznalo_nev: 'User3'}]
      }));
    } else if (data.update_status) {
      callback();
    } else if (data.finished_order) {
      callback();
    }
  });
 
  // Mock Bootstrap modal
  $.fn.modal = jest.fn();
});

afterEach(() => {
  jest.clearAllMocks();
});

describe('Dolgozoi Felulet JavaScript', () => {
  describe('Initialization', () => {
    it('should have all required elements', () => {
      expect($('#pending_orders').length).toBe(1);
      expect($('#in_progress_orders').length).toBe(1);
      expect($('#completed_orders').length).toBe(1);
      expect($('#order-details').length).toBe(1);
      expect($('#message').length).toBe(1);
      expect($('#confirmationModal').length).toBe(1);
    });
  });

  describe('Dropdown change handlers', () => {
    it('should update order details when pending order is selected', () => {
      const dropdown = $('#pending_orders');
      dropdown.val('1');
      dropdown.trigger('change');
     
      expect($.post).toHaveBeenCalledWith(
        "dolgozoi_felulet.php",
        {
          ajax: 'get_order_details',
          order_id: '1'
        },
        expect.any(Function)
      );
     
      // Other dropdowns should be reset
      expect($('#in_progress_orders').val()).toBe('');
      expect($('#completed_orders').val()).toBe('');
    });

    it('should update order details when in-progress order is selected', () => {
      const dropdown = $('#in_progress_orders');
      dropdown.val('2');
      dropdown.trigger('change');
     
      expect($.post).toHaveBeenCalledWith(
        "dolgozoi_felulet.php",
        {
          ajax: 'get_order_details',
          order_id: '2'
        },
        expect.any(Function)
      );
     
      // Other dropdowns should be reset
      expect($('#pending_orders').val()).toBe('');
      expect($('#completed_orders').val()).toBe('');
    });

    it('should update order details when completed order is selected', () => {
      const dropdown = $('#completed_orders');
      dropdown.val('3');
      dropdown.trigger('change');
     
      expect($.post).toHaveBeenCalledWith(
        "dolgozoi_felulet.php",
        {
          ajax: 'get_order_details',
          order_id: '3'
        },
        expect.any(Function)
      );
     
      // Other dropdowns should be reset
      expect($('#pending_orders').val()).toBe('');
      expect($('#in_progress_orders').val()).toBe('');
    });
  });

  describe('Order status update functions', () => {
    beforeEach(() => {
      // Mock the confirmation modal callback
      global.openConfirmationModal = (action) => {
        action();
      };
    });

    it('should move order to preparation when moveToPreparation is called', () => {
      $('#pending_orders').val('1');
      const moveToPreparation = require('../public/js/dolgozoi_felulet.js').moveToPreparation;
      moveToPreparation();
     
      expect($.post).toHaveBeenCalledWith(
        "dolgozoi_felulet.php",
        {
          update_status: true,
          order_id: '1',
          new_status: 1
        },
        expect.any(Function)
      );
     
      // Should also refresh dropdowns
      expect($.post).toHaveBeenCalledWith(
        "dolgozoi_felulet.php",
        {
          ajax: 'refresh_dropdowns'
        },
        expect.any(Function)
      );
    });

    it('should complete order when completeOrder is called', () => {
      $('#in_progress_orders').val('2');
      const completeOrder = require('../public/js/dolgozoi_felulet.js').completeOrder;
      completeOrder();
     
      expect($.post).toHaveBeenCalledWith(
        "dolgozoi_felulet.php",
        {
          update_status: true,
          order_id: '2',
          new_status: 2
        },
        expect.any(Function)
      );
    });

    it('should finish order when finishOrder is called', () => {
      $('#completed_orders').val('3');
      const finishOrder = require('../public/js/dolgozoi_felulet.js').finishOrder;
      finishOrder();
     
      expect($.post).toHaveBeenCalledWith(
        "dolgozoi_felulet.php",
        {
          finished_order: true,
          order_id: '3'
        },
        expect.any(Function)
      );
     
      // Should clear the selection
      expect($('#completed_orders').val()).toBe('');
    });

    it('should show error when no order is selected', () => {
      $('#pending_orders').val('');
      const moveToPreparation = require('../public/js/dolgozoi_felulet.js').moveToPreparation;
      const showMessage = jest.fn();
      global.showMessage = showMessage;
     
      moveToPreparation();
     
      expect(showMessage).toHaveBeenCalledWith(
        "Válassz ki egy rendelést a készítésre áthelyezéshez!",
        "error"
      );
    });
  });

  describe('Message handling', () => {
    it('should show success message', () => {
      const showMessage = require('../public/js/dolgozoi_felulet.js').showMessage;
      showMessage("Test success", "success");
     
      expect($('#message-text').text()).toBe("Test success");
      expect($('#message').hasClass('alert-success')).toBe(true);
      expect($('#message').css('display')).not.toBe('none');
    });

    it('should show error message', () => {
      const showMessage = require('../public/js/dolgozoi_felulet.js').showMessage;
      showMessage("Test error", "error");
     
      expect($('#message-text').text()).toBe("Test error");
      expect($('#message').hasClass('alert-danger')).toBe(true);
      expect($('#message').css('display')).not.toBe('none');
    });
  });

  describe('Confirmation modal', () => {
    it('should open confirmation modal and execute action', () => {
      const openConfirmationModal = require('../public/js/dolgozoi_felulet.js').openConfirmationModal;
      const mockAction = jest.fn();
     
      openConfirmationModal(mockAction);
     
      // Simulate confirm button click
      $('#confirmAction').trigger('click');
     
      expect(mockAction).toHaveBeenCalled();
      expect($.fn.modal).toHaveBeenCalledWith('hide');
    });
  });

  describe('Button event handlers', () => {
    it('should call moveToPreparation when preparation button is clicked', () => {
      const moveToPreparation = jest.fn();
      global.moveToPreparation = moveToPreparation;
     
      $("button[name='update_status']").first().trigger('click');
     
      expect(moveToPreparation).toHaveBeenCalled();
    });

    it('should call completeOrder when complete button is clicked', () => {
      const completeOrder = jest.fn();
      global.completeOrder = completeOrder;
     
      $("button[name='update_status']").last().trigger('click');
     
      expect(completeOrder).toHaveBeenCalled();
    });

    it('should call finishOrder when finish button is clicked', () => {
      const finishOrder = jest.fn();
      global.finishOrder = finishOrder;
     
      $("button[name='finished_order']").trigger('click');
     
      expect(finishOrder).toHaveBeenCalled();
    });
  });
});