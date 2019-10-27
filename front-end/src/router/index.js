import Vue from 'vue'
import Router from 'vue-router'

import Restaurants from '@/pages/Restaurants'
import XYZValue from '@/pages/XYZValue'
import Linebot from '@/pages/Linebot'
import Resume from '@/pages/Resume'

Vue.use(Router)

export default new Router({
    mode: "history",
    routes: [
        {
            path: '/',
            name: 'Home',
            component: Restaurants
        },
        {
            path: '/restaurants',
            name: 'Restaurants',
            component: Restaurants
        },
        {
            path: '/scg',
            name: 'SCG',
            component: XYZValue
        },
        {
            path: '/linebot',
            name: 'Linebot',
            component: Linebot
        },
        {
            path: '/resume',
            name: 'Resume',
            component: Resume
        }
    ]
})
