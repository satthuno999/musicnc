<template>
    <div class="wrapper">
        <!-- Use $store.state.page for page matching to make sure everything else has been set beforehand! -->
        <div class="status-header">
            <ModeIndicator v-if="isSearch" :title="searchTitle" />
            <ModeIndicator
                v-else-if="isEdit"
                :title="t('cookbook', 'Editing recipe')"
            />
            <ModeIndicator
                v-else-if="isRecipe"
                :title="t('cookbook', 'Viewing recipe')"
            />
            <!-- INDEX PAGE -->
            <Location v-if="isIndex" :title="t('cookbook', 'All recipes')" />
            <Location
                v-else-if="isSearch && $route.params.value"
                :title="
                    $route.params.value === '_' // TRANSLATORS Shown, e.g., as the recipe category in the navigation/title bar for uncategorized recipes.
                        ? t('cookbook', 'None')
                        : decodeURIComponent($route.params.value)
                "
            />
            <!-- Recipe view / edit -->
            <Location
                v-else-if="isEdit || isRecipe"
                :title="$store.state.recipe.name"
            />
            <!-- Is app loading? -->
            <Location
                v-else-if="isLoading"
                :title="t('cookbook', 'Loading app')"
            />
            <!-- Is a recipe loading? -->
            <Location
                v-else-if="isLoadingRecipe"
                :title="t('cookbook', 'Loading recipe')"
            />
            <!-- No recipe found -->
            <Location
                v-else-if="recipeNotFound"
                :title="t('cookbook', 'Recipe not found')"
            />
            <!-- No page found -->
            <Location
                v-else-if="pageNotFound"
                :title="t('cookbook', 'Page not found')"
            />
            <!-- Create new recipe -->
            <Location
                v-else-if="isCreate"
                :title="t('cookbook', 'Creating new recipe')"
            />
        </div>
        {{/* Primary buttons */}}
        <NcButton
            v-if="isRecipe"
            type="primary"
            :aria-label="t('cookbook', 'Edit')"
            @click="goToRecipeEdit($store.state.recipe.id)"
        >
            <template #icon>
                <PencilIcon :size="20" />
            </template>
            {{ t("cookbook", "Edit") }}
        </NcButton>
        <NcButton
            v-if="isEdit || isCreate"
            type="primary"
            :aria-label="t('cookbook', 'Save')"
            @click="saveChanges()"
        >
            <template #icon>
                <LoadingIcon
                    v-if="$store.state.savingRecipe"
                    :size="20"
                    class="animation-rotate"
                />
                <CheckmarkIcon v-else :size="20" />
            </template>
            {{ t("cookbook", "Save") }}
        </NcButton>
        <!-- This is clumsy design but the component cannot display just one input element on the breadcrumbs bar -->
        <NcActions
            v-if="isIndex"
            default-icon="icon-search-white"
            :menu-title="t('cookbook', 'Search')"
            :primary="true"
        >
            <NcActionInput
                icon="icon-quota"
                :value="filterValue"
                @update:value="updateFilters"
            >
                {{ t("cookbook", "Filter") }}
            </NcActionInput>
            <NcActionInput icon="icon-search" @submit="search">
                {{ t("cookbook", "Search") }}
            </NcActionInput>
        </NcActions>
        {{/* Overflow buttons (3-dot menu) */}}
        <NcActions
            v-if="isRecipe || isEdit"
            :force-menu="true"
            class="overflow-menu"
        >
            <NcActionButton
                v-if="isEdit"
                :icon="
                    $store.state.reloadingRecipe === parseInt($route.params.id)
                        ? 'icon-loading-small'
                        : 'icon-history'
                "
                class="action-button"
                :aria-label="t('cookbook', 'Reload recipe')"
                @click="reloadRecipeEdit()"
            >
                {{ t("cookbook", "Reload recipe") }}
            </NcActionButton>
            <NcActionButton
                v-if="isEdit"
                class="action-button"
                :aria-label="t('cookbook', 'Abort editing')"
                @click="goToRecipe($store.state.recipe.id)"
            >
                {{ t("cookbook", "Abort editing") }}
                <template #icon>
                    <NcLoadingIcon
                        v-if="
                            $store.state.reloadingRecipe ===
                            parseInt($route.params.id)
                        "
                        :size="20"
                    />
                    <eye-icon v-else :size="20" />
                </template>
            </NcActionButton>
            <NcActionButton
                v-if="isRecipe"
                :icon="
                    $store.state.reloadingRecipe === parseInt($route.params.id)
                        ? 'icon-loading-small'
                        : 'icon-history'
                "
                class="action-button"
                :aria-label="t('cookbook', 'Reload recipe')"
                @click="reloadRecipeView()"
            >
                {{ t("cookbook", "Reload recipe") }}
            </NcActionButton>
            <NcActionButton
                v-if="isRecipe"
                class="action-button"
                :aria-label="t('cookbook', 'Print recipe')"
                @click="printRecipe()"
            >
                <template #icon=""><printer-icon :size="20" /></template>
                {{ t("cookbook", "Print recipe") }}
            </NcActionButton>
            <NcActionButton
                v-if="isRecipe"
                icon="icon-delete"
                class="action-button"
                :aria-label="t('cookbook', 'Delete recipe')"
                @click="deleteRecipe()"
            >
                {{ t("cookbook", "Delete recipe") }}
            </NcActionButton>
        </NcActions>
    </div>
