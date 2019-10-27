<template>
    <div class="container_web">
        <!--        <h2>All restaurants in Bangsue </h2>-->

        <div class="pro-catagory">
            <template v-for="(restaurant,idx) in this.restaurants">
                <template v-if="restaurant.loaded_picture">
                    <div class="single-products-catagory " :key='idx'>
                        <a href="JavaScript:Void(0)" @click="showDetails(restaurant)">
                            <img :src="restaurant.loaded_picture" class="img_res">

                            <div class="hover-content">
                                <div class="line"></div>
                                <h5>{{restaurant.name}}</h5>
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
            let restaurants = JSON.parse(sessionStorage.getItem("restaurants"));
            if (!restaurants) {
                this.getRestaurants()
            } else {
                this.restaurants = restaurants
            }
            this.loadRenderMasonry()
        },
        methods: {
            async getRestaurants() {
                const res = await axios.get('/restaurants')
                if (res.data.restaurants) {
                    this.restaurants = res.data.restaurants

                    let $this = this
                    setTimeout(function () {
                        $this.renderMasonry()
                    }, 200);

                    sessionStorage.setItem("restaurants", JSON.stringify(this.restaurants));
                }

            },
            loadRenderMasonry() {
                this.restaurants.sort(function () {
                    return .5 - Math.random();
                });

                let $this = this
                setTimeout(function () {
                    $this.renderMasonry()
                }, 500);
            },
            renderMasonry() {

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
