<template>
    <div class="container_web">
        <div class="ped10"><h3>LineBot Order Report</h3></div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Restaurant</th>
                    <th scope="col">Menu Name</th>
                    <th scope="col">LineID</th>
                    <th scope="col">GoogleID</th>
                    <th scope="col">Restaurant Picture</th>
                </tr>
                </thead>
                <tbody>
                <template v-for="(order,idx) in this.orderList">
                    <tr>
                        <th scope="row">{{idx+1}}</th>
                        <td>{{order.name}}</td>
                        <td>{{order.order_text}}</td>
                        <td>{{order.id_line_users}}</td>
                        <td>{{order.google_id}}</td>
                        <td><img :src="order.img" width="120px"></td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>

        <img class="img_line_diagram"
             src="https://raw.githubusercontent.com/freedommmoto/scg/master/line_bot_diagram.png">

    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        name: 'LineBot',
        data() {
            return {
                orderList: []
            }
        }, mounted() {
            let orderList = JSON.parse(sessionStorage.getItem("orderList"));
            if (!orderList) {
                this.getOrder()
            } else {
                this.orderList = orderList
            }

        }, methods: {
            async getOrder() {
                const res = await axios.get('/order')
                if (res.data.order) {
                    this.orderList = res.data.order
                    sessionStorage.setItem("orderList", JSON.stringify(this.orderList));
                }
            }
        }
    }
</script>
