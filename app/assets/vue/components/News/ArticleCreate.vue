<template>
    <div>
        <div v-if="NewsArticleIsLoading" class="row col">
            <p>Loading...</p>
        </div>

        <div v-else>
            <div class="card shadow-light">
                <div class="card-header white mb-1 pt-3half pb-0 border-0 text-dark">
                    <h4 class="card-title mb-0" style="font-weight: 500">
                        <span> Создание новости </span>
                    </h4>
                </div>
                <hr style="border-top-width: 2px"/>
                <div class="card-body pt-0 pb-3">
                    <label>Название</label>
                    <div class="form-group">
                        <input type="text" v-model="title" class="form-control rounded-0">
                    </div>

                    <label>Описание</label>
                    <div class="form-group">
                        <textarea v-model="coverText" class="form-control rounded-0" length="120" rows="2"></textarea>
                    </div>

                    <label>Тело новости</label>
                    <tinymce id="editor" :toolbar1="toolbar1" v-model="text" ref="tm" @editorInit="editorInit" class="mb-3"></tinymce>

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
                        <input v-model="coverImage" type="text" value="Choose file..." class="form-control text-file-input" placeholder="" aria-label="" aria-describedby="basic-addon1">
                    </form>

                    <button form="file-form" @click="createNews()" class="btn btn-md m-0 btn-primary">Сохранить</button>
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
        name: "ArticleCreate",
        data() {
            return {
                toolbar1: '',
                coverText: '',
                coverImage: '',
                text: '',
                title: '',
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
            //auth
            canCreatePost() {
                return this.$store.getters["security/hasRole"]("ROLE_FOO");
            },
        },
        methods: {
            fileChange(event){
                //this.coverImage = event.target.files[0];
                let file = event.target.files[0];
                //let formData = new FormData();
                //formData.append('file', file);
                document.querySelector('.text-file-input').value = file.name;
            },
            editorInit() {
                this.$refs.tm.editor.setContent(this.text);
            },
            async createNews(event){
                console.log(this.coverText, this.coverImage, this.title, this.text);
                let p = [this.coverText, this.coverImage, this.title, this.text];
                const result = await this.$store.dispatch("news/postNews", p);
                this.$router.push({path:'/news/' + result.id });
            },
        }
    };
</script>