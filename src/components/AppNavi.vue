<template>
    <!-- This component should ideally not have a conflicting name with AppNavigation from the nextcloud/vue package -->
    <NcAppNavigation>
        <template slot="list">
            <NcAppNavigationItem
                name="Album"
            >
            </NcAppNavigationItem>
        </template>

        <template slot="footer">
            <NcAppNavigationNew
                :text="t('cookbook', 'Cookbook settings')"
                :button-class="['create', 'icon-settings']"
                @click="handleOpenSettings"
            />
        </template>
    </NcAppNavigation>
</template>

<script>
import NcAppNavigation from "@nextcloud/vue/dist/Components/NcAppNavigation"
import NcAppNavigationItem from '@nextcloud/vue/dist/Components/NcAppNavigationItem'
import NcAppNavigationNew from "@nextcloud/vue/dist/Components/NcAppNavigationNew"

export default {
    name: "AppNavi",
    components: {
        NcAppNavigation,
        NcAppNavigationItem,
        NcAppNavigationNew,
    },
}
</script>
