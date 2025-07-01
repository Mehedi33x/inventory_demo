<template>
    <div class="container">
        <h3 class="text-center mt-4">{{ isEdit ? 'Edit' : 'Create' }} Stock</h3>
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
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="quantity">Quantity</label>
                    <input v-model="form.quantity" v-numbers-only type="text" id="quantity" class="form-control"
                        placeholder="Enter stock" />
                </div>
            </div>
            <button @click="saveData" type="button" class="btn btn-success" :disabled="loading">
                {{ loading ? 'Saving...' : isEdit ? 'Update Stock' : 'Create Stock' }}
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
                quantity: 0,
                product_id: '',
            },
            isEdit: false,
            allProducts: [],
            loading: false,
            stockId: null,
        };
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
                if (this.isEdit && this.stockId) {
                    response = await axios.post(`/api/stock/update/${this.stockId}`, this.form);
                } else {
                    response = await axios.post('/api/stock/store', this.form);
                }
                if (response.status === 200 || response.status === 201) {
                    Swal.fire('Success', response.data.message || 'Stock saved successfully.', 'success');
                    this.$router.push({ name: 'stock.index' });
                }
            } catch (error) {
                Swal.fire('Error', error.response?.data?.message || 'Something went wrong!', 'error');
            } finally {
                this.loading = false;
            }
        },
        async fetchStockData() {
            try {
                const response = await axios.get(`/api/stock/edit/${this.stockId}`);
                this.form = { quantity: response.data.quantity, product_id: response.data.product_id, };
                this.isEdit = true;
            } catch (error) {
                Swal.fire('Error', 'Failed to fetch stock details.', 'error');
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
    },
    created() {
        this.getAllProducts();
        if (this.$route.params.id) {
            this.stockId = this.$route.params.id;
            this.isEdit = true;
            this.fetchStockData();
        }
    },
};
</script>
