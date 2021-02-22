<template>
    <div>
        <div v-if="isLoading" class="row col">
            <p>Loading...</p>
        </div>
        <div v-else>
            <div class="row">
                <div class="col mb-3half">
                    <!-- Card Light -->
                    <div class="card shadow-light">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-auto pr-1">
                                    <i class="fa fa-list-ul" aria-hidden="true"></i>
                                </div>
                                <div class="col pl-0"> Каталог/Файл </div>
                                <div class="col-auto"> Автор </div>
                                <div class="col-auto"> Дата </div>
                                <div class="col-auto pr-0" style="opacity: 0">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </div>
                                <div class="col-auto" style="opacity: 0">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                </div>
                            </div>
                            <hr class="my-1 mb-2" style="border: 0px; border-top:1px solid darkgray;">
                            <div v-for="catalog in catalogsArray">
                                <catalog class="my-1" :id="catalog.id"
                                         :name="catalog.name"
                                         :author="catalog.author"
                                         :createdAt="catalog.createdAt"
                                         :slug="catalog.slug"
                                         :childsCount="catalog.childsCount"
                                         :documentsCount="catalog.documentsCount"
                                         :parent="catalog.parent"
                                         :childs="catalog.childs"
                                         :documents="catalog.documents"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" ref="vuemodal1" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="createModalLabel">Добавить каталог/файлы</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-check form-check-inline mb-3" type="button" @click="isDisabled=true">
                                    <input class="form-check-input" name="optionRadio" type="radio" id="inlineCheckbox1" value="addCatalog" checked>
                                    <label class="form-check-label" for="inlineCheckbox1">Создать новый каталог</label>
                                </div>
                                <div class="form-group" :class="!isDisabled ? 'disabled' : ''">
                                    <div class="row">
                                        <label class="col-auto"> Название </label>
                                        <input id="catalogName" type="text" class="col form-control rounded-0 mr-3">
                                    </div>
                                </div>
                                <div class="form-check form-check-inline mb-3" type="button" @click="isDisabled=false">
                                    <input class="form-check-input" name="optionRadio" type="radio" id="inlineCheckbox2" value="addDocuments">
                                    <label class="form-check-label" for="inlineCheckbox2">Добавить файлы</label>
                                </div>
                                <form class="input-group mb-3" id="file-form" :class="isDisabled ? 'disabled' : ''">
                                    <div class="input-group-prepend">
                                        <label id="file-input-btn" class="btn btn-md btn-outline-primary m-0 px-3 py-2 z-depth-0 waves-effect">
                                            Обзор <input @change="uploadFiles($event)" id="file-input" type="file" hidden multiple>
                                        </label>
                                    </div>
                                    <input id="filesInput" type="text" value="Choose file..." class="form-control text-file-input rounded-0" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" @click="createCatalogOrAddDocs($event)" class="btn btn-md btn-primary">Создать</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" ref="vuemodal2" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updateModalLabel">Добавить каталог/файлы</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!--
                                <div class="form-check form-check-inline mb-3" type="button" @click="isDisabled=true">
                                    <input class="form-check-input" name="optionRadio" type="radio" id="inlineCheckbox1" value="addCatalog" checked>
                                    <label class="form-check-label" for="inlineCheckbox1">Создать новый каталог</label>
                                </div>
                                <div class="form-group" :class="!isDisabled ? 'disabled' : ''">
                                    <div class="row">
                                        <label class="col-auto"> Название </label>
                                        <input id="catalogName" type="text" class="col form-control rounded-0 mr-3">
                                    </div>
                                </div>
                                <div class="form-check form-check-inline mb-3" type="button" @click="isDisabled=false">
                                    <input class="form-check-input" name="optionRadio" type="radio" id="inlineCheckbox2" value="addDocuments">
                                    <label class="form-check-label" for="inlineCheckbox2">Добавить файлы</label>
                                </div>
                                <form class="input-group mb-3" id="file-form" :class="isDisabled ? 'disabled' : ''">
                                    <div class="input-group-prepend">
                                        <label id="file-input-btn" class="btn btn-md btn-outline-primary m-0 px-3 py-2 z-depth-0 waves-effect">
                                            Обзор <input @change="uploadFiles($event)" id="file-input" type="file" hidden multiple>
                                        </label>
                                    </div>
                                    <input id="filesInput" type="text" value="Choose file..." class="form-control text-file-input rounded-0" placeholder="" aria-label="" aria-describedby="basic-addon1">
                                </form>
                                -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" @click="updateCatalogOrDocs($event)" class="btn btn-md btn-primary">Создать</button>
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
    import Catalog from "../components/Catalog/Catalog";
    export default {
        name: "Catalogs",
        components: {
            ErrorMessage,
            Catalog
        },
        data() {
            return {
                isDisabled: true,
                files:[],
                catalogsArray:[]
            }
        },
        created() {
            this.$store.dispatch("catalog/catalogGetAll").then(response =>{
                this.catalogsArray = response;
            });
        },
        mounted() {
            $(this.$refs.vuemodal).on("hidden.bs.modal", this.onHiddenCreateModal);
        },
        computed: {
            isLoading() {
                return this.$store.getters["catalog/isLoading"];
            },
            hasError() {
                return this.$store.getters["catalog/hasError"];
            },
            getError() {
                return this.$store.getters["catalog/getError"];
            },
            hasCatalogs() {
                return this.$store.getters["catalog/hasCatalogs"];
            },
            getCatalogs() {
                return this.$store.getters["catalog/getCatalogs"];
            },
            canCreatePost() {
                return this.$store.getters["security/hasRole"]("ROLE_FOO");
            },
        },
        methods: {
            onHiddenCreateModal(){
                alert('asad');
                $('#filesInput').val('');
                $('#catalogName').val('Choose file...');
            },
            uploadFiles(event){
                event.preventDefault();
                this.files = event.target.files;
                let filenames = Array.from(this.files).reduce((x,y) => x + '"' + y.name + '", ' ,"");
                $('#filesInput').val(filenames);
            },
            canEditPost(newsArticle){
                var curuser = this.$store.getters["security/getUserData"];
                var curnews = this.newsArray.find(x=>x === newsArticle);
                if(curnews.user.id === curuser.id) return true;
                else return false;
            },
            async createCatalogOrAddDocs(event) {
                window.$('#createModal').modal('hide');
                let actionString = '';
                let catalogid = $('#createModal').attr('catalogid');
                let data;
                if(this.isDisabled) {
                    actionString = 'catalog/catalogPost';
                    let catalogName = $('#catalogName').val();
                    data = [ catalogid, catalogName ];
                } else {
                    data = new FormData();
                    actionString = 'catalog/documentPost';
                    this.files.forEach((x,i=1)=> { data.append('file'+i, x); i++; } )
                    //data.append('file', this.files);
                    data.append('catalogId', catalogid);
                }
                console.log(actionString, data);
                const result = await this.$store.dispatch(actionString, data);
            },
            async updateCatalogOrDocs(event) {
                /*
                window.$('#createModal').modal('hide');
                let actionString = '';
                let catalogid = $('#createModal').attr('catalogid');
                let data;
                if(this.isDisabled) {
                    actionString = 'catalog/catalogPost';
                    let catalogName = $('#catalogName').val();
                    data = [ catalogid, catalogName ];
                } else {
                    data = new FormData();
                    actionString = 'catalog/create';
                    data.append('files', this.files);
                    data.append('catalogId', catalogid);
                }
                console.log(actionString, data);
                const result = await this.$store.dispatch(actionString, data);
                */
            },
            /*
            async DeleteNews(event){
                console.log(event.currentTarget.id);
                event.preventDefault();
                if (confirm('Are you sure you want to delete this news?')){
                    await this.$store.dispatch("news/deleteNews", event.currentTarget.id);
                }
            },
             */
        }
    }
</script>