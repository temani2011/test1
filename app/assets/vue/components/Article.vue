<template>
    <!-- Card Light -->
    <div class="card">

        <!-- Card image -->
        <div class="view overlay">
            <img class="card-img-top"
                 src="https://mdbootstrap.com/img/Photos/Horizontal/Nature/4-col/img%20%28131%29.jpg" alt="Card image cap">
            <a>
                <div class="mask rgba-white-slight"></div>
            </a>
        </div>

        <!-- Card content -->
        <div class="card-body">

            <!-- Social shares button -->
            <a class="activator waves-effect waves-light mr-4"><i class="fas fa-share-alt"></i></a>
            <!-- Title -->
            <h4 class="card-title"> {{ title }} </h4>
            <hr>
            <!-- Text -->
            <p class="card-text"> {{ text }} </p>
            <!-- Link -->
            <router-link class="black-text d-flex justify-content-end"
                         :to="'/news/' + id">
                <h5>Read more <i class="fas fa-angle-double-right"></i></h5>
            </router-link>

        </div>

    </div>
    <!-- Card Light -->
</template>
<script>
    export default {
        name: "Article",
        props: {
            id:{
              type: String,
              required: true
            },
            title: {
                type: String,
                required: true
            },
            text: {
                type: String,
                required: true
            },
            author: {
                type: String,
                //required: true
            }
        }
    };
</script>