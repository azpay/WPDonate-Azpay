
<!-- template for the alert error -->
<template id="alert-error-template">
  <div class="modal-mask" v-show="show" transition="modal">
    <div id="modal-wrapper" class="modal-wrapper">
      <div class="modal-container success" :class="{'error': nott == 'error'}">
        <div class="notify-body">
          {{msg}}
        </div>
        <div class="notify-close" @click="closeNotify">&times;</div>
      </div>
    </div>
  </div>
</template>

<template v-if="loadingPay">
  <div class="loading-pay-mask">
    <div class="loading-pay">
      <div class="preloader-wrapper big active">
        <div class="spinner-layer spinner-blue-only">
          <div class="circle-clipper left">
            <div class="circle"></div>
          </div><div class="gap-patch">
            <div class="circle"></div>
          </div><div class="circle-clipper right">
            <div class="circle"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<!-- template for the input type="text" -->
<template id="input-template">
  <div class="form-item">
    <input
      id={{inputName}}
      type={{inputType}}
      v-model="inputModel | simpleName"
      :value="inputModel | simpleName"
      :class="{'valid': isFieldValid == 'yes', 'invalid': isFieldValid == 'no'}"
      @blur="validation($event)">
    <label class="active" for={{inputName}}>{{inputLabel}}</label>
  </div>
</template>

<div class="checkout-bg"></div>

