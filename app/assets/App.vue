<template>
    <h1>Welcome to my Vue-Symfony application</h1>
    <div>
        <CreditCardForm></CreditCardForm>
    </div>
</template>

<script>
    import CreditCardForm from './components/Form.vue';
    export default {
        name: 'app',
        components: {
            CreditCardForm
        }
   };
</script>