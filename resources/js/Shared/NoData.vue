<template>
    <div class="components">
        <img src="../../images/nodata.gif" alt="" class="nodata">
        <span class="message">
            <slot></slot>
        </span>
        
    </div>
</template>

<script>
</script>

<style scoped>
/*!!! START OF NO DATA !!!*/

.nodata{
    filter: grayscale(1) contrast(0.5) brightness(1.47);    
}

.components{
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.message{
    margin-top: -5%;
    font-size: 20px;
    color: rgb(143, 139, 139);
}
</style>