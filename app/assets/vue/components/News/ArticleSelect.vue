<template>
    <div>
        <div v-if="NewsArticleIsLoading" class="row col">
            <p>Loading...</p>
        </div>

        <div v-else>
            <div class="row">
                <div class="col mb-3half">
                    <!-- Card Light -->
                    <div class="card shadow-light">
                        <div class="card-header white mb-1 pt-3half pb-0 border-0 text-dark">
                            <!-- Category -->
                            <router-link class="text-dark mr-2" :to="'#'"><i class="fas fa-globe-europe pr-1"></i> Общие </router-link>
                            <!-- Author -->
                            <router-link class="text-dark" :to="{ name: 'user', params: { id: user.id } }"> <u> {{ user.login }} </u> </router-link>
                            <!-- Date -->
                            <div class="d-inline float-right text-muted">
                                <i class="far fa-clock pr-1"></i> {{ created }}
                                <div class="btn-group" role="group" v-if="canEditPost">
                                    <div id="btnGroupDropEdit" type="button" class="close pl-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h" aria-hidden="true"></i>
                                    </div>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDropEdit">
                                        <router-link class="dropdown-item" :id="id" :to="id + '/edit'">
                                            Редактировать
                                        </router-link>
                                        <a class="dropdown-item" :id="id" @click="DeleteNews($event)">Удалить</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Link -->

                        <!-- Card content -->
                        <div class="card-body pt-0 pb-3">
                            <!-- Title -->
                            <h4 class="card-title mb-0" style="font-weight: 500"> {{ title }} </h4>
                            <!-- Text -->
                            <p class="card-text mb-0"> {{ coverText }} </p>
                        </div>

                        <!-- Card image -->
                        <div class="view overlay">
                            <img class="card-img-top rounded-0 shadow-light"
                                 :src="coverImage ? coverImage : defaultImage" alt="Card image cap">
                            <a>
                                <div class="mask rgba-white-slight"></div>
                            </a>
                        </div>

                        <!-- Main text -->
                        <div class="card-body pt-3 pb-0" v-html="text">
                        </div>
                        <hr class="my-0" style="border-top-width: 2px"/>

                        <!-- Card footer -->
                        <div class="card-footer pb-3half border-0 pt-3 white">
                            <ul class="list-unstyled list-inline m-0">
                                <li class="list-inline-item pr-2"><a href="#" class="text-muted"><i class="fas fa-bookmark pr-1"></i></a></li>
                                <li class="list-inline-item pr-2"><a href="#" class="text-muted"><i class="fas fa-share-alt pr-1"></i></a></li>
                                <li class="list-inline-item pr-2"><a href="#" @click="show($event)"class="text-muted"><i class="fas fa-comments pr-1"></i><span>{{ comments.length }}</span></a></li>
                                <li class="list-inline-item float-right"><a href="#" class="text-muted"><i class="fas fa-heart pr-1"> </i>5</a></li>
                            </ul>
                        </div>

                        <!-- Card comment input -->
                        <div v-if="canCreatePost && !isHidden" class="card-body pt-0 pb-3">
                            <!-- Text input -->
                            <tinymce id="editor1" :toolbar1="toolbar1" v-model="commentText" ref="tm1" class="mb-3half"></tinymce>
                            <button @click="createComment()" type="button" class="btn btn-md m-0 btn-primary">Отправить</button>
                        </div>

                    </div>
                </div>
            </div>

            <div v-if="!isHidden">
                <div v-if="CommentsIsLoading" class="row col">
                    <p>Loading...</p>
                </div>
                <div v-else-if="CommentsHasError" class="row col">
                    <error-message :error="CommentsGetError" />
                </div>
                <div v-else v-for="item in this.comments" class="card mb-3half shadow-light">
                    <div :key="item.id" class="row col">
                        <router-link class="text-dark" :to="'../user/' + item.author"> {{ item.authorLogin }}  </router-link>
                        <span v-html="item.text"></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
    import tinymce from 'vue-tinymce-editor';
    import ErrorMessage from "../ErrorMessage";
    export default {
        components:{
            tinymce,
            ErrorMessage
        },
        name: "ArticleSelect",
        data() {
            return {
                defaultImage: "https://mdbootstrap.com/img/Photos/Horizontal/Nature/4-col/img%20%28131%29.jpg",
                isHidden: true,
                toolbar1: '',
                commentText: "",
                created: '',
                coverText: '',
                coverImage: '',
                text: '',
                title: '',
                user: { id: '', login: ''},
                comments: []
            }
        },
        created() {
            if(this.$store.getters["news/hasNews"]) {
                let news = this.$store.getters["news/getNews"];
                news = news.find(x => x.id == this.$route.params.id);
                if(typeof news !== 'undefined') {
                    this.id = news.id;
                    this.title = news.title;
                    this.text = news.text;
                    this.created = news.created;
                    this.coverText = news.coverText;
                    this.coverImage = news.coverImage;
                    this.user.id = news.user.id;
                    this.user.login = news.user.login;
                }
            }
            else this.$store.dispatch("news/getNews", this.$route.params.id).then(responce => {
                this.id = responce.id;
                this.title = responce.title;
                this.text = responce.text;
                this.created = responce.created;
                this.coverText = responce.coverText;
                this.coverImage = responce.coverImage;
                this.user.id = responce.user.id;
                this.user.login = responce.user.login;
            });
        },
        computed: {
            NewsArticleIsLoading() {
                return this.$store.getters["news/isLoading"];
            },
            NewsArticleHasError() {
                return this.$store.getters["news/hasError"];
            },
            NewsArticleGetError() {
                return this.$store.getters["news/getError"];
            },
            hasNews() {
                return this.$store.getters["news/hasNews"];
            },
            getNews() {
                return this.$store.getters["news/getNews"];
            },
            //comments
            CommentsIsLoading() {
                return this.$store.getters["comment/isLoading"];
            },
            CommentsHasError() {
                return this.$store.getters["comment/hasError"];
            },
            CommentsGetError() {
                return this.$store.getters["comment/getError"];
            },
            hasComments() {
                return this.$store.getters["comment/hasComments"];
            },
            getComments() {
                return this.$store.getters["comment/getComments"];
            },
            //auth
            canCreatePost() {
                return this.$store.getters["security/hasRole"]("ROLE_FOO");
            },
            canEditPost(){
                var curuser = this.$store.getters["security/getUserData"];
                console.log(this.user.id + ' ' + curuser.id);
                if(this.user.id === curuser.id) return true;
                else return false;
            }
        },
        methods: {
            editorInit() {
                this.$refs.tm.editor.setContent(this.text);
            },
            show(event){
                event.preventDefault();
                if(this.comments.length == 0) this.$store.dispatch("comment/getAllComments", this.$route.params.id)
                    .then(response => this.comments = response)
                    .then(request => {
                        this.comments.forEach(comment => this.$store.dispatch("user/getUserById", comment.author)
                            .then(response => comment.authorLogin = response.login))
                    });
                this.isHidden = !this.isHidden;
            },
            async createComment(event){
                console.log(this.article.id, this.commentText, this.$store.getters['security/getUserData'].id);
                let p = [this.article.id, this.commentText, this.$store.getters['security/getUserData'].id];
                const result = await this.$store.dispatch("comment/postComment", p);
            },
            async DeleteNews(event){
                console.log(event.currentTarget.id);
                event.preventDefault();
                if (confirm('Are you sure you want to delete this news?')){
                    await this.$store.dispatch("news/deleteNews", event.currentTarget.id);
                    this.$router.push({path:'/news'});
                }
            },
        }
    };

</script>