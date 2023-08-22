<template>
    <div>
        <p>{{ message }}</p>
    </div>
</template>

<script>
export default {
    props: [window.appData.posts],
    data() {
        return {
            message: 'Hello from Vue.js !'
        };
    }
};
</script>