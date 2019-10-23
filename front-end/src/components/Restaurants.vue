<template>
    <div class="">
        <div v-for="(restaurant,idx) in this.restaurants.name">
            {{restaurant}}
        </div>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        name: 'Restaurants',
        data() {
            return {
                restaurants: []
            }
        },
        mounted() {
            this.getRestaurants()
        },
        methods: {
            async getRestaurants() {
                const res = await axios.get('/restaurants')
                if (res.data.restaurants) {
                    this.restaurants = res.data.restaurants
                }
                console.log(this.restaurants)
            }
        }
    }
</script>

<style scoped>

</style>
