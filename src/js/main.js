
import Vue from 'vue';
import Resource from 'vue-resource';

// Import Components
import inputText from './components/input-text';
import alertError from './components/alert-error';

// Functions
function isNumericStr ( str ) {
  return /^[-+]?[0-9]+$/.test( str );
}

function clone(obj) {
  if (null == obj || "object" != typeof obj) return obj;
  var copy = obj.constructor();
  for (var attr in obj) {
    if (obj.hasOwnProperty(attr)) copy[attr] = obj[attr];
  }
  return copy;
}

function isValidCpf(str) {
  str.replace(/[^0-9]+/g, "");

  var cpf = str;
  var numeros, digitos, soma, i, resultado, digitos_iguais;
  digitos_iguais = 1;
  if (cpf.length < 11)
    return false;
  for (i = 0; i < cpf.length - 1; i++)
    if (cpf.charAt(i) != cpf.charAt(i + 1)){
      digitos_iguais = 0;
      break;
    }
  if (!digitos_iguais){
    numeros = cpf.substring(0,9);
    digitos = cpf.substring(9);
    soma = 0;
    for (i = 10; i > 1; i--)
      soma += numeros.charAt(10 - i) * i;
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0))
      return false;
    numeros = cpf.substring(0,10);
    soma = 0;
    for (i = 11; i > 1; i--)
      soma += numeros.charAt(11 - i) * i;
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1))
      return false;
    return true;
  }
  else
    return false;
}

if (!Array.prototype.find) {
  Array.prototype.find = function(predicate) {
    if (this === null) {
      throw new TypeError('Array.prototype.find called on null or undefined');
    }
    if (typeof predicate !== 'function') {
      throw new TypeError('predicate must be a function');
    }
    var list = Object(this);
    var length = list.length >>> 0;
    var thisArg = arguments[1];
    var value;

    for (var i = 0; i < length; i++) {
      value = list[i];
      if (predicate.call(thisArg, value, i, list)) {
        return value;
      }
    }
    return undefined;
  };
}

function cleanExtraWhiteSpace ( str ) {
  return str.replace(/\s+/g, ' ').trim();
}

function setCaretOnEnd (el) {
  setTimeout(function (){
    var strLength = el.value.length;
    if(el.setSelectionRange !== undefined) {
      el.setSelectionRange(strLength, strLength);
    } else {
      el.value = el.value;
    }
  }, 10);

}

function onlyNumberStr( str ) {
  return str.replace(/[^0-9]+/g, "");
}

function numberToCurrency (n, c, d, t) {
  c = isNaN(c = Math.abs(c)) ? 2 : c,
  d = d == undefined ? "," : d,
  t = t == undefined ? "." : t;
  var s = n < 0 ? "-" : "";
  var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "";
  var j = (j = i.length) > 3 ? j % 3 : 0;
  return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}


Vue.use(Resource);
Vue.http.options.emulateJSON = true;

Vue.filter('trim', {
  read: function(val) {
    return val.trim();
  },
  write: function(val, oldVal) {
    return val.trim();
  }
});

Vue.filter('phone', {
  read: function ( val ) {
    if (val.length == 0) return val;
    else if (val.length < 2) return ['(', val.slice(0)].join('').trim();
    else if (val.length < 3) return ['(', val.slice(0), ') '].join('');
    else if (val.length <= 6) return ['(', val.slice(0, 2), ') ', val.slice(2)].join('').trim();
    else return ['(', val.slice(0, 2), ') ', val.slice(2, (val.length - 4)), ' ', val.slice((val.length - 4))].join('').trim();
  },
  write: function ( val ) {
    if (val.length > 11)
      return onlyNumberStr(val).slice(0, 11);
    else
      return onlyNumberStr(val);
  }
});

Vue.filter('cpf', {
  read: function ( val ) {
    if (val.length < 3) return val;
    else if (val.length < 6) return [val.slice(0, 3), '.', val.slice(3)].join('').trim();
    else if (val.length < 9) return [val.slice(0, 3), '.', val.slice(3, 6), '.', val.slice(6)].join('').trim();
    else return [val.slice(0, 3), '.', val.slice(3, 6), '.', val.slice(6, 9), '-', val.slice(9)].join('').trim();
  },
  write: function ( val ) {
    if (val.length > 11)
      return onlyNumberStr(val).slice(0, 11);
    else
      return onlyNumberStr(val);
  }
});

