<template>
    <div>
        <div v-if="NewsArticleIsLoading" class="row col">
            <p>Loading...</p>
        </div>

        <div v-else-if="state=='select'">
            <div class="row">
                <div class="col mb-3half">
                    <!-- Card Light -->
                    <div class="card shadow-light">
                        <div class="card-header white mb-1 pt-3half pb-0 border-0 text-dark">
                            <!-- Category -->
                            <router-link class="text-dark mr-2" :to="'#'"><i class="fas fa-globe-europe pr-1"></i> Общие </router-link>
                            <!-- Author -->
                            <router-link class="text-dark" :to="{ name: 'user', params: { id: article.user.id } }"> <u> {{ article.user.login }} </u> </router-link>
                            <!-- Date -->
                            <div class="d-inline float-right text-muted">
                                <i class="far fa-clock pr-1"></i> {{ article.created }}
                                <div class="btn-group" role="group" v-if="canEditPost">
                                    <div id="btnGroupDropEdit" type="button" class="close pl-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h" aria-hidden="true"></i>
                                    </div>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDropEdit">
                                        <router-link class="dropdown-item" :id="article.id" :to="{ name: 'article', params: { id: article.id + '?edit', msg:'edit'} }">
                                            Редактировать
                                        </router-link>
                                        <a class="dropdown-item" :id="article.id" @click="DeleteNews($event)">Удалить</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Link -->

                        <!-- Card content -->
                        <div class="card-body pt-0 pb-3">
                            <!-- Title -->
                            <h4 class="card-title mb-0" style="font-weight: 500"> {{ article.title }} </h4>
                            <!-- Text -->
                            <p class="card-text mb-0"> {{ article.coverText }} </p>
                        </div>

                        <!-- Card image -->
                        <div class="view overlay">
                            <img class="card-img-top rounded-0 shadow-light"
                                 :src="article.coverImage ? article.coverImage : ''" alt="Card image cap">
                            <a>
                                <div class="mask rgba-white-slight"></div>
                            </a>
                        </div>

                        <!-- Main text -->
                        <div class="card-body pt-3 pb-0" v-html="article.text">
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

        <div v-else>
            <div class="card shadow-light">
                <div class="card-header white mb-1 pt-3half pb-0 border-0 text-dark">
                    <h4 class="card-title mb-0" style="font-weight: 500">
                        <span v-if="state === 'new'"> Создание новости </span>
                        <span v-else> Редактирование новости </span>
                    </h4>
                </div>
                <hr style="border-top-width: 2px"/>
                <div class="card-body pt-0 pb-3">
                    <label>Название</label>
                    <div class="form-group">
                        <input type="text" v-model="article.title" class="form-control rounded-0">
                    </div>

                    <label>Описание</label>
                    <div class="form-group">
                        <textarea v-model="article.coverText" class="form-control rounded-0" length="120" rows="2"></textarea>
                    </div>

                    <label>Тело новости</label>
                    <tinymce id="editor" :toolbar1="toolbar1" v-model="article.text" ref="tm" @editorInit="editorInit" class="mb-3"></tinymce>

                    <label style="line-height: 14pt">
                        Загрузите изображение для превью новости
                        <br>
                        <em style="font-size:12px">(В случае отутсвия загруженного изображения будет использовано стандартное)</em>
                    </label>

                    <form class="input-group mb-3" id="file-form">
                        <div class="input-group-prepend">
                            <label id="file-input-btn" class="btn btn-md btn-outline-primary m-0 px-3 py-2 z-depth-0 waves-effect">
                                Обзор <input @change="fileChange($event)" id="file-input" type="file" hidden>
                            </label>
                        </div>
                        <input v-model="article.coverImage" type="text" value="Choose file..." class="form-control text-file-input" placeholder="" aria-label="" aria-describedby="basic-addon1">
                    </form>

                    <button form="file-form" @click="state === 'new' ? createNews() : editNews()" class="btn btn-md m-0 btn-primary">Сохранить</button>
                </div>
            </div>
        </div>

    </div>
</template>
<script>
    import tinymce from 'vue-tinymce-editor';
    import ErrorMessage from "../components/ErrorMessage";
    export default {
        components:{
            tinymce,
            ErrorMessage
        },
        name: "Article",
        props: ['msg'],
        data() {
            return {
                isHidden: true,
                state: 'select',
                toolbar1: '',
                commentText: "",
                article: {
                    coverText: '',
                    coverImage: '',
                    text: '',
                    title: '',
                    user: { id: '', login: ''}
                },
                comments: []
            }
        },
        created() {
            console.log(this.$route.params.msg);
            console.log(this.$route.params.id);
            if(this.$route.params.msg === 'new') { this.state = 'new'; return; }
            else {
                this.$store.dispatch("news/getNews", this.$route.params.id).then(responce => {
                    this.article = responce;
                });
                if(this.$route.params.msg === 'edit') this.state = 'edit';
            }
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
                console.log(this.id);
                if(this.article.user.id === curuser.id) return true;
                else return false;
            }
        },
        methods: {
            fileChange(event){
                var fileName = event.target.files[0].name;
                document.querySelector('.text-file-input').value = fileName;
            },
            editorInit() {
                this.$refs.tm.editor.setContent(this.article.text);
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
            async editNews(event){
                console.log(this.id,
                    this.article.coverText,
                    this.article.coverImage,
                    this.article.title,
                    this.article.text);
                let p = [this.article.id,
                    this.article.coverText,
                    this.article.coverImage,
                    this.article.title,
                    this.article.text];
                const result = await this.$store.dispatch("news/putNews", p);
            },
            async createNews(event){
                console.log(this.article.coverText,
                    this.article.coverImage,
                    this.article.title,
                    this.article.text);
                let p = [this.article.coverText,
                    this.article.coverImage,
                    this.article.title,
                    this.article.text];
                const result = await this.$store.dispatch("news/postNews", p);
            },
            async createComment(event){
                console.log(this.article.id, this.commentText, this.$store.getters['security/getUserData'].id);
                let p = [this.article.id, this.commentText, this.$store.getters['security/getUserData'].id];
                const result = await this.$store.dispatch("comment/postComment", p);
            }
        }
    };

    /* show file value after file select */
    $('#file-input').on('change',function(){
        var fileName = document.getElementById("file-input").files[0].name;
        $('.text-file-input').val(fileName);
    })

</script>