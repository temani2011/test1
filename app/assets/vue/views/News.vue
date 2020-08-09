<template>
    <div>
        <div v-if="isLoading" class="row col">
            <p>Loading...</p>
        </div>
        <div v-else>
            <div v-if="canCreatePost" class="row">
                <div class="col" style="padding-bottom:15px">
                <router-link :to="'/news/new'" tag="button"
                             class="btn btn-primary btn-block">
                    Создать новость
                </router-link>
                </div>
            </div>
            <div v-for="item in newsArray">
                <div class="row">
                    <div class="col mb-3half">
                        <!-- Card Light -->
                        <div class="card shadow-light">
                            <div class="card-header white mb-1 pt-3half pb-0 border-0 text-dark">
                                <!-- Category -->
                                <router-link class="text-dark mr-2" :to="'#'"><i class="fas fa-globe-europe pr-1"></i> Общие </router-link>
                                <!-- Author -->
                                <router-link class="text-dark" :to="'user/'+ item.user.id"> <u> {{ item.user.login }} </u> </router-link>
                                <!-- Date -->
                                <div class="d-inline float-right text-muted">
                                    <i class="far fa-clock pr-1"></i> {{ item.created }}
                                    <div class="btn-group" role="group" v-if="canEditPost(item)">
                                        <div id="btnGroupDropEdit" type="button" class="close pl-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h" aria-hidden="true"></i>
                                        </div>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDropEdit">
                                            <router-link class="dropdown-item" :id="item.id" :to="'news/'+ item.id +'/edit'">
                                                Редактировать
                                            </router-link>
                                            <a class="dropdown-item" :id="item.id" @click="DeleteNews($event)">Удалить</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Link -->
                            <router-link :to="'/news/' + item.id" class="black-text ">
                                <!-- Card content -->
                                <div class="card-body pt-0 pb-3">
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
        </div>
    </div>
</template>
<script>
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
            },
        },
        methods: {
            canEditPost(newsArticle){
                var curuser = this.$store.getters["security/getUserData"];
                var curnews = this.newsArray.find(x=>x === newsArticle);
                if(curnews.user.id === curuser.id) return true;
                else return false;
            },
            async DeleteNews(event){
                console.log(event.currentTarget.id);
                event.preventDefault();
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