Vue.filter('creditCardNum', {
  read: function ( val ) {
    if (val.length < 4) return val;
    else if (val.length < 8) return [val.slice(0, 4), '-', val.slice(4)].join('').trim();
    else if (val.length < 12) return [val.slice(0, 4), '-', val.slice(4, 8), '-', val.slice(8)].join('').trim();
    else return [val.slice(0, 4), '-', val.slice(4, 8), '-', val.slice(8, 12), '-', val.slice(12)].join('').trim();
  },
  write: function ( val ) {
    if (val.length > 16)
      return onlyNumberStr(val).slice(0, 16);
    else
      return onlyNumberStr(val);
  }
});

Vue.filter('simpleName', {
  read: function ( val ) {
    return val;
  },
  write: function ( val ) {
    return cleanExtraWhiteSpace(val);
  }
});

Vue.filter('onlyNumber', {
  read: function ( val ) {
    return val;
  },
  write: function ( val ) {
    return onlyNumberStr(val);
  }
});

Vue.filter('real', {
  read: function ( val ) {
    var num = parseFloat(val) / 100;
    return numberToCurrency(num);
  },
  write: function ( val ) {
    var num = val.replace(/[^0-9]+/g, "");
    if( num.length === 4 ){
      if(num.indexOf('000') == 0) return num.slice(3);
      else if (num.indexOf('00') == 0) return num.slice(2);
      else if (num.indexOf('0') == 0) return num.slice(1);
      else  return num;
    } else {
      return num;
    }
  }
});

