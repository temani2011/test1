<template>
    <div>
        <div v-if="NewsArticleIsLoading && CommentsIsLoading" class="row col">
            <p>Loading...</p>
        </div>

        <div v-else-if="state === 'new' || state === 'edit'">
            <!-- Material input -->
            <div class="md-form form-group mt-5">
                <input type="text" v-model="title" class="form-control" id="formGroupExampleInputMD">
                <label for="formGroupExampleInputMD">Title</label>
            </div>
            <!-- Material input -->
            <div class="md-form">
                <textarea v-model="coverText" class="form-control md-textarea" length="120" rows="1"></textarea>
                <label for="textarea-char-counter">Type your text</label>
            </div>
            <!-- File input -->
            <form class="md-form">
                <div class="file-field">
                    <div class="btn btn-primary btn-sm float-left">
                        <span>Choose file</span>
                        <input type="file" @change="fileListener">
                    </div>
                    <div class="file-path-wrapper">
                        <input v-model="coverImage" class="file-path validate" type="text" placeholder="Upload your file">
                    </div>
                </div>
                <button @click="state === 'new' ? createNews() : editNews()" type="button" class="btn btn-primary">Send</button>
            </form>
            <tinymce id="editor" v-model="text" ref="tm" @editorInit="editorInit"></tinymce>
        </div>

        <div v-else>
            <!-- Card Light -->
            <div class="card mb-3half shadow-light">

                <!-- Social shares button -->
                <div class="card-header white mb-1 pt-3half pb-0 border-0 text-dark">
                    <!-- Category -->
                    <router-link class="text-dark mr-2" :to="'#'"><i class="fas fa-globe-europe pr-1"></i> Общие </router-link>
                    <!-- Author -->
                    <router-link class="text-dark" :to="'#'"> <u> {{ author }} </u> </router-link>
                    <!-- Date -->
                    <div class="d-inline float-right text-muted"><i class="far fa-clock pr-1"></i> {{ created }} </div>
                </div>

                <!-- Card Content-Header -->
                <div class="card-body pt-0 pb-3">
                    <!-- Title -->
                    <h4 class="card-title mb-0" style="font-weight: 500"> {{ title }} </h4>
                    <!-- Text -->
                    <p class="card-text mb-0"> {{ coverText }} </p>
                </div>

                <!-- Card Content -->
                <div class="card-body pt-0 pb-3">
                    <!-- Text -->
                    <p class="card-text mb-0"> <span v-html="text"></span> </p>
                </div>

                <hr class="my-0">

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
                    <tinymce id="editor1" v-model="commentText" ref="tm1"></tinymce>
                    <form class="md-form">
                        <button @click="createComment()" type="button" class="btn btn-primary">Send</button>
                    </form>
                </div>
            </div>

            <!-- Card Error -->
            <div v-if="CommentsHasError" class="row col">
                <error-message :error="CommentsGetError" />
            </div>

            <div v-if="!isHidden" v-for="item in getComments" class="card mb-3half shadow-light">
                <div :key="item.id" class="row col">
                    <span>{{ item.author }}</span>
                    <span>{{ item.text }}</span>
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
                id: "",
                coverText: "",
                coverImage: "",
                title: "",
                text: "",
                created: "",
                author: "",
                commentText: "",
                comments: []
            }
        },
        created() {
            console.log(this.$route.params.msg);
            console.log(this.$route.params.id);
            if(this.$route.params.msg === 'new') { this.state = 'new'; return; }
            else this.$store.dispatch("news/getNews", this.$route.params.id).then(responce => {
                    this.id = responce.id;
                    this.title = responce.title;
                    this.text = responce.text;
                    this.created = responce.created;
                    this.author = responce.user.login;
                }).then(request => this.$store.dispatch("comment/getAllComments", this.$route.params.id))
                    .then(response => this.comments = response);
                    // .then(request => {
                    //     this.comments.forEach(comment => this.$store.dispatch("user/getUser", this.comment.id)
                    //         .then(response => comment.author = response.login;));
                    // }););
            if(this.$route.params.msg === 'edit') this.state = 'edit';
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
            }
        },
        methods: {
            editorInit() {
                this.$refs.tm.editor.setContent(this.text);
            },
            fileListener(event){
                console.log(event.target.files);
            },
            show(event){
                event.preventDefault();
                this.isHidden = !this.isHidden;
            },
            async editNews(event){
                console.log(this.id,
                    this.coverText,
                    this.coverImage,
                    this.title,
                    this.text);
                let p = [this.id,
                    this.coverText,
                    this.coverImage,
                    this.title,
                    this.text];
                const result = await this.$store.dispatch("news/putNews", p);
            },
            async createNews(event){
                console.log(this.coverText,
                    this.coverImage,
                    this.title,
                    this.text);
                let p = [this.coverText,
                    this.coverImage,
                    this.title,
                    this.text];
                const result = await this.$store.dispatch("news/postNews", p);
            },
            async createComment(event){
                console.log(this.id, this.commentText, this.$store.getters['security/getUserData'].id);
                let p = [this.id, this.commentText, this.$store.getters['security/getUserData'].id];
                const result = await this.$store.dispatch("comment/postComment", p);
            }
        }
    };
</script>