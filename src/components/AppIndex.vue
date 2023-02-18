<template>
    <recipe-list :recipes="recipes" />
</template>

<script>

import RecipeList from "./RecipeList.vue"

export default {
    name: "AppIndex",
    components: {
        RecipeList,
    }
}
</script>
