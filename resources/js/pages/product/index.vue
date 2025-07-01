<template>
    <div class="container">
        <h3 class="text-center mt-4">Product List</h3>
        <button @click="createProduct" class="btn btn-success text-end">Create</button>
        <div class="container mt-5">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Image</th>
                        <th scope="col">Prouct Name</th>
                        <th scope="col">Purchase Price</th>
                        <th scope="col">Selling Price</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody v-if="products.length > 0">
                    <tr v-for="(item, index) in products" :key="index">
                        <th scope="row">{{ index + 1 }}</th>
                        <td><img v-if="item.image_url" :src="item.image_url" alt="Product Image"
                                style="height: 40px;width: 40px;" /></td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.purchase_price }}</td>
                        <td>{{ item.price }}</td>
                        <td>
                            <button @click="showProduct(item.id)" class="btn btn-success btn-sm">View</button>
                            <button @click="editProduct(item.id)" class="btn btn-primary btn-sm mx-1">Edit</button>
                            <button :disabled="loading" @click="deleteProduct(item.id)"
                                class="btn btn-danger btn-sm mx-0">Delete</button>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td :colspan="5" class="no-data">No data found</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Bootstrap Modal -->
        <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel">Product Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="selectedProduct">                            <p><strong>Image:</strong></p>
                             <img v-if="selectedProduct.image_url" :src="selectedProduct.image_url" alt="Product Image"
                                class="img-fluid" style="height: 180px;width: 300px;"/>
                            <p><strong>Name:</strong> {{ selectedProduct.name }}</p>
                            <p><strong>Purchase Price:</strong> {{ selectedProduct.purchase_price }}</p>
                            <p><strong>Selling Price:</strong> {{ selectedProduct.price }}</p>
                            <p><strong>Description:</strong> {{ selectedProduct.description }}</p>
                           
                        </div>
                    </div>
                    <!-- <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            products: [],
            loading: false,
            selectedProduct: null,
        }
    },
    methods: {
        createProduct() {
            this.$router.push({ name: 'product.create' });
        },
        async fetchProducts() {
            try {
                const response = await axios.get('/api/product');
                this.products = response.data;
                // console.log('products:', this.products);
            } catch (error) {
                console.error('Error fetching products:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch products.',
                });
            }
        },
        async showProduct(productId) {
            try {
                const response = await axios.get(`/api/product/show/${productId}`);
                if (response.status === 200) {
                    this.selectedProduct = response.data;
                    const modal = new bootstrap.Modal(document.getElementById('productModal'));
                    modal.show();
                }
            } catch (error) {
                console.error('Error fetching product details:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch product details.',
                });
            }
        },
        deleteProduct(productId) {
            this.loading = true;
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    axios
                        .delete(`/api/products/${productId}`)
                        .then((response) => {
                            if (response.status === 200) {
                                this.products = this.products.filter(
                                    (product) => product.id !== productId
                                );
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Your product has been deleted',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    toast: true,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Something went wrong.',
                                });
                            }
                        })
                        .catch((error) => {
                            console.error('Error deleting product:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to delete the product.',
                            });
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                } else {
                    this.loading = false;
                }
            });
        },
        editProduct(productId) {
            this.$router.push({ name: 'product.edit', params: { id: productId } });
        }
    },
    mounted() {
        const successMessage = localStorage.getItem('successMessage');
        if (successMessage) {
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: 'Success',
                text: successMessage,
                showConfirmButton: false,
                timer: 3000,
                toast: true,
            });
            localStorage.removeItem('successMessage');
        }
    },
    created() {
        this.fetchProducts();
    },
};
</script>

<style scoped>
.no-data {
    text-align: center;
    vertical-align: middle;
    height: 20px;
}
</style>