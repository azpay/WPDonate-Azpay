// register input component
module.exports = {
  name: 'input-text',
  template: '#input-template',
  props: [
    'inputName',
    'inputLabel',
    'inputModel',
    'inputType',
    'isFieldValid'
  ],
  methods: {
    // Validation Method
    validation ( ev ) {
      var val = ev.target.value;
      this.isFieldValid = this.$parent.validations().simpleName( val );
    }
  }
};
