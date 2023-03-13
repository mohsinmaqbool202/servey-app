import { defineRule } from 'vee-validate';
import { required, email, min, max } from '@vee-validate/rules';

defineRule('required', (value) => {
   if(value == '') {
      return 'Password is required';
   }
})

defineRule('email', (value) => {
   if(email(value) && required(value)) {
      return true
   }

   return 'A valid email address is required';
})

defineRule('password', (value) => {
   if(
      required(value) &&
      min(value, {length: 6}) &&
      max(value, {length: 20})
   ) {
      return true;
   }

   return 'Password must be between 6 and 30 characters';
})

defineRule('confirmed', (value, [target], ctx) => {
   if (value === ctx.form[target]) {
      return true;
    }

   return 'Passwords must match';
})

defineRule('name', (value) => {
   if(value == '') {
      return 'Name is required';
   }
   if(
      max(value, {length: 60})
   ) {
      return true
   }
   return 'Name may not exceed 60 characters';
})