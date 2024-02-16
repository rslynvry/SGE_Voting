<template>
    <div class="components">
        <img src="../../images/loading.gif" alt="" class="loading" :style="{ 'margin-top': marginTop}">
        <span class="message">
        </span>
    </div>
</template>

<script>
export default {
    name: 'Loading',
    props: {
        marginTop:{
            type: String,
            default: '5%'
        }
    }
}
</script>

<style scoped>
/*!!! START OF NO DATA !!!*/

.loading{
    margin-top: 5%;
    /* filter: grayscale(1) contrast(1) brightness(1.47); */
}

.components{
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.message{
    margin-top: 5%;
    font-size: 20px;
    color: transparent;
}
</style>