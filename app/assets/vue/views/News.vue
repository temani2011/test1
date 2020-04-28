<template>
    <div>
        <div v-if="isLoading" class="row col">
            <p>Loading...</p>
        </div>
        <div v-else>
            <div v-if="canCreatePost">
                <div class="card mb-3half shadow-light">
                    <router-link v-if="canCreatePost" :to="{ name: 'article', params: { id: 'new', msg:'new'} }" type="button d-block">
                                 Create New
                    </router-link>
                </div>
            </div>
            <div v-for="item in getNews">
            <!-- Button -->
            <button v-if="canCreatePost" :id="item.id" @click="DeleteNews($event)" type="button d-inline-block"
                    class="close ml-3 mt-1" aria-label="Close">
                <i class="fa fa-times black-text" aria-hidden="true"></i>
            </button>
            <!-- Button -->
            <router-link v-if="canCreatePost" :id="item.id" :to="{ name: 'article', params: { id: item.id, msg:'edit'} }" type="button d-block"
                    class="close ml-3 mt-1" aria-label="edit">
                <i class="fa fa-pen black-text" aria-hidden="true"></i>
            </router-link>
            <!-- Card Light -->
            <div class="card mb-3half shadow-light">
                <!-- Social shares button -->
                <!--<a class="activator waves-effect waves-light mr-4"><i class="fas fa-share-alt"></i></a>-->
                <div class="card-header white mb-1 pt-3half pb-0 border-0 text-dark">
                    <!-- Category -->
                    <router-link class="text-dark mr-2" :to="'#'"><i class="fas fa-globe-europe pr-1"></i> Общие </router-link>
                    <!-- Author -->
                    <router-link class="text-dark" :to="'#'"> <u> {{ item.user.login }} </u> </router-link>
                    <!-- Date -->
                    <div class="d-inline float-right text-muted"><i class="far fa-clock pr-1"></i> {{ item.created }} </div>
                </div>

                <!-- Link -->
                <router-link :to="'/news/' + item.id" class="black-text ">
                    <!-- Card content -->
                    <div :to="'/news/' + item.id" class="card-body pt-0 pb-3">
                        <!-- Title -->
                        <h4 class="card-title mb-0" style="font-weight: 500"> {{ item.title }} </h4>
                        <!-- Text -->
                        <p class="card-text mb-0"> {{ item.coverText }} </p>
                    </div>

                    <!-- Card image -->
                    <div class="view overlay">
                        <img class="card-img-top rounded-0 shadow-light"
                             :src="item.coverImage ? item.coverImage : defaultImage" alt="Card image cap">
                        <a>
                            <div class="mask rgba-white-slight"></div>
                        </a>
                    </div>
                </router-link>

                <!-- Card footer -->
                <div class="card-footer pb-3half border-0 pt-3 white">
                    <ul class="list-unstyled list-inline m-0">
                        <li class="list-inline-item pr-2"><a href="#" class="text-muted"><i class="fas fa-bookmark pr-1"></i></a></li>
                        <li class="list-inline-item pr-2"><a href="#" class="text-muted"><i class="fas fa-share-alt pr-1"></i></a></li>
                        <li class="list-inline-item pr-2"><router-link :to="'/news/' + item.id" class="text-muted"><i class="fas fa-comments pr-1"></i></router-link></li>
                        <li class="list-inline-item float-right"><a href="#" class="text-muted"><i class="fas fa-heart pr-1"> </i>5</a></li>
                    </ul>
                </div>

            </div>
        </div>
        </div>
    </div>
</template>
<script>
    //import Article from "../views/NewsArticle";
    import ErrorMessage from "../components/ErrorMessage";
    export default {
        name: "News",
        components: {
            ErrorMessage
        },
        data() {
            return {
                defaultImage: "https://mdbootstrap.com/img/Photos/Horizontal/Nature/4-col/img%20%28131%29.jpg",
                newsArray:[]
            }
        },
        created() {
            this.$store.dispatch("news/getAllNews").then(response =>{
                this.newsArray = response;
            });
        },
        computed: {
            isLoading() {
                return this.$store.getters["news/isLoading"];
            },
            hasError() {
                return this.$store.getters["news/hasError"];
            },
            getError() {
                return this.$store.getters["news/getError"];
            },
            hasNews() {
                return this.$store.getters["news/hasNews"];
            },
            getNews() {
                return this.$store.getters["news/getNews"];
            },
            canCreatePost() {
                return this.$store.getters["security/hasRole"]("ROLE_FOO");
            }
        },
        methods: {
            async DeleteNews(event){
                console.log(event.currentTarget.id);
                if (confirm('Are you sure you want to delete this news?')){
                    await this.$store.dispatch("news/deleteNews", event.currentTarget.id);
                }
            },
            async createPost() {
                const result = await this.$store.dispatch("post/create", this.$data.message);
                if (result !== null) {
                    this.$data.message = "";
                }
            }
        }
    }
</script>