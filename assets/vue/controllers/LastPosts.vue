<template>
    <h1>Derniers articles</h1>
    <div v-for="post in this.lastPosts">
        {{ post.name[0] }}
    </div>
</template>

<script>
    export default { 
        data() {
            return {
                lastPosts: window.appData.lastPosts
            }
        }
    }
</script>