</template>

<script>
import NcActions from "@nextcloud/vue/dist/Components/NcActions"
import NcActionButton from "@nextcloud/vue/dist/Components/NcActionButton"
// Cannot use `Button` else get `vue/no-reserved-component-names` eslint errors
import NcButton from "@nextcloud/vue/dist/Components/NcButton"
import NcActionInput from "@nextcloud/vue/dist/Components/NcActionInput"
import NcLoadingIcon from "@nextcloud/vue/dist/Components/NcLoadingIcon"

import PencilIcon from "icons/Pencil.vue"
import LoadingIcon from "icons/Loading.vue"
import CheckmarkIcon from "icons/Check.vue"
import PrinterIcon from "icons/Printer.vue"
import EyeIcon from "icons/Eye.vue"

import Location from "./Location.vue"
import ModeIndicator from "./ModeIndicator.vue"

export default {
    name: "AppControls",
    components: {
        NcActions,
        NcActionButton,
        NcActionInput,
        NcLoadingIcon,
        PrinterIcon,
        NcButton,
        PencilIcon,
        LoadingIcon,
        CheckmarkIcon,
        EyeIcon,
        ModeIndicator,
        Location,
    },
}
</script>

<style scoped>
.wrapper {
    /* 44px is the height of nextcloud/vue button (not exposed as a variable :[ ) */
    --nc-button-size: 44px;

    --vertical-padding: 4px;

    /* Sticky is better than fixed because fixed takes the element out of flow,
     which breaks the height, putting elements underneath */
    position: sticky;

    /* This is competing with the recipe instructions which have z-index: 1 */
    z-index: 2;

    /* The height of the nextcloud header */
    top: 0px;
    display: flex;
    width: 100%;
    /* Make sure the wrapper is always at least as tall as the tallest element
     * we expect (primary button) to prevent flickering when loading, etc. */
    min-height: calc(44px + 2 * var(--vertical-padding));
    flex-direction: row;

    padding: var(--vertical-padding) 1rem var(--vertical-padding)
        calc(44px + 2 * var(--vertical-padding));
    border-bottom: 1px solid var(--color-border);
    background-color: var(--color-main-background);
    gap: 8px;
}

.status-header {
    display: flex;
    /* Allow the title to shrink*/
    min-width: 0;
    flex-basis: 0;
    flex-direction: column;
    flex-grow: 1;
    flex-shrink: 1;
    align-items: flex-start;
    justify-content: space-around;
}

.mode-indicator {
    font-size: 0.9em;
    line-height: 0.9em;
}

.location-wrapper {
    width: 100%;
}

.location-wrapper:only-child {
    display: flex;
    flex: 1;
    flex-direction: column;
    justify-content: center;
}

/* The .status-header is justify-content: space-around. If there is no
 * .mode-indicator, this will put the .location at the top. Override to place in
 * the center */
.status-header:deep(.location) {
    /* Don't let the location go wider than the space available */
    width: 100%;
    margin: 0;
    font-size: 1.2em;
    line-height: 1em;
    /* overflow-x: hidden breaks overflow-y: visible
    https://stackoverflow.com/questions/6421966/css-overflow-x-visible-and-overflow-y-hidden-causing-scrollbar-issue */
    overflow-x: clip;
    overflow-y: visible;
    /* Show ... when overflowing */
    text-overflow: ellipsis;
    white-space: nowrap;
}

.animation-rotate {
    animation: rotate var(--animation-duration, 0.8s) linear infinite;
}

@media print {
    * {
        display: none !important;
    }
}
</style>

<style>
@media print {
    .vue-tooltip {
        display: none !important;
    }
}
</style>
