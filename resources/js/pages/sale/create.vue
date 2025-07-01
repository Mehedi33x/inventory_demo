<template>
    <div class="container">
        <h3 class="text-center mt-4">{{ isEdit ? 'Edit' : 'Create' }} Sale</h3>
        <hr />
        <div class="mx-5 mt-2">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="product">Select Product</label>
                    <select v-model="form.product_id" id="product" class="form-select" required>
                        <option disabled value="">Select a product</option>
                        <option v-for="product in allProducts" :key="product.id" :value="product.id">
                            {{ product.name }}
                        </option>
                    </select>
                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input v-model="form.price" type="text" class="form-control" readonly />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">VAT</label>
                        <input v-model.number="form.vat" v-numbers-only type="text" class="form-control"
                            placeholder="Enter VAT" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Paid</label>
                        <input v-model="form.paid" v-numbers-only type="text" name="paid" class="form-control"
                            placeholder="Enter Paid Amount">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div>
                        <label class="form-label">Invoice</label>
                        <input v-model="form.invoice_number" type="text" class="form-control" readonly />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity</label>
                        <input v-model.number="form.quantity" v-numbers-only type="text" class="form-control"
                            placeholder="Enter Quantity" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Discount</label>
                        <input v-model.number="form.discount" v-numbers-only type="text" class="form-control"
                            placeholder="Enter Discount" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total</label>
                        <input :value="calculatedTotal" type="text" class="form-control" readonly />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due</label>
                        <input v-model="form.due" v-numbers-only type="text" name="due" class="form-control"
                            placeholder="Enter due amount" disabled>
                    </div>
                </div>
            </div>
            <button @click="saveData" type="button" class="btn btn-success" :disabled="loading">
                {{ loading ? 'Saving...' : isEdit ? 'Update Sale' : 'Create Sale' }}
            </button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            form: {
                quantity: 1,
                price: 0,
                vat: 0,
                discount: 0,
                product_id: '',
                total_price: 0,
                paid: 0,
                due: 0,
            },
            isEdit: false,
            allProducts: [],
            loading: false,
            saleId: null,
        };
    },
    computed: {
        calculatedTotal() {
            const qty = this.form.quantity || 0;
            const price = this.form.price || 0;
            const discount = this.form.discount || 0;
            const vat = this.form.vat || 0;

            const baseTotal = qty * price;
            const afterDiscount = baseTotal - discount;
            const vatAmount = (afterDiscount * vat) / 100;
            const finalTotal = afterDiscount + vatAmount;

            return finalTotal.toFixed(2);
        }
    },
    watch: {
        'form.product_id'(newProductId) {
            const selectedProduct = this.allProducts.find(p => p.id === newProductId);
            if (selectedProduct) {
                this.form.price = parseFloat(selectedProduct.price);
            } else {
                this.form.price = 0;
            }
        },
        'form.paid'(newVal) {
            const paid = parseFloat(newVal) || 0;
            const total = this.form.total_price || 0;
            const due = total - paid;
            this.form.due = due >= 0 ? due.toFixed(2) : 0;
        },
        calculatedTotal(newVal) {
            this.form.total_price = parseFloat(newVal);
            this.form.due = (this.form.total_price - (this.form.paid || 0)).toFixed(2);
        },
    },
    methods: {
        async saveData() {
            if (!this.form.product_id) {
                return Swal.fire('Error', 'Please select a product.', 'error');
            }
            if (this.form.quantity < 0) {
                return Swal.fire('Error', 'Quantity cannot be less than zero.', 'error');
            }
            this.loading = true;
            try {
                let response;
                if (this.isEdit && this.saleId) {
                    response = await axios.post(`/api/sale/update/${this.saleId}`, this.form);
                } else {
                    response = await axios.post('/api/sale/store', this.form);
                }
                if (response.status === 200 || response.status === 201) {
                    Swal.fire('Success', response.data.message || 'sale saved successfully.', 'success');
                    this.$router.push({ name: 'sale.index' });
                }
            } catch (error) {
                Swal.fire('Error', error.response?.data?.message || 'Something went wrong!', 'error');
            } finally {
                this.loading = false;
            }
        },
        async fetchSaleData() {
            try {
                const response = await axios.get(`/api/sale/edit/${this.saleId}`);
                this.form = {
                    quantity: response.data.quantity,
                    product_id: response.data.product_id,
                    price: response.data.price,
                    vat: response.data.vat,
                    discount: response.data.discount,
                    paid: response.data.paid,
                    total_price: response.data.total_price,
                };
                this.isEdit = true;
            } catch (error) {
                Swal.fire('Error', 'Failed to fetch sale details.', 'error');
            }
        },
        async getAllProducts() {
            try {
                const response = await axios.get('/api/get-products');
                this.allProducts = response.data;
            } catch (error) {
                console.error('Error fetching products:', error);
            }
        },
        async getInvoice() {
            try {
                const response = await axios.get('/api/generate-sale-invoice');
                this.form.invoice_number = response.data.invoice_number;
            } catch (error) {
                console.error('Error fetching generate invoice:', error);
            }
        },
    },
    created() {
        this.getAllProducts();
        this.getInvoice();
        if (this.$route.params.id) {
            this.saleId = this.$route.params.id;
            this.isEdit = true;
            this.fetchSaleData();
        }
    },
};
</script>
