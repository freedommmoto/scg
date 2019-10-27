<template>
    <div class="container_web">
        <div class="ped10"><h3>Finding XYZValue </h3></div>
        <div>
            <template v-for="(num,idx) in xyzNumberList">
                <h3 v-bind:class="[xyzPosition.includes(idx) ? 'red' : '']">
                    {{num}}
                </h3>
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'XYZValue',
        data() {
            return {
                'xyzNumberList': [],
                'xyzPosition': [1, 6, 7]
            }
        },
        created() {
            let xyzNumberList = JSON.parse(localStorage.getItem("xyzNumberList"));

            if (!xyzNumberList) {
                this.findingXYZ()
                localStorage.setItem("xyzNumberList", JSON.stringify(this.xyzNumberList));
            } else {
                this.xyzNumberList = xyzNumberList
            }

        }, methods: {
            findingXYZ() {
                for (let round = 1; round <= 7; round++) {
                    let lastNumber = (this.xyzNumberList[round - 1]) ? this.xyzNumberList[round - 1] : 1
                    let lastRound = (round - 1 === 0) ? 1 : round - 1
                    let thisRoundNum = (lastRound * 2) + lastNumber;
                    this.xyzNumberList[round] = thisRoundNum
                }
            },
        },
    }
</script>

