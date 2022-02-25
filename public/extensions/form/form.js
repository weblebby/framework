/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*****************************************!*\
  !*** ./resources/js/extensions/form.js ***!
  \*****************************************/
var columnsElem = document.getElementById('form-columns');
var columnTemplate = document.getElementById('form-column-template');
var newColumnButton = document.querySelector('[data-toggle="new-column"]');
var templateElem = document.getElementById('template');
var Form = {
  columns: {
    lastKey: 0,
    add: function add(data) {
      var _this = this;

      var column = columnTemplate.content.cloneNode(true);

      if (data !== null && data !== void 0 && data.id) {
        var idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = "columns[".concat(this.lastKey, "][id]");
        idInput.value = data.id;
        column.appendChild(idInput);
      }

      column.querySelectorAll('[data-form-group]').forEach(function (group) {
        group.dataset.formGroup = group.dataset.formGroup.replace(':key', _this.lastKey);
      });
      column.querySelectorAll('input, select').forEach(function (element) {
        var name = element.name.replace('columns[:key][', '').replace(']', '');
        element.id = element.id.replace(':key', _this.lastKey);
        element.name = element.name.replace(':key', _this.lastKey);

        if (data !== null && data !== void 0 && data[name]) {
          element.type === 'checkbox' ? element.checked = data[name] : element.value = data[name];
        }
      });
      columnsElem.appendChild(column);
      this.lastKey++;
      this.watch();
    },
    remove: function remove(element) {
      element.remove();
      this.watch();
    },
    reset: function reset() {
      this.removeAll();
      this.add();
      this.watch();
    },
    removeAll: function removeAll() {
      columnsElem.innerHTML = '';
      this.lastKey = 0;
    },
    watch: function watch() {
      removeButtonElem = columnsElem.children[0].querySelector('[data-toggle="remove-column"]');

      if (columnsElem.children.length === 1) {
        removeButtonElem.setAttribute('disabled', 'disabled');
        removeButtonElem.classList.add('disabled');
      } else {
        removeButtonElem.removeAttribute('disabled');
        removeButtonElem.classList.remove('disabled');
      }
    }
  }
};
newColumnButton.addEventListener('click', function () {
  Form.columns.add();
});
document.addEventListener('click', function (e) {
  var button = e.target.closest('[data-toggle="remove-column"]');

  if (button) {
    Form.columns.remove(button.parentElement);
  }
});
templateElem === null || templateElem === void 0 ? void 0 : templateElem.addEventListener('change', function (_ref) {
  var target = _ref.target;
  var template = target.querySelector(':checked').dataset.template;

  if (!template) {
    Form.columns.reset();
    return;
  }

  Form.columns.removeAll();
  template = JSON.parse(template);
  template.columns.forEach(function (column) {
    Form.columns.add(column);
  });
});
DefaultFormColumns.forEach(function (column) {
  Form.columns.add(column);
});

if (DefaultFormColumns.length <= 0) {
  Form.columns.add();
}
/******/ })()
;
//# sourceMappingURL=form.js.map