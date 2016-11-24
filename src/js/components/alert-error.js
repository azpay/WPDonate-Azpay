// register modal component
module.exports = {
  name: 'alert-error',
  template: '#alert-error-template',
  props: [
    'show',
    'nott',
    'msg'
  ],
  methods: {
    closeNotify () {
      this.show = false;
    }
  }
};
