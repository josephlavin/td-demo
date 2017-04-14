// Register component
// Normally this would be in a separate .vue file
Vue.component('price-select', {
    template: `
<div>
<div v-show="!message && !processing">
    <div class="form-item">
        <legend>
            <span class="fieldset-legend">Donation Amount</span>
        </legend>
        <div class="fieldset-wrapper">
            <input type="radio" name="price-select" value="1000" v-model="choice">&nbsp;$10.00</input><br/>
            <input type="radio" name="price-select" value="1500" v-model="choice">&nbsp;$15.00</input><br/>
            <input type="radio" name="price-select" value="2000" v-model="choice">&nbsp;$20.00</input><br/>
            <input type="radio" name="price-select" value="other" v-model="choice">&nbsp;Other&nbsp;</input>
            <input type="number" min="1.00" v-model="manual" :disabled="!manualInput">
        </div>
    </div>
    <br><br>
    <button v-on:click.stop.prevent="donate">Donate {{displayPrice}}</button>
</div>
<div v-show="processing">
    <p>Processing Payment...</p>
</div>
<div v-show="message">
    <h3>{{message}}</h3>
    <button v-on:click.stop.prevent="message=false">Make Another Donation</button>
</div>
</div>`,
    mounted: function () {
        var _this = this;
        this.handler = StripeCheckout.configure({
            key: 'pk_test_A3RYGGcwZ6OWq5ff1i3lgelQ',
            image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
            locale: 'auto',
            token: function (token) {
                // You can access the token ID with `token.id`.
                // Get the token ID to your server-side code for use.
                _this.submitDonation(token);
            }
        });
    },
    computed: {
        price: function () {
            if (this.manualInput) {
                return parseInt(((this.manual || 0) * 100));
            }

            return parseInt(this.choice || 0);
        },
        displayPrice: function () {
            if (!this.canDonate) {
                return '';
            }

            return '$' + (this.price / 100).toFixed(2);
        },
        manualInput() {
            return this.choice === 'other';
        },
        canDonate() {
            return this.price !== 0;
        }
    },
    data: function () {
        return {
            choice: '',
            manual: '',
            handler: null,
            stripeToken: null,
            message: false,
            processing: false
        };
    },
    methods: {
        donate: function () {
            if (!this.canDonate) {
                window.alert('Please Select or Enter a Valid Amount to Donate');
                return;
            }

            this.handler.open({
                amount: this.price
            });
        },
        submitDonation: function (token) {
            this.processing = true;
            this.$http.post('/api/donate.json', {
                token: token,
                amount: this.price
            }).then(
                function (response) {
                    this.processing = false;
                    this.message = 'Thank you for your donation.  ID # ' + response.data.donation_id;
                },
                function (response) {
                    this.processing = false;
                    this.message = 'An Error Has Occurred: ' + response.data.msg
                }
            )
        }
    }
});

// Register the root component
new Vue({
    'el': '#donation-form'
});