let vm = new Vue({
  el: 'body',

  components: {
    inputText,
    alertError
  },

  data: {
    // The properties that must to be inside 'sale', 'boleto' and 'rebil' object
    defaultsPaymentProps: {
      order:       ['reference', 'totalAmount'],
      payment:     ['acquirer', 'amount', 'currency', 'country', 'instructions'],
      billing:     ['customerIdentity', 'name', 'address', 'address2', 'city', 'state', 'postalCode', 'country', 'phone', 'email'],
      urlReturn:   'urlReturn',
      customField: 'customField'
    },

    // The properties that are specific in 'sale', 'boleto' and 'rebil' object
    paymentOptions: {
      sale: {
        order:   [],
        payment: ['method', 'numberOfPayments', 'groupNumber', 'flag', 'cardHolder', 'cardNumber', 'cardSecurityCode', 'cardExpirationDate', 'saveCreditCard', 'generateToken', 'departureTax','softDescriptor'],
        billing: [],
        fraud:   'fraud'
      },
      rebil: {
        order:   ['period', 'frequency', 'dateStart', 'dateEnd'],
        payment: ['method', 'numberOfPayments', 'groupNumber', 'flag', 'cardHolder', 'cardNumber', 'cardSecurityCode', 'cardExpirationDate', 'saveCreditCard', 'generateToken', 'departureTax','softDescriptor'],
        billing: [],
        fraud:   'fraud'
      },
      boleto: {
        order:   [],
        payment: ['expire', 'nrDocument'],
        billing: []
      }
    },

    // ======================================================
    // ================ TRANSACTION-REQUEST =================

    // === VERIFICATION ===
    // Stored in computed properties

    // === Payment method ===
    // Order
    order: {
      period:             "3",
      frequency:          "1"
    },

    // Payment
    payment: {
      acquirer:           "13",
      method:             '',
      expire:             "",
      nrDocument:         "",
      amount:             '1000',
      currency:           "986",
      instructions:       "",
      numberOfPayments:   "1",
      groupNumber:        "",
      flag:               "",
      cardHolder:         "",
      cardNumber:         "",
      cardSecurityCode:   "",
      saveCreditCard:     '',
      generateToken:      '',
      departureTax:       '',
      softDescriptor:     ''
    },

    // Billing
    billing: {
      customerIdentity: '',
      address:          '',
      address2:         '',
      city:             '',
      state:            '',
      postalCode:       '',
      country:          '',
      phone:            '',
      email:            ''
    },

    // urlReturn
    urlReturn: "",

    // customField
    customField: '',

    // fraud
    fraud: 'false',

    // ================ TRANSACTION-REQUEST =================
    // ======================================================

    /*
    * This object store the state of the input validation,
    * The options are:
    * ''    -> initial state, no className is attached on input
    * 'yes' -> the input is valid, 'valid' className is attached
    * 'no'  -> the input is invalid, 'invalid' className is attached
    */
    isFieldValid: {
      firstName:    '',
      lastName:     '',
      email:        '',
      cpf:          '',
      phone:        '',
      amount:       'yes',
      cardName:     '',
      cardNumber:   '',
      cardExpMonth: '',
      cardExpYear:  '',
      cardSec:      '',
      flag:         '',
      duration:     ''
    },

    /*
    * Write here the properties stored inside isFieldValid object that must to
    * be validate according to payment method choice
    */
    fieldsValidation: {
      defaults: ['firstName','lastName','email','cpf','phone','amount'],
      sale:     ['cardName','cardNumber','cardExpMonth', 'cardExpYear', 'cardSec','flag'],
      rebil:    ['cardName','cardNumber','cardExpMonth', 'cardExpYear', 'cardSec','flag', 'duration'],
      boleto:   []
    },

    // Helper data
    firstName:       '',
    lastName:        '',
    paymentMensal:   false,
    paymentCicle:    ['3','6','9', '12'],
    paymentDuration: '',
    paymentCard:     'sale',
    cardExpMonths:   [ '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12' ],
    cardExpMonth:    '',
    cardExpYear:     '',
    showNotify:      false,
    loadingPay:      false,
    notifyMsg:       '',
    notifyType:      '',
    currencies: [
      {code: '986', name: 'Real', country: 'BRL', symbol: 'R$'}
      // {code: '840', name: 'Dólar', country: 'USD', symbol: '$'},
      // {code: '978', name: 'Euro', country: 'EUR', symbol: '€'}
    ],
    lastExecution:   null,
    intervalExecution: 30
  },

  computed: {

    cardExpYears () {
      let firstYear   = new Date().getFullYear().toString(),
      yearExtract = parseFloat(firstYear.slice(2)),
      arr         = [];
      for (let i = 0; i < 11; i++) {
        arr.push(yearExtract + i);
      }
      return arr;
    },

    // ======================================================
    // ============= START TRANSACTION-REQUEST ==============

    // Order
    Computed_order () {
      const that = this;
      return {
        reference () {
          return new Date().getTime();
        },

        totalAmount () {
          return that.payment.amount;
        },

        dateStart () {
          let today      = new Date(),
              todayMonth =  today.getMonth() + 1,
              todayMM    = (todayMonth < 10 ? '0' + todayMonth : todayMonth),
              todayDay   = today.getDate(),
              todayDD    = (todayDay < 10 ? '0' + todayDay : todayDay);

          return today.getFullYear() + '-' + todayMM + '-' + todayDD;
        },

        dateEnd () {
          let future      = new Date(),
              futureMonth = future.getMonth();
          future.setMonth( futureMonth + (parseFloat(that.paymentDuration) - 1));
          let endMM       = future.getMonth() + 1,
              futureMM    = (endMM < 10 ? '0' + endMM : endMM),
              futureDay   = future.getDate(),
              futureDD    = (futureDay < 10 ? '0' + futureDay : futureDay);

          return future.getFullYear() + '-' + futureMM + '-' + futureDD;
        }
      };
    },

    // Payment
    Computed_payment () {
      const that = this;
      return {
        country () {
          let obj = that.currencies.find(function ( o ) {
            return o.code == that.payment.currency;
          });

          return obj.country;
        },

        cardExpirationDate () {
          return '20' + that.cardExpYear + that.cardExpMonth;
        }
      };
    },

    // billing
    Computed_billing () {
      const that = this;
      return {
        name () {
          return that.firstName.trim() + ' ' + that.lastName.trim();
        }
      };
    },

    // ============= END TRANSACTION-REQUEST ================
    // ======================================================

    paymentMethod () {
      return (this.paymentMensal ? 'rebil' : this.paymentCard);
    },

    // Support properties
    buttonLabel () {
      return (this.paymentMethod != 'boleto' ? 'Finalizar doação' : 'Gerar boleto');
    }
  },

  methods: {

    /*
    * Returns an object that contains all methods which validade each input,
    * it change the value of its respective this.isFieldValid property
    * @return {Object}
    */
    validations () {
      const that = this;
      return {
        simpleName ( val ) {
          return ( val.length < 1 ? 'no' : 'yes');
        },
        firstName () {
          let val = that.firstName;
          that.isFieldValid.firstName = this.simpleName(val);
        },
        lastName () {
          let val = that.lastName;
          that.isFieldValid.lastName = this.simpleName(val);
        },
        email () {
          let val = that.billing.email;
          let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          that.isFieldValid.email = (re.test(val) ? 'yes' : 'no');
        },
        cpf () {
          let val = that.billing.customerIdentity;
          that.isFieldValid.cpf = (isValidCpf(val) ? 'yes' : 'no');
        },
        phone () {
          let val = that.billing.phone;
          let state;
          if (val.length < 10 || val.length > 11) {
            state = 'no';
          } else state = 'yes';
          that.isFieldValid.phone = state;
        },
        cardName () {
          //setTimeout ( function () {
            let val = that.payment.cardHolder;
            that.isFieldValid.cardName = this.simpleName(val);
          //}, 150);
        },
        cardNumber () {
          let val = that.payment.cardNumber;
          if (val == '' || !isNumericStr(val) || val.length !== 16)
          that.isFieldValid.cardNumber = 'no';
          else
          that.isFieldValid.cardNumber = 'yes';
        },
        cardSec () {
          let val = that.payment.cardSecurityCode,
              state;
          if (val == '' || !isNumericStr(val) || val.length < 3 || val.length > 4)
            state = 'no';
          else
            state = 'yes';
          that.isFieldValid.cardSec = state;
        },
        flag () {
          setTimeout(function () {
            if (that.payment.flag !== '')
              that.isFieldValid.flag = 'yes';
          }, 150 );
        },
        duration () {
          setTimeout(function () {
            if (that.paymentDuration !== '') {
              that.isFieldValid.duration = 'yes';
            }
          }, 150 );
        },
        amount () {
          setTimeout(function () {
            let val = that.payment.amount;
            let state;
            if (val === '' || parseFloat(val) < 1000 ) state = 'no';
            else state = 'yes';
            that.isFieldValid.amount = state;
          }, 150);
        },
        cardExpMonth () {
          let val = that.cardExpMonth;

          if (val === 'MM') {
            that.isFieldValid.cardExpMonth = 'no';
          }

          else if (that.cardExpYear === 'YY') {
            that.isFieldValid.cardExpMonth = (val === 'MM' ? 'no' : 'yes');
          }

          else {
            let theYY = new Date().getFullYear().toString(),
            currentYY = parseFloat(theYY.slice(2)),
            chosedYY = parseFloat(that.cardExpYear);
            let currentMM = new Date().getMonth() + 1,
            chosedMM = parseFloat(val);

            if (chosedYY <= currentYY && chosedMM < currentMM) {
              that.isFieldValid.cardExpMonth = 'no';
            } else {
              that.isFieldValid.cardExpMonth = 'yes';
            }
          }
        },
        cardExpYear () {
          let val = that.cardExpYear;
          that.isFieldValid.cardExpYear = (val === 'YY' ? 'no' : 'yes');
          this.cardExpMonth();
        }
      }
    },

    choseFrequency ( freq ) {
      this.paymentMensal = (freq == 'mensal');
    },

    clean ( ev ) {
      this.payment.amount = '0';
      setCaretOnEnd(ev.target);
    },

    /*
    * Return an array with the properties that are not setted
    * as 'yes' in this.isFieldValid object
    * @return {Array}
    */
    invalidFields () {
      var arr = [];
      for ( let key in this.isFieldValid ) {
        if (this.isFieldValid[key] !== 'yes') {
          arr.push(key);
        }
      }
      return arr;
    },

    /*
    * list that contains all properties according to payment method choice
    * 'rebil', 'sale' or 'boleto'
    * @return {Array}
    */
    listToValidation () {
      let arr = [],
      defProps = this.fieldsValidation.defaults,
      payProps = this.fieldsValidation[this.paymentMethod];
      arr = defProps.concat(payProps);
      return arr;
    },

    /*
    * After clicking on submit all fields are tested to check if they are valid
    */
    runValidationsOnSubmit () {
      let list = this.listToValidation(),
          valis = this.validations();
      for (let i = 0; i < list.length; i++) {
        if (valis.hasOwnProperty(list[i]))
          valis[list[i]]();
      }
    },

    /*
    * Compare invalidFields() list to listToValidation() list
    * @return {Boolean}
    */
    allFieldsAreValids () {
      this.runValidationsOnSubmit();
      const invalidList = this.invalidFields(),
      propsList   = this.listToValidation(),
      len         = invalidList.length;
      if (!len) {
        return true;
      } else {
        for (let i = 0; i < len; i++ ) {
          if (propsList.indexOf(invalidList[i]) !== -1) {
            return false;
          }
        }
        return true;
      }
    },

    /*
    * Merge this.paymentOptions[this.paymentMethod] with this.defaultsPaymentProps
    * and return a new object that contains all properties required inside
    * 'rebil', 'sale' or 'boleto'
    * @return {Object}
    */
    allRequiredProps () {
      let obj = clone(this.paymentOptions[this.paymentMethod]);
      let objDefaults = clone(this.defaultsPaymentProps);

      for ( let key in objDefaults ) {
        let defaultProp = objDefaults[key];
        if (obj.hasOwnProperty(key)) {
          obj[key] = defaultProp.concat(obj[key]);
        } else {
          obj[key] = defaultProp;
        }
      }
      return obj;
    },

    /*
    * Called after clicking on submit button
    */
    confirmPayment () {

      if (this.allFieldsAreValids()) {
        this.loadingPay = true;

        // Initialize the final object
        let finalObject = {
          'transaction-request': {
            /**
            * In the main version (this one), the property 'verification'
            * will be inserted through AZPay pannel.
            * Here it is commented to demostrade how
            * would be added in the front.
            * e.g
            *  'verification': {
            *    'merchantId': this.merchantId,
            *    'merchantKey': this.merchantKey
            *  },
            */

            // Complete the final object with payment method object
            // e.g. rebil { ... }
            [this.paymentMethod]: this.createPaymentMethodObject()
          }
        };

        // the final Object is now ready, it's time to export it
        let now = new Date().getTime();
        if (this.lastExecution === null || (now - this.lastExecution) > this.intervalExecution) {
          this.exportJson(finalObject);
        }

      } else {
        this.notifyMsg = 'Existem campos incorretos';
        this.notifyType = 'error';
        this.showNotify = true;
      }
    },

    createPaymentMethodKey ( key, keyChilds ) {
      let dataReturn;
      if( typeof keyChilds === 'string'){
        dataReturn = this[key];
      } else {
        dataReturn = {};
        for(let i = 0, l = keyChilds.length; i < l; i++) {
          let prop = keyChilds[i],
          val  = (this[key].hasOwnProperty(prop) ? this[key][prop] : this['Computed_' + key][prop]());
          dataReturn[prop] = val;
        }
      }
      return dataReturn;
    },

    createPaymentMethodObject () {
      const methodObj = this.allRequiredProps();
      let obj = {};

      for ( let key in methodObj ) {
        var methodObjValues = methodObj[key];
        obj[key] = this.createPaymentMethodKey(key, methodObjValues);
      }
      return obj;
    },

    resetFiledsValidation ( arr ) {
      let that = this;
      arr.forEach( ( el ) => {
        that.isFieldValid[el] = '';
      });
    },

    cleanCardFields () {
      this.payment.flag = '';
      this.payment.cardHolder = '';
      this.payment.cardNumber = '';
      this.cardExpMonth = 'MM';
      this.cardExpYear = 'YY';
      this.payment.cardSecurityCode = '';
      let arr = [ 'cardName', 'cardNumber', 'cardExpMonth', 'cardExpYear', 'cardSec', 'flag'];
      this.resetFiledsValidation(arr);
    },

    exportJson ( finalObject ) {
      this.lastExecution = new Date().getTime();
      let data = {
        action: 'wpac_response',
        checkout_data: finalObject,
        nonce: wpac_ajax_script.ajax_nonce
      };


      this.$http.post(wpac_ajax_script.ajaxurl, data ).then(function(response) {
        this.loadingPay = false;
        this.notifyMsg  = response.data.title;
        this.notifyType = (response.data.status ? '' : 'error');
        this.showNotify = true;
        this.cleanCardFields();
      }, function (response) {
         console.log('error');
         this.loadingPay = false;
         this.notifyMsg  = 'Erro ao processar o pagamento, tente novamente.';
         this.notifyType = 'error';
         this.showNotify = true;
      });
    }
  }
});
