<template>
    <div>
        <recipe-list :recipes="results" />
    </div>
</template>

<script>
import helpers from "musicnc/js/helper"

import RecipeList from "./RecipeList.vue"

export default {
    name: "SearchResult",
    components: {
        RecipeList,
    },
    beforeRouteUpdate(to, from, next) {
        // Move to next route as expected
        next()
        // Reload view
        this.setup()
    },
    props: {
        query: {
            type: String,
            default: "",
        },
    },
    data() {
        return {
            results: [],
        }
    },
    watch: {
        // eslint-disable-next-line no-unused-vars
        $route(to, from) {
            this.keywordFilter = []
        },
    },
    mounted() {
        this.setup()
        this.$root.$off("categoryRenamed")
        this.$root.$on("categoryRenamed", (val) => {
            if (
                // eslint-disable-next-line no-underscore-dangle
                !this._inactive &&
                this.query === "cat" &&
                this.$route.params.value === val[1]
            ) {
                helpers.goTo(`/category/${val[0]}`)
            }
        })
    },
}
</script>
