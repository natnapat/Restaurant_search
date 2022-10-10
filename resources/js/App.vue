<template>
    <div>
        <Navbar />
        <Location />
    </div>
</template>

<script>
import Navbar from './components/Navbar.vue';
import Location from './components/Location.vue';

export default {
    name: "App",
    components: {
        Navbar,
        Location,
    },
};
</script>