<section class="checkout-wrapper">
  <div class="checkout-header">
    <div class="checkout-container">
      <div class="checkout-title-wrapper">
        <?php if (!empty(WP_AZPayCheckout::$config['titlecheckout'])) : ?>
          <div class="checkout-title"><?php echo WP_AZPayCheckout::$config['titlecheckout']; ?></div>
        <?php endif; ?>
      </div>

      <div class="-row">

        <?php if (isset(WP_AZPayCheckout::$config['rebill'])) : ?>
          <div class="col tn-6">
            <div :class="['checkout-aba', paymentMensal ? 'on' : '']" @click="choseFrequency('mensal')">
              <input name="frequencia" value="mensal" type="radio" id="mensal"  checked :checked="paymentMensal"/>
              <label class="checkout-white" for="mensal">Mensal</label>
            </div>
          </div>
        <?php endif; ?>

        <div class="col tn-6">
          <div :class="['checkout-aba', !paymentMensal ? 'on' : '']" @click="choseFrequency('eventual')">
            <input name="frequencia" value="eventual" type="radio" id="eventual" :checked="!paymentMensal"/>
            <label class="checkout-white" for="eventual">Eventual</label>
          </div>
        </div>
      </div>

    </div>

    <div class="checkout-valor-wrapper">
      <div class="checkout-container">
        <div class="-row">
          <template v-if="paymentMensal" v-for="cicle in paymentCicle">

            <div class="col tn-6 md-3">
              <div class="checkout-mrg-btm">
                <input name="meses" value="{{cicle}}" type="radio" id="meses-{{cicle}}"
                v-model="paymentDuration"
                @click="validations().duration()"/>
                <label class="checkout-white" for="meses-{{cicle}}">{{cicle}} meses</label>
              </div>
            </div>

          </template>

          <div class="col tn-12">
            <div class="form-item checkout-flex">
              <div class="select-currency-wrapper">
                <div class="arrow-options hide"></div>
                <select disabled class="browser-default select-currency" v-model="payment['currency']">
                  <optgroup label="Moeda">
                    <option v-for="currency in currencies" value="{{currency.code}}">{{currency.name}}-{{currency.symbol}}</option>
                  </optgroup>
                </select>
              </div>
              <div class="donation-value-wrapper">
                <input
                  :class="['donation-value', {'valid': isFieldValid.amount == 'yes', 'invalid': isFieldValid.amount == 'no'}]"
                  id="valor"
                  type="text"
                  v-model="payment['amount'] | real"
                  :value="payment['amount'] | real"
                  @focus="clean"
                  @blur="validations().amount()">
                <label class="checkout-white active" for="valor">Quanto você quer doar?</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="checkout-body">
    <div class="checkout-title-wrapper second">
      <div class="checkout-container">
        <div class="checkout-title title-two">Dados de pagamento</div>
      </div>
    </div>

    <div class="checkout-container">
      <form class="checkout-form">
        <div class="-row">
          <div class="col tn-12 md-6">
            <input-text
            input-name="firstName"
            input-type="text"
            input-label="Nome"
            :input-model.sync="firstName"
            :is-field-valid.sync="isFieldValid['firstName']">
          </input-text>
          </div>

          <div class="col tn-12 md-6">
            <div class="form-item">
              <input-text
                input-name="lastName"
                input-type="text"
                input-label="Sobrenome"
                :input-model.sync="lastName"
                :is-field-valid.sync="isFieldValid['lastName']">
              </input-text>
            </div>
          </div>
        </div>

        <div class="-row">
          <div class="col tn-12">
            <div class="form-item">
              <input id="email" type="text"
                v-model="billing['email']"
                :value="billing['email']"
                :class="{'valid': isFieldValid.email == 'yes', 'invalid': isFieldValid.email == 'no'}"
                @blur="validations().email()">
              <label class="active" for=email>Email</label>
            </div>
          </div>
        </div>

        <div class="-row">
          <div class="col tn-12 md-6">
            <div class="form-item">

              <input id="cpf" type="text"
                v-model="billing['customerIdentity'] | cpf"
                :value="billing['customerIdentity'] | cpf"
                :class="{'valid': isFieldValid.cpf == 'yes', 'invalid': isFieldValid.cpf == 'no'}"
                @blur="validations().cpf()">
              <label class="active" for=cpf>CPF</label>
            </div>
          </div>

          <div class="col tn-12 md-6">
            <div class="form-item">

              <input id="phone" type="text"
                v-model="billing['phone'] | phone"
                :value="billing['phone'] | phone"
                :class="{'valid': isFieldValid.phone == 'yes', 'invalid': isFieldValid.phone == 'no'}"
                @blur="validations().phone()">
              <label class="active" for=phone>Telefone</label>

            </div>
          </div>
        </div>

        <div class="-row">

          <template  v-if="paymentMethod != 'boleto'">
            <div class="col tn-12">
              <div class="cardInfoLabel">Dados do cartão</div>
            </div>

            <?php foreach (WP_AZPayCheckout::$config['flags'] as $flag) : ?>
              <?php if ($flag['value'] != 0) : ?>
                <div class="col tn-6 md-6">
                  <div class="checkout-mrg-btm">
                    <input name="bandeira" value="<?php echo $flag['name']; ?>" type="radio" id="<?php echo $flag['name']; ?>" v-model="payment['flag']" @click="validations().flag()"/>
                    <label class="checkout-blue blucs" for="<?php echo $flag['name']; ?>"><?php echo $flag['title']; ?></label>
                  </div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>

            <div class="col tn-12 md-6">

              <input-text
                input-name="cardName"
                input-type="text"
                input-label="Nome do cartão"
                :input-model.sync="payment['cardHolder']"
                :is-field-valid.sync="isFieldValid['cardName']">
              </input-text>

            </div>

            <div class="col tn-12 md-6">
              <div class="form-item">
                <input id="cardNumber" type="text"
                  v-model="payment['cardNumber'] | creditCardNum"
                  :value="payment['cardNumber'] | creditCardNum"
                  :class="{'valid': isFieldValid.cardNumber == 'yes', 'invalid': isFieldValid.cardNumber == 'no'}"
                  @blur="validations().cardNumber()">
                <label class="active" for=cardNumber>Número do cartão</label>
              </div>
            </div>

            <div class="col tn-12 md-6">
              <div class="form-item checkout-flex">
                <div class="inline-select-wrapper">
                  <div class="arrow-options"></div>
                  <select
                    class="browser-default inline-select"
                    :class="{'valid': isFieldValid.cardExpMonth == 'yes', 'invalid': isFieldValid.cardExpMonth == 'no'}"
                    v-model="cardExpMonth"
                    @change="validations().cardExpMonth()">
                    <option selected disabled>MM</option>
                    <option v-for="month in cardExpMonths" value="{{month}}">{{month}}</option>
                  </select>
                </div>
                <div class="slash-date">/</div>
                <div class="inline-select-wrapper">
                  <div class="arrow-options"></div>
                  <select
                    class="browser-default inline-select"
                    :class="{'valid': isFieldValid.cardExpYear == 'yes', 'invalid': isFieldValid.cardExpYear == 'no'}"
                    v-model="cardExpYear"
                    @change="validations().cardExpYear()">
                    <option selected disabled>YY</option>
                    <option v-for="year in cardExpYears" value="{{year}}">{{year}}</option>
                  </select>
                  <label class="active">Data de validade</label>
                </div>
              </div>
            </div>

            <div class="col tn-12 md-6">

              <div class="form-item">
                <input id="cardSec" type="text"
                  v-model="payment['cardSecurityCode'] | onlyNumber"
                  :value="payment['cardSecurityCode'] | onlyNumber"
                  :class="{'valid': isFieldValid.cardSec == 'yes', 'invalid': isFieldValid.cardSec == 'no'}"
                  @blur="validations().cardSec()">
                <label class="active" for=cardSec>Código de segurança</label>
              </div>
            </div>
          </template>

        </div>
      </form>
    </div>
  </div>

  <div class="checkout-footer">
    <a class="checkout-btn" @click="confirmPayment">
      <?php
        if ( empty(WP_AZPayCheckout::$config['titlebtn'])) {
          echo 'Finalizar Pagamento';
        } else {
          echo WP_AZPayCheckout::$config['titlebtn'];
        }
      ?>
    </a>
  </div>

  <alert-error
  :show.sync="showNotify"
  :msg.sync="notifyMsg"
  :nott="notifyType">
  </alert-error>
</section>
