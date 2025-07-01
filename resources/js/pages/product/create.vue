<template>
    <div class="container">
        <h3 class="text-center mt-4">Create Product</h3>
        <hr>
        <div class="mx-5 mt-2">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input v-model="form.name" type="text" name="name" class="form-control"
                            placeholder="Enter Product Name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Selling Price</label>
                        <input v-model="form.price" v-numbers-only type="text" name="price" class="form-control"
                            placeholder="Enter Product Price">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Purchase Price</label>
                        <input v-model="form.purchase_price" v-numbers-only type="text" name="purchase_price" class="form-control"
                            placeholder="Enter Product Purchase Price">
                    </div>
                    <div class="input-group mb-3">
                        <label for="inputGroupSelect04" class="input-group mb-2">Images</label>
                        <input @change="handleFileUpload" name="image" type="file" class="form-control"
                            accept="image/*">
                        <button class="btn btn-outline-secondary" type="button"
                            id="inputGroupFileAddon04">Button</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label">Description</label>
                    <textarea v-model="form.description" class="form-control" id="exampleFormControlTextarea1"
                        rows="3"></textarea>
                </div>
            </div>
            <button @click="saveData" type="button" class="btn btn-success">Success</button>
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
                name: '',
                price: '',
                purchase_price:'',
                category_id: '',
                description: '',
                image: '',
            },
            isEdit: false,
            productId: null,

        };
    },
    methods: {
        handleFileUpload(event) {
            this.form.image = event.target.files[0];
            console.log('upload', this.form.image);

        },
        async saveData() {
            try {
                const formData = new FormData();
                formData.append('name', this.form.name);
                formData.append('price', this.form.price);
                formData.append('purchase_price', this.form.purchase_price);
                formData.append('description', this.form.description);
                formData.append('image', this.form.image);
                
                if (this.isEdit == true) {
                    const response = await axios.post(`/api/product/update/${this.productId}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    });
                    if (response.status === 200) {
                        localStorage.setItem('successMessage', response.data.message);
                        this.$router.push({
                            name: 'product.index',
                        });
                    }
                } else {
                    const response = await axios.post('/api/product/store', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                    });
                    if (response.status === 201) {
                        localStorage.setItem('successMessage', response.data.message);
                        this.$router.push({
                            name: 'product.index',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong!',
                        });
                    }
                }
            } catch (error) {
                console.error(error.response?.data || error.message);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.response?.data?.message || 'Something went wrong!',
                });
            }
        },
        async fetchProductData() {
            if (this.productId) {
                try {
                    const response = await axios.get(`/api/product/edit/${this.productId}`);
                    this.form = response.data;
                    this.isEdit = true;
                } catch (error) {
                    console.error('Error fetching product:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch product details.',
                    });
                }
            }
        }
    },
    created() {
        if (this.$route.params.id) {
            this.isEdit = true;
            this.productId = this.$route.params.id;
            this.fetchProductData();
        }
    }
};
</script>

<style></style>