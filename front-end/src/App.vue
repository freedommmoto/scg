<template>
    <div id="app">
        <TopBar></TopBar>
        <div class="space_router"></div>
        <router-view/>
        <div class="space_router"></div>
        <Footer></Footer>
    </div>
</template>

<script>

    import TopBar from './components/TopBar.vue'
    import Footer from './components/Footer.vue'

    export default {
        name: 'App',
        components: {
            TopBar: TopBar,
            Footer: Footer
        },
    }
</script>

<style>
    @import '/assets/css/main.css';
</style>
