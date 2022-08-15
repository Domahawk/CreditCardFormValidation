<template>
  <div id="app">
    <form @submit.prevent="validateCardData">
      <div class="inputField">
        <label for="firstName">Name</label>
        <input name="firstName" v-model="firstName" placeholder="firstName">
      </div>
      <div class="inputField">
        <label for="lastName">Surname</label>
        <input name="lastName" v-model="lastName" placeholder="lastName">
      </div>
      <div class="inputField">
        <label for="cardNumber">Card Number</label>
        <input name="cardNumber" v-model="cardNumber" placeholder="cardNumber">
        <p v-if="isInvalidElement('cardNumber')">Invalid</p>
      </div>
      <div class="inputField">
        <label for="cvv">CVV</label>
        <input name="cvv" v-model="cvv" placeholder="cvv">
        <p v-if="isInvalidElement('cvv')">Invalid</p>
      </div>
      <div class="inputField">
        <label for="cardMonth">
          Expiration Date
        </label>
        <select
          id="cardMonth"
          v-model="expireMonth"
        >
          <option value="">Month</option>
          <option
            v-for="n in 12"
            :value="n < 10 ? '0' + n : n"
            :key="n"
          >
            {{ 10 > n ? "0" + n : n }}
          </option>
        </select>
        <select
          class="card-input__input -select"
          id="cardYear"
          v-model="expireYear"
        >
          <option value="">Year</option>
          <option
            v-for="(n, $index) in 12"
            :value="$index + currentYear"
            :key="n"
          >
            {{ $index + currentYear }}
          </option>
        </select>
        <p v-if="isInvalidElement('expireMonth') || isInvalidElement('expireYear')">Invalid</p>
      </div>
      <input type="submit" value="register">
    </form>
  </div>
</template>

<script>
export default {
  name: "App",
  data() {
    return {
      firstName: "",
      lastName: "",
      cardNumber: "",
      cvv: "",
      expireMonth: "",
      expireYear: "",
      currentYear: new Date().getFullYear(),
      invalid: []
    };
  },
  methods: {
    async validateCardData() {
      const { firstName, lastName, cardNumber, cvv, expireMonth, expireYear } = this;
      const res = await fetch(
        "http://localhost:8080/form",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            firstName,
            lastName,
            cardNumber,
            cvv,
            expireMonth,
            expireYear
          })
        }
      );
      const data = await res.json();

      this.invalid = data;
    },

    isInvalidElement(element) {
      return this.invalid.includes(element);
    }
  }
};
</script>

<style>
  .inputField {
    align-items: stretch;
    margin: 10, 10, 10, 10;
  }
</style>