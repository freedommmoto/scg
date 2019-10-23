<template>
    <div class="asd">
        restaurant page
        <div v-for="(restaurant,idx) in this.restaurants">
            asd
            {{restaurant.name}}
            <img :src="restaurant.loaded_picture" width="220">
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
