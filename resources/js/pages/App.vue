<template>
    <HeaderSection />
    <router-view />
    <FooterSection v-if="showFooter"/>
</template>

<script setup>
import { useRoute } from 'vue-router';
import HeaderSection from '../pages/fixed/Header.vue'
import FooterSection from '../pages/fixed/Footer.vue'
import { computed } from 'vue';

const route = useRoute();
const showFooter= computed(()=>route.name==='home');

</script>