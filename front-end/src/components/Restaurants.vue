<template>
    <div class="asd">
        <h2>All restaurants in Bangsue </h2>

        <div class="pro-catagory">
            <template v-for="(restaurant,idx) in this.restaurants">
                <template v-if="restaurant.loaded_picture">
                    <div class="single-products-catagory " :key='idx'>
                        <a href="JavaScript:Void(0)" @click="showDetails(restaurant)">
                            <img :src="restaurant.loaded_picture" width="350px">

                            <div class="hover-content">
                                <div class="line"></div>
                                <h4>{{restaurant.name}}</h4>
                            </div>
                        </a>
                    </div>
                </template>
            </template>
        </div>

    </div>
</template>

<script>
    import Swal from 'sweetalert2'
    import axios from 'axios'
    import Masonry from 'masonry-layout'

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

                    this.restaurants.sort(function () {
                        return .5 - Math.random();
                    });

                    let $this = this
                    setTimeout(function () {
                        $this.renderMasonry()
                    }, 200);
                }

            }, renderMasonry() {
                const grid = document.querySelector('.pro-catagory')
                const singleProCata = '.single-products-catagory'
                const msnry = new Masonry(grid, {
                    itemSelector: singleProCata,
                    percentPosition: true,
                    masonry: {columnWidth: singleProCata},
                })

                msnry.once('layoutComplete', () => {
                    grid.classList.add('load')
                })

                msnry.layout()
            }, showDetails(restaurant) {

                let info = `<div style="text-align: left; padding-left: 10px">
                    <b>Name:</b> ${restaurant.name} <br>
                    <b>Address:</b> ${restaurant.formatted_address}'<br>
                    <b>Rating:</b> ${restaurant.rating}
                    </div>
                    `;

                let $restaurant = restaurant
                Swal.queue([{
                    title: 'Restaurant Details',
                    confirmButtonText: 'Show Google Map',
                    html: info,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {

                        let lat = $restaurant.geometry.location.lat
                        let lng = $restaurant.geometry.location.lng

                        window.open(`http://maps.google.com/maps?q=${lat},${lng}`, '_blank');
                    }
                }])
            }
        }
    }
</script>

<style scoped>

</style>
